<?php
$active = 'data';
$head = '<link rel="stylesheet" href="//cdn.bootcss.com/webui-popover/1.2.5/jquery.webui-popover.min.css">
<script src="//cdn.bootcss.com/webui-popover/1.2.5/jquery.webui-popover.min.js"></script>';
require_once 'header.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$start = isset($_GET['loc']) ? (int) $_GET['loc'] : 0;
$piece = isset($_GET['piece']) ? (int) $_GET['piece'] : 20;
switch ($action) {
  case 'rejudge':
  case 'del':
    if ($action == 'del') {
        $sid = $_GET['sid'];
        $sql = 'DELETE FROM `mo_judge_solution` WHERE `id` = ?';
        $db->prepare($sql);
        $db->bind('i', $sid);
        $db->execute();
        mo_del_cache('mo:solution:'.$sid);
        $msg = '成功删除评测记录#'.$sid.'。';
    } else {
        $sid = $_GET['sid'];
        if (mo_load_problem($sid)) {
            mo_write_cache_array_item('mo:solution:'.$sid, 'state', '0');
            $sql = 'UPDATE `mo_judge_solution` SET `state` = 0 WHERE `id` = ?';
            $db->prepare($sql);
            $db->bind('i', $sid);
            $db->execute();
            $msg = '提交#'.$sid.'将很快再次评测。';
        } else {
            $msg = '原题目已删除，无法重新评测。';
        }
    }
    default:
    case 'list':
    $sql = 'SELECT `id`, `pid`, `uid`, `client`, `post_time`, `state`, `language`, `code_length`,'.
    " `used_time`, `used_memory` FROM `mo_judge_solution` ORDER BY `id` DESC LIMIT $start,$piece";
        $db->prepare($sql);
        $result = $db->execute();
        $solution_count = mo_get_solution_count();
        break;
    case 'search':
    $sql = 'SELECT `id`, `pid`, `uid`, `client`, `post_time`, `state`, `language`, `code_length`,'.
    ' `used_time`, `used_memory` FROM `mo_judge_solution` WHERE 1=1';
        if (isset($_GET['pid']) && $_GET['pid']) {
            $sql .= ' AND `pid` = '.$db->clean((int) $_GET['pid']);
        }
        if (isset($_GET['state']) && $_GET['state']) {
            $sql .= ' AND `state` = '.$db->clean((int) $_GET['state']);
        }
        if (isset($_GET['lang']) && $_GET['lang']) {
            $sql .= ' AND `language` = '.$db->clean((int) $_GET['lang']);
        }
        if (isset($_GET['user']) && $_GET['user']) {
            if ($_GET['user_type'] == 'uid') {
                $sql .= ' AND `uid` = '.$db->clean((int) $_GET['user']);
            } else {
                $uid = mo_get_uid_by_username($_GET['user']);
                $sql .= ' AND `uid` = '.$uid;
            }
        }
        $sql .= " ORDER BY `id` DESC LIMIT $start,$piece";
        $db->prepare($sql);
        $result = $db->execute();
        $solution_count = count($result);
}
$page = ceil($solution_count / $piece);
?>
<div class="container">
    <div class="col-md-3">
        <form method="get" action="view_solution.php">
          <h4>快速查看</h4>
            <div class="input-group">
             <span class="input-group-addon">#</span>
             <input type="text" name="sid" class="form-control" placeholder="评测编号">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="submit" >
                     VIEW
                </button>
               </span>
          </div>
        </form>
        <h4>筛选器</h4>
        <form method="get" action="solution.php">
  		  	<input type="hidden" name="action" value="search">
          <div class="input-group">
             <span class="input-group-addon">#</span>
             <input type="text" name="pid" class="form-control" placeholder="题号" value="<?php if (isset($_GET['pid'])) {
    echo $_GET['pid'];
}?>">
          </div>
					<div class="input-group">
               <span class="input-group-addon">@</span>
	             <input type="text" name="user" class="form-control" placeholder="用户" value="<?php if (isset($_GET['user'])) {
    echo $_GET['user'];
}?>">
	        </div>
          <div class="form-group">
          <label class="checkbox-inline">
             <input type="radio" name="user_type" id="user_type_name"
                value="username"<?php if (!isset($_GET['user_type']) || $_GET['user_type'] != 'uid') {
    echo ' checked';
}?>> 用户名
          </label>
          <label class="checkbox-inline">
             <input type="radio" name="user_type" id="user_type_id"
                value="uid"<?php if (isset($_GET['user_type']) && $_GET['user_type'] == 'uid') {
    echo ' checked';
}?>> 用户ID
          </label>
          </div>
          <div class="form-group">
             <select name="state" class="form-control">
              <option value="">状态不限</option>
              <option value="10"<?php if (isset($_GET['state']) && $_GET['state'] == '10') {
    echo ' selected';
}?>>Accepted</option>
              <option value="6"<?php if (isset($_GET['state']) && $_GET['state'] == '6') {
    echo ' selected';
}?>>Wrong Answer</option>
              <option value="4"<?php if (isset($_GET['state']) && $_GET['state'] == '4') {
    echo ' selected';
}?>>Runtime Error</option>
              <option value="1"<?php if (isset($_GET['state']) && $_GET['state'] == '1') {
    echo ' selected';
}?>>Compile Error</option>
              <option value="2"<?php if (isset($_GET['state']) && $_GET['state'] == '2') {
    echo ' selected';
}?>>Memory Limit Exceed</option>
              <option value="3"<?php if (isset($_GET['state']) && $_GET['state'] == '3') {
    echo ' selected';
}?>>Time Limit Exceed</option>
             </select>
          </div>
          <div class="form-group">
             <select name="lang" class="form-control">
              <option value="">语言不限</option>
              <option value="1"<?php if (isset($_GET['lang']) && $_GET['lang'] == '1') {
    echo ' selected';
}?>>C/C++</option>
              <option value="2"<?php if (isset($_GET['lang']) && $_GET['lang'] == '2') {
    echo ' selected';
}?>>Pascal</option>
              <option value="3"<?php if (isset($_GET['lang']) && $_GET['lang'] == '3') {
    echo ' selected';
}?>>Java</option>
             </select>
          </div>
					<button class="btn btn-default pull-right" type="submit" ><span class="glyphicon glyphicon-search"></span> 搜索</button>
        </form>
    </div>
    <div class="col-md-9">
        <div class="row">
            <?php if (!$result) {
    echo '<div class="alert alert-warning">评测记录暂时为空！还没有人提交评测。</div>';
} ?>
            <?php if (isset($msg)) {
    echo '<div class="alert alert-success">'.$msg.'</div>';
} ?>
          <table class="table table-striped table-hover">
           <tbody>
            <?php
            $detail = array();
            foreach ($result as $solution) {
                $detail[$solution['id']] = json_encode($solution);
                $tr = (isset($_GET['sid']) && (string) $solution['id'] == $_GET['sid']) ? '<tr class="success">' : '<tr>';
                echo '
				'.$tr.'
				 <td>'.$solution['id'].'</td>
 				 <td><a href="edit_user.php?uid='.$solution['uid'].'">'.mo_get_user_nickname($solution['uid']).'</a></td>
 				 <td><a href="edit_problem.php?action=edit&pid='.$solution['pid'].'">'.mo_get_problem_title($solution['pid']).'</a></td>
 				 <td class="hidden-xs">'.mo_lang($solution['language']).'</td>
 				 <td>'.mo_state_r($solution['state']).'</td>
 				 <td class="hidden-xs hidden-sm hidden-md">'.$solution['used_time'].' ms</td>
 				 <td class="hidden-xs hidden-sm hidden-md">'.$solution['used_memory'].' KiB</td>
				 <td>
				 <button type="button" class="btn btn-warning btn-sm" onclick="rejudge_solution('.$solution['id'].')">重评</button>
         <a class="btn btn-info btn-sm" onClick="window.open(\'view_solution.php?sid='.$solution['id'].'\')">详情</a>
				 <button type="button" class="btn btn-danger btn-sm" onclick="del_solution('.$solution['id'].')">删除</button>
				 </tr>';
            }
            ?>
           </tbody>
           <thead>
            <tr>
             <th>#</th>
             <th>提交者</th>
             <th>题目</th>
             <th class="hidden-xs">语言</th>
             <th>状态</th>
             <th class="hidden-xs hidden-sm hidden-md">运行时间</th>
             <th class="hidden-xs hidden-sm hidden-md">使用内存</th>
             <th>操作</th>
            </tr>
           </thead>
          </table>
            <ul class="pager">
              <li class="<?php echo $start >= $piece ? 'previous' : 'previous disabled';?>"><a href="<?php echo $start >= $piece ? 'solution.php?loc='.($start - $piece) : '#';?>">&larr; 上一页</a></li>
              共<?php echo ceil($solution_count / $piece); ?>页，正在浏览第<?php echo ceil($start / $piece) + 1; ?>页
              <li class="<?php echo $solution_count - $start >= $piece ? 'next' : 'next disabled';?>"><a href="<?php echo $start + $piece < $solution_count ? 'solution.php?loc='.($start + $piece) : '#';?>">下一页 &rarr;</a></li>
            </ul>
         </div>
    </div>
