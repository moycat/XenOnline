{extends file='common.tpl'}
{block name="body"}
    <div id="Yuki">
        <nav class="left-nav">
            {block name="nav"}{include file='common/sidebar.tpl'}{/block}
        </nav>
        <div class="openNav" onclick="toggleMenu()" title="开关导航栏">
            <div class="icon"></div>
        </div>
        <a class="gotop tool-button" onclick="gotop()" href="#" title="返回顶部">
            <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span>
        </a>
        <div class="wrapper">
            {block name='wrapper'}{/block}
        </div>
{/block}