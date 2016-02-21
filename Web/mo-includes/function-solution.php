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

function mo_load_solutions($start, $end, $pid = '', $uid = '', $state = '')
{
    global $db, $mo_solution;
    $rt = array();
    $start -= 1;
    $sql = 'SELECT * FROM `mo_judge_solution` WHERE 1=1';
    $piece = $end - $start + 1;
    if ($pid) {
        $sql .= " AND `pid` = $pid";
    }
    if ($uid) {
        $sql .= " AND `uid` = $uid";
    }
    if ($state) {
        $sql .= " AND `uid` = $state";
    }
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
        $mo_now_solution = NULL;

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
        $mo_now_solution = null;

        return false;
    }
}

function mo_set_now_solution($sid)
{
    global $mo_now_solution;
    $mo_now_solution = $sid;
}

function mo_get_solution()
{
    global $mo_solution, $mo_now_solution;
    $args = func_get_args();
    if (count($args) == 1) { // 获取当前指向的solution ==> mo_get_solution($category)
        $category = $args[0];
        if ($mo_now_solution == NULL || !mo_load_solution($mo_now_solution)) {
            return NULL;
        }
        return $mo_solution[$mo_now_solution][$category];
    } else { // 获取指定$sid的solution ==> mo_get_solution($sid, $category)
        $sid = (string) $args[0];
        $category = $args[1];
        if (!mo_load_solution($sid)) {
            return NULL;
        }

        return $mo_solution[$sid][$category];
    }
}

function mo_cache_solution($solution)
{
    if (mo_exist_cache('mo:solution:'.$solution['id'])) {
        return false;
    }

    return mo_write_cache_array('mo:solution:'.$solution['id'], $solution);
}

function mo_add_new_solution($pid, $lang, $post, $uid = 0)
{
    global $user;
    // Check permission
    if (!$uid) {
        $uid = $user->getUID();
    }
    if (!($uid && $pid && $post)) {
        return false;
    }
    // Prepare info
    global $db;
    $length = strlen($post);
    $post = base64_encode($post);
    $submit_problem_raw = mo_get_user_submit_problem($uid);
    $submit_problem = explode(' ', $submit_problem_raw);
    if (!in_array((string) $pid, $submit_problem)) {
        $sql = 'UPDATE `mo_stat_user` SET submit = submit+1, try = try+1, submit_problem = ? WHERE `uid` = ?';
        $submit_problem_raw .= "$pid ";
        mo_write_cache_array_item('mo:user:'.$uid.':stat', 'submit_problem', $submit_problem_raw);
        mo_incr_cache_array('mo:user:'.$uid.':stat', 'submit');
        mo_incr_cache_array('mo:user:'.$uid.':stat', 'try');
        mo_problem_add_submit($pid, true);
    } else {
        $sql = 'UPDATE `mo_stat_user` SET submit = submit+1, submit_problem = ? WHERE `uid` = ?';
        mo_incr_cache_array('mo:user:'.$uid.':stat', 'submit');
        mo_problem_add_submit($pid);
    }
    $db->prepare($sql);
    $db->bind('si', $submit_problem_raw,  $uid);
    $db->execute();
    $sql = 'INSERT INTO `mo_judge_solution` (`pid`, `uid`, `code`, `post_time`, `language`, `code_length`) VALUES (?, ?, ?, CURRENT_TIMESTAMP, ?, ?)';
    $db->prepare($sql);
    $db->bind('iisii', $pid, $uid, $post, $lang, $length);
    $db->execute();
    $sid = $db->getInsID();
    // Prepare the data for the clients' server
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
        if (mo_exist_cache('mo:problem:'.$pid)) {
            mo_incr_cache_array('mo:problem:'.$pid, 'try');
            mo_incr_cache_array('mo:problem:'.$pid, 'submit');
        }
    } else {
        $sql = 'UPDATE `mo_judge_problem` SET submit = submit+1 WHERE `id` = ?';
        if (mo_exist_cache('mo:problem:'.$pid)) {
            mo_incr_cache_array('mo:problem:'.$pid, 'submit');
        }
    }
    $db->prepare($sql);
    $db->bind('i', $pid);
    $db->execute();
}

