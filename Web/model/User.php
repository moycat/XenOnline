<?php
/**
 * model/User.php @ XenOnline
 *
 * The model of users.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

namespace Model;

use \Model\Contract\ModelContract;
use \Facade\DB;
use \MongoDB\BSON\ObjectID as ObjectID;

class User extends ModelContract {
    protected $_collection = 'users';
    protected $_bson_map = [
        'username' => 'username'
    ];

    public function refreshCache()
    {
        // TODO: Implement refreshCache() method.
    }
}