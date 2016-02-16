<?php

use Workerman\Worker;

define('ABSPATH', '../../');
define('MOINC', ABSPATH.'mo-includes/');
define('MOCON', ABSPATH.'mo-content/');

require_once './Workerman/Autoloader.php';
require_once './class-db.php';
require_once './class-solution.php';
require_once './functions.php';
require_once ABSPATH.'mo-config.php';
require_once MOINC.'functions.php';

$worker_tasker = new Worker('Text://0.0.0.0:6666');
$worker_tasker->name = 'Tasker';
$worker_tasker->count = 2;

$task = array();
$cid = array();
$ava_client = array();
$client_sorted = false;

require_once 'tasker.php';

Worker::runAll();
