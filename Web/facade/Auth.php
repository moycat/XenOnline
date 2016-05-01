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
        $uid = Session::get('uid');
        if (!$uid) {
            return false;
        }
        $user = User::load($uid);
    }
}