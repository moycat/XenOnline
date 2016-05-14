{*********************************************************
 * view/admin/index.tpl @ XenOnline
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
**********************************************************}
{extends file='admin/page.tpl'}
{block name='wrapper' prepend}
    <div class="container">
        <h1>{$site_name} 管理后台</h1>
        <div class="row">
            <div class="col-sm-12">
                {if !isset($count['online_client']) || $count['online_client'] == 0}
                    <div class="alert alert-danger" role="alert">
                        没有在线的评测端！请检查相关配置。
                    </div>
                {/if}
                <div class="card">
                    <h2>概览</h2>
                    <hr>
                    <div class="row">
                        <div class="col-sm-4">
                            <p>
                                <b>题库统计</b>
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                题目数<code>{$count['problem']}</code>
                            </p>
                            <p>
                                <b>评测机状态</b>
                                <span class="glyphicon glyphicon-tasks" aria-hidden="true"></span>
                                总数<code>0</code>
                                <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span>
                                在线数<code>0</code>
                            </p>
                        </div>
                        <div class="col-sm-4">
                            <p>
                                <b>提交统计</b>
                                <span class="glyphicon glyphicon-check" aria-hidden="true"></span>
                                提交数<code>0</code>
                            </p>
                            <p>
                                <b>讨论统计</b>
                                <span class="glyphicon glyphicon-comment" aria-hidden="true"></span>
                                讨论数<code>0</code>
                            </p>
                        </div>
                        <div class="col-sm-4">
                            <p>
                                <b>注册用户统计</b>
                                <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                                注册用户<code>{$count['user']}</code>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="card">
                    <h2>题库
                        <a class="btn btn-info btn-xs" href="/admin/problem" role="button">管理</a>
                    </h2>
                    <hr>
                </div>
                <div class="card">
                    <h2>评测机
                        <a class="btn btn-info btn-xs" href="/admin/client" role="button">管理</a>
                    </h2>
                    <hr>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card">
                    <h2>提交
                        <a class="btn btn-info btn-xs" href="/admin/solution" role="button">管理</a>
                    </h2>
                    <hr>
                </div>
            </div>
        </div>
    </div>
{/block}