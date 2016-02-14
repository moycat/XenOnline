<?php
/*
 * mo-includes/function-cache.php @ MoyOJ
 *
 * This file provides all functions for cache.
 *
 */

function mo_read_cache_array($cache)
{
    return mo_read_cache($cache, true);
}

function mo_read_cache($cache, $isarray = false)
{
    global $redis;
    $data = $redis->get($cache);
    if ($isarray) {
        return unserialize($data);
    } else {
        return $data;
    }
}

function mo_write_cache($cache, $data)
{
    global $redis;
    if (is_array($data)) {
        $data = serialize($data);
    }
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

function mo_incr_cache($cache, $i = 1)
{
    global $redis;
    if ($i == 1) {
        return $redis->incr($cache);
    } else {
        return $redis->incrBy($cache, $i);
    }
}

function mo_decr_cache($cache, $i = 1)
{
    global $redis;
    if ($i == 1) {
        return $redis->decr($cache);
    } else {
        return $redis->decrBy($cache, $i);
    }
}
