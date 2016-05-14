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
        // Process time should be computed just before showing
        self::$smarty->assign('process_time', Site::timing());

        self::$smarty->display($tp.'.tpl');
    }

    public static function assign($name, $var)
    {
        self::setup();
        self::$smarty->assign($name, $var);
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

        // Assign default values
        $default_value = [
            'site_name' => SITE_NAME,
        ];
        foreach ($default_value as $var => $value) {
            self::$smarty->assign($var, $value);
        }
    }
}