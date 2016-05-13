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
                <div class="card">
                    <h2>概览</h2>
                    <hr>
                    <div class="row">
                        <div class="col-sm-4">
                            <p>题库统计：</p>
                            <p>评测机状态：</p>
                        </div>
                        <div class="col-sm-4">
                            <p>提交统计：</p>
                            <p>讨论统计：</p>
                        </div>
                        <div class="col-sm-4">
                            <p>注册用户统计：</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="card">
                    <h2>题库 <small><a href="/admin/problem">管理</a></small></h2>
                    <hr>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card">
                    <h2>提交 <small><a href="/admin/solution">管理</a></small></h2>
                    <hr>
                </div>
            </div>
        </div>
    </div>
{/block}