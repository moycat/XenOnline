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

use \Model\Problem;
use \Model\User;

class AdminController {
    use \Controller\Traits\AdminProblem;

    static private $piece_per_page = 20;
    private $count = [];

    public function home()
    {
        $this->show('admin/index');
    }

    private function check()
    {
        if (!Auth::admin()) {
            Site::go('/');
        }
    }

    private function show($tpl)
    {
        View::assign('user', Auth::user());
        View::show($tpl);
    }

    private function info($type, $msg, $method = 'smarty')
    {
        $info = '<div class="alert alert-'.$type.'" role="alert">'.$msg.'</div>';
        switch ($method) {
            case 'smarty':
                View::assign('info', $info);
                break;
            case 'session':
                Session::set('info', $info);
                break;
        }

    }

    public function __construct()
    {
        $this->check();

        $this->count = [
            'problem' => Problem::count(),
            'user' => User::count(),
            // TODO
            'solution' => 0,
            'client' => 0,
            'online_client' => 0,
            'discussion' => 0
        ];

        View::assign('count', $this->count);
    }
}