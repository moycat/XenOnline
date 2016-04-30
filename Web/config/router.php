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

Router::get('', function() {
    echo "成功！";
});

Router::get('(:all)', function($fu) {
    echo '未匹配到路由：'.$fu;
});
