<?php
	global $user;
	if( isset( $mo_request[1] )	&& is_numeric( $mo_request[1] ) )
	{
		$sid = $mo_request[1];
		if ( !mo_load_solution( $sid ) || mo_get_solution( $sid, 'uid' ) != $user->getUID() )
		{
			require_once( $mo_theme_floder. '404.php' );
		}
		echo '<h2>提交：#'. mo_get_solution( $sid, 'id' ). '</h2>';
		echo '用户：#'. mo_get_solution( $sid, 'uid' ). '<br>';
		echo '问题：#<a href="/?r=problem/'. mo_get_solution( $sid, 'pid' ). '">'. mo_get_solution( $sid, 'pid' ).
					'</a><br>';
		echo '语言：#'. mo_get_solution( $sid, 'language' ). '<br>';
		echo '评测机：#'. mo_get_solution( $sid, 'client' ). '<br><br>';
		if ( mo_get_solution( $sid, 'state' ) <= 0 )
		{
			echo '评测中，当前状态：'. mo_get_solution( $sid, 'state' ). '<br>';
		}
		else
		{
			echo '总耗时：'. mo_get_solution( $sid, 'used_time' ). 'MS 最大使用内存：'.
						mo_get_solution( $sid, 'used_memory' ). 'KB<br>';
			echo '<table width="100%" border="1">
				  <tbody>
					<tr>
					  <td width="20%"><strong>测试数据</strong></td>
					  <td width="30%"><strong>耗时（MS）</strong></td>
					  <td width="30%"><strong>内存（KB）</strong></td>
					  <td width="20%"><strong>结果</strong></td>
					</tr>';
			$detail_time = explode( ' ', mo_get_solution( $sid, 'detail_time' ) );
			$detail_memory = explode( ' ', mo_get_solution( $sid, 'detail_memory' ) );
			$detail_result = explode( ' ', mo_get_solution( $sid, 'detail_result' ) );
			$turn = count( $detail_result );
			for ( $i = 0; $i < $turn && $detail_result[$i]; ++$i )
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
		echo '  <textarea name="code" id="code" cols="45" rows="5">'. mo_get_solution( $sid, 'code' ). '</textarea>';
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
