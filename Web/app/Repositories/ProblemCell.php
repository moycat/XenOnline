<?php

namespace App\Repositories;

use Auth;
use Cell;
use CacheCell;
use App\Problem;
use Illuminate\Support\Facades\Cache;

class ProblemCell extends Cell
{
    protected $load;
    protected $failedToLoad;

    function __construct() {
        $this->cacheAll();
    }

    /*
     * Get the list of problems with given filters
     *
     * @param int $size
     * @param int $startID
     * @param array $filter
     *
     * @return array
     */
    public function index($size, $startID = 0, $filter = array())
    {
        sort($filter);
        // Get the IDs
        switch (count($filter)) {
            case 0: // No tag specified
                $result['count'] = CacheCell::count('mo:problems');
                $problems = CacheCell::readSortedSet('mo:problems', $startID, $startID + $size - 1);
                break;

            case 1: // One tag only
                $result['count'] = CacheCell::count('mo:problem:tag:'.$filter[0]);
                $problems = CacheCell::readSortedSet('mo:problem:tag:'.$filter[0], $startID, $startID + $size - 1);
                break;

            default: // More than one tag
                // Read the list from a temporary zset
                $hash = $this->cacheTag($filter);
                $result['count'] = CacheCell::count($hash);
                $problems = CacheCell::readSortedSet($hash, $startID, $startID + $size - 1);
                break;
        }

        // Fill the problems
        $result['problems'] = array(); // Error without this when no problems
        foreach ($problems as $pid) {
            $result['problems'][] = $this->find($pid);
        }
        $result['previous'] = ($startID > 0);
        $result['next'] = $result['count'] >= $startID + $size - 1;
        $result['tags'] = CacheCell::readSet('mo:problem:tags');

        return $result;
    }

    /*
     * Get a problem
     *
     * @param string $pid
     *
     * @return object
     */
    public function find($pid)
    {
        $problem = CacheCell::read('mo:problem:'.$pid);
        if ($problem) {
            $problem = json_decode($problem);
            $problem->try = CacheCell::hget('mo:problem:try', $pid);
            $problem->submit = CacheCell::hget('mo:problem:submit', $pid);
            $problem->solve = CacheCell::hget('mo:problem:solve', $pid);
            $problem->ac = CacheCell::hget('mo:problem:ac', $pid);
        } else {
            $problem = Problem::findOrFail($pid);
            CacheCell::write('mo:problem:'.$pid, $problem->toJson());
            CacheCell::hset('mo:problem:try', $pid, $problem->try);
            CacheCell::hset('mo:problem:submit', $pid, $problem->submit);
            CacheCell::hset('mo:problem:solve', $pid, $problem->solve);
            CacheCell::hset('mo:problem:ac', $pid, $problem->ac);
        }

        return $problem;
    }

    public function add($info, $option)
    {
        // TODO
    }

    /*
     * Count the number of all problems
     *
     * @return int
     */
    public function count()
    {
        return Problem::count();
    }

    /*
     * Get the IDs of the problems with given tags
     *
     * @return array
     */
    protected function cacheTag($filter)
    {
        $hash = 'mo:problem:tags:'.md5(implode(' ', $filter));
        if (!CacheCell::exists($hash)) {
            $pram = [$hash, count($filter)];
            foreach ($filter as $f) {
                $pram[] = 'mo:problem:tag:'.$f;
            }
            call_user_func_array(['CacheCell','zinterstore'], $pram);
        }
        CacheCell::expire($hash, 3600);
        return $hash;
    }

    /*
     * Get all problems' IDs & tags cached
     *
     * @return array
     */
    protected function cacheAll()
    {
        if (CacheCell::exists('mo:problems')) {
            return;
        }
        $problems = Problem::all();
        foreach ($problems as $problem) {
            $id = (string)$problem['id'];
            $time = parent::oidToTimestamp($id);
            CacheCell::zadd('mo:problems', $time, $id);
            foreach ($problem['tag'] as $tag) {
                CacheCell::zadd('mo:problem:tag:'.$tag, $time, $id);
                CacheCell::sadd('mo:problem:tags', $tag);
            }
        }
    }
}
