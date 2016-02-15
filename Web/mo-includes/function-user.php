<?php
/*
 * mo-includes/function-user.php @ MoyOJ
 *
 * This file provides the functions ralated to the user system.
 *
 * Licensed under GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 */

function mo_add_user($username, $password, $email, $nickname = '')
{
    global $db;
    $password = password_hash($password, PASSWORD_DEFAULT, ['cost' => CRYPT_COST]);
    $ip = mo_get_user_ip();
    $sql = 'INSERT INTO `mo_user` (`username`, `password`, `email`, `nickname`, `reg_time`, `reg_ip`) VALUES ( ?, ?, ?, ?, CURRENT_TIMESTAMP, ?)';
    $db->prepare($sql);
    $db->bind('ssssi', $username, $password, $email, $nickname, $ip);
    $db->execute();
    $uid = $db->getInsID();
    if ($uid == 0) {
        return false;
    }
    $sql = 'INSERT INTO `mo_user_extra` (`uid`, `info`, `preference`) VALUES (\''.$uid.'\', \'a:0:{}\', \'a:0:{}\')';
    $db->prepare($sql);
    $db->execute();
    $sql = 'INSERT INTO `mo_stat_user` (`uid`) VALUES (\''.$uid.'\')';
    $db->prepare($sql);
    $db->execute();
    mo_write_note("A new user (ID = $uid) has been added.");

    return $uid;
}

function mo_del_user($uid)
{
    global $db;
    $sql = 'DELETE FROM `mo_user` WHERE `id` = ?';
    $db->prepare($sql);
    $db->bind('i', $uid);
    $db->execute();
    $sql = 'DELETE FROM `mo_user_extra` WHERE `uid` = ?';
    $db->prepare($sql);
    $db->bind('i', $uid);
    $db->execute();
    $sql = 'DELETE FROM `mo_stat_user` WHERE `uid` = ?';
    $db->prepare($sql);
    $db->bind('i', $uid);
    $db->execute();
    mo_write_note("The user (ID = $uid) has been deleted.");
    mo_log_user("The user (ID = $uid) has been deleted.");

    return true;
}

function mo_set_now_user($uid)
{
    global $mo_now_user;
    $mo_now_user = $uid;
}

function mo_get_user($uid, $category, $key)
{
    global $mo_user;
    if ((int) $uid < 1) {
        return;
    }
    if (!isset($mo_user[$uid])) {
        $mo_user[$uid] = new User($uid);
    }
    if (!$mo_user[$uid]->is_loaded()) {
        return false;
    }

    return $mo_user[$uid]->get($category, $key);
}

function mo_get_user_name($sid = -1)
{
    //TODO
    return mo_get_user($sid, 'info', 'nickname');
}
