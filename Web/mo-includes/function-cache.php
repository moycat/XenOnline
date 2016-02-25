<?php
/*
 * mo-includes/function-cache.php @ MoyOJ
 *
 * This file provides all functions for cache.
 *
 */

function mo_set_cache_timeout($key, $timeout)
{
    global $redis;

    return $redis->setTimeout($key, $timeout);
}

function mo_read_cache_array($cache)
{
    global $redis;

    return $redis->hGetAll($cache);
}

function mo_read_cache_array_item($key, $hashkey)
{
    global $redis;

    return $redis->hGet($key, $hashkey);
}

function mo_read_cache($cache)
{
    global $redis;

    return $redis->get($cache);
}

function mo_write_cache_array($cache, $data)
{
    global $redis;
    if ($redis->exists($cache)) {
        $old = $redis->hGetAll($cache);
        $redis->hMSet($cache, $data);

        return $old;
    } else {
        return $redis->hMSet($cache, $data);
    }
}

function mo_write_cache_array_item($key, $hashkey, $value)
{
    if (!mo_exist_cache($key)) {
        return false;
    }
    global $redis;

    return $redis->hSet($key, $hashkey, $value);
}

function mo_write_cache($cache, $data)
{
    global $redis;
    if ($redis->exists($cache)) {
        return $redis->getset($cache, $data);
    } else {
        return $redis->set($cache, $data);
    }
}

function mo_del_cache($cache)
{
    global $redis;

    return $redis->delete($cache);
}

function mo_exist_cache($cache)
{
    global $redis;

    return $redis->exists($cache);
}

function mo_incr_cache($cache, $i = 1)
{
    global $redis;
    if (!mo_exist_cache($cache)) {
        return false;
    }
    if ($i == 1) {
        return $redis->incr($cache);
    } else {
        return $redis->incrBy($cache, $i);
    }
}

function mo_incr_cache_array($key, $hashkey, $i = 1)
{
    global $redis;
    if (!mo_exist_cache($key)) {
        return false;
    }

    return $redis->hIncrBy($key, $hashkey, $i);
}

function mo_decr_cache($cache, $i = 1)
{
    global $redis;
    if (!mo_exist_cache($cache)) {
        return false;
    }
    if ($i == 1) {
        return $redis->decr($cache);
    } else {
        return $redis->decrBy($cache, $i);
    }
}
