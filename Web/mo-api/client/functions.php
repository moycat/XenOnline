<?php

use Workerman\Lib\Timer;

function heartbeat($connection, $data)
{
    if (!$connection->cid || !isset($data['mem_ratio'], $data['loadavg']['lavg_1'], $data['loadavg']['lavg_5'], $data['loadavg']['lavg_15'], $data['timestamp'])) {
        p("Bad heartbeat. ( cid = $connection->cid, IP = $connection->IP )");

        return false;
    }
    $connection->last_ping = (int) $data['timestamp'];
    global $db;
    $timestamp = date('Y-m-d G:i:s');
    $sql = 'UPDATE `mo_judge_client` SET `load_1` = ?, `load_5` = ?, `load_15` = ?, `memory` = ?, `last_ping` = ? WHERE `id` = ?';
    $mark = $db->prepare($sql);
    $db->bind($mark, 'sssssi', $data['loadavg']['lavg_1'], $data['loadavg']['lavg_5'], $data['loadavg']['lavg_15'], $data['mem_ratio'], $timestamp, $connection->cid);
    $db->execute($mark);
    p("Get a heartbeat. ( cid = $connection->cid, IP = $connection->IP )");

    return true;
}

function update($connection, $data)
{
    global $db, $task;
    if (!$connection->cid || !isset($data['state'], $data['used_time'], $data['used_memory'], $data['detail'], $data['detail_result'],
            $data['detail_time'], $data['detail_memory'], $data['sid'])) {
        return false;
    }
    $sid = (int) $data['sid'];
    if (!isset($task[$sid]) || $task[$sid]->cid != $connection->cid) {
        p("Bad update. ( cid = $connection->cid, IP = $connection->IP )");

        return false;
    }
    mo_del_cache('mo:solution:'.$data['sid']);
    $sql = 'UPDATE `mo_judge_solution` SET `client` = ?, `state` = ?, `used_time` = ?, `used_memory` = ?, `detail` = ?, '.
                '`detail_result` = ?, `detail_time` = ?, `detail_memory` = ? WHERE `id` = ?';
    $mark = $db->prepare($sql);
    $db->bind($mark, 'iiiissssi', $connection->cid, $data['state'], $data['used_time'], $data['used_memory'], $data['detail'], $data['detail_result'],
                        $data['detail_time'], $data['detail_memory'], $data['sid']);
    $db->execute($mark);
    $uid = (int) $task[$sid]->uid;
    $pid = (int) $task[$sid]->pid;
    if ((int) $data['state'] == 10) {
        $sql = 'SELECT `ac_problem`, `accept`, `solve` FROM `mo_stat_user` WHERE `uid` = ?';
        $mark = $db->prepare($sql);
        $db->bind($mark, 'i', $uid);
        $user = $db->execute($mark);
        $user_ac = $user[0]['ac_problem'];
        $tmp = explode(' ', $user_ac);
        if (!in_array((string) $pid, $tmp)) {
            $user_ac .= "$pid ";
            $sql1 = 'UPDATE `mo_judge_problem` SET solved = solved+1, ac = ac+1 WHERE `id` = ?';
            $sql2 = 'UPDATE `mo_stat_user` SET ac_problem = ?, accept = accept+1, solve = solve+1 WHERE `uid` = ?';
            mo_incr_cache_array('mo:problem:'.$pid, 'solved');
            mo_incr_cache_array('mo:problem:'.$pid, 'ac');
            mo_incr_cache_array('mo:user:'.$uid.':stat', 'accept');
            mo_incr_cache_array('mo:user:'.$uid.':stat', 'solve');
            mo_write_cache_array_item('mo:user:'.$uid.':stat', 'ac_problem', $user_ac);
        } else {
            $sql1 = 'UPDATE `mo_judge_problem` SET ac = ac+1 WHERE `id` = ?';
            $sql2 = 'UPDATE `mo_stat_user` SET ac_problem = ?, accept = accept+1 WHERE `uid` = ?';
            mo_incr_cache_array('mo:problem:'.$pid, 'ac');
            mo_incr_cache_array('mo:user:'.$uid.':stat', 'accept');
            mo_write_cache_array_item('mo:user:'.$uid.':stat', 'ac_problem', $user_ac);
        }
        $mark = $db->prepare($sql1);
        $db->bind($mark, 'i', $pid);
        $db->execute($mark);
        $mark = $db->prepare($sql2);
        $db->bind($mark, 'si', $user_ac, $uid);
        $db->execute($mark);
    }
    p("Get a update. The solution is done. ( sid = $sid, cid = $connection->cid, IP = $connection->IP )");
    unset($task[$sid]);

    return true;
}

