<?php
/**
 * facade/Session.php @ XenOnline
 *
 * The facade of the session.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

namespace Facade;

class Session {
    static public function get($name)
    {
        if (self::has($name)) {
            return $_SESSION[$name];
        }
        return null;
    }

    /* Get it and delete it */
    static public function fetch($name)
    {
        if (self::has($name)) {
            $value = $_SESSION[$name];
            unset($_SESSION[$name]);
            return $value;
        }
        return null;
    }

    static public function set($name, $value)
    {
        if (self::has($name)) {
            $old = $_SESSION[$name];
            $_SESSION[$name] = $value;
            return $old;
        }
        $_SESSION[$name] = $value;
        return $value;
    }

    static public function del($name)
    {
        unset($_SESSION[$name]);
        return true;
    }

    static public function has($name)
    {
        return isset($_SESSION[$name]);
    }

    static public function clear()
    {
        session_destroy();
        session_start();
    }
}