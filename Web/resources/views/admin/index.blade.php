@extends('admin.page')
@section('container')
<div class="row">
    <div class="col-md-12">
        <h1 class="text-center">
            总览
        </h1>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <img src="/static/img/icon/problem.png" align="left" class="img-rounded img-responsive signimg">
        <h3 class="text-left">
            题库
        </h3>
        <p class="text-left">
            题库当前线上题目数：{{ $problemCount }}<br>
        </p>
        <a href="/admin/problem/add" class="btn btn-primary btn-default" role="button">添加题目</a>
        <a href="/admin/problem" class="btn btn-default btn-default" role="button">管理题目</a>
    </div>
    <div class="col-md-6">
        <img src="/static/img/icon/data.png" align="left" class="img-rounded img-responsive signimg">
        <h3 class="text-left">
            数据
        </h3>
        <p class="text-left">
            用户提交数：{{ $solutionCount }}<br>
            用户讨论主题数：{{ 'N/A' }}<br>
            <a href="/admin/solution" class="btn btn-default btn-default" role="button">管理提交</a>
            <a href="/admin/discussion" class="btn btn-default btn-default" role="button">管理讨论</a>
        </p>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <img src="/static/img/icon/<?php echo $clientOnCount ? 'client.png' : 'client-warn.png'; ?>" align="left" class="img-rounded img-responsive signimg">
        <h3 class="text-left">
            评测机
        </h3>
        <p class="text-left">
            当前评测机状态：<br>
            <span class="glyphicon glyphicon-arrow-up"></span>{{ $clientOnCount }}　
            <span class="glyphicon glyphicon-arrow-down"></span>{{ $clientCount - $clientOnCount }}
        </p>
        <a href="/admin/client" class="btn btn-primary btn-default" role="button">查看详情</a>
    </div>
    <div class="col-md-6">
        <img src="/static/img/icon/user.png" align="left" class="img-rounded img-responsive signimg">
        <h3 class="text-left">
            用户
        </h3>
        <p class="text-left">
            当前已注册用户：{{ $userCount }}
        </p>
        <a href="user.php" class="btn btn-primary btn-default" role="button">管理用户</a>
    </div>
</div>
@stop