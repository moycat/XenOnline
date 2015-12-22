<?php
$active = 'overview';
require_once 'header.php';
$status = array();
$sql = 'SELECT COUNT(*) AS total FROM `mo_judge_client` WHERE TO_SECONDS(NOW()) - TO_SECONDS(last_ping) <= 200';
$db->prepare( $sql );
$result = $db->execute();
$ava_client_count = (int)$result[0]['total'];
$sql = 'SELECT COUNT(*) AS total FROM `mo_judge_client`';
$db->prepare( $sql );
$result = $db->execute();
$client_count = (int)$result[0]['total'];
$sql = 'SELECT COUNT(*) AS total FROM `mo_user` WHERE user_group = -1';
$db->prepare( $sql );
$result = $db->execute();
$user_to_verify = (int)$result[0]['total'];
?>
<div class="section">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h1 class="text-center">
					总览
				</h1>
				<p class="text-center hitokoto">
					<script>
						$().ready(function() {
							hitokoto();
						});
					</script>
				</p>
			</div>
		</div>
		<?php
		if (!$ava_client_count)
		{
			echo '<div class="alert alert-danger">当前没有评测端在线！请检查Socket服务端是否开启，以及评测端是否正常。</div>';
		}
		?>
		<div class="row">
			<div class="col-md-6">
				<img src="inc/icon/problem.png" align="left" class="img-rounded img-responsive signimg">
				<h3 class="text-left">
					题目
				</h3>
				<p class="text-left">
					题库当前线上题目数：<?php echo mo_get_problem_count(); ?><br>
				</p>
				<a href="edit_problem.php?action=add" class="btn btn-primary btn-default" role="button">添加题目</a>
				<a href="problem.php" class="btn btn-default btn-default" role="button">浏览题目</a>
			</div>
			<div class="col-md-6">
				<img src="inc/icon/data.png" align="left" class="img-rounded img-responsive signimg">
				<h3 class="text-left">
					数据
				</h3>
				<p class="text-left">
					用户提交数：<?php echo mo_get_solution_count(); ?><br>
					用户讨论主题数：<?php echo mo_get_discussion_count(); ?><br>
				<a href="solution.php" class="btn btn-default btn-default" role="button">管理提交</a>
				<a href="discussion.php" class="btn btn-default btn-default" role="button">管理讨论</a>
				</p>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<img src="inc/icon/<?php echo $ava_client_count ? 'client.png' : 'client-warn.png'; ?>" align="left" class="img-rounded img-responsive signimg">
				<h3 class="text-left">
					评测机
				</h3>
				<p class="text-left">
					当前评测机状态：<br>
					<span class="glyphicon glyphicon-arrow-up"></span><?php echo $ava_client_count; ?>　
					<span class="glyphicon glyphicon-arrow-down"></span><?php echo $client_count-$ava_client_count; ?>
				</p>
				<a href="client.php" class="btn btn-primary btn-default" role="button">查看详情</a>
			</div>
			<div class="col-md-6 text-center">
				<img src="inc/icon/<?php echo $user_to_verify ? 'user-warn.png' : 'user.png'; ?>" align="left" class="img-rounded img-responsive signimg">
				<h3 class="text-left">
					用户
				</h3>
				<p class="text-left">
					当前已注册用户：<?php echo mo_get_user_count(); ?><br>
					待审核用户：<?php echo $user_to_verify; ?>
				</p>
				<a href="user.php" class="btn btn-primary btn-default" role="button">管理用户</a>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="hitokoto.php"></script>
<?php
require_once 'footer.php';
