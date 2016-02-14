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

function mo_list_problems($start, $end, $tag = '')
{
    global $db;
    $start -= 1;
    $sql = 'SELECT `id`, `title`, `tag`, `extra`, `solved`, `try` FROM `mo_judge_problem` WHERE `state` = 1 ';
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
    if (isset($mo_temp['mo:probname:'.$pid])) {
        return;
    }
    $probname = mo_read_cache('mo:probname:'.$pid);
    if (!$probname) {
        global $db;
        $sql = 'SELECT `title` FROM `mo_judge_problem` WHERE `id` = ?';
        $db->prepare($sql);
        $db->bind('i', $pid);
        $result = $db->execute();
        if (count($result)) {
            mo_write_cache('mo:probname:'.$pid, $result[0]['title']);

            return $result[0]['title'];
        } else {
            $mo_temp['mo:probname:'.$pid] = 1;

            return;
        }
    }

    return $probname;
}
