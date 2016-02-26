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
        $mo_user[$uid] = $result;
        mo_write_cache_array('mo:user:'.$uid, $result);
        mo_write_cache_array_item('mo:user:username', $mo_user[$uid]['username'], $uid, True);
        mo_set_cache_timeout('mo:user:'.$uid, WEEK);
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

        return apply_filter('user_'.$category,
                                htmlspecialchars($mo_user[$mo_now_user][$category]));
    } else { // 获取指定$sid的solution ==> mo_get_solution($sid, $category)
        $uid = $args[0];
        $category = $args[1];
        if (!mo_load_user($uid) || !isset($mo_user[$uid][$category])) {
            return NULL;
        }

        return apply_filter('user_'.$category,
                                htmlspecialchars($mo_user[$uid][$category]));
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
