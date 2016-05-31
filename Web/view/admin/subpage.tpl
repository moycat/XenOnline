{extends file='admin/page.tpl'}
{block name='wrapper'}
<div class="container">
    <h1>{$site_name} {block name="subtitle"}{/block}</h1>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                {block name='menu'}{/block}
                <hr>
                {if isset($info)}
                    {$info}
                {/if}
                {Facade\Session::fetch('info')}
                {block name='content'}{/block}
            </div>
        </div>
    </div>
</div>
{/block}