function mo_get_solution_id($sid = '')
{
    if (!$sid) {
        return mo_get_solution('id');
    } else {
        return mo_get_solution($sid, 'id');
    }
}

function mo_get_solution_pid($sid = '')
{
    if (!$sid) {
        return mo_get_solution('pid');
    } else {
        return mo_get_solution($sid, 'pid');
    }
}

function mo_get_solution_uid($sid = '')
{
    if (!$sid) {
        return mo_get_solution('uid');
    } else {
        return mo_get_solution($sid, 'uid');
    }
}

function mo_get_solution_client($sid = '')
{
    if (!$sid) {
        return apply_filter('solutionClient', mo_get_solution('client'));
    } else {
        return apply_filter('solutionClient', mo_get_solution($sid, 'client'));
    }
}

function mo_get_solution_code($sid = '')
{
    if (!$sid) {
        return apply_filter('solutionCode', mo_get_solution('code'));
    } else {
        return apply_filter('solutionCode', mo_get_solution($sid, 'code'));
    }
}

function mo_get_solution_post_time($sid = '')
{
    if (!$sid) {
        return apply_filter('solutionPostTime', mo_get_solution('post_time'));
    } else {
        return apply_filter('solutionPostTime', mo_get_solution($sid, 'post_time'));
    }
}

function mo_get_solution_state($sid = '')
{
    if (!$sid) {
        return apply_filter('solutionState', mo_get_solution('state'));
    } else {
        return apply_filter('solutionState', mo_get_solution($sid, 'state'));
    }
}

function mo_get_solution_language($sid = '')
{
    if (!$sid) {
        return apply_filter('solutionLanguage', mo_get_solution('language'));
    } else {
        return apply_filter('solutionLanguage', mo_get_solution($sid, 'language'));
    }
}

function mo_get_solution_code_length($sid = '')
{
    if (!$sid) {
        return apply_filter('solutionCodeLength', mo_get_solution('code_length'));
    } else {
        return apply_filter('solutionCodeLength', mo_get_solution($sid, 'code_length'));
    }
}

function mo_get_solution_used_time($sid = '')
{
    if (!$sid) {
        return apply_filter('solutionUsedTime', mo_get_solution('used_time'));
    } else {
        return apply_filter('solutionUsedTime', mo_get_solution($sid, 'used_time'));
    }
}

function mo_get_solution_used_memory($sid = '')
{
    if (!$sid) {
        return apply_filter('solutionUsedMemory', mo_get_solution('used_memory'));
    } else {
        return apply_filter('solutionUsedMemory', mo_get_solution($sid, 'used_memory'));
    }
}

function mo_get_solution_detail($sid = '')
{
    if (!$sid) {
        return apply_filter('solutionDetail', mo_get_solution('detail'));
    } else {
        return apply_filter('solutionDetail', mo_get_solution($sid, 'detail'));
    }
}

function mo_get_solution_detail_result($sid = '')
{
    if (!$sid) {
        return apply_filter('solutionDetailResult', mo_get_solution('detail_result'));
    } else {
        return apply_filter('solutionDetailResult', mo_get_solution($sid, 'detail_result'));
    }
}

function mo_get_solution_detail_time($sid = '')
{
    if (!$sid) {
        return apply_filter('solutionDetailTime', mo_get_solution('detail_time'));
    } else {
        return apply_filter('solutionDetailTime', mo_get_solution($sid, 'detail_time'));
    }
}

function mo_get_solution_detail_memory($sid = '')
{
    if (!$sid) {
        return apply_filter('solutionDetailMemory', mo_get_solution('detail_memory'));
    } else {
        return apply_filter('solutionDetailMemory', mo_get_solution($sid, 'detail_memory'));
    }
}
