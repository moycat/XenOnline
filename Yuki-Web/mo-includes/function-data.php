<?php
/*
 * mo-includes/function-data.php @ MoyOJ
 *
 * This file provides functions of the database.
 *
 * Licensed under GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 */

spl_autoload_register('mongodb');

// Autoload MongoDB lib
function mongodb($classname)
{
    $class = explode('\\', $classname);
    $file = MOINC.'mongolib/src';
    $c = count($class);
    for ($i = 1; $i < $c; ++$i) {
        $file .= '/'.$class[$i];
    }
    $file .= '.php';
    if (is_file($file)) {
        require_once MOINC.'mongolib/src/functions.php';
        require $file;
    }
}

function mo_db_insertone($col, $content, $option = array())
{
    global $db_col;
    mo_db_select($col);

    return $db_col[$col]->insertOne($content, $option);
}

function mo_db_readone($col, $filter, $option = array())
{
    global $db_col;
    mo_db_select($col);
    $result = $db_col[$col]->findOne($filter, $option);
    if ($result) {
        return iterator_to_array($result);
    } else {
        return;
    }
}

function mo_db_updateone($col, $filter, $content, $option = array())
{
    global $db_col;
    mo_db_select($col);

    return $db_col[$col]->updateOne($filter, $content, $option);
}

function mo_db_count($col)
{
    global $db_col;
    mo_db_select($col);

    return $db_col[$col]->count();
}

function mo_db_select($col)
{
    global $db, $db_col;
    if (!isset($db_col[$col])) {
        $db_col[$col] = $db->selectCollection($col);
    }
}

function mo_connect_database()
{
    global $db, $db_conn;
    $url = 'mongodb://';
    if (DB_USER || DB_PASS) {
        $url .= DB_USER.':'.DB_PASS.'@';
    }
    $url .= DB_HOST.':'.DB_PORT;
    $db_conn = new MongoDB\Client($url);
    $db = $db_conn->selectDatabase('moyoj');
}

class NoDB
{
    public function __call($method, $args)
    {
        mo_connect_database();
        global $db;

        return call_user_func_array(array($db, $method), $args);
    }
}
