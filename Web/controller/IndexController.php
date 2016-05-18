<?php
/**
 * config/IndexController.php @ XenOnline
 *
 * The controller of the index.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

use \Facade\Auth;
use \Facade\Site;
use \Facade\View;

class IndexController {
    public function home()
    {
        echo "Welcome!\n";
        //Auth::login(['username'=>'moycat'], 'qiiq123', 30);
        //$m = User::load('572999eae64b463b1538f8c1');
        //var_dump($m);
        $p = new \Model\Problem;
        $o = [
            'id' => 1,
            'title' => 'A+B Problem',
            'content' => '神奇的题目，永恒的挑战。',
            'tag' => ['123', '基础', '23333'],
            'turn' => 10,
            'time_limit' => 1000,
            'mem_limit' => 64
        ];
        foreach ($o as $k => $v) {
            $p[$k] = $v;
        }
        //$p->save();
        echo 'Processed in ', Site::timing(), ' ms.';
        View::show('index');
    }
}