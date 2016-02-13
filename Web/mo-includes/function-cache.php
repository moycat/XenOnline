<?php
/*
 * mo-includes/function-cache.php @ MoyOJ
 *
 * This file provides all functions for cache.
 *
 */

function mo_read_cache($cache)
{
    if (defined('MEM') && MEM == true) {
        if (strlen($cache) > 100) {
            return false;
        }
        global $mem;

        return $mem->get($cache);
    } else {
        $cache_file = MOCACHE.$cache.'.php';
        if (file_exists($cache_file)) {
            require_once $cache_file;

            return unserialize($mo_cache[$cache]);
        } else {
            return false;
        }
    }
}

function mo_write_cache($cache, $data)
{
    if (defined('MEM') && MEM == true) {
        if (strlen($cache) > 100) {
            return false;
        }
        global $mem;
        if (!$mem->set($cache, $data)) {
            $mem->replace($cache, $data);
        }

        return true;
    } else {
        if (!is_writable(MOCACHE)) {
            return false;
        }
        $cache_file = MOCACHE.$cache.'.php';
        $to_cache = "<?php\n"."\$mo_cache['$cache'] = '".serialize($data)."';\n";
        $file = fopen($cache_file, 'w');
        fwrite($file, $to_cache);
        fclose($file);

        return true;
    }
}

function mo_del_cache($cache)
{
    if (defined('MEM') && MEM == true) {
        if (strlen($cache) > 100) {
            return false;
        }
        global $mem;

        return $mem->delete($cache);
    } else {
        $cache_file = MOCACHE.$cache.'.php';
        if (file_exists($cache_file) && is_writable(MOCACHE)) {
            return unlink($cache_file);
        }

        return false;
    }
}
