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

    static public function date($time = null)
    {
        if (!$time) {
            return '从未';
        }
        $time = $time === null || $time > time() ? time() : intval($time);
        $t = time() - $time; // Time lag
        if ($t == 0) {
            $text = '刚刚';
        } elseif ($t < 60) {
            $text = $t . '秒前';
        } // Less than a minute
        elseif ($t < 60 * 60) {
            $text = floor($t / 60) . '分钟前';
        } // Less than an hour
        elseif ($t < 60 * 60 * 24) {
            $text = floor($t / (60 * 60)) . '小时前';
        } // Less than an day
        elseif ($t < 60 * 60 * 24 * 3) {
            $text = floor($time / (60 * 60 * 24)) == 1 ? '昨天 ' . date('H:i', $time) :
                '前天 ' . date('H:i', $time);
        } // Less than 3 days
        elseif ($t < 60 * 60 * 24 * 30) {
            $text = date('m月d日 H:i', $time);
        } // Less than a mouth
        elseif ($t < 60 * 60 * 24 * 365) {
            $text = date('m月d日', $time);
        } // Less than a year
        else {
            $text = date('Y年m月d日', $time);
        } // More than a year
        return $text;
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