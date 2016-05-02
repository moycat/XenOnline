<?php
/**
 * config/router.php @ XenOnline
 *
 * The controller of the index.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

use \Facade\Auth;

class IndexController {
    public function home()
    {
        echo "Welcome!";
        var_dump($_SESSION);
        $a = Auth::user();
        unset($a['lsls']);
        $a->save();
    }
}