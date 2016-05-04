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

/* Public Pages */
Router::get('', 'IndexController@home');

/* Problems Related */
Router::get('problem', 'ProblemController@home');
Router::get('problem/(:any)', 'ProblemController@view');

/* Administration */
Router::get('admin', 'AdminController@home');

/* Errors */
Router::error(
    function() {
        //Site::view('404');
        echo '未匹配到路由';
    }
);
