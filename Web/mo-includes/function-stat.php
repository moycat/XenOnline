<?php
/*
 * mo-includes/function-stat.php @ MoyOJ
 *
 * This file provides the functions of usual state.
 *
 * Licensed under GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 */

function mo_get_solution_count($pid = 'all', $uid = 'all', $state = 'all')
{
    global $db;
    $sql = 'SELECT COUNT(*) AS total FROM mo_judge_solution WHERE 1=1';
    if (is_numeric($pid)) {
        $sql .= " AND `pid` = $pid";
    }
    if (is_numeric($uid)) {
        $sql .= " AND `uid` = $uid";
    }
    if (is_numeric($state)) {
        $sql .= " AND `state` = $state";
    }
    $db->prepare($sql);
    $result = $db->execute();
    $count = (int) $result[0]['total'];

    return $count;
}

function mo_get_problem_count($tag = '')
{
    global $db;
    $sql = 'SELECT COUNT(*) AS total FROM `mo_judge_problem` WHERE `state` = 1';
    if ($tag) {
        $sql .= ' AND (MATCH (tag) AGAINST (?))';
        $db->prepare($sql);
        $db->bind('s', $tag);
    } else {
        $db->prepare($sql);
    }
    $result = $db->execute();
    $count = (int) $result[0]['total'];

    return $count;
}

function mo_get_discussion_count($parent = 0, $category = 'all', $uid = 'all', $status = 1)
{
    global $db;
    $sql = 'SELECT COUNT(*) AS total FROM `mo_discussion`  WHERE `parent` = ? AND `status` = ?';
    if (is_numeric($category)) {
        $sql .= " AND `category` = $category";
    }
    if (is_numeric($uid)) {
        $sql .= " AND `uid` = $uid";
    }
    $db->prepare($sql);
    $db->bind('ii', $parent, $status);
    $result = $db->execute();
    $count = (int) $result[0]['total'];

    return $count;
}

function mo_get_user_count()
{
    global $db;
    $sql = 'SELECT COUNT(*) AS total FROM `mo_user`';
    $db->prepare($sql);
    $result = $db->execute();
    $count = (int) $result[0]['total'];

    return $count;
}
