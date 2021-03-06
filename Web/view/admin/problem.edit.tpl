{extends file='admin/subpage.tpl'}
{block name='menu'}
    <div class="row">
        <div class="col-sm-12">
            <ul class="nav nav-pills">
                <li role="presentation">
                    <a href="/admin/problem">所有题目
                        <span class="badge">{$count['problem']}</span>
                    </a>
                </li>
                {if isset($problem) && !isset($failtoadd)}
                    <li role="presentation"><a href="/admin/problem/add">添加题目</a></li>
                    <li role="presentation" class="active"><a href="#">编辑题目</a></li>
                {else}
                    <li role="presentation" class="active"><a href="/admin/problem/add">添加题目</a></li>
                {/if}
            </ul>
        </div>
    </div>
{/block}
{block name='content'}
    <div class="row">
        {literal}
        <form class="col-sm-12" method="post" action="/admin/problem/post"
              onkeydown="if(event.keyCode==13){return false;}" enctype="multipart/form-data">
            {/literal}
            <div class="form-group">
                <label for="title" class="h3">标题</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="题目标题" autofocus required>
            </div>
            <div class="form-group">
                <label for="editor" class="h3" autocomplete="off">题面</label>
                <div id="editormd">
                    <textarea id="editor" name="content" style="display:none;">
                    </textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="tag" class="h3">标签</label>
                <input type="text" class="form-control" id="tag" name="tag" placeholder="回车添加标签">
            </div>
            <div class="form-group" id="test-data">
                <label class="h3">测试数据</label>
                {if isset($problem['id'])}
                    <div class="alert alert-warning">
                        <b>如不更新测试数据，请不要在此选择文件！</b>一旦选择文件，将覆盖此题已上传的所有测试数据。
                        <br>
                        不修改测试数据请按红×，手动删除所有下方文件框。
                    </div>
                {/if}
                <div id="data0">
                    <div class="row">
                        <div class="col-md-4">
                            <p><input type="file" id="input0" title="输入数据 #0" name="input[]" class="btn-info btn-sm"
                                      required>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p><input type="file" id="stdout0" title="输出数据 #0" name="stdout[]" class="btn-info btn-sm"
                                      required>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger btn-sm"
                                    onclick="adminDelData(0)">
                                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-primary" onclick="adminNewData()">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                添加一组
            </button>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="time-limit" class="h3">时间限制（ms）</label>
                        <input type="number" min=1 step=1 class="form-control" id="time-limit" name="time_limit"
                               placeholder="单位：毫秒" required>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="mem-limit" class="h3">内存限制（MiB）</label>
                        <input type="number" min=1 step=1 class="form-control" id="mem-limit" name="mem_limit"
                               placeholder="单位：兆字节" required>
                    </div>
                </div>
            </div>
            <br>
            {if isset($problem['id'])}
                <input type="hidden" name="pid" value="{$problem['id']}">
            {/if}
            <button type="submit" class="btn btn-info btn-lg">发布</button>
        </form>
    </div>
{/block}
{block name='extra_head' append}
    <link rel="stylesheet" href="/static/vendor/editor.md/css/editormd.min.css"/>
{/block}
{block name='extra_foot' append}
    <script src="/static/vendor/editor.md/editormd.min.js"></script>
    <script src="/static/vendor/bootstrap.file-input.js"></script>
    <script src="/static/vendor/inputTags.jquery.min.js"></script>
    <script>
        var mdEditor;
        $(function () {
            $('input[type=file]').bootstrapFileInput();
            $('.file-inputs').bootstrapFileInput();

            {if isset($problem)}
            $("#title").attr('value', "{$problem['title']}");
            $("#time-limit").attr('value', "{$problem['time_limit']}");
            $("#mem-limit").attr('value', "{$problem['mem_limit']}");
            {if isset($problem['tag'])}
            $('#tag').attr('value', '{implode(',', $problem['tag'])}');
            {/if}
            {/if}
            $('#tag').inputTags(
                    {
                        init: function ($elem) {
                            $(".inputTags-field").attr('placeholder', '输入新标签，回车添加');
                        }
                    }
            );
            if ($(document.body).outerWidth(true) > 768)
                toggleMenu();
            $.get('/static/problem-instruction.md', function (md) {
                Editor = editormd("editormd", {
                    height: "80vh",
                    path: '/static/vendor/editor.md/lib/',
                    markdown: {if isset($problem)}decodeURIComponent("{urlencode($problem['content'])}"){else}md{/if},
                    codeFold: true,
                    saveHTMLToTextarea: true,
                    searchReplace: true,
                    htmlDecode: "",
                    emoji: true,
                    taskList: true,
                    tocm: true,
                    tex: true,
                    flowChart: true,
                    sequenceDiagram: true,
                    imageUpload: true,
                    imageFormats: ["jpg", "jpeg", "gif", "png", "bmp", "webp"],
                    imageUploadURL: "/upload",
                    onload: function () {
                        $(".CodeMirror-wrap").css({
                            'width': "50%"
                        });
                        $(".editormd-preview").css({
                            'width': "50%"
                        });
                    }
                });
            });
        });
    </script>
{/block}