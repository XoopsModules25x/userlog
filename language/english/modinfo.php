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
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         userlog language
 * @since           1
 * @author          irmtfan (irmtfan@yahoo.com)
 * @author          The XOOPS Project <www.xoops.org> <www.xoops.ir>
 * @version         $Id: modinfo.php 1 2013-02-26 16:25:04Z irmtfan $
 */
// The name of this module
define('_MI_USERLOG_NAME', "Userlog");
// A brief description of this module
define('_MI_USERLOG_DSC', "Log user/visitor activities and navigations");
// configs
// config status
define('_MI_USERLOG_STATUS', "Log status:");
define('_MI_USERLOG_STATUS_DSC', "Active: Module will do its job. Idle: The module will not log anything");
define('_MI_USERLOG_ACTIVE', "Active");
define('_MI_USERLOG_IDLE', "Idle");
define('_MI_USERLOG_POSTLOG', "Log any POST method?");
define('_MI_USERLOG_POSTLOG_DSC', "In some weird situations (e.g.: send a pm, custom forms, hacks, ported modules like mediawiki, ...), there is no redirect message after submit. If you want to log any POST method regardless of redirect message set it to yes.");
// config categories
define('_MI_USERLOG_CONFCAT_LOGFILE', "Log file settings");
define('_MI_USERLOG_CONFCAT_LOGFILE_DSC', "Log file (Set it if you need to store logs in a file, otherwise ignore it)");
define('_MI_USERLOG_CONFCAT_FORMAT', "Format");
define('_MI_USERLOG_CONFCAT_FORMAT_DSC', "Format settings");
define('_MI_USERLOG_CONFCAT_PAGENAV', "Page navigation");
define('_MI_USERLOG_CONFCAT_PAGENAV_DSC', "Page navigation settings");
define('_MI_USERLOG_CONFCAT_LOGDB', "Log database settings");
define('_MI_USERLOG_CONFCAT_LOGDB_DSC', "Log database (Set it if you need to store logs in database, otherwise ignore it)");
define('_MI_USERLOG_CONFCAT_PROB', "Probability settings");
define('_MI_USERLOG_CONFCAT_PROB_DSC', "Probability to work on database (These default numbers are recommended for a high traffic website, e.g.: more than 30,000 hits per day).");

// config logfile
define('_MI_USERLOG_MAXLOGFILESIZE', "Maximum file size for current working Log file (in bytes)");
define('_MI_USERLOG_MAXLOGFILESIZE_DSC', "Advise: Set it below 1MB because some servers set limitations for viewing large files in CPanel.");
define('_MI_USERLOG_LOGFILEPATH', "Log file full path");
define('_MI_USERLOG_LOGFILEPATH_DSC', "Advise: a path outside wwwroot is safe from browsing by everybody");
define('_MI_USERLOG_LOGFILENAME', "Current working Log file name");
define('_MI_USERLOG_LOGFILENAME_DSC', "Older Log files will be stored with this prefix: Log_file_name_date('Y-m-d_H-i-s').log");
// config format
define('_MI_USERLOG_DATEFORMAT', "Date format");
define('_MI_USERLOG_DATEFORMAT_DSC', "If you leave it empty, this module will use Core default");
define('_MI_USERLOG_DATEFORMAT_HISTORY', "Date format for historical times, e.g.: 1 day ago");
// config pagenav
define('_MI_USERLOG_SETS_PERPAGE', "Number of settings per page");
define('_MI_USERLOG_SETS_PERPAGE_DSC', "The default value for viewing settings");
define('_MI_USERLOG_LOGS_PERPAGE', "Number of logs per page");
define('_MI_USERLOG_LOGS_PERPAGE_DSC', "The default value for viewing logs");
define('_MI_USERLOG_ENGINE', "Select the default engine for browsing logs");
define('_MI_USERLOG_ENGINE_DSC', "This will be the default engine in logs browsing.");
define('_MI_USERLOG_FILE', "Select the default files for browsing logs");
define('_MI_USERLOG_FILE_DSC', "This will be the default files in logs browsing.");
// config logdb
define('_MI_USERLOG_MAXLOGS', "Maximum logs stored in database");
define('_MI_USERLOG_MAXLOGS_DSC', "Logs will be deleted from database after reaching this number");
define('_MI_USERLOG_MAXLOGSPERIOD', "Maximum time that logs are stored in the database. 0 = store forever");
define('_MI_USERLOG_MAXLOGSPERIOD_DSC', "Logs older than this period will be deleted from database. Positive for days and negative for hours. Advise: use a large number");
// config probability
define('_MI_USERLOG_PROBSET', "Probability to check database for a match setting");
define('_MI_USERLOG_PROBSET_DSC', "Probability percentage to check database ONLY if it didnt find any setting in cache files for the current visitor/user. 20 means in one of each 5 hits it will check.");
define('_MI_USERLOG_PROBSTATS', "Probability to update statistics in database when the visitor is logged.");
define('_MI_USERLOG_PROBSTATS_DSC', "Probability percentage to update database logs. 10 means in 1 of each 10 visits of a visitor who have a match setting, it will update statistics. 0 means no update so you should manually update by visiting userlog > admin > home.");
define('_MI_USERLOG_PROBSTATSALLHIT', "Probability to update statistics in database in each hit.");
define('_MI_USERLOG_PROBSTATSALLHIT_DSC', "Probability percentage to update database logs. 1 means in 1 of each 100 hits it will update statistics.0 means no update so you should manually update by visiting userlog > admin > home. Advise: set a low percentage based on your website traffic.");
// blocks
define('_MI_USERLOG_BLOCK_VIEWS', "All views in site");
define('_MI_USERLOG_BLOCK_VIEWS_DSC', "Show views in the whole site");
// webmaster permission
define('_MI_USERLOG_WEBMASTER_NOPERM', "You are one of the webmasters but don't have permission to access this area. Please contact webmasters with %1\$s UID or webmasters belong to %2\$s admin groups for more information.");