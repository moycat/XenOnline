<?php
$active = 'client';
$head = '<link rel="stylesheet" href="inc/jquery.webui-popover.css">
<script src="inc/jquery.webui-popover.js"></script>';
require_once 'header.php';
$sql = 'SELECT * FROM `mo_judge_client`';
$db->prepare($sql);
$result = $db->execute();
?>
<div class="container">
	<div class="col-md-12" id = "client">
		<table class="table table-striped table-hover">
		<tbody>
		<?php
		$now = time();
		$js_tmp = '';
		foreach ($result as $client)
		{
			$client['hash'] = '<code>'. $client['hash']. '</code>';
			$client['last_ping'] = mo_date(strtotime($client['last_ping']));
			$js_tmp .= 'client[\''. $client['id']. '\']='. json_encode($client). ";\n";
			$tr = ($now - strtotime($client['last_ping']) < 200) ? '<tr class="success"' : '<tr class="danger"';
			$tr .= ' id="client-'. $client['id']. '">';
			echo  '
			'.$tr.'
			 <td>'.$client['id'].'</td>
			 <td>'.$client['name'].'</td>
			 <td class="hidden-xs">'.$client['intro'].'</td>
			 <td class="hidden-xs">'.$client['load_1'].', '.$client['load_5'].', '.$client['load_15'].'</td>
			 <td class="hidden-xs">'.$client['memory'].'%</td>
			 <td>'.$client['last_ping'].'</td>
			 <td><div class="btn-group">
			 <button type="button" class="btn btn-primary btn-sm">编辑</button>
			 <button type="button" class="btn btn-info btn-sm hidden-xs" onclick="show_hash('. $client['id']. ')">查看hash</button>
			 <button type="button" class="btn btn-info btn-sm visible-xs" onclick="client_detail('. $client['id']. ')">查看详情</button>
			 <button type="button" class="btn btn-danger btn-sm">删除</button>
			 </div></tr>';
		}
		?>
		</tbody>
		<thead>
		<tr>
		 <th>#</th>
		 <th>名称</th>
		 <th class="hidden-xs">简介</th>
		 <th class="hidden-xs">平均负载</th>
		 <th class="hidden-xs">已用内存</th>
		 <th>上一次心跳</th>
		 <th>操作</th>
		</tr>
		</thead>
		</table>
		<?php if (!$result) echo '<div class="alert alert-warning">暂无评测端！请先添加一个。</div>'; ?>
		<button type="button" class="btn btn-primary" onclick="show_detail('#client', '添加评测端', add_win, 320)">添加评测端</button>
	</div>
</div>
<script>
add_win = '<form id="newform" role="form" method="post" action="client.php" enctype="multipart/form-data">\
	<div class="form-group input-group-lg">\
	 <label class="control-label" for="name"><h4>名称</h4></label>\
	 <input id="name" class="form-control" type="text" name="name" placeholder="评测端 #X">\
	</div>\
	<div class="form-group input-group-lg">\
	 <label class="control-label" for="intro"><h4>简介</h4></label>\
	 <input id="intro" class="form-control" type="text" name="intro" placeholder="这是一台萌萌哒的评测端">\
	</div>\
	<input type="hidden" name="action" value="add">\
	<button type="submit" class="btn btn-default btn-lg">发布</button>\
</form>';
client_hash = new Array();
<?php
	echo $js_tmp;
?>
function show_hash(cid) {
	show_detail('#client-'+cid, '评测端#'+cid+' 通信密钥', client[cid]['hash'], 310);
}
</script>
<?php
require_once 'footer.php';
