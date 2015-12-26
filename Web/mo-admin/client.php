<?php
$active = 'client';
$head = '<link rel="stylesheet" href="inc/jquery.webui-popover.css">
<script src="inc/jquery.webui-popover.js"></script>';
require_once 'header.php';
if (isset($_POST['action']))
{
	if ($_POST['action'] == 'add')
	{
		$new_name = $_POST['name'];
		$new_intro = $_POST['intro'];
		$new_hash = md5(password_hash(rand(10000, 99999), PASSWORD_DEFAULT));
		$sql = 'INSERT INTO `moyoj`.`mo_judge_client` (`name`, `hash`, `intro`, `load_1`, `load_5`, `load_15`, `memory`, `last_ping`) VALUES (?, ?, ?, \'0\', \'0\', \'0\', \'0\', NULL)';
		$db->prepare($sql);
		$db->bind('sss', $new_name, $new_hash, $new_intro);
		$db->execute();
		$new_cid = $db->getInsID();
		$msg = '<div class="alert alert-success">添加成功！新的评测端ID为'. $new_cid. '。</div>';
	}
	elseif($_POST['action'] == 'edit')
	{
		$edit_cid = $_POST['cid'];
		$edit_name = $_POST['name'];
		$edit_intro = $_POST['intro'];
		$sql = 'UPDATE `mo_judge_client` SET `name` = ?, `intro` = ? WHERE `id` = ?';
		$db->prepare($sql);
		$db->bind('ssi', $edit_name, $edit_intro, $edit_cid);
		$db->execute();
		$msg = '<div class="alert alert-success">ID为'. $edit_cid. '的评测端修改成功！</div>';
	}
	elseif($_POST['action'] == 'del')
	{
		$sql = 'DELETE FROM `mo_judge_client` WHERE `id` = ?';
		$db->prepare($sql);
		$db->bind('i', $_POST['cid']);
		$db->execute();
		$order = array('action' => 'kill', 'cid' => $_POST['cid']);
		mo_com_socket($order);
	}
}
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
			$tr = ($now - strtotime($client['last_ping']) < 200) ? '<tr class="success"' : '<tr class="danger"';
			$tr .= ' id="client-'. $client['id']. '">';
			$client['hash'] = '<code>'. $client['hash']. '</code>';
			$client['last_ping'] = mo_date(strtotime($client['last_ping']));
			$js_tmp .= 'client[\''. $client['id']. '\']='. json_encode($client). ";\n";
			echo  '
			'.$tr.'
			 <td>'.$client['id'].'</td>
			 <td id="name">'.$client['name'].'</td>
			 <td id="intro" class="hidden-xs">'.$client['intro'].'</td>
			 <td class="hidden-xs"><code>'.$client['load_1'].'</code>&nbsp;<code>'.$client['load_5'].'</code>&nbsp;<code>'.$client['load_15'].'</code></td>
			 <td class="hidden-xs">'.$client['memory'].'%</td>
			 <td>'.$client['last_ping'].'</td>
			 <td><div class="btn-group">
			 <button type="button" class="btn btn-primary btn-sm" onclick="edit_client('. $client['id']. ')">编辑</button>
			 <button type="button" class="btn btn-info btn-sm hidden-xs" onclick="show_hash('. $client['id']. ')">查看hash</button>
			 <button type="button" class="btn btn-info btn-sm visible-xs" onclick="client_detail('. $client['id']. ')">查看详情</button>
			 <button type="button" class="btn btn-danger btn-sm" onclick="del_client('. $client['id']. ')">删除</button>
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
		<?php if (!$result) echo '<div class="alert alert-warning">暂无评测端！请先添加一个。</div>'; if (isset($msg)) echo $msg; ?>
		<button type="button" class="btn btn-primary" onclick="show_detail('#client', '添加评测端', add_win, 320)">添加评测端</button>
	</div>
</div>
<div class="modal fade" id="del_client" tabindex="-1" role="dialog" 
   aria-labelledby="myModalLabel" aria-hidden="true">
   <form id="delform" role="form" method="post" action="client.php" enctype="multipart/form-data">
	   <div class="modal-dialog">
		  <div class="modal-content">
			 <div class="modal-header">
				<button type="button" class="close" 
				   data-dismiss="modal" aria-hidden="true">
					  &times;
				</button>
				<h4 class="modal-title" id="del_client_title">
				   
				</h4>
			 </div>
			 <div class="modal-body">
				删除后此评测端将会被立刻踢下线，无法再参与评测。
			 </div>
			 <div class="modal-footer">
				 <input type="hidden" name="action" value="del">
				 <input type="hidden" id="del_cid" name="cid" value="0">
				<button type="button" class="btn btn-default"  data-dismiss="modal">
					取消
				</button>
				<button type="submit" class="btn btn-danger">删除</button>
			 </div>
		  </div>
      </form>
</div>
<script>
function edit_client(cid)
{
	name = $("#client-"+cid+" #name").text();
	intro = $("#client-"+cid+" #intro").text();
	edit_win = '<form id="newform" role="form" method="post" action="client.php" enctype="multipart/form-data">\
	<div class="form-group input-group-lg">\
	 <label class="control-label" for="name"><h4>名称</h4></label>\
	 <input id="name" class="form-control" type="text" name="name" placeholder="评测端 #X" value="'+name+'">\
	</div>\
	<div class="form-group input-group-lg">\
	 <label class="control-label" for="intro"><h4>简介</h4></label>\
	 <input id="intro" class="form-control" type="text" name="intro" placeholder="这是一台萌萌哒的评测端" value="'+intro+'">\
	</div>\
	<input type="hidden" name="action" value="edit">\
	<input type="hidden" name="cid" value="'+cid+'">\
	<button type="submit" class="btn btn-default btn-lg">发布</button>\
	</form>';
	show_detail('#client-'+cid, '编辑评测端#', edit_win, 320);
}
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
	show_detail('#client-'+cid, '评测端#'+cid+' 通信密钥', client[cid]['hash'], 320);
}
function del_client(cid) {
	$('#del_client_title').html('<span class="glyphicon glyphicon-warning-sign"></span> 删除评测端#'+cid);
	$('#del_confirm').remove();
    $('#del_cid').val(cid);
	$('.modal').modal();
}
</script>
<?php
require_once 'footer.php';