</div>
<div class="modal fade" id="op_solution" tabindex="-1" role="dialog"
   aria-labelledby="myModalLabel" aria-hidden="true">
   <form id="opform" role="form" method="get" action="solution.php" enctype="multipart/form-data">
	   <div class="modal-dialog">
		  <div class="modal-content">
			 <div class="modal-header">
				<button type="button" class="close"
				   data-dismiss="modal" aria-hidden="true">
					  &times;
				</button>
				<h4 class="modal-title" id="op_solution_title">

				</h4>
			 </div>
			 <div class="modal-body" id="op_body">

			 </div>
			 <div class="modal-footer">
				 <input type="hidden" id="op_action" name="action" value="">
				 <input type="hidden" id="op_sid" name="sid" value="0">
				<button type="button" class="btn btn-default"  data-dismiss="modal">
					取消
				</button>
				<button id="op_name" type="submit" class="btn btn-danger"></button>
			 </div>
		  </div>
      </form>
</div>
<script>
function del_solution(sid) {
	$('#op_solution_title').html('<span class="glyphicon glyphicon-warning-sign"></span> 删除评测#'+sid);
  $('#op_body').html('删除后本次评测的数据将会消失，但相关的统计数据不会被修改。');
  $('#op_name').html('删除');
  $("#op_action").val("del");
	$('#op_confirm').remove();
    $('#op_sid').val(sid);
	$('.modal').modal();
}
function rejudge_solution(sid) {
	$('#op_solution_title').html('<span class="glyphicon glyphicon-warning-sign"></span> 重新评测#'+sid);
  $('#op_body').html('你确定要重新评测这一次提交吗？');
  $('#op_name').html('重评');
  $("#op_action").val("rejudge");
	$('#op_confirm').remove();
    $('#op_sid').val(sid);
	$('.modal').modal();
}
</script>
<?php
if ($detail) {
    echo "<script>\nsolution = new Array();\n";
    foreach ($detail as $sid => $solution) {
        echo 'solution[\''.$sid.'\'] = '.$solution.";\n";
    }
    echo "</script>\n";
}
require_once 'footer.php';
