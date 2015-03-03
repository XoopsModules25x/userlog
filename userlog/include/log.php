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
 * @package         userlog include
 * @since           1
 * @author          irmtfan (irmtfan@yahoo.com)
 * @author          The XOOPS Project <www.xoops.org> <www.xoops.ir>
 * @version         $Id: log.php 1 2013-02-26 16:25:04Z irmtfan $
 */

defined('XOOPS_ROOT_PATH') or die('Restricted access');
// to insure include only once
if (defined('USERLOG_LOG_DEFINED')) return;
define('USERLOG_LOG_DEFINED',true);

require_once dirname(__FILE__) . '/common.php';
$Userlog = Userlog::getInstance(false);
if (!$Userlog->getConfig("status")) return;
$logsetObj = UserlogSetting::getInstance();
$statsObj = UserlogStats::getInstance();
list($setting, $scope) = $logsetObj->getSet();

// if there is a setting
if (!empty($setting) ) {
    // check scope
    if (!empty($scope)) { // empty scope means ALL
        $scope_arr = explode(",",$scope);
        // if this URI is not in scope return
        if(!in_array($Userlog->getLogModule()->dirname(), $scope_arr)) return true;
    }
    
    // get log values
    $tolog = $logsetObj->getOptions($setting, "value");
    // check if all values are empty
    if(empty($tolog)) return true;
    
    if($Userlog->getLogModule()->dirname() == "system-root"){
        $Userlog->getLogModule()->setVar("dirname","system");
    }
    
    // create log
    $logObj = $Userlog->getHandler('log')->create();
    
    // store: 0,1->db 2->file 3->both
    $logObj->_store = !empty($tolog["store_db"]) ? $tolog["store_db"] : 0;
    if (!empty($tolog["store_file"])) {
        $logObj->_store = $logObj->_store + $tolog["store_file"] * 2;
    }

    // logger
    if (!empty($tolog["logger"])) {
        xoops_loadLanguage("logger");
        $GLOBALS['xoopsLogger']->activated = true;
        //$GLOBALS['xoopsLogger']->enableRendering();
    }
    
    // set item in db for views
    if (!empty($tolog["views"])) {
        $logObj->setItem();
        // add to save for file
        $tolog["item_name"] = $logObj->item_name();
        $tolog["item_id"] = $logObj->item_id();
    }
    
    // remove used settings that should not be logged
    unset($tolog["store_db"], $tolog["store_file"], $tolog["views"]);
    
    // store log
    $logObj->store($tolog, true);
    // update all time stats
    $statsObj->updateAll("log", $Userlog->getConfig("probstats")); // default prob = 10
}
// update all time stats
$statsObj->updateAll("log", $Userlog->getConfig("probstatsallhit")); // default prob = 1

// START to log redirects when $xoopsConfig['redirect_message_ajax'] = true
// We need to shift the position of userlog to the top of 'system_modules_active' cache file list.
// because to log redirect pages when $xoopsConfig['redirect_message_ajax'] = true IF eventCoreIncludeFunctionsRedirectheader in system module runs first it will exit()
// IMO It is a bug in XOOPS255/modules/system/preloads/core.php
// IMO exit() should be commented or we should find some way to make sure all eventCoreIncludeFunctionsRedirectheader events will run before any exit();
/*
if (!headers_sent() && isset($xoopsConfig['redirect_message_ajax']) && $xoopsConfig['redirect_message_ajax']) {
    $_SESSION['redirect_message'] = $args[2];
    header("Location: " . preg_replace("/[&]amp;/i", '&', $url));
    exit(); // IMO exit() should be commented
}
*/
if ($modules_list = XoopsCache::read('system_modules_active')) {
    $key = array_search(USERLOG_DIRNAME, $modules_list);
    // if userlog is not in the top
    if ($key != 0) {
        unset($modules_list[$key]);
        array_unshift($modules_list, USERLOG_DIRNAME);
        XoopsCache::write('system_modules_active', $modules_list);
    }
}
// END to log redirects when $xoopsConfig['redirect_message_ajax'] = true
