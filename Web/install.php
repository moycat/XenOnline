<?php
/**
 * install.php @ XenOnline
 *
 * This file is used to install the database.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

//error_reporting(E_WARNING);
require_once 'config/config.php';

use MongoDB\Client;

$DB_HOST =      DB_HOST;
$DB_PORT =      DB_PORT;
$DB_NAME =      DB_NAME;
$DB_USER =      DB_USER     ? DB_USER   : '(null)';
$DB_PWD =       DB_PWD      ? '******'  : '(null)';
$REDIS_HOST =   REDIS_HOST;
$REDIS_PORT =   REDIS_PORT;
$REDIS_PWD =    REDIS_PWD   ? '******'  : '(null)';

$collections_to_create = [
    'users',
    'problems',
    'solutions',
    'solutions_pending',
    'discussions',
    'admins'
];

$indexes_to_create = [
    'users' => [
        'username' => 1
    ],

];

echo "
=============================================================
==    XenOnline - An Open-source Online Judge System       ==
==          https://github.com/moycat/XenOnline            ==
==---------------------------------------------------------==
==      Now we are going to install the database.          ==
==      Please check whether the connection information    ==
==   is correct. If OK, input 'y' to start installing.     ==
==      You can change them at config/config.php .         ==
=============================================================

=============================================================
==                      MongoDB Server                     ==
==---------------------------------------------------------==
==                  [Host]      $DB_HOST
==                  [Port]      $DB_PORT
==                  [Database]  $DB_NAME
==                  [User]      $DB_USER
==                  [Password]  $DB_PWD
=============================================================

=============================================================
==                       Redis Server                      ==
==---------------------------------------------------------==
==                  [Host]      $REDIS_HOST
==                  [Port]      $REDIS_PORT
==                  [Password]  $REDIS_PWD
=============================================================

Input 'y' and press enter key to install XenOnline, or other letters to stop.
";
$check = trim(fgets(STDIN));
if ($check !== 'y') {
    die('Installing abort.');
}

// MongoDB things
echo "Now trying to connect to the database(mongodb://$DB_HOST:$DB_PORT/$DB_NAME)...\n";
if (DB_USER && DB_PWD) {
    $conn_string = 'mongodb://'. DB_USER. ':'. DB_PWD. '@'.
        DB_HOST. ':'. DB_PORT;
} else {
    $conn_string = 'mongodb://'. DB_HOST. ':'. DB_PORT;
}
try {
    $mongodb = new Client($conn_string);
}
catch(Exception $e) {
    die("Failed to connect to the database!\n\n");
}
echo "Successfully connected to the database!\n";

// Redis things
echo "Now trying to connect to the Redis server($REDIS_HOST:$REDIS_PORT)...\n";
$redis = new Redis();
if (!$redis->pconnect(REDIS_HOST, REDIS_PORT)) {
    die("Failed to connect to the Redis server!\n\n");
} elseif (REDIS_PWD && !$redis->auth(REDIS_PWD)) {
    die("Failed to login in the Redis server! (Connected though)\n\n");
}
echo "Successfully connected to the Redis server!\n";
$redis->delete('xen:*');
echo "\n";

// Check if installed
foreach ($mongodb->listDatabases() as $databaseInfo) {
    if ($databaseInfo->getName() === DB_NAME) {
        echo "There has been a database named \"{$databaseInfo->getName()}\"!\n";
        echo "Do you want to continue?\n";
        echo "!!!If so, the existing database will be removed permanently!!!\n";
        $check = trim(fgets(STDIN));
        if ($check !== 'y') {
            die("Installing abort.\n");
        }
        $mongodb->dropDatabase(DB_NAME);
        echo "Existing database removed.\n\n";
        break;
    }
}

// Build a new database
$db = $mongodb->selectDatabase(DB_NAME);
echo "Now creating the collections...\n";
foreach ($collections_to_create as $new_col) {
    $db->createCollection($new_col);
}
echo "Now creating the indexes...\n";
foreach ($indexes_to_create as $now_col => $new_index) {
    if (!isset($col[$now_col])) {
        $col[$now_col] = $db->selectCollection($now_col);
    }
    $col[$now_col]->createIndex($new_index);
}
echo "Finished installing the database!\n";

// Add a new admin account
echo "Now, it's high time we set up the first admin account.\n";
echo "Admin Name:";
$admin_name = trim(fgets(STDIN));
echo "Admin Password:";
$admin_pwd = trim(fgets(STDIN));
echo "Password hashing...\n";
$admin_pwd = password_hash($admin_pwd, PASSWORD_DEFAULT);