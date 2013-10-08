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
	$ret = false;
	// first db update
	if ($prev_version == 100) {
		$ret = update_userlog_v100($module);
	}
	print_r($module->getErrors());
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
		$ret2 = $Userlog->getHandler('log')->changeField($field, "VARCHAR(50)");
	} else {
		$ret2 = true;	
		$module->setErrors("Your table field ({$field}) with size {$ret[$field]['Type']} dont need change.");
	}
	return $ret2;
}