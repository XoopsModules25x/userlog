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
 * @package         userlog admin
 * @since           1
 * @author          irmtfan (irmtfan@yahoo.com)
 * @author          The XOOPS Project <www.xoops.org> <www.xoops.ir>
 * @version         $Id: stats.php 1 2013-02-26 16:25:04Z irmtfan $
 */

include_once dirname(__FILE__) . '/admin_header.php';
include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
xoops_cp_header();

$Userlog = Userlog::getInstance(false);
// Where do we start ?
$startentry = UserlogRequest::getInt('startentry',0);
$limitentry = UserlogRequest::getInt('limitentry',10);
$sortentry = UserlogRequest::getString('sortentry','count');
$orderentry = UserlogRequest::getString('orderentry','DESC');
$modules = UserlogRequest::getArray("modules");
$moduleScriptItem = UserlogRequest::getArray("moduleScriptItem");
$log_timeGT = UserlogRequest::getInt('log_timeGT',1);
$users = UserlogRequest::getArray("users", -1);
$groups = UserlogRequest::getArray("groups", 0);

// update all time stats
$statsObj = UserlogStats::getInstance();
$statsObj->updateAll("log", 100); // prob = 100
$statsObj->updateAll("set", 100); // prob = 100
$statsObj->updateAll("file", 100); // prob = 100

$stats = $statsObj->getAll();
$indexAdmin = new ModuleAdmin();
$indexAdmin->addInfoBox(_AM_USERLOG_STATS_ABSTRACT);
$periods = array_flip($statsObj->_period);
$types = $statsObj->_type;
foreach($stats as $type=>$arr) {
	if(strlen($type) > 10) continue;
	foreach($arr as $period=>$arr2) {
		// use sprintf in moduleadmin: sprintf($text, "<span style='color : " . $color . "; font-weight : bold;'>" . $value . "</span>")
		$indexAdmin->addInfoBoxLine(_AM_USERLOG_STATS_ABSTRACT,
				sprintf(_AM_USERLOG_STATS_TYPE_PERIOD, "%s\1", $types[$type], constant("_AM_USERLOG_" . strtoupper($periods[$period]))),
				$arr2["value"],
				$arr2["value"] ? 'GREEN' : 'RED');
	}
}
$criteria = new CriteriaCompo();
$criteria->setGroupby("module");
$moduleViews = $Userlog->getHandler('log')->getCounts($criteria);
$dirNames = $Userlog->getModules();
if (!empty($moduleViews)) {
	$indexAdmin->addInfoBox(_AM_USERLOG_VIEW_MODULE);
	foreach($moduleViews as $mDir=>$views) {
		$indexAdmin->addInfoBoxLine(_AM_USERLOG_VIEW_MODULE,
									$dirNames[$mDir] . ": %s",
									$views,
									$views? 'GREEN' : 'RED');
	}
}
$criteria = new CriteriaCompo();
$criteria->setGroupby("uid");
$criteria->setLimit(10);
$userViews = $Userlog->getHandler('log')->getCounts($criteria);
if (!empty($userViews)) {
	$indexAdmin->addInfoBox(_AM_USERLOG_VIEW_USER);
	foreach($userViews as $uid=>$views) {
		$indexAdmin->addInfoBoxLine(_AM_USERLOG_VIEW_USER,
									(($uid) ? "<a href=\"" . XOOPS_URL . "/userinfo.php?uid=" . $uid . "\">" . XoopsUserUtility::getUnameFromId($uid) . "</a>" : XoopsUserUtility::getUnameFromId(0)) . ": %s",
									$views,
									$views? 'GREEN' : 'RED');
	}
}
$criteria = new CriteriaCompo();
$criteria->add(new Criteria("groups", "%g%", "LIKE")); // Why cannot use this?: $criteria->add(new Criteria("groups", "", "!="))
$criteria->setGroupby("groups");
$criteria->setLimit(10);
$groupViews = $Userlog->getHandler('log')->getCounts($criteria);
if (!empty($groupViews)) {
	$indexAdmin->addInfoBox(_AM_USERLOG_VIEW_GROUP);
	foreach($groupViews as $gids=>$views) {
		$groupArr = explode("g", substr($gids, 1)); // remove the first "g" from string
		$groupArr = array_unique($groupArr);
		foreach($groupArr as $group) {
			if(isset($gidViews[$group])) {
				$gidViews[$group] += $views;
			} else {
				$gidViews[$group] = $views;
			}
		}
	}
	$groupNames = $Userlog->getGroupList();
	foreach($gidViews as $gid=>$views) {
		$indexAdmin->addInfoBoxLine(_AM_USERLOG_VIEW_GROUP,
									$groupNames[$gid] . ": %s",
									$views,
									$views? 'GREEN' : 'RED');
	}
}

// START module - script - item
$module=array();
// items
foreach ($moduleScriptItem as $key=>$item) {
	$module_script_item = explode('-', $item); // news:article.php-storyid news:index.php-storytopic => $module["news"]=array("storyid","storytopic");
	$module_script = explode(':', $module_script_item[0]); // 	news:article.php => $module_script = array(news,article.php);
	if (!isset($module[$module_script[0]])) {
		$module[$module_script[0]]["item_name"] = array();
		$module[$module_script[0]]["script"] = array_slice($module_script,1);
	}
	$module[$module_script[0]]["script"] = array_unique(array_merge($module[$module_script[0]]["script"], array_slice($module_script,1)));
	$module[$module_script[0]]["item_name"][] = $module_script_item[1];
}
// add modules dont have item_name
foreach($modules as $dir) {
	if(!isset($module[$dir])) $module[$dir] = null;
}
// END module - script - item
$loglogObj = UserlogLog::getInstance();

