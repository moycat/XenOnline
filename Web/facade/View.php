<?php
/**
 * facade/View.php @ XenOnline
 *
 * The facade of the views.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

namespace Facade;

use \Smarty;

class View {
    private static $smarty = null;

    public static function show($tp)
    {
        self::setup();
        self::$smarty->display($tp.'.tpl');
    }

    private static function setup()
    {
        if (self::$smarty) {
            return;
        }
        self::$smarty = new Smarty();
        if (DEBUG) {
            self::$smarty->caching = false;
        } else {
            self::$smarty->caching = true;
        }
        self::$smarty->template_dir = ROOT.'view';
        self::$smarty->compile_dir = ROOT.'tmp';
        self::$smarty->cache_dir = ROOT.'tmp';
    }
}