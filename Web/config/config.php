<?php
/**
 * config/config.php @ XenOnline
 *
 * This file defines the basic information.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

/* Database */
define('DB_HOST', '127.0.0.1');
define('DB_PORT', 27017);
define('DB_NAME', 'Xen');
define('DB_USER', null);    // Username, null if none
define('DB_PWD', null);     // Password, null if none

/* Redis */
define('REDIS_HOST', '127.0.0.1');
define('REDIS_PORT', 6379);
define('REDIS_PWD', null);

/* Debug */
define('DEBUG', true);

/* Envionment variables */
define('ROOT', __DIR__. '/../');
define('PUBLIC', __DIR__. 'public/');
define('CONFIG', ROOT. 'config/');
define('FUNC', ROOT. 'function/');
define('VIEW', ROOT. 'view/');

/* OK, now let's require some strange things... */
require_once ROOT. 'vendor/autoload.php';
require_once ROOT. 'function/functions.php';
require_once CONFIG. 'router.php';