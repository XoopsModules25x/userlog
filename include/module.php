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
 * @version         $Id: module.php 1 2013-02-26 16:25:04Z irmtfan $
 */
defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");
require_once dirname(__FILE__) . '/common.php';
function xoops_module_uninstall_userlog(&$module)
{
	$logsetObj = UserlogSetting::getInstance();
	return $logsetObj->cleanCache(); // delete all settings caches
}
function xoops_module_update_userlog(&$module, $prev_version = null)
{
	if ($prev_version == round( $module->getInfo("version") * 100, 2 )) {
		$module->setErrors("You have the latest " . $module->getInfo("name") . " module (". $module->getInfo("dirname") . " version " . $module->getInfo("version") . ") and update is not necessary"); 
		print_r($module->getErrors());
		return true;
	}
	$ret = true;
	// first db update
	if ($prev_version == 100) {
		$ret = update_userlog_v100($module);
	}
	if ($prev_version < 114) {
		$ret = update_userlog_v114($module);
	}
	if ($prev_version < 115) {
		$ret = update_userlog_v115($module);
	}
	$errors = $module->getErrors();
	if(!empty($errors)) print_r($errors);
	return $ret;
}

// update database from v1 to 1.01 (Beta1 to RC1)
// module_name VARCHAR(25) change to VARCHAR(50)
function update_userlog_v100(&$module)
{
	$field = "module_name";
	$Userlog = Userlog::getInstance();
	$ret = $Userlog->getHandler('log')->showFields($field);
	preg_match_all('!\d+!', $ret[$field]["Type"], $nums);
	// only change if module_name Type was VARCHAR(25)
	if($nums[0][0] == 25) {
		$ret2 = $Userlog->getHandler('log')->changeField($field, "VARCHAR(50) NOT NULL default ''");
	} else {
		$ret2 = true;	
		$module->setErrors("Your table field ({$field}) with size {$ret[$field]['Type']} dont need change.");
	}
	return $ret2;
}
// add ",active,inside,outside,unset_pass" to all settings
function update_userlog_v114(&$module)
{
	$Userlog = Userlog::getInstance();
	$logsetsObj = $Userlog->getHandler('setting')->getAll();
	$ret = true;	
	foreach($logsetsObj as $setObj) {
		if(strpos($setObj->getVar("options"), "active")) continue;
		$setObj->setVar("options", $setObj->getVar("options") . ",active,inside,outside,unset_pass");
		if (!$setObj->storeSet(true)) {
			$ret = false;	
			$module->setErrors(_AM_USERLOG_SET_ERROR . " id=" . $setObj->set_id() . " options=" . $setObj->options());
		}
	}
	return $ret;
}

function update_userlog_v115(&$module)
{
	$Userlog = Userlog::getInstance();
	// Only change the field from INDEX to UNIQUE if it is not unique
	// if (isset($indexArr[0]["Non_unique"]) || $indexArr[0]["Non_unique"] == 1) { }
	// change the index in _stats table
	if(!$ret = $Userlog->getHandler('stats')->changeIndex("stats_type_link_period", array("stats_type", "stats_link", "stats_period"), "UNIQUE")) {
		$module->setErrors("'stats_type_link_period' index is not changed to unique. Warning: do not use module until you change this index to unique.");
	}
	// drop the index in _log table
	if(!$ret = $Userlog->getHandler('log')->dropIndex("log_id_uid")) {
		$module->setErrors("'log_id_uid' index is not dropped.");
	}
	// add the index in _log table
	if(!$ret = $Userlog->getHandler('log')->addIndex("log_time", array("log_time"))) {
		$module->setErrors("'log_time' index is not added.");
	}
	return $ret;
}