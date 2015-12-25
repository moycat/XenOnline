<?php
$active = 'client';
require_once 'header.php';
$sql = 'SELECT * FROM `mo_judge_client`';
$db->prepare($sql);
$result = $db->execute();
?>
<div class="container">
	<div class="col-md-12">
		<table class="table table-striped table-hover">
		<tbody>
		<?php
		$now = time();
		foreach ($result as $client)
		{
			$tr = ($now - strtotime($client['last_ping']) < 200) ? '<tr class="success">' : '<tr class="danger">';
			echo  '
			'.$tr.'
			 <td>'.$client['id'].'</td>
			 <td>'.$client['name'].'</td>
			 <td>'.$client['intro'].'</td>
			 <td>'.$client['load_1'].', '.$client['load_5'].', '.$client['load_15'].'</td>
			 <td>'.$client['memory'].'%</td>
			 <td>'.mo_date(strtotime($client['last_ping'])).'</td>
			 <td><div class="btn-group">
			 <button type="button" class="btn btn-primary btn-sm">编辑</button>
			 <button type="button" class="btn btn-info btn-sm">查看hash</button>
			 <button type="button" class="btn btn-danger btn-sm">删除</button>
			 </div></tr>';
		}
		?>
		</tbody>
		<thead>
		<tr>
		 <th>#</th>
		 <th>名称</th>
		 <th>简介</th>
		 <th>平均负载</th>
		 <th>已用内存</th>
		 <th>上一次心跳包</th>
		 <th>操作</th>
		</tr>
		</thead>
		</table>
		<?php if (!$result) echo '<div class="alert alert-warning">暂无评测端！请先添加一个。</div>'; ?>
		<button type="button" class="btn btn-primary">添加评测端</button>
	</div>
</div>
<?php
require_once 'footer.php';
