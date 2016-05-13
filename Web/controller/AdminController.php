<?php
/**
 * config/AdminController.php @ XenOnline
 *
 * The controller of admins.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

use \Facade\Auth;
use \Facade\Site;
use \Facade\Session;
use \Facade\View;

class AdminController {
    public function home()
    {
        $this->check();
        $this->show('admin/index');
    }

    private function check()
    {
        if (!Auth::admin()) {
            header('location: /');
            exit();
        }
    }

    private function show($tpl)
    {
        View::assign('user', Auth::user());
        View::show($tpl);
    }
}