<div class="outer">
    <{foreach from=$block key=log_id item=modulesadmin}>
        <div class="<{cycle values="even,odd"}> border" alt="<{$modulesadmin.op_lang}>"
             title="<{$modulesadmin.op_lang}>">
            <span class="bold
                <{if $modulesadmin.op eq 'install'}>green
                <{elseif $modulesadmin.op eq 'update'}>yellow
                <{elseif $modulesadmin.op eq 'uninstall'}>red
                <{/if}>">
                <img width="16" src="
                                <{if $modulesadmin.op eq 'install'}><{xoAdminIcons install.png}>
                                <{elseif $modulesadmin.op eq 'update'}><{xoAdminIcons reload.png}>
                                <{elseif $modulesadmin.op eq 'uninstall'}><{xoAdminIcons uninstall.png}>
                                <{/if}>" alt="<{$modulesadmin.op_lang}>" title="<{$modulesadmin.op_lang}>">
                <{$modulesadmin.op_lang}>
            </span>
            :&nbsp;
            <span><{$modulesadmin.module}></span>
            &nbsp;
            <a href="<{$smarty.const.USERLOG_ADMIN_URL}>/logs.php?options[log_id]=<{$log_id}>,<{$log_id-1}>"><{$smarty.const._AM_USERLOG_LOG_ID}>
                : <{$log_id}></a>
            <span> <{$smarty.const._AM_USERLOG_LOG_TIME}>: <{$modulesadmin.log_time}></span>
        </div>
    <{/foreach}>
</div>
