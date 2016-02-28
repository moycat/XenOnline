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

// Check if the client has logged in
function mo_user_login($type, $username = '', $password = '', $cookie_timeout = 0)
{
    global $user_logged;
    switch ($type) {
        case 'auto':
        if (isset($_SESSION['uid'])) { // within a session
            $user_logged = $_SESSION['uid'];
            mo_write_note('Logged in within the session.');
        } elseif (isset($_COOKIE['mo_auth'])) { // session timeout but with cookies
            mo_user_memauth($_COOKIE['mo_auth']);
        }
        break;
        case 'check':
        $id = mo_user_passauth($username, $password, $cookie_timeout);
        break;
    }
    return mo_user_check();
}

function mo_user_memauth($cookie)
{
    global $mo_user, $user_logged;
    $input = explode('&', $cookie);
    if (count($input) != 3 || !is_numeric($input[1])) {
        return False;
    }
    $uid = $input[0];
    $random = $input[1];
    $pass = $input[2];
    if (!mo_load_user($uid)) {
        return False;
    }
    $check = md5($mo_user[$uid]['password'].$random.$mo_user[$uid]['mask']);
    if ($check == $pass) {
        mo_write_note('Logged in with cookies.');
        $_SESSION['uid'] = $uid;
        $_SESSION['mask'] = $mo_user[$uid]['mask'];
        $_SESSION['password'] = $mo_user[$uid]['password'];
        $user_logged = $uid;
        mo_log_login($uid, 1);

        return $uid;
    }
    mo_log_login($uid, 1, False);
    setcookie('mo_auth', '', time() - 3600);

    return False;
}

function mo_user_passauth($username, $password, $cookie_timeout = 0)
{
    global $mo_user, $user_logged;
    $uid = mo_read_cache_array_item('mo:user:username', $username);
    if (!$uid) {
        $result = mo_db_readone('mo_user', array('username'=>$username));
        if (!$result) {
            mo_log_login($username, 0, false);

            return False;
        }
        $uid = (string) $result['_id'];
    }
    mo_load_user($uid);
    if ($mo_user[$uid]['password'] != mo_password($password, $mo_user[$uid]['username'])) {
        mo_log_login($uid, 0, false);

        return False;
    }
    $_SESSION['uid'] = $uid;
    $_SESSION['mask'] = $mo_user[$uid]['mask'];
    $_SESSION['password'] = $mo_user[$uid]['password'];
    $user_logged = $uid;
    if ($cookie_timeout) {
        $random = (string) rand(100000, 999999);
        $cookie_to_write = $uid.'&'.$random.'&'.md5($mo_user[$uid]['password'].$random.$mo_user[$uid]['mask']);
        setcookie('mo_auth', $cookie_to_write, time() + $cookie_timeout);
    }
    mo_log_login($uid, 0);
    mo_write_note('Logged in with a password.');

    return $uid;
}

// Load a user to memory
function mo_load_user($uid)
{
    if (!$uid || !is_string($uid)) {
        return False;
    }
    global $mo_user, $mo_user_failed, $mo_now_user;
    if (isset($mo_user[$uid])) {
        $mo_now_user = $uid;

        return True;
    }
    if (isset($mo_user_failed[$uid])) {
        $mo_now_user = NULL;

        return False;
    }
    $user = mo_read_cache_array('mo:user:'.$uid);
    if ($user) {
        $mo_user[$uid] = $user;
        $mo_now_user = $uid;
        mo_set_cache_timeout('mo:user:'.$uid, WEEK);

        return True;
    }
    $result = mo_db_readone('mo_user', array('_id'=>new MongoDB\BSON\ObjectID($uid)));
    if (count($result)) {
        mo_write_cache_array('mo:user:'.$uid, $result);
        mo_write_cache_array_item('mo:user:username', $result['username'], $uid, True);
        mo_set_cache_timeout('mo:user:'.$uid, WEEK);
        $mo_user[$uid] = mo_read_cache_array('mo:user:'.$uid);
        $mo_now_user = $uid;

        return True;
    } else {
        $mo_user_failed[$uid] = True;
        $mo_now_user = NULL;

        return False;
    }

    return True;
}

function mo_user_check()
{
    global $mo_user, $user_logged;
    if (!isset($_SESSION['uid'], $_SESSION['mask'], $_SESSION['password']) || !$user_logged) {
        return False;
    }
    if (!mo_load_user($_SESSION['uid']) || ($_SESSION['mask'] != $mo_user[$user_logged]['mask']) ||
        ($_SESSION['password'] != $mo_user[$user_logged]['password'])) {
        mo_log('Logged out by force.', $_SESSION['uid']);
        mo_user_logout(True);

        return False;
    }

    do_action('login');
    return True;
}

