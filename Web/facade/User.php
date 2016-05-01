<?php
/**
 * facade/User.php @ XenOnline
 *
 * The facade of the users.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

namespace Facade;


class User {
    static private $users;

    static public function load($uid, $reload = false) {
        if (!$reload && isset(self::$users[$uid])) {
            return self::$users[$uid];
        }
        DB::select('users');
        self::$users[$uid] = DB::findOne(['_id' => oid($uid)]);
        return self::$users[$uid];
    }

    static public function one()
    {
        return new \Model\User();
    }
}