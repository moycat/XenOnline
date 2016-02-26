<?php
/*
 * mo-includes/function-cache.php @ MoyOJ
 *
 * This file provides all functions for cache.
 *
 */

function mo_connect_redis()
{
    global $redis;
    $redis = new Redis();
    $redis->pconnect(REDIS_HOST, REDIS_PORT);
    if (REDIS_PASS) {
        $redis->auth(REDIS_PASS);
    }
}

function mo_set_cache_timeout($key, $timeout)
{
    global $redis;

    return $redis->setTimeout($key, $timeout);
}

function mo_read_cache($key)
{
    global $redis;

    return $redis->get($key);
}

function mo_write_cache($key, $data)
{
    global $redis;

    return $redis->getset($key, $data);
}

function mo_read_cache_array($key)
{
    global $redis;
    $result = $redis->hGetAll($key);
    mo_unflat($result);

    return $result;
}

function mo_read_cache_array_item($key, $hashkey)
{
    global $redis;

    return $redis->hGet($key, $hashkey);
}

function mo_write_cache_array($key, $data)
{
    global $redis;
    mo_flat($data);

    return $redis->hMSet($key, $data);
}

function mo_write_cache_array_item($key, $hashkey, $value, $force = false)
{
    if (!$force && !mo_exist_cache($key)) {
        return false;
    }
    global $redis;

    return $redis->hSet($key, $hashkey, $value);
}

function mo_del_cache($key)
{
    global $redis;

    return $redis->delete($key);
}

function mo_exist_cache($key)
{
    global $redis;

    return $redis->exists($key);
}

function mo_incr_cache($key, $i = 1)
{
    global $redis;
    if ($i == 1) {
        return $redis->incr($key);
    } else {
        return $redis->incrBy($key, $i);
    }
}

function mo_decr_cache($key, $i = 1)
{
    global $redis;
    if ($i == 1) {
        return $redis->decr($key);
    } else {
        return $redis->decrBy($key, $i);
    }
}

function mo_incr_cache_array_item($key, $hashkey, $i = 1)
{
    global $redis;
    if (!mo_exist_cache($key)) {
        return false;
    }

    return $redis->hIncrBy($key, $hashkey, $i);
}
