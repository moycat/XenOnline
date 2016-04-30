<?php
/**
 * facade/Cache.php @ XenOnline
 *
 * The facade of the Redis object.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

class Cache {
    private static $redis;

    public static function init($host, $port, $password)
    {
        self::$redis = new Redis();
        self::$redis->pconnect($host, $port);
        if ($password) {
            self::$redis->auth($password);
        }
        debug('Connected to the Redis.');
    }

    public static function __callStatic($name, $arg)
    {
        return call_user_func_array([self::$redis, $name], $arg);
    }
}