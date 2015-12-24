<?php
$active = 'problem';
require_once 'header.php';
$start = isset($_GET['loc']) ? (int)$_GET['loc'] : 0;
$piece = isset($_GET['piece']) ? (int)$_GET['piece'] : 20;
$sql = "SELECT `id`, `title`, `tag`, `ver`, `post_time`, `time_limit`, `memory_limit`, `state`, `ac`, `submit`,".
			" `solved`, `try`, `test_turn` FROM `mo_judge_problem` ORDER BY `id` DESC LIMIT $start,$piece";
$db->prepare($sql);
$result = $db->execute();
$problem_count = mo_get_problem_count();
$page = ceil($problem_count / $piece);
?>
<div class="container">
	<ul class="nav nav-tabs">
	<li class="active"><a href="problem.php">管理题目</a></li>
	<li><a href="edit_problem.php?action=add">添加题目</a></li>
	</ul>
	<?php if (!$result) echo '<div class="alert alert-warning">题库暂时为空！请先添加题目。</div>'; ?>
	<?php if (isset($_GET['result']) && $_GET['result'] == 0) echo '<div class="alert alert-warning">未知错误。</div>'; ?>
	<div class="col-md-3">
		<h3>快速编辑</h3>
		  <div class="input-group">
           <span class="input-group-addon">#</span>
           <input type="text" class="form-control" placeholder="题号">
              <span class="input-group-btn">
                <button class="btn btn-default" type="button">
                   EDIT
                </button>
             </span>
          </div>
		<h3>筛选器</h3>
	</div>
	<div class="col-md-9">
		<div class="row">
		  <table class="table table-striped table-hover">
		   <tbody>
			<?php
			if ($result)
			{
				foreach ($result as $prob)
				{
					$tr = (isset($_GET['pid']) && (string)$prob['id'] == $_GET['pid']) ? '<tr class="success">' : '<tr>';
					echo '
					'.$tr.'
					 <td>'.$prob['id'].'</td>
					 <td><a href="'.mo_get_problem_url($prob['id']).'">'.(($prob['state'] == 1) ? $prob['title'] : '<del>'. $prob['title']. '</del>').'</a></td></td>
					 <td>'.$prob['time_limit'].'/'.$prob['memory_limit'].'</td></td>
					 <td>'.$prob['test_turn'].' ('.$prob['ver'].')</td>
					 <td><div class="btn-group">
					 <a class="btn btn-primary btn-sm" href="edit_problem.php?action=edit&id='.$prob['id'].'">编辑</a>
					 <button type="button" class="btn btn-info btn-sm">详情</button> 
					 <button type="button" class="btn btn-danger btn-sm">删除</button>
					 <div class="btn-group">
					<button type="button" class="btn btn-default btn-sm dropdown-toggle" 
					  data-toggle="dropdown">
					  更多操作
					  <span class="caret"></span>
					</button>
					<ul class="dropdown-menu">'.
					  (($prob['state'] == 1) ? '<li><a href="#">锁定</a></li>' : '<li><a href="#">解锁</a></li>').
					  '</ul>
					</div>
					 </div></tr>';
				}
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
			  <li class="<?php echo $start >= $piece ? 'previous' : 'previous disabled';?>"><a href="problem.php?loc=<?php echo $start-$piece; ?></a>">&larr; 上一页</a></li>
			  <li class="<?php echo $problem_count - $start >= $piece ? 'next' : 'next disabled';?>"><a href="problem.php?loc=<?php echo $start+$piece; ?></a>">下一页 &rarr;</a></li>
			</ul>
		 </div>
	</div>
</div>
<?php
require_once 'footer.php';
