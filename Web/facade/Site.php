<?php
/**
 * facade/Site.php @ XenOnline
 *
 * The facade of the site.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

namespace Facade;

use \NoahBuscher\Macaw\Macaw as Router;

class Site {
    private static $begin_time;

    static public function init()
    {
        if (SITE_CLOSE) {
            die("The site is away from home. Come later.");
        }
        self::$begin_time = microtime();
        session_start();

        DB::init(DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PWD);
        Cache::init(REDIS_HOST, REDIS_PORT, REDIS_PWD);
        Auth::check();

        Router::dispatch();
    }

    static public function ObjectID($str = null)
    {
        return $str ? new \MongoDB\BSON\ObjectID($str) : new \MongoDB\BSON\ObjectID();
    }

    static public function timing($n = 2)
    {
        $now_time = microtime();
        list($m0, $s0) = explode(" ", self::$begin_time);
        list($m1, $s1) = explode(" ", $now_time);
        return sprintf('%.'.$n.'f', ($s1 + $m1 - $s0 - $m0) * 1000);
    }
    
    static public function debug($msg)
    {
        if (DEBUG) {
            $process_time = self::timing();
            echo "<!-- [DEBUG][$process_time]$msg -->\n";
        }
    }
}