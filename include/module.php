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
 * @copyright       XOOPS Project (http://xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         userlog include
 * @since           1
 * @author          irmtfan (irmtfan@yahoo.com)
 * @author          XOOPS Project <www.xoops.org> <www.xoops.ir>
 */
defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');
require_once __DIR__ . '/common.php';
/**
 * @param XoopsModule $module
 *
 * @return int
 */
function xoops_module_uninstall_userlog(XoopsModule $module)
{
    $logsetObj = UserlogSetting::getInstance();

    return $logsetObj->cleanCache(); // delete all settings caches
}

/**
 * @param XoopsModule $module
 * @param null $prev_version
 *
 * @return bool|mixed
 */

function xoops_module_update_userlog(XoopsModule $module, $prev_version = null)
{
    if ($prev_version == round($module->getInfo('version') * 100, 2)) {
        $module->setErrors('You have the latest ' . $module->getInfo('name') . ' module (' . $module->getInfo('dirname') . ' version ' . $module->getInfo('version') . ') and update is not necessary');
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
    if ($prev_version < 116) {
        $ret = update_userlog_v116($module);
    }
    $errors = $module->getErrors();
    if (!empty($errors)) {
        print_r($errors);
    }

    return $ret;
}

// update database from v1 to 1.01 (Beta1 to RC1)
// module_name VARCHAR(25) change to VARCHAR(50)
/**
 * @param XoopsModule $module
 *
 * @return bool
 */
function update_userlog_v100(XoopsModule $module)
{
    $field   = 'module_name';
    $Userlog = Userlog::getInstance();
    $ret     = $Userlog->getHandler('log')->showFields($field);
    preg_match_all('!\d+!', $ret[$field]['Type'], $nums);
    // only change if module_name Type was VARCHAR(25)
    if ($nums[0][0] == 25) {
        $ret2 = $Userlog->getHandler('log')->changeField($field, "VARCHAR(50) NOT NULL default ''");
    } else {
        $ret2 = true;
        $module->setErrors("Your table field ({$field}) with size {$ret[$field]['Type']} don't need change.");
    }

    return $ret2;
}

// add ",active,inside,outside,unset_pass" to all settings
/**
 * @param XoopsModule $module
 *
 * @return bool
 */
function update_userlog_v114(XoopsModule $module)
{
    $Userlog    = Userlog::getInstance();
    $logsetsObj = $Userlog->getHandler('setting')->getAll();
    $ret        = true;
    foreach ($logsetsObj as $setObj) {
        if (strpos($setObj->getVar('options'), 'active')) {
            continue;
        }
        $setObj->setVar('options', $setObj->getVar('options') . ',active,inside,outside,unset_pass');
        if (!$setObj->storeSet(true)) {
            $ret = false;
            $module->setErrors(_AM_USERLOG_SET_ERROR . ' id=' . $setObj->set_id() . ' options=' . $setObj->options());
        }
    }

    return $ret;
}

/**
 * @param XoopsModule $module
 *
 * @return mixed
 */
function update_userlog_v115(XoopsModule $module)
{
    $Userlog = Userlog::getInstance();
    // Only change the field from INDEX to UNIQUE if it is not unique
    // if (isset($indexArr[0]["Non_unique"]) || $indexArr[0]["Non_unique"] == 1) { }
    // change the index in _stats table
    if (!$ret = $Userlog->getHandler('stats')->changeIndex('stats_type_link_period', array('stats_type', 'stats_link', 'stats_period'), 'UNIQUE')) {
        $module->setErrors("'stats_type_link_period' index is not changed to unique. Warning: do not use module until you change this index to unique.");
    }
    // drop the index in _log table
    if (!$ret = $Userlog->getHandler('log')->dropIndex('log_id_uid')) {
        $module->setErrors("'log_id_uid' index is not dropped.");
    }
    // add the index in _log table
    if (!$ret = $Userlog->getHandler('log')->addIndex('log_time', array('log_time'))) {
        $module->setErrors("'log_time' index is not added.");
    }

    return $ret;
}

/**
 * @param XoopsModule $module
 *
 * @return bool
 */
function update_userlog_v116(XoopsModule $module)
{
    // remove old html template files
    $template_directory = XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname', 'n') . '/templates/';
    $template_list      = array_diff(scandir($template_directory), array('..', '.'));
    foreach ($template_list as $k => $v) {
        $fileinfo = new SplFileInfo($template_directory . $v);
        if ($fileinfo->getExtension() === 'html' && $fileinfo->getFilename() !== 'index.html') {
            @unlink($template_directory . $v);
        }
    }

    return true;
}
