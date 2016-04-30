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

function web_init()
{
    timing();
    DB::init(DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PWD);
    Cache::init(REDIS_HOST, REDIS_PORT, REDIS_PWD);

    Router::dispatch();
}