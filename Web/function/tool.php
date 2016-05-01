<?php
/**
 * function/tool.php @ XenOnline
 *
 * This file provides some small tool functions.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

function debug($msg)
{
    if (DEBUG) {
        $process_time = timing();
        echo "<!-- [DEBUG][$process_time]$msg -->\n";
    }
}

function timing($n = 2)
{
    static $begin_time;
    if (!$begin_time) {
        $begin_time = microtime();
        return '';
    }
    $now_time = microtime();
    list($m0, $s0) = explode(" ", $begin_time);
    list($m1, $s1) = explode(" ", $now_time);
    return sprintf('%.'.$n.'f', ($s1 + $m1 - $s0 - $m0) * 1000);
}

function oid($str = null)
{
    return new \MongoDB\BSON\ObjectID($str);
}