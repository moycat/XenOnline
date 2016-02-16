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

function mo_get_user()
{
    global $mo_user, $mo_now_user;
    $args = func_get_args();
    if (count($args) == 2) { // ==>mo_get_user($category, $key)
        $category = $args[0];
        $key = $args[1];
        if ($mo_now_user == null) {
            return;
        } else {
            $uid = $mo_now_user;
        }
    } else { // ==>mo_get_user($uid, $catgegory, $key)
        $uid = $args[0];
        $category = $args[1];
        $key = $args[2];
    }
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

function mo_get_user_nickname($sid = '-1')
{
    if ($sid == '-1') {
        return mo_get_user('info', 'nickname');
    } else {
        return mo_get_user($sid, 'info', 'nickname');
    }
}

function mo_get_user_username($sid = '-1')
{
    if ($sid == '-1') {
        return mo_get_user('info', 'username');
    } else {
        return mo_get_user($sid, 'info', 'username');
    }
}

function mo_get_user_password($sid = '-1')
{
    if ($sid == '-1') {
        return mo_get_user('info', 'password');
    } else {
        return mo_get_user($sid, 'info', 'password');
    }
}

function mo_get_user_mask($sid = '-1')
{
    if ($sid == '-1') {
        return mo_get_user('info', 'mask');
    } else {
        return mo_get_user($sid, 'info', 'mask');
    }
}

function mo_get_user_email($sid = '-1')
{
    if ($sid == '-1') {
        return mo_get_user('info', 'email');
    } else {
        return mo_get_user($sid, 'info', 'email');
    }
}

function mo_get_user_reg_time($sid = '-1')
{
    if ($sid == '-1') {
        return mo_get_user('info', 'reg_time');
    } else {
        return mo_get_user($sid, 'info', 'reg_time');
    }
}

function mo_get_user_last_time($sid = '-1')
{
    if ($sid == '-1') {
        return mo_get_user('info', 'last_time');
    } else {
        return mo_get_user($sid, 'info', 'last_time');
    }
}

function mo_get_user_user_group($sid = '-1')
{
    if ($sid == '-1') {
        return mo_get_user('info', 'user_group');
    } else {
        return mo_get_user($sid, 'info', 'user_group');
    }
}

function mo_get_user_reg_ip($sid = '-1')
{
    if ($sid == '-1') {
        return mo_get_user('info', 'reg_ip');
    } else {
        return mo_get_user($sid, 'info', 'reg_ip');
    }
}

function mo_get_user_last_ip($sid = '-1')
{
    if ($sid == '-1') {
        return mo_get_user('info', 'last_ip');
    } else {
        return mo_get_user($sid, 'info', 'last_ip');
    }
}

function mo_get_user_extra_info($sid, $key)
{
    return mo_get_user($sid, 'extra_info', $key);
}

function mo_get_user_preference($sid, $key)
{
    return mo_get_user($sid, 'preference', $key);
}

function mo_get_user_submit($sid = '-1')
{
    if ($sid == '-1') {
        return mo_get_user('stat', 'submit');
    } else {
        return mo_get_user($sid, 'stat', 'submit');
    }
}

function mo_get_user_accept($sid = '-1')
{
    if ($sid == '-1') {
        return mo_get_user('stat', 'accept');
    } else {
        return mo_get_user($sid, 'stat', 'accept');
    }
}

function mo_get_user_try($sid = '-1')
{
    if ($sid == '-1') {
        return mo_get_user('stat', 'try');
    } else {
        return mo_get_user($sid, 'stat', 'try');
    }
}

function mo_get_user_solved($sid = '-1')
{
    if ($sid == '-1') {
        return mo_get_user('stat', 'solved');
    } else {
        return mo_get_user($sid, 'stat', 'solved');
    }
}

function mo_get_user_submit_problem($sid = '-1')
{
    if ($sid == '-1') {
        return mo_get_user('stat', 'submit_problem');
    } else {
        return mo_get_user($sid, 'stat', 'submit_problem');
    }
}

function mo_get_user_ac_problem($sid = '-1')
{
    if ($sid == '-1') {
        return mo_get_user('stat', 'ac_problem');
    } else {
        return mo_get_user($sid, 'stat', 'ac_problem');
    }
}

function mo_get_user_msg_session($sid = '-1')
{
    if ($sid == '-1') {
        return mo_get_user('stat', 'msg_session');
    } else {
        return mo_get_user($sid, 'stat', 'msg_session');
    }
}
