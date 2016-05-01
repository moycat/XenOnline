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
    public function save()
    {
        // TODO: Implement save() method.
    }
    public function toJson()
    {
        // TODO: Implement toJson() method.
    }
    public function refreshCache()
    {
        // TODO: Implement refreshCache() method.
    }
    protected function load($id)
    {
        $query = ['_id' => $id];
        DB::select('users');
        $rs = DB::findOne($query);
        if (!$rs) {
            return false;
        }

        var_dump($rs);
    }
}