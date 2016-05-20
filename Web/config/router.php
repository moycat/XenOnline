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
use \Facade\View;

/* Public Pages */
Router::get('', 'IndexController@home');
Router::post('upload', 'IndexController@upload');

/* Problem */
Router::get('problem', 'ProblemController@home');
Router::get('problem/(:any)', 'ProblemController@view');
Router::get('problem/(:any)/md', 'ProblemController@viewMD');

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
Router::get('admin/problem', 'AdminController@problemList');
Router::get('admin/problem/add', 'AdminController@problemAddPage');
Router::post('admin/problem/add', 'AdminController@problemAdd');
Router::get('admin/problem/(:any)/edit', 'AdminController@problemEditPage');
Router::post('admin/problem/(:any)/edit', 'AdminController@problemEdit');
Router::get('admin/problem/page/(:any)', 'AdminController@problemList');

/* Errors */
Router::error(
    function() {
        View::error404();
    }
);