// get items views
$items = $loglogObj->getViews($limitentry , $startentry, $sortentry, $orderentry, $module, $log_timeGT, ($users[0] != -1) ? $users : array(), ($groups[0] != 0) ? $groups : array());
$GLOBALS['xoopsTpl']->assign('sortentry',$sortentry);
$GLOBALS['xoopsTpl']->assign('items',$items);
// SRART form
$form = new XoopsThemeForm(_AM_USERLOG_VIEW,'views','stats.php', 'post');
// number of items to display element
$limitEl = new XoopsFormText(_AM_USERLOG_ITEMS_NUM, "limitentry", 10, 255, $limitentry);
$sortEl = new XoopsFormSelect(_AM_USERLOG_SORT,"sortentry", $sortentry);
$sortEl->addOptionArray(array(
							"count"=>_AM_USERLOG_VIEW,
							"module"=>_AM_USERLOG_MODULE,
							"module_name"=>_AM_USERLOG_MODULE_NAME,
							"module_count"=>_AM_USERLOG_VIEW_MODULE
							));
$sortEl->setDescription(_AM_USERLOG_SORT_DSC);
$orderEl = new XoopsFormSelect(_AM_USERLOG_ORDER,"orderentry", $orderentry);
$orderEl->addOption("DESC", _DESCENDING);
$orderEl->addOption("ASC",  _ASCENDING);
$orderEl->setDescription(_AM_USERLOG_ORDER_DSC);
// modules, items elements
$moduleObjs = $Userlog->getModules(array(), null, true);
$itemLinks = array();
foreach ($moduleObjs as $mObj) {
	$dirNames[$mObj->dirname()] = $mObj->name();
	$not_config = $mObj->getInfo('notification');
	if (!empty($not_config['category'])) {
		foreach ($not_config['category'] as $category) {
			if (!empty($category['item_name'])) {
				$script = is_array($category["subscribe_from"]) ? implode(":", $category["subscribe_from"]) : $category["subscribe_from"];
				$itemLinks[$mObj->dirname(). ":" . $script . "-" . $category['item_name']] = $mObj->dirname()."/" . $script ."?".$category['item_name']."=ITEM_ID";
			}
		}
	}
}
$moduleEl = new XoopsFormSelect(_AM_USERLOG_MODULES,"modules",$modules,5, true);
$moduleEl->addOptionArray($dirNames);
$itemsEl = new XoopsFormSelect(_AM_USERLOG_ITEMS,"moduleScriptItem",$moduleScriptItem,5, true);
$itemsEl->addOptionArray($itemLinks);
$itemsEl->setDescription(_AM_USERLOG_ITEMS_DSC);
	
$timeEl = new XoopsFormText(_AM_USERLOG_LOG_TIMEGT, "log_timeGT", 10, 255, $log_timeGT);
$timeEl->setDescription(_AM_USERLOG_LOG_TIMEGT_FORM);

$userRadioEl = new XoopsFormRadio(_AM_USERLOG_UID, "users", $users[0]);
$userRadioEl->addOption(-1,_ALL);
$userRadioEl->addOption(($users[0] != -1) ? $users[0] : 0,_SELECT); // if no user in selection box it select uid=0 anon users
$userRadioEl->setExtra("onchange=\"var el=document.getElementById('users'); el.disabled=(this.id == 'users1'); if (!el.value) {el.value= this.value}\""); // if user dont select any option it select "all"
$userSelectEl = new XoopsFormSelectUser(_AM_USERLOG_UID, "users", true, $users, 3, true);
$userEl = new XoopsFormLabel(_AM_USERLOG_UID, $userRadioEl->render().$userSelectEl->render());

$groupRadioEl = new XoopsFormRadio(_AM_USERLOG_GROUPS, "groups", $groups[0]);
$groupRadioEl->addOption(0,_ALL);
$groupRadioEl->addOption(($groups[0] != 0) ? $groups[0] : 2,_SELECT); // if no group in selection box it select gid=2 registered users
$groupRadioEl->setExtra("onchange=\"var el=document.getElementById('groups'); el.disabled=(this.id == 'groups1'); if (!el.value) {el.value= this.value}\""); // if group dont select any option it select "all"
$groupSelectEl = new XoopsFormSelectGroup(_AM_USERLOG_GROUPS, "groups", true, $groups, 3, true);
$groupEl = new XoopsFormLabel(_AM_USERLOG_GROUPS, $groupRadioEl->render().$groupSelectEl->render());

$submitEl = new XoopsFormButton(_SUBMIT, 'submitlogs', _SUBMIT, 'submit');
// add all elements to form
$form->addElement($limitEl);
$form->addElement($moduleEl);
$form->addElement($itemsEl);
$form->addElement($timeEl);
$form->addElement($userEl);
$form->addElement($groupEl);
$form->addElement($sortEl);
$form->addElement($orderEl);
$form->addElement($submitEl);
$GLOBALS['xoopsTpl']->assign('form',$form->render());
$GLOBALS['xoopsTpl']->assign('stats_abstract',$indexAdmin->renderInfoBox());
$GLOBALS['xoopsTpl']->assign('logo',$indexAdmin->addNavigation('stats.php'));
// template
$template_main = "userlog_admin_stats.html";
if ( !empty($template_main)  ) {
    $GLOBALS['xoopsTpl']->display("db:{$template_main}");
}
xoops_cp_footer();