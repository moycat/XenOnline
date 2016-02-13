<?php

function check_login()
{
    global $db, $mem;
    if (defined('MEM') && MEM == true) {
        $mem = new Memcached('moyoj');
        $mem->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
        if (!count($mem->getServerList())) {
            $mem->addServer(MEM_HOST, MEM_PORT);
        }
    }
    $db = new DB();
    $db->init(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->connect();
    $admin_info = mo_read_cache('mo-admin-'.$_SESSION['aid']);
    if (!$admin_info) {
        $sql = 'SELECT `id`, `username`, `password`, `nickname`, `role` FROM `mo_admin` WHERE `id` = ? AND `role` > 0';
        $db->prepare($sql);
        $db->bind('i', $_SESSION['aid']);
        $result = $db->execute();
        if (!$result || $result[0]['password'] != $_SESSION['admin_password']) {
            unset($_SESSION['aid']);
            header('Location: login.php');
            exit(0);
        }
        mo_write_cache('mo-admin-'.$_SESSION['aid'], $result[0]);
    }
    $mo_settings = array();
    mo_load_settings();
    if (!isset($active)) {
        $active = '';
    }
}

function get_problem($pid)
{
    if (!isset($pid) || !is_numeric($pid)) {
        return false;
    }
    global $db;
    $sql = 'SELECT `id`, `title`, `description`, `hash`, `tag`, `extra`, `ver`, `time_limit`, `memory_limit`, `state` FROM `mo_judge_problem` WHERE `id` = ?';
    $db->prepare($sql);
    $db->bind('i', $pid);
    $result = $db->execute();

    return $result ? $result[0] : false;
}

function get_solution($sid)
{
    global $db;
    $sql = 'SELECT * FROM `mo_judge_solution` WHERE `id` = ?';
    $db->prepare($sql);
    $db->bind('i', $sid);
    $result = $db->execute();

    return $result ? $result[0] : false;
}

function undefined_error()
{
    echo '<div class="alert alert-warning">题库暂时为空！请先添加题目。</div>';
    require_once 'footer.php';
    exit(0);
}
