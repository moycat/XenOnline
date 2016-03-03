<?php

namespace App\Repositories;

use Cell;
use pRedis;
use App\Problem;

class ProblemCell extends Cell
{
    protected $load;
    protected $failedToLoad;

    function __construct() {
        $this->cacheAll();
    }

    public function index($size, $startID = 0, $filter = array())
    {
        if (count($filter) > 1) {
            $hash = $this->cacheTag($filter);
            $result['count'] = pRedis::zcard($hash);
            $problems = pRedis::zrange($hash, $startID, $startID + $size - 1);
        } elseif(count($filter) == 1) {
            $result['count'] = pRedis::zcard('mo:problem:tag:'.$filter[0]);
            $problems = pRedis::zrange('mo:problem:tag:'.$filter[0], $startID, $startID + $size - 1);
        } else {
            $result['count'] = pRedis::zcard('mo:problem');
            $problems = pRedis::zrange('mo:problem', $startID, $startID + $size - 1);
        }
        foreach ($problems as $pid) {
            $result['problems'][] = $this->find($pid);
        }
        $result['previous'] = ($startID > 0);
        $result['next'] = $result['count'] >= $startID + $size - 1;
        $result['tags'] = pRedis::smembers('mo:problem:tags');

        return $result;
    }

    public function find($id)
    {
        return Problem::findOrFail($id);
    }

    public function add($info, $option)
    {
        // TODO: Implement add() method.
    }

    public function count($filter = array())
    {
        return Problem::count();
    }

    protected function cacheTag($filter)
    {
        sort($filter);
        $hash = 'mo:problem:tags:'.md5(implode(' ', $filter));
        if (!pRedis::exists($hash)) {
            $pram = [$hash, count($filter)];
            foreach ($filter as $f) {
                $pram[] = 'mo:problem:tag:'.$f;
            }
            call_user_func_array(['pRedis','zinterstore'], $pram);
        }
        pRedis::expire($hash, 3600);
        return $hash;
    }

    // To get all problems' IDs cached
    protected function cacheAll()
    {
        if (pRedis::exists('mo:problems')) {
            return;
        }
        $problems = Problem::all();
        foreach ($problems as $problem) {
            $id = (string)$problem['id'];
            $time = parent::oidToTimestamp($id);
            pRedis::zadd('mo:problems', $time, $id);
            foreach ($problem['tag'] as $tag) {
                pRedis::zadd('mo:problem:tag:'.$tag, $time, $id);
                pRedis::sadd('mo:problem:tags', $tag);
            }
        }
    }
}