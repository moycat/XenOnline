<?php
/*
 * mo-includes/function-problem.php @ MoyOJ
 *
 * This file provides the functions of viewing problems, submitting
 * a new solution.
 *
 * Licensed under GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 */

function mo_list_problems($start, $end, $tag = '')
{
    global $db;
    $start -= 1;
    $sql = 'SELECT `id`, `title`, `tag`, `extra`, `solved`, `try` FROM `mo_judge_problem` WHERE `state` = 1 ';
    $piece = $end - $start + 1;
    if ($tag) {
        $sql .= "AND (MATCH ($tag) AGAINST (?)) ORDER BY `id` DESC LIMIT $start,$piece";
        $db->prepare($sql);
        $db->bind('s', $tag);
    } else {
        $sql .= "ORDER BY `id` DESC LIMIT $start,$piece";
        $db->prepare($sql);
    }
    $result = $db->execute();

    return $result;
}

function mo_list_solutions($start, $end, $pid = 'all', $uid = 'all', $state = 'all')
{
    global $db;
    $start -= 1;
    $sql = 'SELECT `id`, `pid`, `uid`, `post_time`, `state`, `language`, `code_length`, `used_time`, `used_memory` FROM `mo_judge_solution` WHERE 1=1 ';
    if (is_numeric($pid)) {
        $sql .= " AND `pid` = $pid";
    }
    if (is_numeric($uid)) {
        $sql .= " AND `uid` = $uid";
    }
    if (is_numeric($state)) {
        $sql .= " AND `uid` = $state";
    }
    $piece = $end - $start + 1;
    $sql .= " ORDER BY `id` DESC LIMIT $start,$piece";
    $db->prepare($sql);
    $result = $db->execute();

    return $result;
}

function mo_load_problem($pid)
{
    global $db, $mo_problem;
    $sql = 'SELECT * FROM `mo_judge_problem` WHERE `id` = ?';
    $db->prepare($sql);
    $db->bind('i', $pid);
    $result = $db->execute();
    if (!$result || !$result[0]['state']) {
        return false;
    }
    $mo_problem[$pid] = $result[0];
    $mo_problem[$pid]['extra'] = unserialize($mo_problem[$pid]['extra']);

    return true;
}

function mo_load_solution($sid)
{
    global $db, $user, $mo_solution;
    $sql = 'SELECT * FROM `mo_judge_solution` WHERE `id` = ?';
    $db->prepare($sql);
    $db->bind('i', $sid);
    $result = $db->execute();
    if (!$result || $result[0]['uid'] != $user->getUID()) {
        return false;
    }
    $mo_solution[$sid] = $result[0];
    $mo_solution[$sid]['code'] = base64_decode($result[0]['code']);

    return true;
}

function mo_get_solution($sid, $category)
{
    global $mo_solution;
    if (isset($mo_solution[$sid][$category])) {
        return $mo_solution[$sid][$category];
    } else {
        return false;
    }
}

function mo_get_problem($pid, $category)
{
    global $mo_problem;
    if (isset($mo_problem[$pid][$category])) {
        return $mo_problem[$pid][$category];
    } else {
        return false;
    }
}

function mo_get_probname_by_pid($pid)
{
    global $mo_temp;
    if (isset($mo_temp['mo-probname-'.$pid])) {
        return;
    }
    $probname = mo_read_cache('mo-probname-'.$pid);
    if (!$probname) {
        global $db;
        $sql = 'SELECT `title` FROM `mo_judge_problem` WHERE `id` = ?';
        $db->prepare($sql);
        $db->bind('i', $pid);
        $result = $db->execute();
        if (count($result)) {
            mo_write_cache('mo-probname-'.$pid, $result[0]['title']);

            return $result[0]['title'];
        } else {
            $mo_temp['mo-probname-'.$pid] = 1;

            return;
        }
    }

    return $probname;
}

function mo_add_new_solution($pid, $lang, $post, $uid = 0)
{
    global $user;
    if (!$uid) {
        $uid = $user->getUID();
    }
    if (!($uid && $pid && $post)) {
        return false;
    }
    global $db;
    $length = strlen($post);
    $post = base64_encode($post);
    //$sql = 'SELECT `submit`, `try`, `submit_problem` FROM `mo_stat_user` WHERE `uid` = ?';
    $sql = 'SELECT `submit_problem` FROM `mo_stat_user` WHERE `uid` = ?';
    $db->prepare($sql);
    $db->bind('i', $uid);
    $result = $db->execute();
    $submit_problem = explode(' ', $result[0]['submit_problem']);
    if (!in_array((string) $pid, $submit_problem)) {
        $sql = 'UPDATE `mo_stat_user` SET submit = submit+1, try = try+1, submit_problem = ? WHERE `uid` = ?';
        $result[0]['submit_problem'] .= "$pid ";
        mo_problem_add_submit($pid, true);
    } else {
        $sql = 'UPDATE `mo_stat_user` SET submit = submit+1, submit_problem = ? WHERE `uid` = ?';
        mo_problem_add_submit($pid);
    }
    $db->prepare($sql);
    $db->bind('si', $result[0]['submit_problem'],  $uid);
    $db->execute();
    $sql = 'INSERT INTO `mo_judge_solution` (`pid`, `uid`, `code`, `post_time`, `language`, `code_length`) VALUES (?, ?, ?, CURRENT_TIMESTAMP, ?, ?)';
    $db->prepare($sql);
    $db->bind('iisii', $pid, $uid, $post, $lang, $length);
    $db->execute();
    $sid = $db->getInsID();
    $data = array('sid' => $sid, 'pid' => $pid, 'uid' => $uid, 'lang' => $lang, 'code' => $post);
    mo_write_note('A new solution has been added.');
    mo_log_user("User added a new solution (SID = $sid).");
    socket_push($data);

    return $sid;
}

function socket_push($data)
{
    if (!mo_com_socket($data)) {
        mo_log_user('Solution Failed Pushing (SID = '.$data['sid'].').');

        return false;
    } else {
        return true;
    }
}

function mo_problem_add_submit($pid, $add_try = false)
{
    global $db;
    if ($add_try) {
        $sql = 'UPDATE `mo_judge_problem` SET try = try+1, submit = submit+1 WHERE `id` = ?';
    } else {
        $sql = 'UPDATE `mo_judge_problem` SET submit = submit+1 WHERE `id` = ?';
    }
    $db->prepare($sql);
    $db->bind('i', $pid);
    $db->execute();
}
