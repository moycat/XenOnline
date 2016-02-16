<?php
/*
 * mo-loader.php @ MoyOJ
 *
 * This file loads the whole site.
 *
 * Licensed under GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 */

/*********************************
/ Initialise the site
/********************************/

// Define the fixed values

define('ABSPATH', __DIR__.'/');
define('MOINC', ABSPATH.'mo-includes/');
define('MOCON', ABSPATH.'mo-content/');

// Check if closed
if (file_exists(MOCON.'closed.lock')) {
    die('<h1>Site Closed Temporarily</h1>');
}

// Require the files
require_once MOINC.'classes.php';
require_once MOINC.'functions.php';

if (!file_exists(ABSPATH.'mo-config.php')) {
    require_once MOINC.'setup.php';
    exit(0);
}

require_once 'mo-config.php';

// Define the values
$db = new DB();
$user = new User();
$mo_settings = array();
$mo_request = '';
$mo_plugin = array();
$mo_plugin_file = array();
$mo_theme = '';
$mo_theme_floder = '';
$mo_theme_file = '';

$mo_user = array();
$mo_user_failed = array();
$mo_now_user = null;

$mo_discussion = array();
$mo_discussion_failed = array();
$mo_now_discussion = null;

$mo_problem = array();
$mo_problem_failed = array();
$mo_now_problem = null;

$mo_solution = array();
$mo_solution_failed = array();
$mo_now_solution = null;

$mo_temp = array();
// Initialise the environment
if (DEBUG == true) {
    error_reporting(E_ALL);
    mo_write_note('DEBUG ENABLED');
} else {
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
}

$db->init(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$db->connect();
$redis = new Redis();
$redis->pconnect(REDIS_HOST, REDIS_PORT);
if (REDIS_PASS) {
    $redis->auth(REDIS_PASS);
}

session_start();
mo_load_settings();
$mo_request = mo_analyze();

// Load the plugins & the theme
mo_loadPT();
if (count($mo_plugin)) {
    foreach ($mo_plugin as $plugin) {
        require_once $plugin;
    }
}
if ($mo_theme_file) {
    require_once $mo_theme_file;
}
do_action('loadPT');

// Check if logged in
$user->login('auto');

do_action('loadStart');

// Hand over the job to the theme
if (defined('OUTPUT') && OUTPUT == true && $mo_theme_file) {
    call_user_func($mo_theme);
}

do_action('loadDone');

mo_write_note('The page has been processed successfully.');
