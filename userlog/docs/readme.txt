userlog guide

Requirements:
=========================
XOOPS 2.5.5 php 5.3 mysql 5.0

Features:
=========================
- Log user activities and navigations.
  Examples:
  1- The possibility to list all the IPs used from a certain user, and conversely to list all the users logged from a defined IP to find duplicate users.
  2- Find deleted items from your database.
  3- Find admin user activities(webmasters, moderators, ...)
  4- Find users who come to your site from Google.
  
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
- Used JSON format to store arrays to database for better performance (instead of xoops core serialize).


To Install
=========================
1- upload the userlog to /modules/userlog (upload the compressed file and decompressed via Cpanel is the best way to insure all files are correctly uploaded)
2- go to your admin -> system -> modules -> install
3- change the default settings to your desired in the module preferences.

Important notice: There is a new "ADDITIONAL permission in file for webmasters" addon introduced in userlog module.
if you want other webmasters dont have access to userlog module this addon is for you.
for more information go to userlog/admin/addon/perm.php
If you dont need this addon you just need to remove addon/perm.php

known bugs/malfunctioning in userlog module:
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
It is because of a length limitation in options field in newblocks table in xoops 255 and xoops 26.
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