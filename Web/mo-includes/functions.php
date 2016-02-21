<?php
/*
 * mo-includes/functions.php @ MoyOJ
 *
 * This file requires other function files and provides some basic ones.
 *
 * Licensed under GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 */

require_once MOINC.'function-action.php';
require_once MOINC.'function-cache.php';
require_once MOINC.'function-discussion.php';
require_once MOINC.'function-data.php';
require_once MOINC.'function-log.php';
require_once MOINC.'function-problem.php';
require_once MOINC.'function-solution.php';
require_once MOINC.'function-stat.php';
require_once MOINC.'function-user.php';

$mo_time = microtime();
$mo_settings = array();

// Analyze the request to be handlered by the theme
function mo_analyze()
{
    $rt = array();
    if (isset($_GET['r']) && $_GET['r']) {
        $arg = $_GET['r'];
    } else {
        $arg = $_SERVER['REQUEST_URI'];
    }
    $arg = explode('/', $arg);
    foreach ($arg as $value) {
        if ($value) {
            $rt[] = $value;
        }
    }
    if (!isset($rt[0])) {
        return array('index');
    }

    return $rt;
}

// Load all plugins and the using theme to global variables
function mo_loadPT()
{
    global $mo_plugin, $mo_plugin_file, $mo_theme, $mo_theme_floder, $mo_theme_file;
    $mo_theme = mo_get_option('theme');
    $mo_plugin = mo_get_option('plugin');
    $plugin_floder = MOCON.'plugin/';
    $mo_theme_floder = MOCON."theme/$mo_theme/";
    $mo_theme_file = $mo_theme_floder."$mo_theme.php";
    if ($mo_plugin) {
        foreach ($mo_plugin as $now_plugin) {
            if (is_dir(file_exists("$plugin_floder$now_plugin/$now_plugin.php"))) {
                $mo_plugin_file[] = "$plugin_floder$now_plugin/$now_plugin.php";
            }
        }
    }
    if (!file_exists($mo_theme_file)) {
        $mo_theme_file = '';
    }
}

function has_login()
{
    return isset($_SESSION['uid']) ? $_SESSION['uid'] : false;
}

function getset(&$key, $value)
{
    $old = $key;
    $key = $value;

    return $old;
}

// Check whether a value is  serialized
function is_serialized($data, $strict = true) // From WordPress
{
    // if it isn't a string, it isn't serialized
        if (!is_string($data)) {
            return false;
        }
    $data = trim($data);
    if ('N;' == $data) {
        return true;
    }
    $length = strlen($data);
    if ($length < 4) {
        return false;
    }
    if (':' !== $data[1]) {
        return false;
    }
    if ($strict) {
        //output
                $lastc = $data[ $length - 1 ];
        if (';' !== $lastc && '}' !== $lastc) {
            return false;
        }
    } else {
        //input
                $semicolon = strpos($data, ';');
        $brace = strpos($data, '}');
                // Either ; or } must exist.
                if (false === $semicolon && false === $brace) {
                    return false;
                }
                // But neither must be in the first X characters.
                if (false !== $semicolon && $semicolon < 3) {
                    return false;
                }
        if (false !== $brace && $brace < 4) {
            return false;
        }
    }
    $token = $data[0];
    switch ($token) {
                case 's' :
                        if ($strict) {
                            if ('"' !== $data[ $length - 2 ]) {
                                return false;
                            }
                        } elseif (false === strpos($data, '"')) {
                            return false;
                        }
                case 'a' :
                case 'O' :
                        echo 'a';

                        return (bool) preg_match("/^{$token}:[0-9]+:/s", $data);
                case 'b' :
                case 'i' :
                case 'd' :
                        $end = $strict ? '$' : '';

                        return (bool) preg_match("/^{$token}:[0-9.E-]+;$end/", $data);
        }

    return false;
}

// Send a message to the socket server
function mo_com_socket($send, $receive = false)
{
    $request = json_encode(array('task' => $send, 'pass' => sha1(DB_PASS)))."\n";
    $errno = 0;
    $errstr = '';
    $socket = fsockopen(SOCK_HOST, SOCK_PORT, $errno, $errstr, 1);
    if (!$socket) {
        return false;
    }
    fwrite($socket, $request);
    if ($receive) {
        $get = socket_read($socket, 8096, PHP_NORMAL_READ);
    }
    fclose($socket);

    return $receive ? json_decode($get) : true;
}

// Return the time used since begin
function mo_time($p = 3)
{
    global $mo_time;
    $t = microtime();
    list($m0, $s0) = explode(' ', $mo_time);
    list($m1, $s1) = explode(' ', $t);

    return round(($s1 + $m1 - $s0 - $m0) * 1000, $p);
}

