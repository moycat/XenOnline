<?php
/**
 * model/Contract/StaticModelTrait.php @ XenOnline
 *
 * The trait of model-operating static functions.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

namespace Model\Contract;

use \Facade\Site;
use \Facade\DB;

trait StaticModelTrait {

    /*
     * You have to define $collection and $model in the class manually.
     *
     * $collection  string   The name of the collection
     * $model       string   The class of the model
     */

    static protected $member;

    static public function getCollectionName()
    {
        return self::$collection;
    }

    /* Load by ObjectID */
    static public function load($id, $reload = false)
    {
        if (!$reload && isset(self::$member[$id])) {
            return self::$member[$id];
        }
        DB::select(self::getCollectionName());
        self::$member[$id] = DB::findOne(['_id' => Site::ObjectID($id)]);
        return self::$member[$id];
    }

    static public function find($filter,  $option = [])
    {
        DB::select(self::getCollectionName());
        return DB::findOne($filter, $option);
    }

    static public function findMany($filter, $option = [])
    {
        DB::select(self::getCollectionName());
        return DB::find($filter, $option);
    }

    /* Create a new model */
    static public function one()
    {
        return new self;
    }
}