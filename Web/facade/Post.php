<?php
/**
 * facade/Post.php @ XenOnline
 *
 * The facade of the post data
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

namespace Facade;

class Post {
    static public function get($name)
    {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }
        return null;
    }
}