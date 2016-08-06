<div class="outer">
    <{foreach from=$block.stats key=stats_link item=views}>
        <div class="<{cycle values="even,odd"}> border">
            <span class="">
                <{if $block.stats_type eq "referral" }>
                    <a href="http://<{$stats_link}>"><{$stats_link}></a>
                    :
                <{else}>
                    <{$stats_link}>:
                <{/if}>
            </span>
            <span class="bold green">
                <{$views.0.value}>
            </span>
            <b>
                <span> <{$smarty.const._AM_USERLOG_STATS_TIME_UPDATE}> <{$views.0.time_update}></span>
        </div>
    <{/foreach}>
</div>
