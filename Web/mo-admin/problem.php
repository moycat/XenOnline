<?php
$active = 'problem';
$head = '<link rel="stylesheet" href="inc/jquery.webui-popover.css">
<script src="inc/jquery.webui-popover.js"></script>';
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
        <div class="input-group">
             <span class="input-group-addon">#</span>
             <input type="text" name="pid" class="form-control" placeholder="题号">
          <span class="input-group-btn">
                  <button class="btn btn-default glyphicon glyphicon-search" type="submit" >
                </button>
               </span>
             <input type="hidden" name="action" value="search">
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
				$tr = (isset($_GET['pid']) && (string)$prob['id'] == $_GET['pid']) ? '<tr id="'.$prob['id'].'" class="success">' : '<tr id="'.$prob['id'].'">';
				echo '
				'.$tr.'
				 <td>'.$prob['id'].'</td>
				 <td><a href="'.mo_get_problem_url($prob['id']).'">'.(($prob['state'] == 1) ? $prob['title'] : '<del>'. $prob['title']. '</del>').'</a></td>
				 <td>'.$prob['time_limit'].'/'.$prob['memory_limit'].'</td>
				 <td>'.$prob['test_turn'].' ('.$prob['ver'].')</td>
				 <td><div class="btn-group">
				 <a class="btn btn-primary btn-sm" href="edit_problem.php?action=edit&pid='.$prob['id'].'">编辑</a>
				 <button type="button" class="btn btn-info btn-sm" onclick="prob_detail('.$prob['id'].')">详情</button> 
				 <button type="button" class="btn btn-danger btn-sm">删除</button>
				 <button type="button" class="btn btn-warning btn-sm">'.(($prob['state'] == 1) ? '锁定' : '解锁').'</button>
				 </div></tr>';
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
