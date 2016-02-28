<?php
/*
 * mo-includes/function-tool.php @ MoyOJ
 *
 * This file provides some basic functions as tools.
 *
 * Licensed under GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 */

// Seconds
define('HOUR', 3600);
define('DAY', 86400);
define('WEEK', 604800);
define('MOUTH', 18144000);

// To print texts safely.
function e($text)
{
    echo htmlspecialchars($text);
}

// Analyze the request in the url
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

// Turn the xxxxxxx MongoDB type into array
function mo_arrayfy(&$obj)
{
    if (!is_array($obj)) {
        return;
    }
    foreach ($obj as $key => $value) {
        if (is_array($value)) {
            mo_arrayfy($obj[$key]);
        } elseif ($value instanceof ArrayObject) {
            $obj[$key] = $value->getArrayCopy();
            mo_arrayfy($obj[$key]);
        } elseif ($value instanceof MongoDB\BSON\ObjectID) {
            $obj[$key] = (string) $value;
        }
    }
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

function mo_flat(&$data)
{
    unset($data['flat']);
    $flat = array();
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $data[$key] = json_encode($value);
            $flat[] = $key;
        }
    }
    $data['flat'] = json_encode($flat);
}

function mo_has_login()
{
    global $user_logged;
    return $user_logged != NULL;
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

// Load all plugins and the using theme to global variables
function mo_loadPT()
{
    global $mo_plugin, $mo_plugin_file, $mo_theme, $mo_theme_floder, $mo_theme_file;
    $mo_plugin = mo_get_setting('plugin');
    $plugin_floder = MOCON.'plugin/';
    if ($mo_plugin) {
        foreach ($mo_plugin as $now_plugin) {
            if (file_exists("$plugin_floder$now_plugin/$now_plugin.php")) {
                $mo_plugin_file[] = "$plugin_floder$now_plugin/$now_plugin.php";
            }
        }
    }
    $mo_theme = mo_get_setting('theme');
    $mo_theme_floder = MOCON."theme/$mo_theme/";
    $mo_theme_file = $mo_theme_floder."$mo_theme.php";
    if (!file_exists($mo_theme_file)) {
        $mo_theme_file = '';
    }
}

function mo_load_setting()
{
   global $db, $db_col, $mo_setting;
   $mo_setting = mo_read_cache_array('mo:setting');
   if (!$mo_setting) {
       $db_col['setting'] = $db->selectCollection('mo_setting');
       $mo_setting_raw = $db_col['setting']->find();
       foreach ($mo_setting_raw as $setting) {
           $mo_setting[$setting['item']] = $setting['value'];
       }
       mo_write_cache_array('mo:setting', $mo_setting);
   }
}

// Return the timestamp from an ObjectID
function mo_oid_to_timestamp($oid)
{
    return hexdec(substr((string) $oid, 0, 8));
}

// Publish a message over Redis
function mo_publish($channel, $msg)
{
    global $redis;
    $redis->publish($channel, json_encode($msg));
}

function mo_get_setting($key)
{
   global $mo_setting;
   return isset($mo_setting[$key]) ? $mo_setting[$key] : NULL;
}

// Generate a password with salt
 function mo_password($password, $salt)
 {
     return sha1(md5($password.$salt).$salt);
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

// Return the time passed since beginning of loading
function mo_time($p = 3)
{
    global $mo_time;
    $t = microtime();
    list($m0, $s0) = explode(' ', $mo_time);
    list($m1, $s1) = explode(' ', $t);

    return round(($s1 + $m1 - $s0 - $m0) * 1000, $p);
}

function mo_unflat(&$data)
{
    $flat = json_decode($data['flat']);
    unset($data['flat']);
    foreach ($flat as $key) {
        $data[$key] = json_decode($data[$key]);
    }
}

// Write debug info to the HTML source
function mo_write_note($note)
{
    if (DEBUG && defined('OUTPUT') && OUTPUT) {
        echo "\n<!-- Note: ".$note.' Time:'.mo_time()." -->\n";
    }
}
