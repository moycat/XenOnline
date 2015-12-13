<?php
use Workerman\Worker;
use Workerman\Lib\Timer;
require_once './Workerman/Autoloader.php';
require_once './class-db.php';
require_once './class-solution.php';
require_once './functions.php';
require_once '../../mo-config.php';

$worker_tasker = new Worker("Text://0.0.0.0:6666");
$worker_tasker->name = 'Tasker';
$worker_tasker->count = 2;

$task = array();
$cid = array();

require_once 'tasker.php';

Worker::runAll();
