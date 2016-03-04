<?php

namespace App\Repositories;

use pRedis;
use Cell;

class CacheCell extends Cell
{
    public function __call ($name, $args)
    {
        return call_user_func_array(['pRedis', $name], $args);
    }

    public function read($key)
    {
        return pRedis::get($key);
    }

    public function write($key, $value)
    {
        return pRedis::getset($key, $value);
    }

    public function readArray($key)
    {
        return pRedis::hgetall($key);
    }

    public function writeArray($key, $array)
    {
        $to_cache = self::merge($key, $array);

        return call_user_func_array('pRedis::hmset', $to_cache);
    }

    public function incArrayItem($key, $item, $num)
    {
        return pRedis::hincrby($key, $item, $num);
    }

    public function writeSortedSet($key, $array)
    {
        $to_cache = self::merge($key, $array);

        return call_user_func_array('pRedis::zadd', $to_cache);
    }

    public function readSortedSet($key, $start = 0, $stop = -1, $reverse = false)
    {
        if ($reverse) {
            return pRedis::zrevrange($key, $start, $stop);
        } else {
            return pRedis::zrange($key, $start, $stop);
        }
    }

    public function writeSet($key, $array)
    {
        $to_cache = array_merge([$key], $array);

        return call_user_func_array('pRedis::sadd', $to_cache);
    }

    public function readSet($key)
    {
        return pRedis::smembers($key);
    }

    public function publish($channel, $message)
    {
        pRedis::publish($channel, $message);
    }

    function count($key)
    {
        $type = pRedis::type($key);
        switch ($type) {
            case 'none':    return NULL;
            case 'string':  return pRedis::strlen($key);
            case 'list':    return pRedis::llen($key);
            case 'set':     return pRedis::scard($key);
            case 'zset':    return pRedis::zcard($key);
            case 'hash':    return pRedis::hlen($key);
            default:        return NULL;
        }
    }

    static private function merge($key, $array)
    {
        $to_cache = [$key];
        foreach ($array as $key => $value) {
            $to_cache[] = $key;
            $to_cache[] = $value;
        }

        return $to_cache;
    }

}