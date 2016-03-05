@extends('page')
@section('container')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="h2 left-border-blue">
                题目列表 <small class="new-line-768">{{ $count }}条结果</small>
                <br>
                <small>搜索 / {{ $keyword }}</small>
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
                        <td><a href="/problem/{{ $problem->_id }}">{{ $problem->title }}</a></a></td>
                        <td class="problem-list-na">{{ $problem->submit or '0' }}</td>
                        <td class="problem-list-na">{{ $problem->ac or '0' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop