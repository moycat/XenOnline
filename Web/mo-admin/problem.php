<?php
$active = 'problem';
$head = '<link rel="stylesheet" href="//cdn.bootcss.com/webui-popover/1.2.5/jquery.webui-popover.min.css">
<script src="//cdn.bootcss.com/webui-popover/1.2.5/jquery.webui-popover.min.js"></script>';
require_once 'header.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$start = isset($_GET['loc']) ? (int)$_GET['loc'] : 0;
$piece = isset($_GET['piece']) ? (int)$_GET['piece'] : 20;
switch ($action)
{
	case 'lock':
	case 'unlock':
		$pid = $_GET['pid'];
		$sql = 'UPDATE `mo_judge_problem` SET `state` = \''.($_GET['action'] == 'lock' ? '0' : '1').'\' WHERE `id` = ?';
		$db->prepare($sql);
		$db->bind('i', $pid);
		$db->execute();
		$msg = '成功'.($_GET['action'] == 'lock' ? '锁定' : '解锁').'题目#'. $pid. '。';
	case 'del':
		$pid = $_GET['pid'];
		$sql = 'DELETE FROM `mo_judge_problem` WHERE `id` = ?';
		$db->prepare($sql);
		$db->bind('i', $pid);
		$db->execute();
		$msg = '成功删除题目#'. $pid. '。';
	default:
	case 'list':
		$sql = "SELECT `id`, `title`, `tag`, `ver`, `post_time`, `time_limit`, `memory_limit`, `state`, `ac`, `submit`,".
		            " `solved`, `try`, `test_turn` FROM `mo_judge_problem` ORDER BY `id` DESC LIMIT $start,$piece";
		$db->prepare($sql);
		$result = $db->execute();
		$problem_count = mo_get_problem_count();
		break;
	case 'search':
		$sql = "SELECT `id`, `title`, `tag`, `ver`, `post_time`, `time_limit`, `memory_limit`, `state`, `ac`, `submit`,".
		            " `solved`, `try`, `test_turn` FROM `mo_judge_problem` WHERE 1=1";
		if (isset($_GET['pid']) && $_GET['pid'])
		{
			$sql .= ' AND `id` = '. $db->clean($_GET['pid']);
		}
		if (isset($_GET['title']) && isset($_GET['title']))
		{
			$sql .= ' AND `title` LIKE \'%'. $db->clean($_GET['title']). '%\'';
		}
		if (isset($_GET['tag']) && isset($_GET['tag']))
		{
			$sql .= ' AND MATCH(tag) AGAINST (\''.$db->clean($_GET['tag']).'\' IN BOOLEAN MODE )';
		}
		$sql .= " ORDER BY `id` DESC LIMIT $start,$piece";
		$db->prepare($sql);
		$result = $db->execute();
		$problem_count = count($result);
}
$page = ceil($problem_count / $piece);
?>
<div class="container">
    <ul class="nav nav-tabs">
    <li class="active"><a href="problem.php">管理题目</a></li>
    <li><a href="edit_problem.php?action=add">添加题目</a></li>
    </ul>
    <?php if (!$result) echo '<div class="alert alert-warning">题库暂时为空！请先添加题目。</div>'; ?>
    <?php if (isset($_GET['result']))
    {
		if ($_GET['result'] == 0)
		{
			echo '<div class="alert alert-warning">未知错误。</div>';
		}
		elseif ($_GET['result'] == 1 && isset($_GET['pid']))
		{
			echo '<div class="alert alert-success">添加成功！新题目编号为#'.$_GET['pid'].'。</div>';
		}
		elseif ($_GET['result'] == 2 && isset($_GET['pid']))
		{
			echo '<div class="alert alert-success">编辑成功！题目编号为#'.$_GET['pid'].'。</div>';
		}
	} ?>
    <?php if (isset($msg)) echo '<div class="alert alert-success">'.$msg.'</div>'; ?>
    <div class="col-md-3">
        <form method="get" action="edit_problem.php">
          <h4>快速编辑</h4>
            <div class="input-group">
             <span class="input-group-addon">#</span>
             <input type="text" name="pid" class="form-control" placeholder="题号">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="submit" >
                     Edit
                </button>
               </span>
             <input type="hidden" name="action" value="edit">
          </div>
        </form>
        <h4>筛选器</h4>
        <form method="get" action="problem.php">
		  	<input type="hidden" name="action" value="search">
        <div class="input-group">
             <span class="input-group-addon">#</span>
             <input type="text" name="pid" class="form-control" placeholder="题号" value="<?php if(isset($_GET['pid'])) echo $_GET['pid'];?>">
          </div>
					<div class="input-group">
						<span class="input-group-addon glyphicon glyphicon-pencil"></span>
	             <input type="text" name="title" class="form-control" placeholder="标题" value="<?php if(isset($_GET['title'])) echo $_GET['title'];?>">
	        </div>
					<div class="input-group">
						<span class="input-group-addon glyphicon glyphicon-tags"></span>
	             <input type="text" name="tag" class="form-control" placeholder="标签" value="<?php if(isset($_GET['tag'])) echo $_GET['tag'];?>">
							 <span class="input-group-btn">
										<button class="btn btn-default glyphicon glyphicon-search" type="submit" >
									</button>
								 </span>
	        </div>
        </form>
    </div>
    <div class="col-md-9">
        <div class="row">
          <table class="table table-striped table-hover">
           <tbody>
            <?php
            $detail = array();
			foreach ($result as $prob)
			{
				$detail[$prob['id']] = json_encode($prob);
				$tr = (isset($_GET['pid']) && (string)$prob['id'] == $_GET['pid']) ? '<tr class="success">' : '<tr>';
				echo '
				'.$tr.'
				 <td>'.$prob['id'].'</td>
				 <td><a href="/?r=problem/'.$prob['id'].'">'.(($prob['state'] == 1) ? $prob['title'] : '<del>'. $prob['title']. '</del>').'</a></td>
				 <td>'.$prob['time_limit'].'/'.$prob['memory_limit'].'</td>
				 <td>'.$prob['test_turn'].' ('.$prob['ver'].')</td>
				 <td>
				 <a class="btn btn-primary btn-sm" onClick="location.href=\'edit_problem.php?action=edit&pid='.$prob['id'].'\'">编辑</a>
				 <button id="'.$prob['id'].'detail" type="button" class="btn btn-info btn-sm" onclick="prob_detail('.$prob['id'].')">详情</button>
				 <button type="button" class="btn btn-danger btn-sm" onclick="del_problem('. $prob['id']. ')">删除</button>
				 <a type="button" class="btn btn-warning btn-sm" href="problem.php?action='.(($prob['state'] == 1) ? 'lock' : 'unlock').'&pid='.$prob['id'].'">'.(($prob['state'] == 1) ? '锁定' : '解锁').'</a>
				 </tr>';
			}
            ?>
           </tbody>
           <thead>
            <tr>
             <th>#</th>
             <th>标题</th>
             <th>限制(ms/MB)</th>
             <th>测试点(ver)</th>
             <th>操作</th>
            </tr>
           </thead>
          </table>
            <ul class="pager">
              <li class="<?php echo $start >= $piece ? 'previous' : 'previous disabled';?>"><a href="<?php echo $start >= $piece ? 'problem.php?loc='.($start-$piece) : '#';?>">&larr; 上一页</a></li>
              共<?php echo ceil($problem_count / $piece); ?>页，正在浏览第<?php echo ceil($start / $piece) + 1; ?>页
              <li class="<?php echo $problem_count - $start >= $piece ? 'next' : 'next disabled';?>"><a href="<?php echo $problem_count - $start >= $piece ? 'problem.php?loc='.($start+$piece) : '#';?>">下一页 &rarr;</a></li>
            </ul>
         </div>
    </div>
</div>
<div class="modal fade" id="del_problem" tabindex="-1" role="dialog"
   aria-labelledby="myModalLabel" aria-hidden="true">
   <form id="delform" role="form" method="get" action="problem.php" enctype="multipart/form-data">
	   <div class="modal-dialog">
		  <div class="modal-content">
			 <div class="modal-header">
				<button type="button" class="close"
				   data-dismiss="modal" aria-hidden="true">
					  &times;
				</button>
				<h4 class="modal-title" id="del_problem_title">

				</h4>
			 </div>
			 <div class="modal-body">
				删除后本题目的数据将会消失，但相关的提交、讨论、文件、用户记录不会被删除。
			 </div>
			 <div class="modal-footer">
				 <input type="hidden" name="action" value="del">
				 <input type="hidden" id="del_pid" name="pid" value="0">
				<button type="button" class="btn btn-default"  data-dismiss="modal">
					取消
				</button>
				<button type="submit" class="btn btn-danger">删除</button>
			 </div>
		  </div>
      </form>
</div>
<script>
function del_problem(pid) {
	$('#del_problem_title').html('<span class="glyphicon glyphicon-warning-sign"></span> 删除题目#'+pid);
	$('#del_confirm').remove();
    $('#del_pid').val(pid);
	$('.modal').modal();
}
</script>
<?php
if ($detail)
{
	echo "<script>\nprob = new Array();\n";
	foreach ($detail as $pid => $prob)
	{
		echo 'prob[\''.$pid.'\'] = '.$prob.";\n";
	}
	echo "</script>\n";
}
require_once 'footer.php';
