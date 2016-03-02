@extends('themes.Yuki.common.page')
@section('container')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="h2 left-border-blue solution-bigheading">
                    评测结果
                    <small>{!! SolutionCell::state($solution->result, true) !!}</small>
                </div>
                <div class="top-border-red solution-area">
                    <div class="h3 solution-heading">评测总结</div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-xs-3">
                                    <p><strong>提交编号</strong></p>
                                </div>
                                <div class="col-xs-9">
                                    <p><code>#{{ $solution->id }}</code></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3">
                                    <p><strong>题目</strong></p>
                                </div>
                                <div class="col-xs-9">
                                    <p>{{ $solution->problem_id }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3">
                                    <p><strong>提交状态</strong></p>
                                </div>
                                <div class="col-xs-9">
                                    <p>{!! SolutionCell::state($solution->result) !!}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3">
                                    <p><strong>提交时间</strong></p>
                                </div>
                                <div class="col-xs-9">
                                    <p>{{ $solution->created_at }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3">
                                    <p><strong>提交语言</strong></p>
                                </div>
                                <div class="col-xs-9">
                                    <p>{!! SolutionCell::language($solution->language) !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if($solution->client)
                            <div class="row">
                                <div class="col-xs-3">
                                    <p><strong>评测机</strong></p>
                                </div>
                                <div class="col-xs-9">
                                    <p>#{{ $solution->client }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3">
                                    <p><strong>消耗时间</strong></p>
                                </div>
                                <div class="col-xs-9">
                                    <p><code>{{ $solution->used_time }} ms</code></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3">
                                    <p><strong>消耗内存</strong></p>
                                </div>
                                <div class="col-xs-9">
                                    <p><code>{{ $solution->used_memory }} KiB</code></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3">
                                    <p><strong>评测时间</strong></p>
                                </div>
                                <div class="col-xs-9">
                                    <p>{{ $solution->updated_at }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @if($solution->detail)
                        <div class="alert alert-danger">
                            <p>代码编译过程中出现错误……( º﹃º )</p>
                            <pre>{{ $solution->detail }}</pre>
                        </div>
                    @endif
                </div>
                @if($solution->detail_result)
                <div class="top-border-red solution-area">
                    <div class="h3 solution-heading">详细评测结果</div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>结果</th>
                                <th>耗时</th>
                                <th>内存</th>
                                <th>分数</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $detail_result = $solution->detail_result;
                            $detail_time = $solution->detail_time;
                            $detail_memory = $solution->detail_memory;
                            $turn = count($solution->detail_result);
                            $eachScore = round((100/$turn), 2);
                            $scorePoint = 0;
                            for ($i = 0; $i < $turn; ++$i) {
                                echo ($detail_result[$i] == 10) ? '<tr class="success">' : '<tr class="danger">';
                            ?>
                                <th scope="row">{{ $i }}</th>
                                <td>{!! SolutionCell::state($detail_result[$i]) !!}</td>
                                <td><code>{{ $detail_time[$i] }} ms</code></td>
                                <td><code>{{ $detail_memory[$i] }} KiB</code></td>
                                <td><?php if($detail_result[$i]==10)
                                    {$scorePoint++;echo $eachScore;}else echo 0;?></td>
                            </tr>
                            <?php } ?>
                            <tr class="info">
                                <th scope="row">总结</th>
                                <td>{!! SolutionCell::state($solution->result) !!}</td>
                                <td><code>{{ $solution->used_time }} ms</code></td>
                                <td><code>{{ $solution->used_memory }} KiB</code></td>
                                <td>{{ round(100*($scorePoint/$turn), 2) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                <div class="top-border-red solution-area">
                    <div class="h3 solution-heading">源代码 <small>{{ $solution->code_length }} Byte</small></div>
                    <div id="source-code">{{ $solution->code }}</div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('startup_js')
    var editor = ace.edit("source-code");
    editor.setTheme("ace/theme/chrome");
    editor.getSession().setMode("ace/mode/c_cpp");
    editor.setReadOnly(true);
@stop
@section('extra_css')
    <style type="text/css" media="screen">
        #source-code {
            position: relative;
            width: auto;
            height: 500px;
            font-size: 14px;
            background-color: #F6F6F6;
        }
    </style>
@stop