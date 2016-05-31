<?php
/**
 * model/Problem.php @ XenOnline
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

namespace Model;

use \Model\Contract\ModelContract;
use \Model\Contract\StaticModelTrait;

use \Facade\Site;

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
        if (!isset($doc['hash']) && !isset($this->_default_item['hash'])) {
            $this->_default_item['hash'] = Site::random($doc['title']);
        }
    }

    protected function onExtract(&$data)
    {
        // Tags to array
        if (isset($data['tag']) && $data['tag'] instanceof \MongoDB\Model\BSONArray) {
            $data['tag'] = $data['tag']->getArrayCopy();
        }
    }

}