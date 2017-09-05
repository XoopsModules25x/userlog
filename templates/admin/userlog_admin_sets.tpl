<{$logo}>
<{if $sets}>
    <div class="outer">
        <div class="head border x-small">
            <div class="width1 floatleft center"><{$smarty.const._AM_USERLOG_SET_ID}></div>
            <div class="width5 floatleft center"><{$smarty.const._AM_USERLOG_SET_ACTIVE}></div>
            <div class="width10 floatleft center"><{$smarty.const._AM_USERLOG_SET_NAME}></div>
            <div class="width5 floatleft center"><{$smarty.const._AM_USERLOG_SET_LOGBY}></div>
            <div class="width5 floatleft center"><{$smarty.const._AM_USERLOG_SET_UNIQUE_ID}></div>
            <div class="width40 floatleft center"><{$smarty.const._AM_USERLOG_SET_OPTIONS}></div>
            <div class="width20 floatleft center"><{$smarty.const._AM_USERLOG_SET_SCOPE}></div>
            <div class="truncate center"><{$smarty.const._EDIT}>|<{$smarty.const._DELETE}>
                |<{$smarty.const._AM_USERLOG_ADMENU_LOGS}></div>
            <div class="clear"></div>
        </div>
        <{foreach item=set from=$sets}>
            <div class="<{cycle values='even,odd'}> <{if $set.active eq 0}> deactive <{/if}> bold border">
                <div class="width1 floatleft center"><{$set.set_id}></div>
                <div class="width5 floatleft center"><{if $set.active eq 0}><{$smarty.const._MI_USERLOG_IDLE}><{else}><{$smarty.const._MI_USERLOG_ACTIVE}><{/if}></div>
                <div class="width10 floatleft center"><{$set.name}></div>
                <div class="width5 floatleft center"><{$set.logby}></div>
                <div class="width5 floatleft center"><{$set.unique_id}></div>
                <div title="<{$set.options}>" class="width40 floatleft left"><{$set.options}></div>
                <div title="<{$set.scope}>" class="width20 floatleft left"><{$set.scope}></div>
                <div class="truncate ellipsis left">
                    <a href="setting.php?set_id=<{$set.set_id}>" title="<{$smarty.const._EDIT}>"><img
                                src="<{xoModuleIcons16 edit.png}>" alt="<{$smarty.const._EDIT}>"
                                title="<{$smarty.const._EDIT}>"></a>
                    |
                    <a href="setting.php?op=del&amp;set_id=<{$set.set_id}>" title="<{$smarty.const._DELETE}>"><img
                                src="<{xoModuleIcons16 delete.png}>" alt="<{$smarty.const._DELETE}>"
                                title="<{$smarty.const._DELETE}>"></a>
                    |
                    <a href=
                       <{if $set.unique_id eq 0}>
                       "logs.php"
                    <{elseif $set.logby eq $smarty.const._AM_USERLOG_UID}>
                    "logs.php?options[uid]=<{$set.unique_id}>"
                    <{elseif $set.logby eq $smarty.const._AM_USERLOG_SET_GID}>
                    "logs.php?options[groups]=g<{$set.unique_id}>"
                    <{elseif $set.logby eq $smarty.const._AM_USERLOG_SET_IP}>
                    "logs.php?options[user_ip]=<{$set.unique_id}>"
                    <{/if}>
                    title="<{$smarty.const._AM_USERLOG_ADMENU_LOGS}> - <{$set.logby}>=<{$set.unique_id}>"
                    alt="<{$smarty.const._AM_USERLOG_ADMENU_LOGS}> - <{$set.logby}>=<{$set.unique_id}>"
                    ><{$smarty.const._AM_USERLOG_ADMENU_LOGS}> - <{$set.logby}>=<{$set.unique_id}></a>
                </div>
                <div class="clear"></div>
            </div>
        <{/foreach}>
        <{$pagenav}>
    </div>
<{/if}>
<{$addset}>
<{$form}>
