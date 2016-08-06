<{$logo}>
<div class="outer">
    <div class="floatleft">
        <fieldset>
            <legend class="label"><{$smarty.const._AM_USERLOG_VIEW}></legend>
            <{foreach from=$items key=link item=item_content}>
                <{if $sortentry eq "module" || $sortentry eq "module_name" || $sortentry eq "module_count"}>
                    <{if $lastmodule neq $item_content.module}>
                        <h1><{$item_content.module_name}>(<{$smarty.const._AM_USERLOG_VIEW_MODULE}>
                            :<{$item_content.module_count}>)</h1>
                    <{/if}>
                    <{assign var=lastmodule value=$item_content.module}>
                <{/if}>
                <div class="<{cycle values="even,odd"}> border">
                    <a href="<{$xoops_url}>/<{$link}>"
                       title="<{$item_content.pagetitle}>(<{$smarty.const._AM_USERLOG_VIEW}>:<{$item_content.count}>)">[<{$item_content.module_name}>
                        ]&nbsp;<{$item_content.pagetitle}>(<{$smarty.const._AM_USERLOG_VIEW}>:<{$item_content.count}>
                        )</a>
                </div>
            <{/foreach}>
            <legend class="label"></legend>
            <{$form}>
        </fieldset>
        <fieldset>
            <legend class="label"><{$smarty.const._AM_USERLOG_STATS_REFERRAL}></legend>
            <{includeq file="db:`$smarty.const.USERLOG_DIRNAME`_block_stats_type.tpl" block=$refViews}>
            <legend class="label"></legend>
        </fieldset>
        <fieldset>
            <legend class="label"><{$smarty.const._AM_USERLOG_STATS_BROWSER}></legend>
            <{includeq file="db:`$smarty.const.USERLOG_DIRNAME`_block_stats_type.tpl" block=$browserViews}>
            <legend class="label"></legend>
        </fieldset>
        <fieldset>
            <legend class="label"><{$smarty.const._AM_USERLOG_STATS_OS}></legend>
            <{includeq file="db:`$smarty.const.USERLOG_DIRNAME`_block_stats_type.tpl" block=$OSViews}>
            <legend class="label"></legend>
        </fieldset>
        <fieldset>
            <legend class="label"><{$smarty.const._AM_USERLOG_LOGIN_REG_HISTORY}></legend>
            <{includeq file="db:`$smarty.const.USERLOG_DIRNAME`_block_login_reg_history.tpl" block=$loginsHistory}>
            <legend class="label"></legend>
        </fieldset>
    </div>
    <div class="floatright">
        <{$stats_abstract}>
        <fieldset>
            <legend class="label"><{$smarty.const._AM_SYSTEM_MODULES_ADMIN}></legend>
            <{includeq file="db:`$smarty.const.USERLOG_DIRNAME`_admin_stats_moduleadmin.tpl" block=$moduleAdmin}>
            <legend class="label"></legend>
        </fieldset>
    </div>
    <div class="clear"></div>
</div>
