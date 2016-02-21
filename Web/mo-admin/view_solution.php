<?php
$head = '<script src="//cdn.bootcss.com/ace/1.2.3/ace.js"></script>
<script src="//cdn.bootcss.com/ace/1.2.3/ext-static_highlight.js"></script>
<style type="text/css" media="screen">
    #editor {
      width: 100%;
      height: 600px;
      font-size: 15px;
    }
</style>';
$active = 'data';
require_once 'header.php';
if (!isset($_GET['sid']) || !is_numeric($_GET['sid']) || (int) $_GET['sid'] < 1) {
    undefined_error();
}
$sid = (int) $_GET['sid'];
$solution = get_solution($sid);
?>
<div class="container">
<h1>评测记录#<?php echo $solution['id']; ?><br class="hidden-lg">
  <small>提交时间：<?php echo $solution['post_time']; ?></small>
</h1>
<HR style="FILTER: alpha(opacity=100,finishopacity=0,style=3)" color=#987cb9 SIZE=3>
<h3>评测状态 <?php echo mo_state($solution['state']); ?></h2>
<h3>编程语言 <?php echo mo_lang($solution['language']); ?></h2>
<?php
if ($solution['state'] == 1) {
    ?>
<h3>错误详情</h3>
<pre>
  <?php e($solution['detail']);
    ?>
</pre>
<?php

} ?>
<?php
if (in_array($solution['state'], array(10, 6, 4, 2, 3))) {
    ?>
<h3>评测结果
  <small>运行耗时 <?php echo $solution['used_time'];
    ?>ms / 使用内存 <?php echo $solution['used_memory'];
    ?>KiB</small>
</h3>
<?php
$detail_time = explode(' ', $solution['detail_time']);
    $detail_memory = explode(' ', $solution['detail_memory']);
    $detail_result = explode(' ', $solution['detail_result']);
    $turn = count($detail_result) - 1;
    $each_score = round(100 / $turn, 2);
    $ac_turn = 0;
    ?>
<table class="table table-responsive table-hover">
  <thead>
     <tr>
        <th>#</th>
        <th>状态</th>
        <th>耗时</th>
        <th>内存</th>
        <th>分数</th>
     </tr>
  </thead>
  <tbody>
    <?php
    for ($i = 0; $i < $turn; ++$i) {
        echo '<tr>';
        echo '<td>'.$i.'</td>';
        echo '<td>'.mo_state($detail_result[$i]).'</td>';
        echo '<td>'.$detail_time[$i].' ms</td>';
        echo '<td>'.$detail_memory[$i].' KiB</td>';
        echo '<td>';
        if ($detail_result[$i] == '10') {
            ++$ac_turn;
            echo $each_score;
        } else {
            echo 0;
        }
        echo '</td>';
        echo '</tr>';
    }
    ?>
        <td>总结</td>
        <td><span class="label label-info">AC: <?php echo $ac_turn;
    ?> / <?php echo $turn;
    ?></span></td>
        <td><?php echo $solution['used_time'];
    ?> ms</td>
        <td><?php echo $solution['used_memory'];
    ?> KiB</td>
        <td><?php echo 100 * ($ac_turn / $turn);
    ?></td>
  </tbody>
</table>
<?php

} ?>
<h3>代码
  <small>长度 <?php echo $solution['code_length']; ?>字节</small>
</h3>
<div id="editor"></div>
<script>
    var code = atob('<?php e($solution['code']);?>');
    var editor = ace.edit("editor");
    editor.setReadOnly(true);
    editor.getSession().setUseWrapMode(true);
    editor.setTheme("ace/theme/clouds");
    editor.getSession().setMode("ace/mode/c_cpp");
    editor.setValue(code);
    editor.gotoLine(1);
</script>

</div>
<?php
require_once 'footer.php';
