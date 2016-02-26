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

//TODO
function mo_load_solutions($start, $end, $pid = '', $uid = '', $state = '')
{

}

function mo_load_solution($sid)
{
    if (!$sid || !is_string($sid)) {
        return False;
    }
    global $mo_solution, $mo_solution_failed, $mo_now_solution;
    if (isset($mo_solution_failed[$sid])) {
        $mo_now_solution = NULL;

        return False;
    }
    if (isset($mo_solution[$sid])) {
        $mo_now_solution = $sid;

        return True;
    }
    $solution = mo_read_cache_array('mo:solution:'.$sid);
    if ($solution) {
        $mo_solution[$sid] = $solution;
        $mo_now_solution = $sid;
        mo_set_cache_timeout('mo:solution:'.$sid, WEEK);

        return True;
    }
    $result = mo_db_readone('mo_solution', array('_id'=>new MongoDB\BSON\ObjectID($sid)));
    if (count($result)) {
        $mo_solution[$sid] = $result;
        mo_cache_solution($result, $sid);
        $mo_now_solution = $sid;

        return True;
    } else {
        $mo_solution_failed[$sid] = True;
        $mo_now_solution = NULL;

        return False;
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
        if ($mo_now_solution == NULL || !mo_load_solution($mo_now_solution)
            || !isset($mo_solution[$mo_now_solution][$category])) {
            return NULL;
        }

        return apply_filter('solution_'.$category,
                                htmlspecialchars($mo_solution[$mo_now_solution][$category]));
    } else { // 获取指定$sid的solution ==> mo_get_solution($sid, $category)
        $sid = $args[0];
        $category = $args[1];
        if (!mo_load_solution($sid) || !isset($mo_solution[$sid][$category])) {
            return NULL;
        }

        return apply_filter('solution_'.$category,
                                htmlspecialchars($mo_solution[$sid][$category]));
    }
}

function mo_cache_solution($solution, $sid)
{
    if (mo_exist_cache('mo:solution:'.$sid)) {
        return false;
    }

    $rt = mo_write_cache_array('mo:solution:'.$sid, $solution);
    mo_set_cache_timeout('mo:solution:'.$sid, WEEK);

    return $rt;
}

function mo_add_new_solution($pid, $lang, $post, $uid = '')
{
    //TODO: Get uid manully if null

    // Prepare info
    $length = strlen($post);
    $post = base64_encode($post);
    $pid = new MongoDB\BSON\ObjectID($pid);
    $uid = new MongoDB\BSON\ObjectID($uid);

    // Add to mo_solution
    $solution = array('pid'=>$pid,
                        'uid'=>$uid,
                        'code'=>$post, 'code_length'=>$length,
                        'language'=>$lang,
                        'post_time'=>$_SERVER['REQUEST_TIME'],
                        'result'=>0);
    $result = mo_db_insertone('mo_solution', $solution);
    if (!$result->getInsertedCount()) {
        mo_log("Fail to add a solution. (PID=$pid)", $uid);
        return False;
    }
    $sid = $result->getInsertedId();

    // Add to mo_solution_pending
    $solution['sid'] = $sid;
    $result = mo_db_insertone('mo_solution_pending', $solution);
    if (!$result->getInsertedCount()) {
        mo_log("Fail to add a solution. (PID=$pid)", $uid);
        return False;
    }
    $spid = $result->getInsertedId();

    // Update the user & the problem
    mo_db_updateone('mo_problem', array('_id'=>$pid),
                    array('$inc'=>array('submit'=>1)));
    mo_db_updateone('mo_user', array('_id'=>$uid),
                    array('$inc'=>array('submit'=>1)));

    $result = mo_db_updateone('mo_user', array('_id'=>$uid),
                    array('$addToSet'=>array('try_list'=>(string) $pid)));
    if ($result->getModifiedCount()) {
        mo_db_updateone('mo_user', array('_id'=>$uid),
                        array('$inc'=>array('try'=>1)));
        mo_db_updateone('mo_problem', array('_id'=>$pid),
                        array('$inc'=>array('try'=>1)));
    }

    // Publish the new solution
    $solution['spid'] = (string) $spid;
    $solution['sid'] = (string) $solution['sid'];
    $solution['uid'] = (string) $solution['uid'];
    $solution['pid'] = (string) $solution['pid'];
    mo_publish('mo://moyoj/ClientServer', $solution);

    mo_write_note('A new solution has been added.');
    mo_log("New solution. (PID=$pid, SID=$sid)", $uid);

    return $sid;
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
        return mo_get_solution('client');
    } else {
        return mo_get_solution($sid, 'client');
    }
}

function mo_get_solution_code($sid = '')
{
    if (!$sid) {
        return mo_get_solution('code');
    } else {
        return mo_get_solution($sid, 'code');
    }
}

function mo_get_solution_post_time($sid = '')
{
    if (!$sid) {
        return mo_get_solution('post_time');
    } else {
        return mo_get_solution($sid, 'post_time');
    }
}

function mo_get_solution_state($sid = '')
{
    if (!$sid) {
        return mo_get_solution('state');
    } else {
        return mo_get_solution($sid, 'state');
    }
}

function mo_get_solution_language($sid = '')
{
    if (!$sid) {
        return mo_get_solution('language');
    } else {
        return mo_get_solution($sid, 'language');
    }
}

function mo_get_solution_code_length($sid = '')
{
    if (!$sid) {
        return mo_get_solution('code_length');
    } else {
        return mo_get_solution($sid, 'code_length');
    }
}

function mo_get_solution_used_time($sid = '')
{
    if (!$sid) {
        return mo_get_solution('used_time');
    } else {
        return mo_get_solution($sid, 'used_time');
    }
}

function mo_get_solution_used_memory($sid = '')
{
    if (!$sid) {
        return mo_get_solution('used_memory');
    } else {
        return mo_get_solution($sid, 'used_memory');
    }
}

function mo_get_solution_detail($sid = '')
{
    if (!$sid) {
        return mo_get_solution('detail');
    } else {
        return mo_get_solution($sid, 'detail');
    }
}

function mo_get_solution_detail_result($sid = '')
{
    if (!$sid) {
        return mo_get_solution('detail_result');
    } else {
        return mo_get_solution($sid, 'detail_result');
    }
}

function mo_get_solution_detail_time($sid = '')
{
    if (!$sid) {
        return mo_get_solution('detail_time');
    } else {
        return mo_get_solution($sid, 'detail_time');
    }
}

function mo_get_solution_detail_memory($sid = '')
{
    if (!$sid) {
        return mo_get_solution('detail_memory');
    } else {
        return mo_get_solution($sid, 'detail_memory');
    }
}
