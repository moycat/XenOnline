<?php
/**
 * install.php @ XenOnline
 *
 * This file is used to install the database.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

error_reporting(E_WARNING);
require_once 'config/config.php';

use MongoDB\Client;

$db_host = DB_HOST;
$db_port = DB_PORT;
$db_name = DB_NAME;
$db_user = DB_USER ? DB_USER : '(null)';
$db_pwd = DB_PWD ? DB_PWD : '(null)';

echo "
=============================================================
==    XenOnline - An Open-source Online Judge System       ==
==          https://github.com/moycat/XenOnline            ==
==---------------------------------------------------------==
==      Now we are going to install the database.          ==
==      Please check whether the connection information    ==
==   is correct. If OK, input 'y' to start installing.     ==
==      You can change them at config/config.php .         ==
==---------------------------------------------------------==
==      [Host]      $db_host
==      [Port]      $db_port
==      [Database]  $db_name
==      [User]      $db_user
==      [Password]  $db_pwd
=============================================================

Input 'y' and press enter key to install XenOnline, or other letters to stop.
";

$check = trim(fgets(STDIN));
if ($check !== 'y') {
    die('Installing abort.');
}
echo "Now trying to connect to the database(mongodb://$db_host:$db_port/$db_name)...\n";
