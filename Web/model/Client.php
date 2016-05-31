<?php
/**
 * model/Client.php @ XenOnline
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

namespace Model;

use \Model\Contract\ModelContract;
use \Model\Contract\StaticModelTrait;

use \Facade\Site;
use \Facade\View;

class Client extends ModelContract {
    use StaticModelTrait;

    protected $_json_item = [
        'name',
        'status',
        'intro',
        'load',
        'memory',
        'last_ping'
    ];

    protected $_default_item = [
        'status'        =>  1,
        'load'          =>  [],
        'memory'        =>  0,
        'last_ping'     =>  0
    ];

    static public function getCollectionName()
    {
        return 'clients';
    }

    public function refreshCache()
    {
        // TODO: Implement refreshCache() method.
    }

    protected function onZip(&$doc)
    {
        // For new problems
        $this->_default_item['created_at'] = time();
        if (!isset($doc['password'])) {
            $this->_default_item['hash'] = Site::random($doc['name']);
        }
    }

}