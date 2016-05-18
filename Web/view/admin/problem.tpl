{extends file='admin/subpage.tpl'}
{block name='menu'}
    <div class="row">
        <div class="col-sm-7">
            <ul class="nav nav-pills">
                <li role="presentation" class="active">
                    <a href="#">所有题目
                        <span class="badge">{$count['problem']}</span>
                    </a>
                </li>
                <li role="presentation"><a href="/admin/problem/add">添加题目</a></li>
            </ul>
        </div>
        <div class="col-sm-5">
            <form class="form-inline pull-right" method="post" action="/admin/problem/search">
                <select name="type" class="form-control">
                    <option value="id">#</option>
                    <option value="title">标题</option>
                    <option value="tag">标签</option>
                </select>
                <div class="input-group">
                    <input name="filter" type="text" class="form-control" placeholder="Search..." aria-describedby="Search">
                      <span class="input-group-btn">
                        <button type="submit" class="btn btn-danger">
                            <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                        </button>
                      </span>
                </div>
            </form>
        </div>
    </div>
{/block}
{block name='content'}
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>#</th>
                <th>标题</th>
                <th>AC/提交(通过/尝试)</th>
                <th>评测限制</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            {foreach $problem as $prob}
                {if $prob['status'] == 1}
                    <tr>
                {else}
                    <tr class="danger">
                {/if}
                    <th scope="row">
                        {$prob['id']}
                    </th>
                    <td>
                        <a href="/problem/{$prob['id']}" target="_blank">{$prob['title']}</a>
                    </td>
                    <td>
                        <code>{$prob['ac_cnt']}/{$prob['submit_cnt']}</code>
                        (<code>{$prob['solve_cnt']}/{$prob['try_cnt']}</code>)
                    </td>
                    <td>
                        <code>{$prob['time_limit']}ms</code>/<code>{$prob['mem_limit']}MiB</code>({$prob['turn']})
                    </td>
                    <td>
                        <a class="btn btn-xs btn-info" href="/admin/problem/{$prob['id']}/edit" role="button">编辑</a>
                        <a class="btn btn-xs btn-default" href="#" role="button">详情</a>
                        {if $prob['status'] == 1}
                            <a class="btn btn-xs btn-warning" href="/admin/problem/{$prob['id']}/lock" role="button">锁定</a>
                        {else}
                            <a class="btn btn-xs btn-primary" href="/admin/problem/{$prob['id']}/unlock" role="button">解锁</a>
                        {/if}
                        <a class="btn btn-xs btn-danger" href="/admin/problem/{$prob['id']}/delete" role="button">删除</a>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
{/block}