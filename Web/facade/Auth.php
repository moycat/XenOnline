<?php
/**
 * facade/Auth.php @ XenOnline
 *
 * The facade of the **present** user.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

namespace Facade;

class Auth {
    private static $uid = null;
    private static $user = null;

    public static function check()
    {
        if (self::$uid) {
            return self::$uid;
        }
        if (self::session_check() || self::cookie_check()) {
            return self::$uid;
        }
        return false;
    }

    public static function user()
    {
        return self::$user;
    }

    /**
     * @param array $info       Filters to find a user
     * @param string $password
     * @param int $forgetmenot  Days before cookies expiring
     * @return bool
     */
    public static function login($info, $password, $forgetmenot = 0)
    {
        $rs = User::find($info);
        if (!$rs) {
            return false;
        }
        if (!password_verify($password, $rs->password)) {
            return false;
        }
        self::session_start($rs);
        if ($forgetmenot) {
            self::cookie_start($rs, $forgetmenot);
        }
        self::$uid = $rs->getID();
        self::$user = $rs;
        debug('Login with the password');
        return true;
    }

    private static function session_start($user)
    {
        Session::set('uid', $user->getID());
        Session::set('mask', $user->mask);
    }

    private static function cookie_start($user, $day)
    {
        $time = time();
        $ticket_exp = $time + $day * 86400; // When the ticket should expire
        $ticket = sha1($user->getID().$user->mask.$user->password.$ticket_exp);
        setcookie("uid", $user->getID(), $ticket_exp);
        setcookie("ticket", $ticket, $ticket_exp);
        setcookie("ticket_exp", $ticket_exp, $ticket_exp);
    }

    private static function session_check()
    {
        $uid = Session::get('uid');
        $mask = Session::get('mask');
        if (!$uid || !$mask) {
            return false;
        }
        $user = User::load($uid);
        if (!$user || $mask !== $user->mask) {
            return false;
        }
        self::$uid = $uid;
        self::$user = $user;
        debug('Login during a session.');
        return true;
    }

    private static function cookie_check()
    {
        if (!isset($_COOKIE['uid'], $_COOKIE['ticket'], $_COOKIE['ticket_exp'])) {
            return false;
        }
        if ($_COOKIE['ticket_exp'] < time()) { // Ticket has expired!
            return false;
        }
        list($uid, $ticket, $ticket_exp) = [
            $_COOKIE['uid'],
            $_COOKIE['ticket'],
            $_COOKIE['ticket_exp']
        ];
        $user = User::load($uid);
        if (!$user) {
            return false;
        }
        $real_ticket = sha1($user->getID().$user->mask.$user->password.$ticket_exp);
        if ($real_ticket !== $ticket) {
            return false;
        }
        self::$uid = $uid;
        self::$user = $user;
        self::session_start($user);
        debug('Login with a cookie.');
        return true;
    }
}