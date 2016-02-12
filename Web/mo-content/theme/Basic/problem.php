<?php
	global $user;
	if( isset( $mo_request[1] )	&& is_numeric( $mo_request[1] ) )
	{
		$pid = $mo_request[1];
		if ( !mo_load_problem( $pid ) )
		{
			require_once( $mo_theme_floder. '404.php' );
		}
		if( isset( $_POST['lang'] ) && isset( $_POST['code'] ) && $user->getUID() )
		{
			// 提交solution
			if ( !b_check_code() )
			{
				echo '提交错误！请检查格式以及是否已经登录！';
			}
			else
			{
				$new_sid = mo_add_new_solution( $pid, $_POST['lang'], $_POST['code'] );
				echo '提交成功！<a href="/?r=solution/'. $new_sid. '">点此</a>查看详情！';
			}
		}
		echo '<h2>'. mo_get_problem( $pid, 'title' ). '</h2>';
		echo '<em>标签：'. mo_get_problem( $pid, 'tag' ). '<br>';
		echo '时间限制：'. mo_get_problem( $pid, 'time_limit' ). 'MS 内存限制：'. mo_get_problem( $pid, 'memory_limit' ). 'MB</em>';
		echo '<h3>问题描述</h3>';
		echo mo_get_problem( $pid, 'description' );
		echo '<br>提交人数：'. mo_get_problem( $pid, 'try' ). ' AC人数：'. mo_get_problem( $pid, 'solved' ). '<br>';
		echo '<h3>提交代码</h3>';
		echo '<form name="form1" method="post" action="">
				语言：
				<p>
				  <label>
					<input name="lang" type="radio" required id="lang-1" value="1" checked>
					C/C++</label>
				  </p>
				<p>代码：</p>
				<p>
				  <textarea name="code" cols="45" rows="5" required id="code"></textarea>
				</p>
				<p>
				  <input type="submit" name="submit" id="submit" value="提交">
				  <br>
				</p>
				</form>';
	}
	else
	{
		$problem_list = mo_list_problems( 1, 100000000 );
		if ( $problem_list )
		{
			echo '<table width="100%" border="1"><tbody>
			<tr>
			  <td width="14%"><strong>编号</strong></td>
			  <td width="35%"><strong>标题</strong></td>
			  <td width="25%"><strong>标签</strong></td>
			  <td width="13%"><strong>尝试人数</strong></td>
			  <td width="13%"><strong>AC人数</strong></td>
			</tr>';
			foreach ( $problem_list as $problem )
			{
				echo '
				<tr>
				  <td width=\n14%\n><strong>'. $problem['id']. '</strong></td>
				  <td width=\n35%\n><strong><a href="/?r=problem/'. $problem['id']. '">'. $problem['title']. '</a></strong></td>
				  <td width=\n25%\n><strong>'. $problem['tag']. '</strong></td>
				  <td width=\n13%\n><strong>'. $problem['try']. '</strong></td>
				  <td width=\n13%\n><strong>'. $problem['solved']. '</strong></td>
				</tr>';
			}
			echo '</tbody></table>';
		}
		else
		{
			echo '暂无！';
		}
	}
