@extends('themes.Yuki.common.page')
@section('container')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="h2 left-border-blue">
                题目列表 <small>{{ $count }}条结果</small>
                @if(isset($problems['filter']))
                <br>
                <small>筛选 / {!! implode(' ', $problems['filter']) !!}</small>
                @endif
            </div>
            <hr>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>标题</th>
                    <th>提交</th>
                    <th>AC</th>
                </tr>
                </thead>
                <tbody>
                @foreach($problems as $problem)
                    <tr>
                        <th scope="row">总结</th>
                        <td>{{ $problem }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">

        </div>
    </div>
</div>
@stop