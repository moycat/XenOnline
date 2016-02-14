<?php
/*
 * mo-config.php @ MoyOJ
 *
 * This file gives information to allow MoyOJ to run.
 * It sets some simple things needed.
 *
 * Licensed under GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 */

// The address of your database server
define('DB_HOST', '127.0.0.1');
// The name of your database
define('DB_NAME', 'moyoj');
// The username of your databse account
define('DB_USER', 'moyoj');
// The password of the account
define('DB_PASS', 'moyoj');

// The address of your redis server
define('REDIS_HOST', '127.0.0.1');
// The port of the redis service
define('REDIS_PORT', 6379);
// Leave it empty if no password
define('REDIS_PASS', '');

// The address of your socket server
define('SOCK_HOST', '127.0.0.1');
// The port of the socket server
define('SOCK_PORT', 6666);

// The cost used when crypting password
// At least 4, and 5 is recommond
define('CRYPT_COST', 4);

// If debugging, set it to True to output details
define('DEBUG', false);

// Set the timezone
date_default_timezone_set('Asia/Chongqing');
