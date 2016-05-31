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

        // Fix the bug when many/no '/'
        $_SERVER['REQUEST_URI'] = '/'.trim($_SERVER['REQUEST_URI'], '/');

        Router::dispatch();
    }

    static public function go($url, $code = 200)
    {
        switch ($code) {
            default:
                break;
            case 404:
                header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
                break;
            case 301:
                header($_SERVER['SERVER_PROTOCOL']." 301 Moved Permanently");
                break;
        }
        header('location: '.$url);
        exit(0);
    }

    static public function ObjectID($str = null)
    {
        return $str ? new \MongoDB\BSON\ObjectID($str) : new \MongoDB\BSON\ObjectID();
    }

    static public function random($seed = '')
    {
        return str_replace(
            '/',
            'x',
            password_hash($seed.(string)rand(1,10000), PASSWORD_DEFAULT)
        );
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