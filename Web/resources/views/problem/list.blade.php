@extends('page')
@section('container')
<?php
$totalPage = ceil($count / 20);
$url = explode('?', Request::getRequestUri());
$pram = isset($url[1])?'?'.$url[1]:'';
?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="h2 left-border-blue">
                题目列表 <small class="new-line-768">{{ $count }}条结果 / {{ $totalPage }}页</small>
                @if($filter)
                <br>
                <small>筛选 / {!! implode(' ', $filter) !!}</small>
                @endif
            </div>
            <hr>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>标题</th>
                    <th class="problem-list-na">提交</th>
                    <th class="problem-list-na">AC</th>
                </tr>
                </thead>
                <tbody>
                @foreach($problems as $problem)
                    <tr>
                        <td><a href="/problem/{{ $problem->id }}">{{ $problem->title }}</a></a></td>
                        <td class="problem-list-na">{{ $problem->submit or '0' }}</td>
                        <td class="problem-list-na">{{ $problem->ac or '0' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="page-nav">
                <div class="pagination pagination-danger">
                    <ul>
                        @if($previous)
                        <li class="previous"><a href="/problem/page/{{ ($page - 1).$pram }}" class="fui-arrow-left"></a></li>
                        @else
                        <li class="previous disabled"><a href="#" class="fui-arrow-left"></a></li>
                        @endif
                        <?php
                            $p = $page - 2 > 0 ? $page - 2 : 1;
                            for ($i = 0; $i < 5 && $p + $i <= $totalPage; ++$i) {
                        ?>
                        @if($p+$i==$page)
                        <li class="active">
                        @else
                        <li>
                        @endif
                            <a href="/problem/page/{{ ($p + $i).$pram }}">{{ $p + $i }}</a>
                        </li>
                        <?php
                            }
                        ?>
                        @if($next)
                            <li class="next"><a href="/problem/page/{{ ($page + 1).$pram }}" class="fui-arrow-right"></a></li>
                        @else
                            <li class="next disabled"><a href="#" class="fui-arrow-right"></a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="h3 left-border-orange">
                筛选
            </div>
            <div class="h4 top-border-red">
                标签
            </div>
            <div class="problems-tag">
            @foreach($tags as $tag)
                <h6>
                    @if(isset($filter)&&$filter)
                    <a href="/problem?tag={{ implode('+', $filter).'+'.$tag }}">
                    @else
                    <a href="/problem?tag={{ $tag }}">
                    @endif
                        <span class="label label-primary">{{ $tag }}</span>
                    </a>
                </h6>
            @endforeach
            </div>
        </div>
        <div class="card">
            <div class="h3 left-border-orange">
                搜索
            </div>
            <div class="h4 top-border-red">
                <small>//TODO: 搜索功能尚未上线</small>
            </div>
        </div>
    </div>
</div>
@stop