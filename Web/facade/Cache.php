<?php
/**
 * facade/Cache.php @ XenOnline
 *
 * The facade of the Redis object.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

namespace Facade;

use \Redis;

class Cache {
    private static $redis;

    public static function init($host, $port, $password)
    {
        self::$redis = new Redis();
        $rs = self::$redis->pconnect($host, $port);
        if (!$rs) {
            die("Failed to connect to the Redis server.");
        }
        if ($password) {
            $rs = self::$redis->auth($password);
            if (!$rs) {
                die("Failed to connect to the Redis server.");
            }
        }
        debug('Connected to the Redis.');
    }

    public static function __callStatic($name, $arg)
    {
        return call_user_func_array([self::$redis, $name], $arg);
    }
}