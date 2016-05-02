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

Router::get('', 'IndexController@home');

Router::error(
    function() {
        //Site::view('404');
        echo '未匹配到路由';
    }
);