// Change a user's mask, causing all cookies of him to expire
function mo_user_refresh_mask($uid)
{
    mo_db_updateone('mo_user', array('_id'=>new MongoDB\BSON\ObjectID($uid)),
                    array('$inc'=>array('mask'=>1)));
    mo_incr_cache_array_item('mo:user:'.$uid, 'mask', 1);
    mo_log('Mask Refreshed.', $uid);
}

function mo_user_logout($forced = false)
{
    global $user_logged;
    $uid = $_SESSION['uid'];
    unset($_SESSION['uid']);
    unset($_SESSION['password']);
    unset($_SESSION['mask']);
    setcookie('mo_auth', '', time() - 3600);
    $user_logged = NULL;
    if (!$forced) {
        mo_log('Logout.', $uid);
        do_action('logout');
    }
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
    if (count($args) == 1) { // 获取当前指向的user ==> mo_get_user($category)
        $category = $args[0];
        if ($mo_now_user == NULL || !mo_load_user($mo_now_user)
            || !isset($mo_user[$mo_now_user][$category])) {
            return NULL;
        }
        if (is_string($mo_user[$mo_now_user][$category])) {
            return apply_filter('user_'.$category,
                                    htmlspecialchars($mo_user[$mo_now_user][$category]));
        } else {
            return apply_filter('user_'.$category,
                                    $mo_user[$mo_now_user][$category]);
        }
    } else { // 获取指定$sid的solution ==> mo_get_solution($sid, $category)
        $uid = $args[0];
        $category = $args[1];
        if (!mo_load_user($uid) || !isset($mo_user[$uid][$category])) {
            return NULL;
        }
        if (is_string($mo_user[$mo_now_user][$category])) {
            return apply_filter('user_'.$category,
                                    htmlspecialchars($mo_user[$uid][$category]));
        } else {
            return apply_filter('user_'.$category,
                                    $mo_user[$uid][$category]);
        }
    }
}

// Update a **root** category
function mo_set_user($uid, $category, $value)
{
    global $mo_user;
    if (!mo_load_user($uid)) {
        return False;
    }
    $mo_user[$uid][$category] = $value;
    mo_db_updateone('mo_user', array('_id'=>new MongoDB\BSON\ObjectID($uid)), array('$set'=>array($category=>$value)));
}

function mo_get_user_id($uid = '')
{
    if (!$uid) {
        return mo_get_user('_id');
    } else {
        return mo_get_user($uid, '_id');
    }
}

function mo_get_user_username($uid = '')
{
    if (!$uid) {
        return mo_get_user('username');
    } else {
        return mo_get_user($uid, 'username');
    }
}

function mo_get_user_email($uid = '')
{
    if (!$uid) {
        return mo_get_user('email');
    } else {
        return mo_get_user($uid, 'email');
    }
}

function mo_get_user_reg_time($uid = '')
{
    if (!$uid) {
        return mo_get_user('reg_time');
    } else {
        return mo_get_user($uid, 'reg_time');
    }
}

function mo_get_user_last_time($uid = '')
{
    if (!$uid) {
        return mo_get_user('last_time');
    } else {
        return mo_get_user($uid, 'last_time');
    }
}

function mo_get_user_reg_ip($uid = '')
{
    if (!$uid) {
        return mo_get_user('reg_ip');
    } else {
        return mo_get_user($uid, 'reg_ip');
    }
}

function mo_get_user_last_ip($uid = '')
{
    if (!$uid) {
        return mo_get_user('last_ip');
    } else {
        return mo_get_user($uid, 'last_ip');
    }
}

function mo_get_user_try($uid = '')
{
    if (!$uid) {
        return mo_get_user('try');
    } else {
        return mo_get_user($uid, 'try');
    }
}

function mo_get_user_ac($uid = '')
{
    if (!$uid) {
        return mo_get_user('ac');
    } else {
        return mo_get_user($uid, 'ac');
    }
}

function mo_get_user_submit($uid = '')
{
    if (!$uid) {
        return mo_get_user('submit');
    } else {
        return mo_get_user($uid, 'submit');
    }
}

function mo_get_user_solved($uid = '')
{
    if (!$uid) {
        return mo_get_user('solved');
    } else {
        return mo_get_user($uid, 'solved');
    }
}

function mo_get_user_try_list($uid = '')
{
    if (!$uid) {
        return mo_get_user('try_list');
    } else {
        return mo_get_user($uid, 'try_list');
    }
}

function mo_get_user_ac_list($uid = '')
{
    if (!$uid) {
        return mo_get_user('ac_list');
    } else {
        return mo_get_user($uid, 'ac_list');
    }
}

function mo_get_user_msg_session($uid = '')
{
    if (!$uid) {
        return mo_get_user('msg_session');
    } else {
        return mo_get_user($uid, 'msg_session');
    }
}

function mo_get_user_new_msg($uid = '')
{
    if (!$uid) {
        return mo_get_user('new_msg');
    } else {
        return mo_get_user($uid, 'new_msg');
    }
}
