<?php
/**
 * facade/User.php @ XenOnline
 *
 * The facade of the users.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

namespace Facade;

use \Facade\Contract\FacadeModelTrait;

class User {
    use FacadeModelTrait;

    static protected $collection = 'users';
    static protected $model = '\Model\User';
}