function update_state($connection, $data)
{
    global $db, $task;
    if (!$connection->cid || !isset($data['timestamp'], $data['sid'], $data['state'])) {
        return false;
    }
    $sid = (int) $data['sid'];
    if (!isset($task[$sid]) || $task[$sid]->cid != $connection->cid || $task[$sid]->state < (int) $data['state']) {
        p("Bad update-state. ( cid = $connection->cid, IP = $connection->IP )");

        return false;
    }
    $task[$sid]->last_time = (int) $data['timestamp'];
    $task[$sid]->got = 1;
    $task[$sid]->state = $data['state'];
    mo_write_cache_array_item('mo:solution:'.$sid, 'state', $task[$sid]->state);
    p("Get a update-state. ( sid = $sid, cid = $connection->cid, IP = $connection->IP )");

    return true;
}

function login($connection, $data)
{
    global $db, $cid, $ava_client, $client_sorted;
    if (!isset($data['client_id'], $data['client_hash']) || $connection->cid) {
        p("Bad Login Action ( IP = $connection->IP )");
        cut($connection, 'refuse');

        return false;
    }
    $sql = 'SELECT name FROM mo_judge_client WHERE id = ? AND hash = ?';
    $mark = $db->prepare($sql);
    $db->bind($mark, 'is', $data['client_id'], $data['client_hash']);
    $result = $db->execute($mark);
    if (!$result) {
        p("Bad Client ID or Hash ( IP = $connection->IP )");
        cut($connection, 'refuse');

        return false;
    }
    Timer::del($connection->deadline);
    $connection->deadline = 0;
    if (isset($cid[$data['client_id']])) {
        cut($cid[$data['client_id']], 'another');
        unset($cid[$data['client_id']]);
    }
    $connection->cid = (string) $data['client_id'];
    $connection->name = $result[0]['name'];
    $cid[$connection->cid] = $connection;
    sendMsg($connection, array('action' => 'admit', 'client_name' => $result[0]['name']));
    $client_sorted = false;
    p("The client <$connection->name> has joined. ( cid = $connection->cid, IP = $connection->IP )");

    return true;
}

function sendMsg(&$connection, $msg)
{
    $msg = json_encode($msg)."\n";
    $connection->send($msg);
};

function kill_client($client)
{
    global $cid;
    $client = (string) $client;
    if (!isset($cid[$client])) {
        return false;
    }
    cut($cid[$client], 'refuse');
}

function cut(&$connection, $reason)
{
    sendMsg($connection, array('action' => $reason));
    $connection->close();
}

function check_forgotten()
{
    global $db, $task;
    $sql = 'SELECT `id`, `pid`, `uid`, `code`, `state`, `language` FROM `mo_judge_solution` WHERE `state` = 0';
    $mark = $db->prepare($sql);
    $result = $db->execute($mark);
    if (!count($result)) {
        return 0;
    }
    foreach ($result as $solution) {
        if (!isset($task[(int) $solution['id']])) {
            $data = array('sid' => $solution['id'], 'pid' => $solution['pid'], 'uid' => $solution['uid'], 'lang' => $solution['language'], 'code' => $solution['code']);
            $new_solution = new Solution($data);
            $new_solution->push();
        }
    }

    return true;
}

function check_lost()
{
    global $task;
    foreach ($task as $now) {
        if (time() - $now->last_time > (5 + $now->got * 55)) {
            $now->push();
        }
    }
}

function get_prob($pid)
{
    mo_load_problem($pid);
    $result = mo_read_cache_array('mo:problem:'.$pid);

    return $result;
}

function p($to_write)
{
    $time = date('Y-m-d H:i:s', time());
    echo "[$time] $to_write\n";
}
