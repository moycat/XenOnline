<?php
/**
 * config/router.php @ XenOnline
 *
 * This file adds routes to the router.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

use \NoahBuscher\Macaw\Macaw as Router;
use \Facade\Site;
use \Facade\User;
use \Facade\DB;
use \Facade\Auth;

Router::get('', function() {


/*
    $a = User::one();
    $a->username = '1333';
    $a->password = 'qing';
    $a->save();
*/
    /*$a = User::load('5726363de64b4627c7618732');
    $a->password = 'qewqeww';
    $a->username = '222333';
    $a->lsls=22;
    $a->save();*/

    //$a = User::load('5726363de64b4627c7618732');
    //$a = User::one();
    //$a = Auth::user();
    //var_dump($a);
    //$a->updateOne(['$set'=>['username'=>'pp']]);
    //Auth::login(['username'=>'222333'], 'qewqeww', 20);
    var_dump($_SESSION);


    //DB::select('users');
    //DB::updateOne(['_id' => oid('5726363de64b4627c7618732')], ['$set'=>['username'=>'pp']]);
    
    


    $t = Site::timing();
    echo "成功！
    Processed in {$t} ms.";
});

Router::get('(:all)', function($fu) {
    echo '未匹配到路由：'.$fu;
});
