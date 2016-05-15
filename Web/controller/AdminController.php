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
    static private $piece_per_page = 20;

    public function home()
    {
        $count = [
            'problem' => Problem::count(),
            'user' => User::count(),
            // TODO
            'solution' => 0,
            'client' => 0,
            'online_client' => 0,
            'discussion' => 0
        ];

        View::assign('count', $count);
        $this->show('admin/index');
    }

    public function problemList($page = 1)
    {
        $count = [
            'problem' => Problem::count()
        ];
        $page = (int)$page;
        $skip = ($page - 1) * self::$piece_per_page;
        $limit = self::$piece_per_page;
        if ($page < 1 || $skip > $count['problem']) {
            View::error404();
        }

        $problem = Problem::findMany([], ['skip'=>$skip, 'limit'=>$limit])->toArray();

        View::assign('problem', $problem);
        View::assign('count', $count);
        $this->show('admin/problem');
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

    public function __construct()
    {
        $this->check();
    }
}