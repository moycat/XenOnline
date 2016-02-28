<?php
/*
 * mo-includes/function-log.php @ MoyOJ
 *
 * This file provides the functions to write and read logs in the database.
 *
 * Licensed under GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 */

 /*
  * 登录mode
  * 0：密码认证
  * 1：cookies认证
  */

function mo_log_login($uid, $mode, $success = true)
{
    if ($success) {
        mo_db_updateone('mo_user', array('_id'=>new MongoDB\BSON\ObjectID($uid)),
                            array('$set'=>array('last_ip'=>$_SERVER['REMOTE_ADDR'],
                                                'last_time'=>$_SERVER['REQUEST_TIME'])));
        mo_write_cache_array_item('mo:user:'.$uid, 'last_ip', $_SERVER['REMOTE_ADDR']);
        mo_write_cache_array_item('mo:user:'.$uid, 'last_time', $_SERVER['REQUEST_TIME']);
    }
    return mo_db_insertone('mo_log_login', array('uid'=>$uid, 'time'=>$_SERVER['REQUEST_TIME'],
                                                    'mode'=>$mode, 'success'=>$success,
                                                    'ip'=>$_SERVER['REMOTE_ADDR'],
                                                    'agent'=>$_SERVER['HTTP_USER_AGENT']));
}

function mo_log($content, $uid = NULL)
{
    return mo_db_insertone('mo_log', array('uid'=>$uid, 'ip'=>$_SERVER['REMOTE_ADDR'], 'content'=>$content));
}
