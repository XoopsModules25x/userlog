userlog guide

Requirements:
=========================
XOOPS 2.5.5 php 5.3 mysql 5.0

To Install
=========================
1- upload the userlog to /modules/userlog (upload the compressed file and decompressed via Cpanel is the best way to insure all files are correctly uploaded)
2- go to your admin -> system -> modules -> install
3- change the default settings to your desired in the module preferences.

Important notice: There is a new "ADDITIONAL permission in file for webmasters" addon introduced in userlog module.
if you want other webmasters dont have access to userlog module this addon is for you.
for more information go to userlog/admin/addon/perm.php
If you dont need this addon you just need to remove addon/perm.php

To Upgrade
==========================
1- Close your website. (highly recommended) be sure you be logged in.
2- Get a backup from your old userlog database.(all XOOPSPREFIX_mod_userlog_* tables)
3- Get a backup from your old XOOPSROOT/modules/userlog folder.
4- IF EXIST get a backup from your all old userlog custom templates in themes folder located in XOOPSROOT/themes/MY_THEME/modules/userlog. eg: XOOPSROOT/themes/default/modules/userlog
5- Go to userlog > preferences and set the module log status as Idle.
6- Delete your old userlog folder located in modules. (Do not rename it. Only delete will work and while you have the backup you should not worry about anything)
7- IF EXIST delete all old userlog custom templates in themes folder located in XOOPSROOT/themes/MY_THEME/modules/userlog. eg: XOOPSROOT/themes/default/modules/userlog
8- Upload the most recent version of userlog to XOOPSROOT/modules/userlog (upload the compressed file and decompressed via Cpanel is the best way to insure all files are correctly uploaded)
9- Go to your admin > system > modules > userlog -> update (important: wait until you see the report page. It is better to save this page for future review)
10- Go to system > maintenance > clear all caches
11- Change the default settings to your desired in the module preferences. Do not forget to back the module log status to Active.
12 - Do not forget to open your website again.

What you should not do in upgrade:
----------------------------------
- Do not install the most recent version and import the old database backup and try to update it in admin. (Do not do it in any other XOOPS module too otherwise it will not work correctly after upgrade)
  It will not work because XOOPS store other information from modules like the module version in some other tables like _modules table and you just import the module tables and not this other information.
- Do not rename the old userlog folder to something like userlog_old. because XOOPS system will find any folder inside modules folder and try to take it as a new module.
- Do not save your old custom template. instead try to implement your changes in templates in the new template.

To Downgrade (To Restore the old version if the upgrade goes wrong)
===================================================
1- Close your website. (highly recommended) be sure you be logged in.
2- IF YOU CAN Go to userlog > preferences and set the module log status as Idle.
3- Delete the most recent bad working userlog folder located in modules. (Do not rename it.)
4- Drop the most recent bad working userlog database. (all XOOPSPREFIX_mod_userlog_* tables)
5- IF EXIST delete all userlog custom templates in themes folder located in XOOPSROOT/themes/MY_THEME/modules/userlog. eg: XOOPSROOT/themes/default/modules/userlog
6- Upload your previous working version of userlog to XOOPSROOT/modules/userlog (upload the compressed file and decompressed via Cpanel is the best way to insure all files are correctly uploaded)
7- Import your previous working userlog database. (all XOOPSPREFIX_mod_userlog_* tables)
8- Go to your admin > system > modules > userlog -> update (important: wait until you see the report page. It is better to save this page for future review)
9- Go to system > maintenance > clear all caches
10- Change the default settings to your desired in the module preferences. Do not forget to back the module log status to Active.
11- Do not forget to open your website again.

Features:
=========================
- Log user activities and navigations.
  Examples:
  1- The possibility to list all the IPs used from a certain user, and conversely to list all the users logged from a defined IP to find duplicate users.
  2- Find deleted items from your database.
        modules/userlog/admin/logs.php?options[referer]=del&options[request_method]=POST
  3- Find admin user activities(webmasters, moderators, ...)
        modules/userlog/admin/logs.php?options[admin]=1
  4- Find users who come to your site from Google.
        modules/userlog/admin/logs.php?options[referer]=google.com
  5- Find all updated modules: (change op=update to op=install or op=uninstall to see install and uninstall activities)
        modules/userlog/admin/logs.php?options[referer]=op=update&options[module]=system&options[request_method]=POST
  6- Find all errors/notices/warnings.
        modules/userlog/admin/logs.php?options[logger]=errno

