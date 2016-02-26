<?php
/*
 * mo-includes/function-problem.php @ MoyOJ
 *
 * This file provides the functions of problems.
 *
 * Licensed under GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 */

function mo_load_problems($start, $end, $tag = '')
{
    //TODO
}

function mo_load_problem($pid)
{
    if (!$pid || !is_numeric($pid)) {
        return false;
    }
    global $mo_problem, $mo_problem_failed, $mo_now_problem;
    if (isset($mo_problem_failed[$pid])) {
        $mo_now_problem = null;

        return false;
    }
    if (isset($mo_problem[$pid])) {
        $mo_now_problem = $pid;

        return true;
    }
    $problem = mo_read_cache_array('mo:problem:'.$pid);
    if (!$problem) { // Not in cache, read it from the database
        $result = mo_db_readone('mo_problem', array('_id' => new MongoDB\BSON\ObjectID($pid)));
        if (!$result) {
            $mo_problem_failed[$pid] = true;
            $mo_now_problem == null;

            return false;
        }
        mo_cache_problem($result, $pid);
        $mo_problem[$pid] = mo_read_cache_array('mo:problem:'.$pid);
    } else {
        $mo_problem[$pid] = $problem;
    }
    if ($mo_problem[$pid]['state'] == 0) { // Return true only if available
        $mo_problem_failed[$pid] = true;
        $mo_now_problem = null;
        mo_set_cache_timeout('mo:problem:'.$pid, 60);

        return false;
    }
    $mo_now_problem = $pid;
    mo_set_cache_timeout('mo:problem:'.$pid, MOUTH);

    return true;
}

function mo_set_now_problem($pid)
{
    global $mo_now_problem;
    $mo_now_problem = $pid;
}

function mo_get_problem()
{
    global $mo_problem, $mo_now_problem;
    $args = func_get_args();
    if (count($args) == 1) { // 获取当前指向的problem ==> mo_get_problem($category)
    $category = $args[0];
        if ($mo_now_problem == null || !mo_load_problem($mo_now_problem)
            || !isset($mo_problem[$mo_now_problem][$category])) {
            return;
        }

        return apply_filter('problem_'.$category,
                                htmlspecialchars($mo_problem[$mo_now_problem][$category]));
    } else { // 获取指定$pid的problem ==> mo_get_problem($pid, $category)
        $pid = $args[0];
        $category = $args[1];
        if (!mo_load_problem($pid) || !isset($mo_problem[$mo_now_problem][$category])) {
            return;
        }

        return apply_filter('problem_'.$category,
                                htmlspecialchars($mo_problem[$pid][$category]));
    }
}

function mo_cache_problem($problem, $pid)
{
    if (mo_exist_cache('mo:problem:'.$pid)) {
        return false;
    }

    $rt = mo_write_cache_array('mo:problem:'.$pid, $problem);
    mo_set_cache_timeout('mo:problem:'.$pid, MOUTH);

    return $rt;
}

function mo_get_problem_id($pid = '')
{
    if (!$pid) {
        return mo_get_problem('_id');
    } else {
        return mo_get_problem($pid, '_id');
    }
}

function mo_get_problem_title($pid = '')
{
    if (!$pid) {
        return mo_get_problem('title');
    } else {
        return mo_get_problem($pid, 'title');
    }
}

function mo_get_problem_description($pid = '')
{
    if (!$pid) {
        return mo_get_problem('description');
    } else {
        return mo_get_problem($pid, 'description');
    }
}

function mo_get_problem_tag($pid = '')
{
    if (!$pid) {
        return mo_get_problem('tag');
    } else {
        return mo_get_problem($pid, 'tag');
    }
}

function mo_get_problem_hash($pid = '')
{
    if ($pid == '-1') {
        return mo_get_problem('hash');
    } else {
        return mo_get_problem($pid, 'hash');
    }
}

function mo_get_problem_ver($pid = '')
{
    if (!$pid) {
        return mo_get_problem('ver');
    } else {
        return mo_get_problem($pid, 'ver');
    }
}

function mo_get_problem_post_time($pid = '')
{
    if (!$pid) {
        return mo_get_problem('post_time');
    } else {
        return mo_get_problem($pid, 'post_time');
    }
}

function mo_get_problem_time_limit($pid = '')
{
    if (!$pid) {
        return mo_get_problem('time_limit');
    } else {
        return mo_get_problem($pid, 'time_limit');
    }
}

function mo_get_problem_memory_limit($pid = '')
{
    if (!$pid) {
        return mo_get_problem('memory_limit');
    } else {
        return mo_get_problem($pid, 'memory_limit');
    }
}

function mo_get_problem_test_turn($pid = '')
{
    if (!$pid) {
        return mo_get_problem('test_turn');
    } else {
        return mo_get_problem($pid, 'test_turn');
    }
}

function mo_get_problem_try($pid = '')
{
    if (!$pid) {
        return mo_get_problem('try');
    } else {
        return mo_get_problem($pid, 'try');
    }
}

function mo_get_problem_solved($pid = '')
{
    if (!$pid) {
        return mo_get_problem('solved');
    } else {
        return mo_get_problem($pid, 'solved');
    }
}

function mo_get_problem_submit($pid = '')
{
    if (!$pid) {
        return mo_get_problem('submit');
    } else {
        return mo_get_problem($pid, 'submit');
    }
}

function mo_get_problem_ac($pid = '')
{
    if (!$pid) {
        return mo_get_problem('ac');
    } else {
        return mo_get_problem($pid, 'ac');
    }
}
