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
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($username) > 32 || strlen($username) < 3 ||
        strlen($password) < 6 || strlen($password) > 50) {
        return false;
    }
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
    $sql = 'INSERT INTO `mo_user_info` (`uid`, `info`, `preference`) VALUES (\''.$uid.'\', \'a:0:{}\', \'a:0:{}\')';
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
    $sql = 'DELETE FROM `mo_user_info` WHERE `uid` = ?';
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
