<?php
/**
 * facade/DB.php @ XenOnline
 *
 * The facade of the database object.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

namespace Facade;

use MongoDB\Client;
use MongoDB\BSON\Regex;
use Exception;

class DB {
    private static $client;
    private static $db;
    private static $col = [];
    private static $col_name = '';

    public static function init($host, $port, $database, $username, $password)
    {
        if ($username && $password) {
            $conn_string = 'mongodb://'.$username.':'.$password.'@'.
                $host.':'.$port;
        } else {
            $conn_string = 'mongodb://'.$host.':'.$port;
        }
        try {
            self::$client = new Client($conn_string);
            
        } catch(Exception $e) {
            die("Failed to connect to the database.");
        }
        self::$db = self::$client->selectDatabase($database);
        Site::debug('Connected to the database.');
    }

    public static function select($collection)
    {
        self::$col_name = $collection;
        if (isset(self::$col[$collection])) {
            return self::$col[$collection];
        }
        self::$col[$collection] = self::$db->selectCollection($collection);
        return self::$col[$collection];
    }

    public static function autoinc($mark)
    {
        $rs = self::select('autoinc')->findOneAndUpdate(
            [
                'mark' => $mark
            ],
            [
                '$inc' => [
                    'id' => 1
                ]
            ],
            [
                'upsert' => true
            ]
        );
        return $rs->id;
    }

    public static function regex($str, $mode = 'i') {
        return new Regex($str, $mode);
    }

    public static function __callStatic($name, $arg)
    {
        return call_user_func_array([self::$col[self::$col_name], $name], $arg);
    }
}