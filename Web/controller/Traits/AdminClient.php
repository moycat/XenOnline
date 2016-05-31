<?php
/**
 * controller/Traits/AdminClient.php @ XenOnline
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

namespace Controller\Traits;

use \Model\Client;
use \Facade\View;

trait AdminClient
{
    public function clientList()
    {
        $clients = Client::findMany([])->toArray();

        View::assign('clients', $clients);
        $this->show('admin/client');
    }
}