// Write debug info to the HTML
function mo_write_note($note)
{
    if (defined('DEBUG') && DEBUG && defined('OUTPUT') && OUTPUT) {
        echo "\n<!-- Note: ".$note.' Time:'.mo_time()." -->\n";
    }
}

function mo_get_user_ip()
{
    return ip2long($_SERVER['REMOTE_ADDR']);
}

function mo_get_url()
{
    $url = MO_URL.'/'.$_SERVER['PHP_SELF'];

    return $url;
}

// Check the entrance
function mo_in_check($autoExit = true)
{
    if (!defined('RUN')) {
        mo_write_note('Invaild entrance.');
        if ($autoExit) {
            exit(0);
        } else {
            return false;
        }
    }

    return true;
}

// Get a friendly expression of time
function mo_date($time = null)
{
    if (!$time) {
        return '从未';
    }
    $text = '';
    $time = $time === null || $time > time() ? time() : intval($time);
    $t = time() - $time; // Time lag
    if ($t == 0) {
        $text = '刚刚';
    } elseif ($t < 60) {
        $text = $t.'秒前';
    } // Less than a minute
    elseif ($t < 60 * 60) {
        $text = floor($t / 60).'分钟前';
    } // Less than an hour
    elseif ($t < 60 * 60 * 24) {
        $text = floor($t / (60 * 60)).'小时前';
    } // Less than an day
    elseif ($t < 60 * 60 * 24 * 3) {
        $text = floor($time / (60 * 60 * 24)) == 1 ? '昨天 '.date('H:i', $time) :
                        '前天 '.date('H:i', $time);
    } // Less than 3 days
    elseif ($t < 60 * 60 * 24 * 30) {
        $text = date('m月d日 H:i', $time);
    } // Less than a mouth
    elseif ($t < 60 * 60 * 24 * 365) {
        $text = date('m月d日', $time);
    } // Less than a year
    else {
        $text = date('Y年m月d日', $time);
    } // More than a year
    return $text;
}

// Change the state into words
function mo_state($state, $short = false, $with_label = true)
{
    $rt = '';
    $label = '';
    switch ((int) $state) {
    case 10:$rt = $short ? 'AC' : 'Accepted';
    $label = 'success';
    break;
    case 6:$rt = $short ? 'WA' : 'Wrong Answer';
    $label = 'danger';
    break;
    case 4:$rt = $short ? 'RE' : 'Runtime Error';
    $label = 'danger';
    break;
    case 0:$rt = $short ? 'WAIT' : 'Waiting...';
    $label = 'primary';
    break;
    case 1:$rt = $short ? 'CE' : 'Compile Error';
    $label = 'warning';
    break;
    case 2:$rt = $short ? 'MLE' : 'Memory Limit Exceed';
    $label = 'danger';
    break;
    case 3:$rt = $short ? 'TLE' : 'Time Limit Exceed';
    $label = 'danger';
    break;
    case -3:$rt = $short ? 'RUN' : 'Running...';
    $label = 'info';
    break;
    case -2:$rt = $short ? 'COM' : 'Compiling...';
    $label = 'info';
    break;
    default:$rt = $short ? '???' : 'Unknown Status';
    $label = 'default';
    break;
  }
    if ($with_label) {
        return '<span class="label label-'.$label.'">'.$rt.'</span>';
    }

    return $rt;
}

function mo_state_r($state)
{
    $info = mo_state($state, false, false);
    $info_short = mo_state($state, true, false);
    switch ($info_short) {
    case 'AC':$label = 'success';break;
    case 'CE':$label = 'warning';break;
    case 'WA':case 'RE':case 'MLE':case 'TLE':$label = 'danger';break;
    case 'RUN':case 'COM':$label = 'info';break;
    case 'WAIT':$label = 'primary';break;
    default:$label = 'default';break;
  }

    return '<span class="visible-lg label label-'.$label.'">'.$info.'</span><span class="hidden-lg label label-'.$label.'">'.$info_short.'</span>';
}

function mo_lang($lang, $code = true)
{
    $rt = '';
    switch ($lang) {
    case 1:$rt = 'C++';break;
    case 2:$rt = 'Pascal';break;
    case 3:$rt = 'Java';break;
    default:$rt = 'Unknown';break;
  }
    if ($code) {
        return '<code>'.$rt.'</code>';
    }

    return $rt;
}

// To print texts safely.
function e($text, $not_show = false)
{
    if ($not_show)
    {
        return htmlspecialchars($text);
    } else {
        echo htmlspecialchars($text);
    }
}