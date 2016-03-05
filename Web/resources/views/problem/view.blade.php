@extends('page')
@section('container')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="h2 left-border-blue solution-bigheading">
                    {{ $problem->title }}
                </div>
                <input type="hidden" id="pid" value="{{ $problem->_id }}">
                <h5><small>
                    时间限制 <code>{{ $problem->time_limit }} ms</code>
                    内存限制 <code>{{ $problem->memory_limit }} MiB</code>
                    <br>
                    @foreach($problem->tag as $tag)
                    <a href="/problem?tag={{ $tag }}">
                        <span class="label label-primary">{{ $tag }}</span>
                    </a>
                    @endforeach
                </small></h5>
                <div class="top-border-red solution-area">
                    <div id="content-md">
                        <div class="alert alert-info" role="alert" id="loading" style="margin-top: 20px;">加载中~</div>
                        <textarea style="display:none;">
{{ $problem->content }}
                        </textarea>
                    </div>
                </div>
                <div class="top-border-red solution-area">
                    <div class="h3 solution-heading">提交代码 <small>10KiB以内</small></div>
                    <div id="source-code"></div>
                    <form class="form-inline solution-post solution-post" role="form">
                        <input type="radio" name="cpp" id="cpp" value="cpp" checked> C++/C
                        <button type="button" class="btn btn-danger" onclick="post_code()">提交</button>
                    </form>
                    <div id="post-info"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="h3 left-border-orange">
                    我的提交
                </div>
                <div id="my-solutions" class="top-border-red">
                    @if(Auth::check())
                        @if(isset($solutions)&&$solutions)
                        @foreach($solutions as $solution)
                        <p>
                            <a href="/solution/{{ $solution['_id'] }}">
                                ·{{ $solution['created_at']->toDateTime()->format('m-d') }}
                                @if(isset($solution['result']))
                                {!! SolutionCell::state($solution['result']) !!}
                                @else
                                {!! SolutionCell::state(0) !!}
                                @endif
                            </a>
                        </p>
                        @endforeach
                        @else
                        <p>暂无提交</p>
                        @endif
                    @else
                        请登录后查看
                    @endif
                </div>
            </div>
            <div class="card">
                <div class="h3 left-border-orange">
                    讨论
                </div>
                <div class="h4 top-border-red">
                    <small>//TODO: 讨论功能尚未上线</small>
                </div>
            </div>
        </div>
    </div>
@stop
@section('startup_js')
    var content;
    content = editormd.markdownToHTML("content-md", {
    htmlDecode      : "style,script,iframe",
    emoji           : true,
    taskList        : true,
    tex             : true,
    //flowChart       : true,
    //sequenceDiagram : true,
    });
    var editor = ace.edit("source-code");
    editor.setTheme("ace/theme/chrome");
    editor.getSession().setMode("ace/mode/c_cpp");
    $("#loading").hide();
@stop
@section('extra_css')
    <style type="text/css" media="screen">
        #source-code {
            position: relative;
            width: auto;
            height: 300px;
            font-size: 14px;
            background-color: #F6F6F6;
        }
    </style>
@stop
@section('extra_js')
    <script src="https://pandao.github.io/editor.md/lib/marked.min.js"></script>
    <script src="https://pandao.github.io/editor.md/lib/prettify.min.js"></script>
    <!-- <script src="https://pandao.github.io/editor.md/lib/raphael.min.js"></script> -->
    <!-- <script src="https://pandao.github.io/editor.md/lib/underscore.min.js"></script> -->
    <!-- <script src="https://pandao.github.io/editor.md/lib/sequence-diagram.min.js"></script> -->
    <!-- <script src="https://pandao.github.io/editor.md/lib/flowchart.min.js"></script> -->
    <!-- <script src="https://pandao.github.io/editor.md/lib/jquery.flowchart.min.js"></script> -->
    <script src="https://pandao.github.io/editor.md/editormd.min.js"></script>
@stop