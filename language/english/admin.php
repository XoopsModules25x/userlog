<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/
/**
 *  userlog module
 *
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         userlog language
 * @since           1
 * @author          irmtfan (irmtfan@yahoo.com)
 * @author          XOOPS Project <www.xoops.org> <www.xoops.ir>
 */
// admin menus
define('_AM_USERLOG_ADMENU_INDEX', 'Home');
define('_AM_USERLOG_ADMENU_SETTING', 'Setting');
define('_AM_USERLOG_ADMENU_LOGS', 'Logs');
define('_AM_USERLOG_ADMENU_FILE', 'File manager');
define('_AM_USERLOG_ADMENU_STATS', 'Statistics');
define('_AM_USERLOG_ABOUT', 'About');
// general error
define('_AM_USERLOG_ERROR', 'An error occurred during the operation. %1$s');
// setting.php loglog object options
define('_AM_USERLOG_LOG_ID', 'Log ID');
define('_AM_USERLOG_LOG_ID_DSC', 'Log ID in the database');
define('_AM_USERLOG_LOG_TIME', 'Log time');
define('_AM_USERLOG_LOG_TIME_DSC', 'Time of log record');
define('_AM_USERLOG_UID', 'User ID');
define('_AM_USERLOG_UID_DSC', 'User ID number');
define('_AM_USERLOG_ADMIN', 'Is Admin?(y/n)');
define('_AM_USERLOG_ADMIN_DSC', 'True if user is Admin in all or any section of your website that have been logged, e.g.: webmasters, moderator in forums.');
define('_AM_USERLOG_PAGEADMIN', 'Is Page admin?(y/n)');
define('_AM_USERLOG_PAGEADMIN_DSC', 'True if page is in admin side.');
define('_AM_USERLOG_GROUPS', 'Groups');
define('_AM_USERLOG_GROUPS_DSC', 'All Groups');
define('_AM_USERLOG_UNAME', 'Username');
define('_AM_USERLOG_UNAME_DSC', 'Username in database');
define('_AM_USERLOG_LAST_LOGIN', 'User Last Visit');
define('_AM_USERLOG_LAST_LOGIN_DSC', 'User Last Visit in the website');
define('_AM_USERLOG_USER_IP', 'User IP');
define('_AM_USERLOG_USER_AGENT', 'User agent');
define('_AM_USERLOG_URL', 'URL (Request URI)');
define('_AM_USERLOG_SCRIPT', 'Script name');
define('_AM_USERLOG_SCRIPT_DSC', 'Script name, e.g.: in your homepage it is index.php.');
define('_AM_USERLOG_REFERER', 'Referer URI');
define('_AM_USERLOG_PAGETITLE', 'Page title');
define('_AM_USERLOG_MODULE', 'Module dirname');
define('_AM_USERLOG_MODULE_NAME', 'Module name');
define('_AM_USERLOG_ITEM_NAME', 'Item name');
define('_AM_USERLOG_ITEM_ID', 'Item ID');
define('_AM_USERLOG_REQUEST_METHOD', 'Request method (GET, POST, ...)');
define('_AM_USERLOG_GET', '$_GET');
define('_AM_USERLOG_POST', '$_POST');
define('_AM_USERLOG_REQUEST', '$_REQUEST');
define('_AM_USERLOG_FILES', '$_FILES');
define('_AM_USERLOG_ENV', '$_ENV');
define('_AM_USERLOG_SESSION', '$_SESSION');
define('_AM_USERLOG_COOKIE', '$_COOKIE');
define('_AM_USERLOG_HEADER', 'Headers list');
define('_AM_USERLOG_LOGGER', 'Logger');
define('_AM_USERLOG_SET_ACTIVE', 'Setting is active?');
define('_AM_USERLOG_INSIDE', 'Log visitors come from inside your site?');
define('_AM_USERLOG_OUTSIDE', 'Log visitors come from outside your site?');
define('_AM_USERLOG_UNSET_PASS', 'Do not log passwords?');
define('_AM_USERLOG_STORE_FILE', 'Store logs in file?');
define('_AM_USERLOG_STORE_DB', 'Store logs in database?');
define('_AM_USERLOG_VIEWS', 'Log user views?');
// setting.php logset object
define('_AM_USERLOG_SET_ADD', 'Add a setting');
define('_AM_USERLOG_SET_ID', 'ID');
define('_AM_USERLOG_SET_NAME', 'Setting name');
define('_AM_USERLOG_SET_NAME_DSC', 'Type a name for this setting in your own language');
define('_AM_USERLOG_SET_LOGBY', 'Log by');
define('_AM_USERLOG_SET_LOGBY_DSC', "Log user activities by fetching this value from users table in database? priority: IF exist uid log it ELSEIF exist gid log it ELSEIf exist ip log it ELSE if Unique id = 0 log all users ELSE don't log");
define('_AM_USERLOG_SET_UNIQUE_ID', 'Unique ID');
define('_AM_USERLOG_SET_UNIQUE_ID_DSC', 'Unique id, e.g.: uid=1, gid=3 (anonymous), ip=66.249.66.1, 0=all users');
define('_AM_USERLOG_SET_GID', 'Group ID');
define('_AM_USERLOG_SET_IP', 'Visitor IP');
define('_AM_USERLOG_SET_OPTIONS', 'Options');
define(
    '_AM_USERLOG_SET_OPTIONS_DSC',
       'Log which user/page data? Notice: selecting no option means all options. Selecting no store option (File and/or Database) means Database. Selecting views means store uid, groups, script name, pagetitle, pageadmin, module dirname, module name, item name, item id in Database'
);
define('_AM_USERLOG_SET_SCOPE', 'Log scope');
define('_AM_USERLOG_SET_SCOPE_DSC', 'Log users activities in which modules? Selecting nothing means whole website');
// setting.php add/edit
define('_AM_USERLOG_SET_ERROR', "Error. You've entered wrong data!");
define('_AM_USERLOG_SET_CREATE', 'Setting %1$s created successfully.');
define('_AM_USERLOG_SET_EDIT', 'Setting %1$s edited successfully.');
define('_AM_USERLOG_SET_UPDATE', 'Caution! Your new submitted setting is not created because setting %1$s has been in database with the same Log by and Unique ID. But it updated with your new options successfully.');
define('_AM_USERLOG_SET_CANCEL', 'Cancel');
// setting.php delete
define('_AM_USERLOG_SET_DELETE_CONFIRM', 'Are you sure to delete setting %1$s?');
define('_AM_USERLOG_SET_DELETE_ERROR', 'Cannot delete setting %1$s');
define('_AM_USERLOG_SET_DELETE_SUCCESS', 'Setting %1$s deleted successfully.');
// setting.php create/clean cache
define('_AM_USERLOG_SET_CACHE', 'Appropriate cached setting file created/edited successfully.');
define('_AM_USERLOG_SET_CLEANCACHE', 'Appropriate cached setting file deleted successfully.');
define('_AM_USERLOG_SET_CLEANCACHE_ALL', 'Delete all cached setting files?');
define('_AM_USERLOG_SET_CLEANCACHE_SUCCESS', '%1$d cached setting files deleted successfully.');
define('_AM_USERLOG_SET_CLEANCACHE_NOFILE', 'No cached setting file is exist to delete.');
// logs.php form
define('_AM_USERLOG_LOGFORM', 'Enter data and push enter or click on submit button to show logs');
define('_AM_USERLOG_LOGS_PERPAGE', 'Number of logs per page');
define('_AM_USERLOG_LOGS_PERPAGE_DSC', 'The default value is %1$s defined in Preferences');
define('_AM_USERLOG_SORT', 'Sort by');
define('_AM_USERLOG_SORT_DSC', 'Select one field to sort logs based on that field');
define('_AM_USERLOG_ORDER', 'Order by');
define('_AM_USERLOG_ORDER_DSC', 'Ascending or Descending order');
// for all INT logs use these definition
define('_AM_USERLOG_INTGT', '%1$s greater than');
define('_AM_USERLOG_INTLT', '%1$s lower than');
define('_AM_USERLOG_TIMEGT', '%1$s since');
define('_AM_USERLOG_TIMELT', '%1$s until');
define('_AM_USERLOG_INT_FORM', "Enter one '%1\$s' or several '%2\$s's separated with comma to show logs based on that, e.g.: 23,32,12.");
define('_AM_USERLOG_INTGT_FORM', "Enter one '%1\$s' to show all '%2\$s's greater than it.");
define('_AM_USERLOG_INTLT_FORM', "Enter one '%1\$s' to show all '%2\$s's lower than it.");
define('_AM_USERLOG_TIMEGT_FORM', "Enter Time of '%1\$s' to show all logs since that time. Positive for days and negative for hours, e.g.: 1 means since one day ago.");
define('_AM_USERLOG_TIMELT_FORM', "Enter Time of '%1\$s' to show all logs until that time. Positive for days and negative for hours, e.g.: 1 means until one day ago.");
// Translators: do not touch below for now
// START DO NOT TOUCH
define('_AM_USERLOG_LOG_ID_FORM', _AM_USERLOG_INT_FORM);
define('_AM_USERLOG_LOG_IDGT', sprintf(_AM_USERLOG_INTGT, _AM_USERLOG_LOG_ID));
define('_AM_USERLOG_LOG_IDGT_FORM', _AM_USERLOG_INTGT_FORM);
define('_AM_USERLOG_LOG_IDLT', sprintf(_AM_USERLOG_INTLT, _AM_USERLOG_LOG_ID));
define('_AM_USERLOG_LOG_IDLT_FORM', _AM_USERLOG_INTLT_FORM);
define('_AM_USERLOG_LOG_TIMEGT', sprintf(_AM_USERLOG_TIMEGT, _AM_USERLOG_LOG_TIME));
define('_AM_USERLOG_LOG_TIMEGT_FORM', sprintf(_AM_USERLOG_TIMEGT_FORM, _AM_USERLOG_LOG_TIME));
define('_AM_USERLOG_LOG_TIMELT', sprintf(_AM_USERLOG_TIMELT, _AM_USERLOG_LOG_TIME));
define('_AM_USERLOG_LOG_TIMELT_FORM', sprintf(_AM_USERLOG_TIMELT_FORM, _AM_USERLOG_LOG_TIME));
define('_AM_USERLOG_UID_FORM', _AM_USERLOG_INT_FORM);
define('_AM_USERLOG_UIDGT', sprintf(_AM_USERLOG_INTGT, _AM_USERLOG_UID));
define('_AM_USERLOG_UIDGT_FORM', _AM_USERLOG_INTGT_FORM);
define('_AM_USERLOG_UIDLT', sprintf(_AM_USERLOG_INTLT, _AM_USERLOG_UID));
define('_AM_USERLOG_UIDLT_FORM', _AM_USERLOG_INTLT_FORM);
define('_AM_USERLOG_LAST_LOGINGT', sprintf(_AM_USERLOG_TIMEGT, _AM_USERLOG_LAST_LOGIN));
define('_AM_USERLOG_LAST_LOGINGT_FORM', sprintf(_AM_USERLOG_TIMEGT_FORM, _AM_USERLOG_LAST_LOGIN));
define('_AM_USERLOG_LAST_LOGINLT', sprintf(_AM_USERLOG_TIMELT, _AM_USERLOG_LAST_LOGIN));
define('_AM_USERLOG_LAST_LOGINLT_FORM', sprintf(_AM_USERLOG_TIMELT_FORM, _AM_USERLOG_LAST_LOGIN));
define('_AM_USERLOG_ITEM_ID_FORM', _AM_USERLOG_INT_FORM);
define('_AM_USERLOG_ITEM_IDGT', sprintf(_AM_USERLOG_INTGT, _AM_USERLOG_ITEM_ID));
define('_AM_USERLOG_ITEM_IDGT_FORM', _AM_USERLOG_INTGT_FORM);
define('_AM_USERLOG_ITEM_IDLT', sprintf(_AM_USERLOG_INTLT, _AM_USERLOG_ITEM_ID));
define('_AM_USERLOG_ITEM_IDLT_FORM', _AM_USERLOG_INTLT_FORM);
// END DO NOT TOUCH
define('_AM_USERLOG_ADMIN_FORM', 'Select "Yes" to show all logs from Admins');
define('_AM_USERLOG_PAGEADMIN_FORM', 'Select Yes to show all pages in admin side of modules');
define(
    '_AM_USERLOG_GROUPS_FORM',
       "Enter one group with 'g' prefix (or several groups separated with comma) to show logs for all users belonging to those groups. Enter '!g' prefix to show logs for users not in those groups, e.g.: g1, g2, !g4 means all users belonging to group 1 OR group 2 AND not belong to group 4."
);
// for all other text logs use just one definition
define('_AM_USERLOG_TEXT_FORM', "Enter one exact '%1\$s' or part of '%2\$s' to show all logs for users have that (with prefix '!' have not that). You can enter several separated with comma, e.g.: TERM1, !TERM2, TERM3 means all logs have TERM1 and TERM3, but have not TERM2.");
// Translators: do not touch below for now
// START DO NOT TOUCH
define('_AM_USERLOG_UNAME_FORM', _AM_USERLOG_TEXT_FORM);
define('_AM_USERLOG_USER_IP_FORM', _AM_USERLOG_TEXT_FORM);
define('_AM_USERLOG_USER_AGENT_FORM', _AM_USERLOG_TEXT_FORM);
define('_AM_USERLOG_URL_FORM', _AM_USERLOG_TEXT_FORM);
define('_AM_USERLOG_SCRIPT_FORM', _AM_USERLOG_TEXT_FORM);
define('_AM_USERLOG_REFERER_FORM', _AM_USERLOG_TEXT_FORM);
define('_AM_USERLOG_PAGETITLE_FORM', _AM_USERLOG_TEXT_FORM);
define('_AM_USERLOG_MODULE_FORM', _AM_USERLOG_TEXT_FORM);
define('_AM_USERLOG_MODULE_NAME_FORM', _AM_USERLOG_TEXT_FORM);
define('_AM_USERLOG_ITEM_NAME_FORM', _AM_USERLOG_TEXT_FORM);
define('_AM_USERLOG_REQUEST_METHOD_FORM', _AM_USERLOG_TEXT_FORM);
define('_AM_USERLOG_GET_FORM', _AM_USERLOG_TEXT_FORM);
define('_AM_USERLOG_POST_FORM', _AM_USERLOG_TEXT_FORM);
define('_AM_USERLOG_REQUEST_FORM', _AM_USERLOG_TEXT_FORM);
define('_AM_USERLOG_FILES_FORM', _AM_USERLOG_TEXT_FORM);
define('_AM_USERLOG_ENV_FORM', _AM_USERLOG_TEXT_FORM);
define('_AM_USERLOG_SESSION_FORM', _AM_USERLOG_TEXT_FORM);
define('_AM_USERLOG_COOKIE_FORM', _AM_USERLOG_TEXT_FORM);
define('_AM_USERLOG_HEADER_FORM', _AM_USERLOG_TEXT_FORM);
define('_AM_USERLOG_LOGGER_FORM', _AM_USERLOG_TEXT_FORM);
// END DO NOT TOUCH
// logs.php engine/file
define('_AM_USERLOG_ENGINE', 'Engine');
define('_AM_USERLOG_ENGINE_DSC', 'Select the engine you want to get logs from.');
define('_AM_USERLOG_ENGINE_FILE', 'FILE');
define('_AM_USERLOG_ENGINE_DB', 'Database');
define('_AM_USERLOG_FILE', 'Files');
define('_AM_USERLOG_FILE_DSC', 'Select files you want to get logs from.');
define('_AM_USERLOG_FILE_WORKING', 'Working file');
// logs.php error
define('_AM_USERLOG_LOG_ERROR', 'No Log is found with this criteria.');
define('_AM_USERLOG_LOG_STATUS', '%1$s logs are found.');
define('_AM_USERLOG_LOG_PAGE', 'Pages');
// logs.php delete
define('_AM_USERLOG_LOG_DELETE_SELECT', 'Delete selected logs in the current page.');
define('_AM_USERLOG_LOG_PURGE_ALL', 'Purge all rendered logs in all pages?');
define('_AM_USERLOG_LOG_DELETE_CONFIRM', 'Are you sure you want to delete logs? Logs will permanently be deleted from database.');
define('_AM_USERLOG_LOG_DELETE_SUCCESS', '%1$d logs deleted successfully.');
define('_AM_USERLOG_LOG_DELETE_SUCCESS_QUERY', "%1\$d logs deleted successfully with '%2\$s' query.");
define('_AM_USERLOG_LOG_DELETE_ERROR', 'Error. You input an invalid criteria for delete.');
define('_AM_USERLOG_LOG_DELETE_ERRORSELECT', 'You select nothing to delete.');
// logs.php select
define('_AM_USERLOG_LOG_SELECT', 'Select action to operate in the current page.');
define('_AM_USERLOG_LOG_SELECT_BULK', 'Select action to operate in all pages.');
define('_AM_USERLOG_LOG_ERRORSELECT', 'You select nothing.');
// logs.php export
define('_AM_USERLOG_LOG_EXPORT_CSV_SELECT', 'Export selected logs in the current page to csv.');
define('_AM_USERLOG_LOG_EXPORT_CSV_ALL', 'Export all rendered logs in all pages to csv?');
define('_AM_USERLOG_LOG_EXPORT_SUCCESS', '%1$d logs exported successfully to csv file. Csv file: %2$s');
define('_AM_USERLOG_LOG_EXPORT_ERROR', 'Error. You input an invalid criteria for export.');
// logs.php template
define('_AM_USERLOG_SHOW_FORM', 'Show head form');
define('_AM_USERLOG_HIDE_FORM', 'Hide head form');
define('_AM_USERLOG_UP', 'Go up');
define('_AM_USERLOG_DOWN', 'Go down');
// views block
define('_AM_USERLOG_VIEW_ALL', 'All views');
define('_AM_USERLOG_VIEW_MODULE', 'Module views');
define('_AM_USERLOG_VIEW_USER', 'User views');
define('_AM_USERLOG_VIEW_GROUP', 'Group views');
define('_AM_USERLOG_VIEW', 'Views');
// index.php stats.php
define('_AM_USERLOG_STATS_ABSTRACT', 'Statistics Overview');
define('_AM_USERLOG_STATS_TYPE_PERIOD', 'There are %1$s %2$s exist in %3$s');
// %2\$s for above
define('_AM_USERLOG_STATS_LOG', 'Logs');
define('_AM_USERLOG_STATS_LOGDEL', 'Deleted logs');
define('_AM_USERLOG_STATS_SET', 'Sets');
define('_AM_USERLOG_STATS_FILE', 'Files');
define('_AM_USERLOG_STATS_FILEALL', 'Files in all paths');
define('_AM_USERLOG_STATS_VIEWS', 'Views');
define('_AM_USERLOG_STATS_REFERRAL', 'Referrals');
define('_AM_USERLOG_STATS_BROWSER', 'Browser');
define('_AM_USERLOG_STATS_OS', 'Operating System');
define('_AM_USERLOG_STATS_TIME_UPDATE', 'Last update time:');
define('_AM_USERLOG_STATS_TYPE', 'Types to get stats');
define('_AM_USERLOG_STATS_TYPE_DSC', _AM_USERLOG_STATS_REFERRAL . ' | ' . _AM_USERLOG_STATS_BROWSER . ' | ' . _AM_USERLOG_STATS_OS);
// %3\$s for above
define('_AM_USERLOG_ALL', 'All Times');
define('_AM_USERLOG_TODAY', 'Today');
define('_AM_USERLOG_WEEK', 'This week');
define('_AM_USERLOG_MONTH', 'This Month');
// index.php summary
define('_AM_USERLOG_SUMMARY', 'Some examples of criteria you can use to get logs');
define('_AM_USERLOG_SUMMARY_DELETED', 'Deleted items from your database');
define('_AM_USERLOG_SUMMARY_ADMIN', 'Admin user activities');
define('_AM_USERLOG_SUMMARY_GOOGLE', 'Users who come to your site from Google.');
// file.php
define('_AM_USERLOG_FILE_ACTION', 'Select one action');
define('_AM_USERLOG_FILE_SELECT_ONE', 'You must select one file.');
define('_AM_USERLOG_FILE_RENAME', 'Rename');
define('_AM_USERLOG_FILE_COPY', 'Copy');
define('_AM_USERLOG_FILE_ZIP', 'Compress (Zip)');
define('_AM_USERLOG_FILE_MERGE', 'Merge');
define('_AM_USERLOG_FILE_EXPORT_CSV', 'Export to CSV');
define('_AM_USERLOG_FILE_FILENAME', 'Enter the result file name, e.g.: myfile');
define('_AM_USERLOG_FILE_FILENAME_DSC', 'Advise: Leave it empty to auto generate by using the current criteria and date. If you enter an already existed file name for zip file, new files will be added/overwritten in old archive file.');
define('_AM_USERLOG_FILE_MERGE_SUCCESS', '%1$d files merged successfully into %2$s');
define('_AM_USERLOG_FILE_DELETE_SUCCESS', '%1$d files deleted successfully.');
define('_AM_USERLOG_FILE_RENAME_SUCCESS', 'file %1$s renamed successfully into %2$s');
define('_AM_USERLOG_FILE_COPY_SUCCESS', 'file %1$s copied successfully into %2$s');
define('_AM_USERLOG_FILE_ZIP_SUCCESS', '%1$d files zipped successfully into %2$s');
define('_AM_USERLOG_FILE_EXOPORT_SUCCESS', '%1$d files exported successfully into %2$s');
define('_AM_USERLOG_FILE_CONFIRM', 'Are you sure you want to do this action?');
// stats.php, views block
define('_AM_USERLOG_MODULES', 'Select modules');
define('_AM_USERLOG_ITEMS', 'Select items');
define('_AM_USERLOG_ITEMS_DSC', 'These are typical links of your active modules.');
define('_AM_USERLOG_ITEMS_NUM', 'Number of items to display.');
define('_AM_USERLOG_CONFIG_CHMOD_ERROR', "Could not create any folder inside '%1\$s' because its chmod is under %2\$d.");
define('_AM_USERLOG_CONFIG_CREATE_FOLDER', "If you need to store logs in file, you should create folder '%1\$s' and set chmod = %2\$d manually using Cpanel.");
define('_AM_USERLOG_LOGIN_REG_HISTORY', 'Login/Register History');
// stats.php, login_reg_history block
define('_AM_USERLOG_FAIL', 'failed');
define('_AM_USERLOG_SUCCESS', 'successful');

//1.17
define('_AM_USERLOG_UPGRADEFAILED0', "Update failed - couldn't rename field '%s'");
define('_AM_USERLOG_UPGRADEFAILED1', "Update failed - couldn't add new fields");
define('_AM_USERLOG_UPGRADEFAILED2', "Update failed - couldn't rename table '%s'");
define('_AM_USERLOG_ERROR_COLUMN', 'Could not create column in database : %s');
define('_AM_USERLOG_ERROR_BAD_XOOPS', 'This module requires XOOPS %s+ (%s installed)');
define('_AM_USERLOG_ERROR_BAD_PHP', 'This module requires PHP version %s+ (%s installed)');
define('_AM_USERLOG_ERROR_TAG_REMOVAL', 'Could not remove tags from Tag Module');
