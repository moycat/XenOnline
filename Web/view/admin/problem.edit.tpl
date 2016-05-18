{extends file='admin/subpage.tpl'}
{block name='menu'}
    <div class="row">
        <div class="col-sm-12">
            <ul class="nav nav-pills">
                <li role="presentation">
                    <a href="#">所有题目
                        <span class="badge">{$count['problem']}</span>
                    </a>
                </li>
                {if isset($problem)}
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
        <form class="col-sm-12" method="post" action="/admin/problem/add">
            <div class="form-group">
                <label for="title" class="h2">标题</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="题目标题">
            </div>
            <div id="editormd">
                <textarea style="display:none;">
                </textarea>
            </div>
        </form>
    </div>
{/block}
{block name='extra_head'}
    <link rel="stylesheet" href="/static/vendor/editor.md/css/editormd.min.css"/>
{/block}
{block name='extra_foot'}
    <{literal}>
    <script src="/static/vendor/editor.md/editormd.min.js"></script>
    <script>
        var mdEditor;
        $(function () {
            toggleMenu();
            $.get('inc/instruction.md', function (md) {
                Editor = editormd("editormd", {
                    height: 740,
                    path: '/static/vendor/editor.md/lib/',
                    markdown: 'md',
                    codeFold: true,
                    saveHTMLToTextarea: true,
                    searchReplace: true,
                    htmlDecode: "style,script,iframe|on*",
                    emoji: true,
                    taskList: true,
                    tocm: true,
                    tex: true,
                    flowChart: true,
                    sequenceDiagram: true,
                    imageUpload: true,
                    imageFormats: ["jpg", "jpeg", "gif", "png", "bmp", "webp"],
                    imageUploadURL: "/mo-includes/upload.php",
                    onload: function () {
                        $(".CodeMirror-wrap").css({'width': "50%"});
                        $(".editormd-preview").css({'width': "50%"});
                    }
                });
            });
        });
    </script>
    <{/literal}>
{/block}