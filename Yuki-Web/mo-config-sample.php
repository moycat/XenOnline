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

/* MongoDB Configuration */
// The address of your database server
define('DB_HOST', 'localhost');
// The port of your database
define('DB_PORT', 27017);
// Leave it empty if not neccesay
define('DB_USER', '');
// Leave it empty if not neccesay
define('DB_PASS', '');

/* Redis Configuration */
// The address of Redis
define('REDIS_HOST', 'localhost');
// The port of Redis
define('REDIS_PORT', 6379);
// Leave it empty if no password
define('REDIS_PASS', '');

// If debugging, set it to True to output details
define('DEBUG', false);
