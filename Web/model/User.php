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

class User extends ModelContract {
    protected $_collection = 'users';
    protected $_json_item = [
        'username',
        'email'
    ];

    public function refreshCache()
    {
        // TODO: Implement refreshCache() method.
    }

    protected function onZip(&$doc)
    {
        if (!password_get_info($doc['password'])['algo']) {
            $doc['password'] = password_hash($doc['password'], PASSWORD_DEFAULT);
        }
    }
}