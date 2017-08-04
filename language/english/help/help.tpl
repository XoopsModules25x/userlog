<div id="help-template" class="outer">
    <{include file=$smarty.const._MI_USERLOG_HELP_HEADER}>

    <h4 class="odd">DESCRIPTION</h4> <br>
    <p class="even">
        Userlog is a node logger which can log your user/visitor activities in your site from a preferred node.<br>
        This is a very useful tool for webmasters in busy sites. For example, you can log your other Admins
        navigation.<br>
        Current nodes for logging are: user ID, user group and visitor IP.<br>
        You can store logs in a database, in a file or both.<br><br>
        <b>Attention: userlog module will only work in Admin part of modules that use the ModuleAdmin class.</b><br><br>
    </p>
    <h4 class="odd"><{$smarty.const._MI_USERLOG_NAME}> > <{$smarty.const._PREFERENCES}></h4>
    After Installation we strongly recommend to go first to Preferences.<br><br>
    Here you can set your desired values for important configuration items.<br><br>
    Important configs are:<br><br>
    <b><{$smarty.const._MI_USERLOG_CONFCAT_LOGFILE}>:</b>
    If you need to store logs in a file, set the working path, working file size, working file name, ... here<br><br>
    <b><{$smarty.const._MI_USERLOG_CONFCAT_LOGDB}>:</b>
    If you need to store logs in database, set the maximum logs thresholds here.<br><br>
    <b><{$smarty.const._MI_USERLOG_POSTLOG}>:</b>
    <{$smarty.const._MI_USERLOG_POSTLOG_DSC}><br><br>

    <h4 class="odd"><{$smarty.const._AM_USERLOG_ADMENU_SETTING}></h4>
    <p class="even">
        "Setting" provides a wide range of options that you can use to set for specific users to be logged.<br>
        <br>
        <b><{$smarty.const._AM_USERLOG_SET_NAME}>:</b> here you can input any name you like. It is not important for the
        module activities. It is just for you.<br>
        <br>
        <b><{$smarty.const._AM_USERLOG_SET_LOGBY}>:</b> here you will choose the 'node' you want to log users by, and in
        the next part you should input the Unique ID for this node. Currently you can
        log users by "user id", "user group" and "visitor IP".<br>
        - by selecting uid only that specific user will be logged. eg uid=1<br>
        - by selecting user group all activities of users belong to that specific group will be logged. eg: gid=3<br>
        - visitor IP is very useful to log robots, malicious visitors and ... eg: ip=66.249.66.1<br>
        - if you set Unique id = 0 (read the next part) Userlog module will log all users/visitors regardless of
        logby.<br><br>
        <{$smarty.const._AM_USERLOG_SET_LOGBY_DSC}><br><br>

        <b><{$smarty.const._AM_USERLOG_SET_UNIQUE_ID}>:</b>: here you must choose one unique ID (node id) to be
        logged.<br>
        <{$smarty.const._AM_USERLOG_SET_UNIQUE_ID_DSC}><br>
        <br>
        <b><{$smarty.const._AM_USERLOG_SET_OPTIONS}>:</b> you can select which user or page data you want to be logged.
        Also here you can choose 7 settings:<br>
        <{$smarty.const._AM_USERLOG_SET_ACTIVE}>,
        <{$smarty.const._AM_USERLOG_INSIDE}>,
        <{$smarty.const._AM_USERLOG_OUTSIDE}>,
        <{$smarty.const._AM_USERLOG_UNSET_PASS}>,
        <{$smarty.const._AM_USERLOG_STORE_FILE}>,
        <{$smarty.const._AM_USERLOG_STORE_DB}>,
        <{$smarty.const._AM_USERLOG_VIEWS}>,
        <br><br>
        <{$smarty.const._AM_USERLOG_SET_OPTIONS_DSC}>
        <br><br>
        <b><{$smarty.const._AM_USERLOG_SET_SCOPE}>:</b> if you want to log users activities in some specific module, you
        can do it here.<br>
        select nothing means all website.<br>
        <br>
        Attention: we assume you will not have many settings in your site (eg: less than 100 settings) so we don't
        provide many navigation facilities like order and sort in set list table. You just
        have a page navigation.<br>
        Advise: more settings will only confuse you more. Choose logby and Unique ID wisely to avoid any overlap.<br>
        For example for a specific user use "uid" but for a group use "gid".<br>
    </p>

    <h4 class="odd"><{$smarty.const._AM_USERLOG_ADMENU_LOGS}></h4>
    <p class="even">
        You can see/delete/purge/export users logs through the Admin section of the userlog Module.<br><br>
        You can get logs from database or file source engine.<br><br>
        To search for logs based on a criteria you have an advance form.<br><br>
        Export files will be stored in "the working path"/export folder.<br><br>
    </p>
    <h4 class="odd"><{$smarty.const._AM_USERLOG_ADMENU_FILE}></h4>
    <p class="even">
        You can see/delete/rename/copy/merge/compress(zip)/export users log files through the file manager of the
        userlog Module.<br><br>
        Zip files will be stored in "the working path"/zip folder.<br><br>
        Export files will be stored in "the working path"/export folder. <br><br>
        You can select a name for your result (e.g.: copied file) but if you leave that empty, userlog will generate an
        automatic name by using the current criteria and date.<br><br>
        The difference between The file manager section and logs section when you select file engine is, in logs section
        you dynamically work with files data and can see them rows by rows in detail
        but in file manager you just can work on the whole data in files.<br><br>
        For example in logs section you can select some rows to export but in file manager you just can export whole
        data from some selected files.<br><br>
        <b>Attention: If you work on too many files at once you may end up a white screen. It is because the server runs
            out of memory and/or cpu.</b>
    </p>
    <h4 class="odd"><{$smarty.const._AM_USERLOG_ADMENU_STATS}></h4>
    <p class="even">
        You can see total module views, total user views, total group views through the Statistics of the userlog
        Module.<br><br>
        You also can see all Referrals, Browsers and Operating systems.<br><br>
        There is a new login/register history which you can see all attempts by anonymous users (failed and successive)
        to login or register in your website.<br><br>
        To start a login/register history, you should add a setting for anonymous users (Group id = 3) or all users
        (Unique ID = 0) which log at least uid and $_POST.(See above how to add a
        setting)<br><br>
        Also you have an advance form to see any item views using some criteria like what is the module/link/log
        time/viewer uid/viewer group id of the item<br><br>
        Statistics is under develop. please let us know what do you like to see here.<br><br>
    </p>

    <h4 class="odd"><{$smarty.const._MI_USERLOG_NAME}> > Blocks</h4>
    <h3><{$smarty.const._MI_USERLOG_BLOCK_VIEWS}></h3>
    <p class="even">
        Many XOOPS users have a wish for a block to see most viewed items in a module or in the whole website in a
        specific period of time.<br><br>
        for example in news module we just have a most viewed block that shows forever views.<br><br>
        Now with userlog module this wish is completely addressed. You have many options in this block. Check it out
        yourself and play with it to see more.<br>
        <br><br>
    </p>
    <h3><{$smarty.const._AM_USERLOG_LOGIN_REG_HISTORY}></h3>
    <p class="even">
        <{$smarty.const._AM_USERLOG_FAIL}> | <{$smarty.const._AM_USERLOG_SUCCESS}>
    </p>
    <h3><{$smarty.const._AM_USERLOG_STATS_TYPE}></h3>
    <p class="even">
        <{$smarty.const._AM_USERLOG_STATS_TYPE_DSC}>
    </p>
</div>
