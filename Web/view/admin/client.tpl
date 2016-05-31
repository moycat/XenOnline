{extends file='admin/subpage.tpl'}
{block name="subtitle"}评测机管理{/block}
{block name='menu'}
    <div id="deleteItem" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="DeleteItem">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="deleteLabel">删除评测机 <a id="idtodelTitle"></a></h4>
                </div>
                <div class="modal-body">
                    <p><b>你确定要删除这台评测机吗？</b>此评测机的内容将被清除，此操作将无法撤销。</p>
                    <p>此评测机的评测记录不会被删除，<b>此评测机将会立即被踢下线</b>。</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <a id="idtodelButton" class="btn btn-danger" href="#" role="button">删除</a>
                </div>
            </div>
        </div>
    </div>
    <div id="addItem" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="AddItem">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="addItemLabel">添加一台评测机</h4>
                </div>
                <form method="post" action="/admin/client/post">
                    <div class="modal-body">
                        <p>请在下方输入评测机的信息：</p>
                        <div class="form-group">
                            <label for="name">评测机名称</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="一只评测机" required>
                        </div>
                        <div class="form-group">
                            <label for="introduction">评测机简介</label>
                            <input type="text" name="introduction" class="form-control" id="introduction" placeholder="Another client." required>
                        </div>
                        <p>评测机添加后，将会生成连接密钥。请在评测端配置此连接密钥，方可进行评测。</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-danger">添加</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <ul class="nav nav-pills">
                <li role="presentation" class="active">
                    <a href="#">查看评测机
                        <span class="badge">{$count['client']}</span>
                    </a>
                </li>
                <li role="presentation"><a onclick="$('#addItem').modal()" href="#">添加评测机</a></li>
            </ul>
        </div>
    </div>
{/block}
{block name='content'}
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>#</th>
                <th>名称</th>
                <th>负载</th>
                <th>内存</th>
                <th>最后活动</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            {foreach $clients as $client}
                <tr>
                    <th scope="row">
                        {$client['id']}
                    </th>
                    <td>
                        {$client['name']}
                    </td>
                    <td>
                        <code>{$client['load'][0]}</code>/
                        <code>{$client['load'][1]}</code>/
                        <code>{$client['load'][2]}</code>
                    </td>
                    <td>
                        <code>{$client['memory']}%</code>
                    </td>
                    <td>
                        {Facade\Site::date($client['last_ping'])}
                    </td>
                    <td>
                        <a class="btn btn-xs btn-info" onclick="" role="button">编辑</a>
                        <button type="button" class="btn btn-xs btn-warning clientHash" data-placement="bottom"
                                data-toggle="popover" title="评测机#{$client['id']} 连接密钥"
                                data-html="true" data-content="<code>{$client['hash']}</code>">
                            查看密钥
                        </button>
                        <a class="btn btn-xs btn-danger"
                           onclick="deleteItem('client', {$client['id']}, '{$client['name']|escape}')"
                           role="button">删除</a>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
{/block}
{block name=extra_foot append}
    <script>
        $(function () {
            $(".clientHash").popover();
        });
    </script>
{/block}