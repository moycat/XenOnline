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

if (!file_exists(ABSPATH.'mo-config.php')) {
    header("Location: /mo-includes/setup.php");
    die('MoyOJ not installed! Go to /mo-includes/setup.php.');
}

// Load the files
require_once MOINC.'functions.php';
require_once 'mo-config.php';

// Define the global variable
session_start();
$mo_time = microtime();

$redis = NULL;
$db = new NoDB();
$db_conn = NULL;
$db_col = array();
$user_logged = NULL;

mo_connect_redis();

$mo_request = mo_analyze();
$mo_setting = mo_load_setting();

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

// Check if logged in
mo_user_login('auto');

//mo_user_login('check', 'moycat', 'qing981203', 36000);
//mo_user_logout();
//mo_set_user('56cf012f8a028d49f68c753f', 'username', 'moycat');
var_dump($_SESSION);
var_dump($user_logged);
//mo_add_new_solution('56cfde268a028d49f68c7541',1,'qwert','56cf012f8a028d49f68c753f');
//echo mo_oid_to_timestamp('56cf07b38a028d49f68c7540');
echo mo_time();

// Hand over the job to the theme
if (defined('OUTPUT') && OUTPUT && $mo_theme_file) {
    call_user_func($mo_theme);
}

mo_write_note('The page has been processed successfully.');
