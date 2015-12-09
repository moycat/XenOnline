<?php
	global $user;
	if( isset( $mo_request[1] )	&& is_numeric( $mo_request[1] ) )
	{
		$solution = new Solution( $mo_request[1] );
		$solution->load();
		if ( !$solution->getSID() || $solution->getInfo( 'uid' ) != $user->getUID() )
		{
			require_once( $mo_theme_floder. '404.php' );
		}
		echo '<h2>提交：#'. $solution->getInfo( 'id' ). '</h2>';
		echo '用户：#'. $solution->getInfo( 'uid' ). '<br>';
		echo '问题：#<a href="/?r=problem/'. $solution->getInfo( 'pid' ). '">'. $solution->getInfo( 'pid' ). '</a><br>';
		echo '语言：#'. $solution->getInfo( 'language' ). '<br>';
		echo '评测机：#'. $solution->getInfo( 'client' ). '<br><br>';
		if ( $solution->getInfo( 'state' ) <= 0 )
		{
			echo '评测中，当前状态：'. $solution->getInfo( 'state' ). '<br>';
		}
		else
		{
			echo '总耗时：'. $solution->getInfo( 'used_time' ). 'MS 最大使用内存：'. $solution->getInfo( 'used_memory' ). 'KB<br>';
			echo '<table width="100%" border="1">
				  <tbody>
					<tr>
					  <td width="20%"><strong>测试数据</strong></td>
					  <td width="30%"><strong>耗时（MS）</strong></td>
					  <td width="30%"><strong>内存（KB）</strong></td>
					  <td width="20%"><strong>结果</strong></td>
					</tr>';
			$detail_time = explode( ' ', $solution->getInfo( 'detail_time' ) );
			$detail_memory = explode( ' ', $solution->getInfo( 'detail_memory' ) );
			$detail_result = explode( ' ', $solution->getInfo( 'detail_result' ) );
			$turn = count( $detail_result );
			for ( $i = 0; $i < $turn; ++$i )
			{
				echo '    <tr>
						  <td>#'. $i. '</td>
						  <td>'. $detail_time[$i]. '</td>
						  <td>'. $detail_memory[$i]. '</td>
						  <td>'. $detail_result[$i]. '</td>
						</tr>';
			}
			echo '  </tbody>
					</table><br>';
		}
		echo '代码：';
		echo '  <textarea name="code" id="code" cols="45" rows="5">'. $solution->getInfo( 'code' ). '</textarea>';
	}
	else
	{
		$solution_list = mo_list_solutions( 1, 100000000 );
		if ( $solution_list )
		{
			echo '<table width="100%" border="1"><tbody>
				<tr>
				  <td width="8%"><strong>问题ID</strong></td>
				  <td width="8%"><strong>用户ID</strong></td>
				  <td width="16%"><strong>提交时间</strong></td>
				  <td width="11%"><strong>语言ID</strong></td>
				  <td width="9%"><strong>代码长度</strong></td>
				  <td width="11%"><strong>状态ID</strong></td>
				  <td width="14%"><strong>运行时间</strong></td>
				  <td width="17%"><strong>使用内存</strong></td>
				  <td width="6%"><strong></strong></td>
				</tr>';
			foreach ( $solution_list as $solution )
			{
				echo '
				<tr>
				  <td><a href="/?r=problem/'. $solution['pid']. '">'. $solution['pid']. '</a></td>
				  <td><a href="/?r=user/'. $solution['uid']. '">'. $solution['uid']. '</a></td>
				  <td>'. $solution['post_time']. '</td>
				  <td>'. $solution['language']. '</td>
				  <td>'. $solution['code_length']. '字节</td>
				  <td>'. $solution['state']. '</td>
				  <td>'. ( $solution['used_time'] != -1 ? $solution['used_time']. 'MS' : '' ). '</td>
				  <td>'. ( $solution['used_memory'] != -1 ? $solution['used_memory']. 'KB' : '' ). '</td>';
				if ( $solution['uid'] == $user->getUID() )
				{
					echo '<td><a href="/?r=solution/'. $solution['id']. '">详情</a></td>';
				}
				else
				{
					echo '<td></td>';
				}
				echo '</tr>';
			}
			echo '</tbody></table>';
		}
		else
		{
			echo '暂无！';
		}
	}