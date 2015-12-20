<?php
$active = 'problem';
$head = '<link rel="stylesheet" href="/mo-includes/editor.md/css/editormd.css" /><script src="inc/bootstrap.file-input.js"></script>';
require_once 'header.php';
if (!isset($_GET['action']))
{
	$error = True;
}
else
{
	switch ($_GET['action'])
	{
		case 'add':
		case 'edit':
		$error = False;
		break;
		default:
		$error = True;
	}
}
if (!$error)
{
?>
<script>
	$().ready(function() {
		filePrint();
	});
</script>
<div class="container">
<ul class="nav nav-tabs">
<li><a href="problem.php">管理题目</a></li>
<li<?php if ($_GET['action'] == 'add') echo ' class="active"'; ?>><a href="edit_problem.php?action=add">添加题目</a></li>
</ul>
<form role="form" method="post" action="add_problem.php">
	<div class="form-group input-group-lg">
	 <label class="control-label" for="title"><h2>标题</h2></label>
	 <input id="title" class="form-control" type="text" name="title" placeholder="标题">
	</div>


<div id="test-editormd">
	<textarea style="display:none;">
	</textarea>
</div>
<script src="/mo-includes/editor.md/editormd.min.js"></script>
<script type="text/javascript">
	var testEditor;
	$(function() {
		$.get('inc/instruction.md', function(md){
			testEditor = editormd("test-editormd", {
				width: "100%",
				height: 740,
				path : '/mo-includes/editor.md/lib/',
				markdown : md,
				codeFold : true,
				saveHTMLToTextarea : true,
				searchReplace : true,
				htmlDecode : "style,script,iframe|on*",
				emoji : true,
				taskList : true,
				tocm            : true,
				tex : true,
				flowChart : true,
				sequenceDiagram : true,
				imageUpload : true,
				imageFormats : ["jpg", "jpeg", "gif", "png", "bmp", "webp"],
				imageUploadURL : "./php/upload.php",
				onload : function() {
					console.log('onload', this);
				}
			});
		});
	});
</script>




	<div class="form-group input-group-sm">
	 <h3>测试数据</h3>
     <div class="row">
		<div class="col-md-3">
		 <p><input type="file" id="input1" title="输入数据 #1" name="input1" class="btn-primary"></p>
		 </div>
		 <div class="col-md-3">
		 <p><input type="file" id="stdout1" title="输出数据 #1" name="stdout1" class="btn-primary"></p>
		 </div>
	 </div>
	</div>
	<div class="form-group input-group-sm">
	 <label class="control-label" for="tag"><h3>标签</h3></label>
	 <input id="tag" class="form-control" type="text" name="tag" placeholder="多个标签使用空格分开">
	</div>
	<div class="form-group input-group-sm">
	 <label class="control-label" for="extra"><h3>额外信息</h3></label>
	 <input id="extra" class="form-control" type="text" name="extra" placeholder="TODO">
	</div>
	<?php
	if ($_GET['action'] == 'add')
	{
		echo '<input type="hidden" name="action" value="add">';
	}
	elseif ($_GET['action'] == 'edit')
	{
		echo '<input type="hidden" name="action" value="edit">';
	//	echo '<input type="hidden" name="edit_id" value="'. $edit_id. '">';
	}
	?>
	<button type="submit" class="btn btn-default btn-lg">发布</button>
</form>
</div>
<?php
}
else
{
?>
<div class="container">
	<div class="alert alert-danger">未定义的操作。</div>
</div>
<?php
}
require_once 'footer.php';
