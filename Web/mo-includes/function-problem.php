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
    global $db, $mo_problem;
    $rt = array();
    $start -= 1;
    $sql = 'SELECT * FROM `mo_judge_problem` WHERE `state` = 1 ';
    $piece = $end - $start + 1;
    if ($tag) {
        $sql .= "AND (MATCH (tag) AGAINST (? IN BOOLEAN MODE)) ORDER BY `id` DESC LIMIT $start,$piece";
        $db->prepare($sql);
        $db->bind('s', $tag);
    } else {
        $sql .= "ORDER BY `id` DESC LIMIT $start,$piece";
        $db->prepare($sql);
    }
    $result = $db->execute();
    foreach ($result as $problem) {
        $rt[] = $problem['id'];
        $mo_problem[$problem['id']] = $problem;
        mo_cache_problem($problem);
    }

    return $rt;
}

function mo_load_problem($pid)
{
    global $db, $mo_problem, $mo_problem_failed, $mo_now_problem;
    $pid = (string) $pid;
    if (isset($mo_problem_failed[$pid])) {
        $mo_now_problem == null;

        return false;
    }
    if (isset($mo_problem[$pid])) {
        $mo_now_problem = $pid;

        return true;
    }
    $problem = mo_read_cache_array('mo:problem:'.$pid);
    if (!$problem) { // Not in cache, read it from the database
        $sql = 'SELECT * FROM `mo_judge_problem` WHERE `id` = ?';
        $db->prepare($sql);
        $db->bind('i', $pid);
        $result = $db->execute();
        if (!$result) {
            $mo_problem_failed[$pid] = true;
            $mo_now_problem == null;

            return false;
        }
        $problem = $result[0];
        $mo_problem[$pid] = $result[0];
        $mo_problem[$pid]['extra'] = unserialize($mo_problem[$pid]['extra']);
        mo_cache_problem($problem);
    }
    if ($problem['state'] == 0) { // Check if available
        $mo_problem_failed[$pid] = true;
        $mo_now_problem == null;

        return false;
    }
    $mo_now_problem = $pid;
    $mo_problem[$pid] = $problem;

    return true;
}

function mo_get_problem()
{
    global $mo_problem, $mo_now_problem;
    $args = func_get_args();
    if (count($args) == 1) { // 获取当前指向的problem ==> mo_get_problem($category)
    $category = $args[0];
        if ($mo_now_problem == null) {
            return;
        } else {
            return $mo_problem[$mo_now_problem][$category];
        }
    } else { // 获取指定$pid的problem ==> mo_get_problem($pid, $category)
        $pid = (string) $args[0];
        $category = $args[1];
        if (!isset($mo_problem[$pid])) {
            if (!mo_load_problem($pid)) {
                return;
            }
        }

        return $mo_problem[$pid][$category];
    }
}

function mo_cache_problem($problem)
{
    if ((int) $problem['state'] == 0 || mo_exist_cache('mo:problem:'.$problem['id'])) {
        return false;
    }

    return mo_write_cache_array('mo:problem:'.$problem['id'], $problem);
}

function mo_get_problem_extra()
{
    global $mo_problem, $mo_now_problem;
    $args = func_get_args();
    if (count($args) == 1) { // 获取当前指向的problem ==> mo_get_problem_extra($category)
        $piece = $args[0];
        if ($mo_now_problem == null) {
            return;
        } else {
            return isset($mo_problem[$mo_now_problem]['extra'][$piece]) ?
                        $mo_problem[$mo_now_problem]['extra'][$piece] : null;
        }
    } else { // 获取指定$pid的problem ==> mo_get_problem_extra($pid, $piece)
        $pid = (string) $args[0];
        $piece = $args[1];
        if (!isset($mo_problem[$pid])) {
            if (!mo_load_problem($pid)) {
                return;
            }
        }

        return isset($mo_problem[$pid]['extra'][$piece]) ?
                    $mo_problem[$pid]['extra'][$piece] : null;
    }
}

function mo_get_problem_id($pid = '-1')
{
    if ($pid == '-1') {
        return mo_get_problem('id');
    } else {
        return mo_get_problem($pid, 'id');
    }
}

function mo_get_problem_title($pid = '-1')
{
    if ($pid == '-1') {
        return apply_filter('problemTitle', mo_get_problem('title'));
    } else {
        return apply_filter('problemTitle', mo_get_problem($pid, 'title'));
    }
}

function mo_get_problem_description($pid = '-1')
{
    if ($pid == '-1') {
        return apply_filter('problemDescription', mo_get_problem('description'));
    } else {
        return apply_filter('problemDescription', mo_get_problem($pid, 'description'));
    }
}

function mo_get_problem_tag($pid = '-1')
{
    if ($pid == '-1') {
        return apply_filter('problemTag', mo_get_problem('tag'));
    } else {
        return apply_filter('problemTag', mo_get_problem($pid, 'tag'));
    }
}

function mo_get_problem_hash($pid = '-1')
{
    if ($pid == '-1') {
        return apply_filter('problemHash', mo_get_problem('hash'));
    } else {
        return apply_filter('problemHash', mo_get_problem($pid, 'hash'));
    }
}

function mo_get_problem_ver($pid = '-1')
{
    if ($pid == '-1') {
        return apply_filter('problemVer', mo_get_problem('ver'));
    } else {
        return apply_filter('problemVer', mo_get_problem($pid, 'ver'));
    }
}

function mo_get_problem_post_time($pid = '-1')
{
    if ($pid == '-1') {
        return apply_filter('problemPostTime', mo_get_problem('post_time'));
    } else {
        return apply_filter('problemPostTime', mo_get_problem($pid, 'post_time'));
    }
}

function mo_get_problem_time_limit($pid = '-1')
{
    if ($pid == '-1') {
        return apply_filter('problemTimeLimit', mo_get_problem('time_limit'));
    } else {
        return apply_filter('problemTimeLimit', mo_get_problem($pid, 'time_limit'));
    }
}

function mo_get_problem_memory_limit($pid = '-1')
{
    if ($pid == '-1') {
        return apply_filter('problemMemoryLimit', mo_get_problem('memory_limit'));
    } else {
        return apply_filter('problemMemoryLimit', mo_get_problem($pid, 'memory_limit'));
    }
}

function mo_get_problem_test_turn($pid = '-1')
{
    if ($pid == '-1') {
        return apply_filter('problemTestTurn', mo_get_problem('test_turn'));
    } else {
        return apply_filter('problemTestTurn', mo_get_problem($pid, 'test_turn'));
    }
}

function mo_get_problem_try($pid = '-1')
{
    if ($pid == '-1') {
        return apply_filter('problemTry', mo_get_problem('try'));
    } else {
        return apply_filter('problemTry', mo_get_problem($pid, 'try'));
    }
}

function mo_get_problem_solved($pid = '-1')
{
    if ($pid == '-1') {
        return apply_filter('problemSolved', mo_get_problem('solved'));
    } else {
        return apply_filter('problemSolved', mo_get_problem($pid, 'solved'));
    }
}

function mo_get_problem_submit($pid = '-1')
{
    if ($pid == '-1') {
        return apply_filter('problemSubmit', mo_get_problem('submit'));
    } else {
        return apply_filter('problemSubmit', mo_get_problem($pid, 'submit'));
    }
}

function mo_get_problem_ac($pid = '-1')
{
    if ($pid == '-1') {
        return apply_filter('problemAC', mo_get_problem('ac'));
    } else {
        return apply_filter('problemAC', mo_get_problem($pid, 'ac'));
    }
}
