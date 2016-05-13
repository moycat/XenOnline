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

/* Problem */
Router::get('problem', 'ProblemController@home');
Router::get('problem/(:any)', 'ProblemController@view');

/* Solution */
Router::get('solution', 'SollutionController@home');
Router::get('solution/(:any)', 'SollutionController@view');
Router::post('solution/add', 'SollutionController@add');

/* User */
Router::get('user', 'UserController@index');
Router::post('user/login', 'UserController@login');
Router::get('user/logout', 'UserController@logout');
Router::get('user/(:any)', 'UserController@view');

/* Dicussion */

/* Administration */
Router::get('admin', 'AdminController@home');

/* Errors */
Router::error(
    function() {
        //Site::view('404');
        echo '未匹配到路由';
    }
);
