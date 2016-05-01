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

use \Facade\User;
use \Facade\DB;

Router::get('', function() {


/*
    $a = User::one();
    $a->username = '1333';
    $a->password = 'qing';
    $a->save();
*/
    $a = User::load('5726363de64b4627c7618732');
    $a->username = 'ssssss';
    $a->save(true);
    var_dump($a);
    //DB::select('users');
    //DB::updateOne(['_id' => oid('5726363de64b4627c7618732')], ['$set'=>['username'=>'pp']]);
    
    


    $t = timing();
    echo "成功！
    Processed in {$t} ms.";
});

Router::get('(:all)', function($fu) {
    echo '未匹配到路由：'.$fu;
});
