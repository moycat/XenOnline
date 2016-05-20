{extends file='page.tpl'}
{block name="title" prepend}管理后台 - {/block}
{block name="nav"}{include file='admin/common/sidebar.tpl'}{/block}
{block name='wrapper'}{/block}
{block name='footer'}
    <div class="footer">
        <div class="row">
            <div class="col-sm-12">
                <h2>
                    <span class="glyphicon glyphicon-th-large" aria-hidden="true"></span>
                    XenOnline <small>About</small>
                </h2>
                <p>This is an online judge system powered by <a href="https://github.com/moycat/XenOnline">XenOnline</a>.</p>
                <p><a href="https://github.com/moycat/XenOnline">XenOnline</a> is free and open-source software licensed under GPLv2.</p>
                <p>Page processed in {$process_time} ms.</p>
            </div>
        </div>
    </div>
{/block}
{block name='extra_head' append}
    <link rel="stylesheet" href="/static/css/admin.css"/>
{/block}
{block name='extra_foot' append}
    <script src="/static/js/admin.js"></script>
{/block}