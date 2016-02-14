<?php
/*
 * mo-includes/function-solution.php @ MoyOJ
 *
 * This file provides the functions of solutions.
 *
 * Licensed under GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 */

function mo_load_solutions($start, $end, $pid = 'all', $uid = 'all', $state = 'all')
{
    global $db, $mo_solution;
    $rt = array();
    $start -= 1;
    $sql = 'SELECT * FROM `mo_judge_solution` WHERE 1=1 ';
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
    foreach ($result as $solution) {
        $rt[] = $solution['id'];
        $mo_solution[$solution['id']] = $solution;
        mo_cache_solution($solution);
    }

    return $rt;
}

function mo_load_solution($sid)
{
    global $db, $mo_solution, $mo_solution_failed, $mo_now_solution;
    $sid = (string) $sid;
    if (isset($mo_solution_failed[$sid])) {
        return false;
    }
    if (isset($mo_solution[$sid])) {
        $mo_now_solution = $sid;

        return true;
    }
    $solution = mo_read_cache_array('mo:solution:'.$sid);
    if ($solution) {
        $mo_solution[$sid] = $solution;
        $mo_now_solution = $sid;

        return true;
    }
    $sql = 'SELECT * FROM `mo_judge_solution` WHERE `id` = ?';
    $db->prepare($sql);
    $db->bind('i', $sid);
    $result = $db->execute();
    if (count($result)) {
        $mo_solution[$sid] = $result[0];
        mo_cache_solution($result[0]);
        $mo_now_solution = $sid;

        return true;
    } else {
        $mo_solution_failed[$sid] = true;

        return false;
    }
}

function mo_get_solution()
{
    global $mo_solution, $mo_now_solution;
    $args = func_get_args();
    if (count($args) == 1) { // 获取当前指向的solution ==> mo_get_solution($category)
    $category = $args[0];
        if ($mo_now_solution == null) {
            return;
        } else {
            return $mo_solution[$mo_now_solution][$category];
        }
    } else { // 获取指定$sid的solution ==> mo_get_solution($sid, $category)
    $sid = (string) $args[0];
        $category = $args[1];
        if (!isset($mo_solution[$sid])) {
            if (!mo_load_solution($sid)) {
                return;
            }
        }

        return $mo_solution[$sid][$category];
    }
}

function mo_cache_solution($solution)
{
    if ((int) $solution['id'] <= 0) {
        return false;
    }

    return mo_write_cache('mo:solution:'.$solution['id'], $solution);
}

// TODO: 整理代码、利用缓存
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

function mo_get_solution_id($sid = '-1')
{
    if ($sid == '-1') {
        return mo_get_solution('id');
    } else {
        return mo_get_solution($sid, 'id');
    }
}

function mo_get_solution_pid($sid = '-1')
{
    if ($sid == '-1') {
        return mo_get_solution('pid');
    } else {
        return mo_get_solution($sid, 'pid');
    }
}

function mo_get_solution_uid($sid = '-1')
{
    if ($sid == '-1') {
        return mo_get_solution('uid');
    } else {
        return mo_get_solution($sid, 'uid');
    }
}

function mo_get_solution_client($sid = '-1')
{
    if ($sid == '-1') {
        return apply_filter('solutionClient', mo_get_solution('client'));
    } else {
        return apply_filter('solutionClient', mo_get_solution($sid, 'client'));
    }
}

function mo_get_solution_code($sid = '-1')
{
    if ($sid == '-1') {
        return apply_filter('solutionCode', mo_get_solution('code'));
    } else {
        return apply_filter('solutionCode', mo_get_solution($sid, 'code'));
    }
}

function mo_get_solution_post_time($sid = '-1')
{
    if ($sid == '-1') {
        return apply_filter('solutionPostTime', mo_get_solution('post_time'));
    } else {
        return apply_filter('solutionPostTime', mo_get_solution($sid, 'post_time'));
    }
}

function mo_get_solution_state($sid = '-1')
{
    if ($sid == '-1') {
        return apply_filter('solutionState', mo_get_solution('state'));
    } else {
        return apply_filter('solutionState', mo_get_solution($sid, 'state'));
    }
}

function mo_get_solution_language($sid = '-1')
{
    if ($sid == '-1') {
        return apply_filter('solutionLanguage', mo_get_solution('language'));
    } else {
        return apply_filter('solutionLanguage', mo_get_solution($sid, 'language'));
    }
}

function mo_get_solution_code_length($sid = '-1')
{
    if ($sid == '-1') {
        return apply_filter('solutionCodeLength', mo_get_solution('code_length'));
    } else {
        return apply_filter('solutionCodeLength', mo_get_solution($sid, 'code_length'));
    }
}

function mo_get_solution_used_time($sid = '-1')
{
    if ($sid == '-1') {
        return apply_filter('solutionUsedTime', mo_get_solution('used_time'));
    } else {
        return apply_filter('solutionUsedTime', mo_get_solution($sid, 'used_time'));
    }
}

function mo_get_solution_used_memory($sid = '-1')
{
    if ($sid == '-1') {
        return apply_filter('solutionUsedMemory', mo_get_solution('used_memory'));
    } else {
        return apply_filter('solutionUsedMemory', mo_get_solution($sid, 'used_memory'));
    }
}

function mo_get_solution_detail($sid = '-1')
{
    if ($sid == '-1') {
        return apply_filter('solutionDetail', mo_get_solution('detail'));
    } else {
        return apply_filter('solutionDetail', mo_get_solution($sid, 'detail'));
    }
}

function mo_get_solution_detail_result($sid = '-1')
{
    if ($sid == '-1') {
        return apply_filter('solutionDetailResult', mo_get_solution('detail_result'));
    } else {
        return apply_filter('solutionDetailResult', mo_get_solution($sid, 'detail_result'));
    }
}

function mo_get_solution_detail_time($sid = '-1')
{
    if ($sid == '-1') {
        return apply_filter('solutionDetailTime', mo_get_solution('detail_time'));
    } else {
        return apply_filter('solutionDetailTime', mo_get_solution($sid, 'detail_time'));
    }
}

function mo_get_solution_detail_memory($sid = '-1')
{
    if ($sid == '-1') {
        return apply_filter('solutionDetailMemory', mo_get_solution('detail_memory'));
    } else {
        return apply_filter('solutionDetailMemory', mo_get_solution($sid, 'detail_memory'));
    }
}
