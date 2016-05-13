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
        //Auth::login(['username'=>'moycat'], 'qiiq123', 1);
        //$m = User::load('572999eae64b463b1538f8c1');
        //var_dump($m);
        echo 'Processed in ', Site::timing(), ' ms.';
        View::show('index');
    }
}