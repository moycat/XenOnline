<?php
/**
 * model/Problem.php @ XenOnline
 *
 * The model of problems.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

namespace Model;

use \Model\Contract\ModelContract;
use \Model\Contract\StaticModelTrait;

class Problem extends ModelContract {
    use StaticModelTrait;

    protected $_json_item = [
        'title',
        'content',
        'score',
        'tag',
        'created_at',
        'time_limit',
        'mem_limit',
        'submit_cnt',
        'try_cnt',
        'ac_cnt',
        'solve_cnt'
    ];

    protected $_default_item = [
        'ver'           =>  1,
        'status'        =>  1,
        'ac_cnt'        =>  0,
        'submit_cnt'    =>  0,
        'try_cnt'       =>  0,
        'solve_cnt'     =>  0
    ];
    
    static public function getCollectionName()
    {
        return 'problems';
    }

    public function refreshCache()
    {
        // TODO: Implement refreshCache() method.
    }

    protected function onZip(&$doc)
    {
        // For new problems
        $this->_default_item['created_at'] = time();
        $this->_default_item['hash'] = sha1($doc['title'].(string)rand(1,10000));
    }

}