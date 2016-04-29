<?php
/**
 * config.php @ XenOnline
 *
 * This file defines the basic information.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

/* Envionment variables */
define('ROOT', __DIR__. '/');
define('PUBLIC', __DIR__. '/public');

/* Database */
define('DB_HOST', '127.0.0.1');
define('DB_PORT', 27017);
define('DB_NAME', 'Xen');
define('DB_USER', null);
define('DB_PWD', null);

/* Redis */
define('REDIS_HOST', '127.0.0.1');
define('REDIS_PORT', 6379);
define('REDIS_PWD', null);

/* Debug */
define('DEBUG', true);

/* OK, now let's require some strange thins... */
require_once ROOT. 'function/functions.php';
require_once ROOT. 'class/classes.php';
require_once ROOT. 'vendor/autoload.php';