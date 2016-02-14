<?php
    global $user, $mo_solution;
    if (isset($mo_request[1])    && is_numeric($mo_request[1])) {
        $sid = $mo_request[1];
        if (!mo_load_solution($sid) || mo_get_solution('uid') != $user->getUID()) {
            require_once $mo_theme_floder.'404.php';
        }
        echo '<h2>提交：#'.mo_get_solution_id().'</h2>';
        echo '用户：#'.mo_get_solution_uid().'<br>';
        echo '问题：#<a href="/?r=problem/'.mo_get_solution_pid().'">'.mo_get_solution_pid().
                    '</a><br>';
        echo '语言：#'.mo_get_solution_language().'<br>';
        echo '评测机：#'.mo_get_solution_client().'<br><br>';
        if (mo_get_solution('state') <= 0) {
            echo '评测中，当前状态：'.mo_get_solution_state().'<br>';
        } else {
            echo '总耗时：'.mo_get_solution_used_time().'MS 最大使用内存：'.
                        mo_get_solution_used_memory().'KB<br>';
            echo '<table width="100%" border="1">
				  <tbody>
					<tr>
					  <td width="20%"><strong>测试数据</strong></td>
					  <td width="30%"><strong>耗时（MS）</strong></td>
					  <td width="30%"><strong>内存（KB）</strong></td>
					  <td width="20%"><strong>结果</strong></td>
					</tr>';
            $detail_time = explode(' ', mo_get_solution('detail_time'));
            $detail_memory = explode(' ', mo_get_solution('detail_memory'));
            $detail_result = explode(' ', mo_get_solution('detail_result'));
            $turn = count($detail_result);
            for ($i = 0; $i < $turn && $detail_result[$i]; ++$i) {
                echo '    <tr>
						  <td>#'.$i.'</td>
						  <td>'.$detail_time[$i].'</td>
						  <td>'.$detail_memory[$i].'</td>
						  <td>'.$detail_result[$i].'</td>
						</tr>';
            }
            echo '  </tbody>
					</table><br>';
        }
        echo '代码：';

        function de($con)
        {
            return base64_decode($con);
        }
        add_filter('solutionCode', 'de');

        echo '  <textarea name="code" id="code" cols="45" rows="5">'.mo_get_solution_code().'</textarea>';
    } else {
        $solution_list = mo_load_solutions(1, 100000000);
        if ($solution_list) {
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
            foreach ($solution_list as $sid) {
                $solution = &$mo_solution[$sid];
                echo '
				<tr>
				  <td><a href="/?r=problem/'.mo_get_solution_pid($sid).'">'.mo_get_solution_pid($sid).'</a></td>
				  <td><a href="/?r=user/'.mo_get_solution_uid($sid).'">'.mo_get_solution_uid($sid).'</a></td>
				  <td>'.mo_get_solution_post_time($sid).'</td>
				  <td>'.mo_get_solution_language($sid).'</td>
				  <td>'.mo_get_solution_code_length($sid).'字节</td>
				  <td>'.mo_get_solution_state($sid).'</td>
				  <td>'.(mo_get_solution_used_time($sid) != -1 ? mo_get_solution_used_time($sid).'MS' : '').'</td>
				  <td>'.(mo_get_solution_used_memory($sid) != -1 ? mo_get_solution_used_memory($sid).'KB' : '').'</td>';
                if (mo_get_solution_uid($sid) == $user->getUID()) {
                    echo '<td><a href="/?r=solution/'.$solution['id'].'">详情</a></td>';
                } else {
                    echo '<td></td>';
                }
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '暂无！';
        }
    }
