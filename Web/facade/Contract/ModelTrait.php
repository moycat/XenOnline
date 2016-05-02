<?php
/**
 * facade/Contract/ModelTrait.php @ XenOnline
 *
 * The trait of model-operating facades.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

namespace Facade\Contract;

use \Facade\DB;

trait ModelTrait {
    static protected $member;

    static public function load($id, $reload = false) {
        if (!$reload && isset(self::$member[$id])) {
            return self::$member[$id];
        }
        DB::select(self::$collection);
        self::$member[$id] = DB::findOne(['_id' => oid($id)]);
        return self::$member[$id];
    }

    static public function find($filter,  $option = [])
    {
        DB::select(self::$collection);
        return DB::find($filter, $option);
    }

    static public function findMany($filter, $option = [])
    {
        DB::select(self::$collection);
        return DB::findOne($filter, $option);
    }

    static public function one()
    {
        return new self::$model();
    }
}