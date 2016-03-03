<?php
namespace App\Repositories\Contracts;

interface Protein {
    public function index($size, $startID, $filter = array());
    public function find($id);
    public function add($info, $option);
    public function count($filter = array());
}

abstract class Cell implements Protein {
    protected $load;
    protected $failedToLoad;

    abstract public function index($size, $startID, $filter = array());
    abstract public function find($id);
    abstract public function add($info, $option);
    abstract public function count($filter = array());

    public function oidToTimestamp($oid) {
        return hexdec(substr((string) $oid, 0, 8));
    }
}