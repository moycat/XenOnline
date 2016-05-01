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

    static public function load($uid) {
        if (isset(self::$users[$uid])) {
            return self::$users[$uid];
        }
        $users[$uid] = new \Model\User($uid);
        return $users[$uid];
    }
}