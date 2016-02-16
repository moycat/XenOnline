<?php

function publish_jump($status, $pid = 0)
{
    header("Location: problem.php?result=$status&pid=$pid");
    exit(0);
}

function edit_problem()
{
    if (!isset($_POST['title'], $_POST['test-editormd-markdown-doc'], $_POST['time_limit'], $_POST['memory_limit'], $_POST['tag'])) {
        return false;
    }
    if (!$_POST['title'] || !$_POST['test-editormd-markdown-doc'] || !is_numeric($_POST['time_limit']) || !is_numeric($_POST['memory_limit'])) {
        $_SESSION['admin_publish_tmp'] = $_POST;
        $_SESSION['admin_publish_tmp']['description'] = $_POST['test-editormd-markdown-doc'];
        $_SESSION['admin_publish_tmp']['error'] = '未定义的操作。';

        return false;
    }
    $extra = '';
    $prob = get_problem($_POST['edit_id']);
    $hash = $prob['hash'];
    $ver = (int) $prob['ver'];
    $datacount = 0;
    while (isset($_FILES["stdout$datacount"], $_FILES["input$datacount"]) && !$_FILES["stdout$datacount"]['error'] && !$_FILES["input$datacount"]['error']) {
        ++$datacount;
    }
    if ($datacount) {
        $floder = dirname(__FILE__).'/../mo-content/data/'.$hash;
        if (file_exists($floder) && $handle = opendir($floder)) {
            while (($item = readdir($handle)) != false) {
                if (!is_dir("$floder/$item")) {
                    unlink("$floder/$item");
                }
            }
            closedir($handle);
        } else {
            mkdir($floder, 0777, true);
        }
        for ($i = 0; $i < $datacount; ++$i) {
            move_uploaded_file($_FILES["stdout$i"]['tmp_name'], $floder."/std$i.out");
            move_uploaded_file($_FILES["input$i"]['tmp_name'], $floder."/test$i.in");
        }
        ++$ver;
    } else {
        $datacount = $prob['test_turn'];
    }
    $sql = 'UPDATE `mo_judge_problem` SET `title` = ?, `description` = ?, `tag` = ?, `extra` = ?, `ver` = ?, `time_limit` = ?, '.
                '`memory_limit` = ?, `test_turn` = ? WHERE `mo_judge_problem`.`id` = ?';
    mo_del_cache('mo:problem:'.$_POST['edit_id']);
    global $db;
    $db->prepare($sql);
    $db->bind('ssssiiiii', $_POST['title'], $_POST['test-editormd-markdown-doc'], $_POST['tag'], serialize($extra), $ver, $_POST['time_limit'],
                        $_POST['memory_limit'], $datacount, $_POST['edit_id']);
    $db->execute();

    return $_POST['edit_id'];
}

function add_problem()
{
    if (!isset($_POST['title'], $_POST['test-editormd-markdown-doc'], $_POST['time_limit'], $_POST['memory_limit'], $_POST['tag'])) {
        return false;
    }
    if (!$_POST['title'] || !$_POST['test-editormd-markdown-doc'] || !is_numeric($_POST['time_limit']) || !is_numeric($_POST['memory_limit'])) {
        $_SESSION['admin_publish_tmp'] = $_POST;
        $_SESSION['admin_publish_tmp']['description'] = $_POST['test-editormd-markdown-doc'];
        $_SESSION['admin_publish_tmp']['error'] = '未定义的操作。';

        return false;
    }
    $datacount = 0;
    while (isset($_FILES["stdout$datacount"], $_FILES["input$datacount"]) && !$_FILES["stdout$datacount"]['error'] && !$_FILES["input$datacount"]['error']) {
        ++$datacount;
    }
    if (!$datacount) {
        return array(false, '没有测试数据！');
    }
    $extra = '';
    $hash = md5((string) rand(100000, 999999).time());
    $floder = dirname(__FILE__).'/../mo-content/data/'.$hash;
    mkdir($floder, 0777, true);
    for ($i = 0; $i < $datacount; ++$i) {
        move_uploaded_file($_FILES["stdout$i"]['tmp_name'], $floder."/std$i.out");
        move_uploaded_file($_FILES["input$i"]['tmp_name'], $floder."/test$i.in");
    }
    $sql = 'INSERT INTO `mo_judge_problem` (`title`, `description`, `tag`, `extra`, `hash`, `ver`, `post_time`, `time_limit`, `memory_limit`, `state`, `ac`, `submit`, `solved`, `try`, `test_turn`)'.
                'VALUES (?, ?, ?, ?, ?, \'0\', CURRENT_TIMESTAMP, ?, ?, \'1\', \'0\', \'0\', \'0\', \'0\', ?)';
    global $db;
    $db->prepare($sql);
    $db->bind('sssssiii', $_POST['title'], $_POST['test-editormd-markdown-doc'], $_POST['tag'], serialize($extra), $hash, $_POST['time_limit'], $_POST['memory_limit'], $datacount);
    $db->execute();
    $new_pid = $db->getInsID();

    return $new_pid;
}

$no_display = true;
require_once 'header.php';
if (!isset($_POST['action'])) {
    publish_jump(0);
}
switch ($_POST['action']) {
    case 'add':
        if ($new_pid = add_problem()) {
            publish_jump(1, $new_pid);
        } else {
            header('Location: problem_edit.php?action=add');
        }
    case 'edit':
        if ($edit_id = edit_problem()) {
            publish_jump(2, $edit_id);
        } else {
            header('Location: problem_edit.php?action=edit&id='.$_GET['edit_id']);
        }
    default:
        publish_jump(0);
}
