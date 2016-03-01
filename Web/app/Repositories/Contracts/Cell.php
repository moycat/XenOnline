<?php
namespace App\Repositories\Contracts;

interface Cell {
    public function index($size, $startID, $filter = array());
    public function find($id);
    public function add($info, $option);
    public function count($filter = array());
}