- Can log users by getting User ID, User group or visitor IP.
- Logs can be stored in file, database or both.
- Any below user information and/or page data can be selected to be logged.
[quote]
User ID,Username,Is Admin?(y/n),Groups,User Last Visit,User IP,User agent,URL (Request URI),Script name,Referer URI,Page title,Is Page admin?(y/n),Module dirname,Module name,Item name,Item ID,Request method (GET, POST, ...),$_GET,$_POST,$_REQUEST,$_FILES,$_ENV,$_SESSION,$_COOKIE,Headers list,Logger
[/quote]
- Any active module in your installation can be selected and userlog will log users activities only in those modules.
- You can navigate/delete/purge/export to CSV user logs in admin/logs.
- You can render logs from database or file source engine in admin/logs.php.
- To search for logs based on a criteria you have an advance form in admin/logs.php
- You can see/delete/rename/copy/merge/compress(zip)/export to CSV log files in admin/file.php.
- You can see total module views, total user views, total group views in admin/stats.php
- you have an advance form to see any item views using some criteria like what is the module/link/log time/viewer uid/viewer group id of the item in admin/stats.php
- by activating the views block you can set a most viewed items in a module or in the whole website in a specific period of time. e.g.: today most viewed (hot) news
- You can set the module as Active or Idle in preferences.
- If you need to store logs in a file, you can set the working path, working file size, working file name, ... in preferences.
- If you need to store logs in database, you can set the maximum logs thresholds (maximum number of logs and maximum time that logs are stored in the database) in preferences.
- Can be used as a backup/restore tool.
- Used JSON format to store arrays to database for better performance (instead of XOOPS core serialize).

Known bugs/malfunctioning:
=========================
1- userlog will not work in XOOPS255/index.php (homepage) when no module is set for start page.
 there is a bug in XOOPS255/header.php
 solution:
in XOOPS255/header.php exit() should be commented.
[code]
$xoopsPreload->triggerEvent('core.header.checkcache');
    if ($xoTheme->checkCache()) {
        $xoopsPreload->triggerEvent('core.header.cacheend');
        //exit(); // irmtfan comment this
    }
[/code]
 more information here: http://sourceforge.net/p/xoops/bugs/1261/

2- You cannot select many items in userlog > blocks > views block.
It is because of a length limitation in options field in newblocks table in XOOPS 255 and XOOPS 26.
solution:
in XOOPS255/kernel/block.php and XOOPS26/kernel/block.php
line 40:
[code]
        $this->initVar('options', XOBJ_DTYPE_TXTBOX, null, false, 255);
[/code]

with this:
[code]
[code]
        $this->initVar('options', XOBJ_DTYPE_TXTBOX, null, false, 600);
[/code]
Then go to your database and change the field options length in newblocks table to higher number than 255.

3- When the URL or The REFERER is longer than 255 characters you have a warning and log will not be stored in database but it will be stored in file.
You can see this warning:
[code]
Warning: Insert failed in method 'cleanVars' of object 'UserlogLog' in file /class/model/write.php line 280
[/code]
It is because I decide to limit URL/Referer to 255 characters because of better performance.
solution:
If you really need to save URLs with more characters than 255. do the following.
a) go to userlog/class/log.php and change the below indicate lines to your desired values.
[code]
        $this->initVar("url", XOBJ_DTYPE_TXTBOX, null, true, 500); // change this
        $this->initVar("script", XOBJ_DTYPE_TXTBOX, null, true, 50);
        $this->initVar("referer", XOBJ_DTYPE_TXTBOX, null, true, 500);// change this
[/code]
b) go to your database and change 'url' and 'referer' fields in table mod_userlog_log to your desired values.

4- If you have userlog version older than 1.15 and update to the recent version you have this warning:
[code]
Warning: Smarty error: unable to read resource: "db:userlog_block_stats_type.tpl" in file /class/smarty/Smarty.class.php line 1094
[/code]
It is a XOOPS core 2.5.6 bug. find below the bug and its solution:
https://sourceforge.net/p/xoops/bugs/1269/ block template file will not updated after update the module

5- If you test userlog in local and you be disconnected or your internet connection was low you may have this error:
[code]
Fatal error: Call to undefined function phpbrowscap\curl_init() in xoops256\modules\userlog\class\phpbrowscap\Browscap.php on line 793
[/code]
or this:
[code]
Fatal error: Maximum execution time of 30 seconds exceeded in xoops256\modules\userlog\class\phpbrowscap\Browscap.php on line 749
[/code]
userlog need php_browscap.ini to get the browser data from user agent.
You should go to:
http://browsers.garykeith.com/stream.asp?PHP_BrowsCapINI

then download php_browscap.ini and copy it to xoops_data/caches/xoops_cache/browscap
