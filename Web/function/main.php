<?php
/**
 * function/main.php @ XenOnline
 *
 * This file provides main functions.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

use \NoahBuscher\Macaw\Macaw as Router;
use \Facade\DB;
use \Facade\Cache;
use \Facade\Auth;

function web_init()
{
    if (SITE_CLOSE) {
        die("The site is away from home. Come later.");
    }
    timing();
    session_start();
    DB::init(DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PWD);
    Cache::init(REDIS_HOST, REDIS_PORT, REDIS_PWD);
    Auth::check();


    Router::dispatch();
}