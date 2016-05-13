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
use \Model\Contract\StaticModelTrait;
use \Facade\Request;

class User extends ModelContract {
    use StaticModelTrait;
    static protected $collection = 'users';

    protected $_json_item = [
        'username',
        'email',
        'score',
        'try_cnt',
        'ac_cnt'
    ];

    protected $_default_item = [
        'role'          =>  1,
        'mask'          =>  1,
        'score'         =>  0,
        'submit_cnt'    =>  0,
        'try_cnt'       =>  0,
        'solve_cnt'     =>  0,
        'tried_prob'    =>  [],
        'solved_prob'   =>  [],
        'msg_session'   =>  []
    ];

    public function refreshCache()
    {
        // TODO: Implement refreshCache() method.
    }

    protected function onZip(&$doc)
    {
        // Hash the password
        if (isset($doc['password']) && !password_get_info($doc['password'])['algo']) {
            $doc['password'] = password_hash($doc['password'], PASSWORD_DEFAULT);
        }
        // For new users
        $this->_default_item['reg_ip'] = Request::getIP();
        $this->_default_item['created_at'] = time();
    }
}