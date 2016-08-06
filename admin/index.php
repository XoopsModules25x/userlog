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
 * @version         $Id: index.php 1 2013-02-26 16:25:04Z irmtfan $
 */

include_once dirname(__FILE__) . '/admin_header.php';

xoops_cp_header();

$indexAdmin = new ModuleAdmin();

$Userlog = Userlog::getInstance(false);

// update all time stats
$statsObj = UserlogStats::getInstance();
$statsObj->updateAll("log", 100); // prob = 100
$statsObj->updateAll("set", 100); // prob = 100
$statsObj->updateAll("file", 100); // prob = 100

$stats = $statsObj->getAll(array("log","logdel","set","file"));
// if no set in database - start with a setting!
if ($stats["set"][0]["value"] == 0) {
	$indexAdmin->addItemButton(_AM_USERLOG_SET_ADD,"setting.php");
} else {
	$indexAdmin->addInfoBox(_AM_USERLOG_SUMMARY);
	$indexAdmin->addInfoBoxLine(_AM_USERLOG_SUMMARY,'<a href="logs.php?options[referer]=del&options[request_method]=POST">' . _AM_USERLOG_SUMMARY_DELETED . '</a>');
	$indexAdmin->addInfoBoxLine(_AM_USERLOG_SUMMARY,'<a href="logs.php?options[admin]=1">' . _AM_USERLOG_SUMMARY_ADMIN . '</a>');
	$indexAdmin->addInfoBoxLine(_AM_USERLOG_SUMMARY,'<a href="logs.php?options[referer]=google.com">' . _AM_USERLOG_SUMMARY_GOOGLE . '</a>');
}
$indexAdmin->addInfoBox(_AM_USERLOG_STATS_ABSTRACT);
$periods = array_flip($statsObj->_period);
$types = $statsObj->_type;
foreach($stats as $type=>$arr) {
	if(strlen($type) > 10) continue;
	foreach($arr as $period=>$arr2) {
		// use sprintf in moduleadmin: sprintf($text, "<span style='color : " . $color . "; font-weight : bold;'>" . $value . "</span>")
		$indexAdmin->addInfoBoxLine(_AM_USERLOG_STATS_ABSTRACT,
				sprintf(_AM_USERLOG_STATS_TYPE_PERIOD, "%1\$s", $types[$type], constant("_AM_USERLOG_" . strtoupper($periods[$period]))) . " " . _AM_USERLOG_STATS_TIME_UPDATE . " " . $arr2["time_update"],
				$arr2["value"],
				$arr2["value"] ? 'GREEN' : 'RED');
	}
}
// if there is no file in working check the parent folder chmod
if ($stats["fileall"][0]["value"] == 0 || $stats["file" . $Userlog->getWorkingFile()][0]["value"] == 0) {
	$indexAdmin->addConfigBoxLine(array($Userlog->getConfig('logfilepath'), 755) , 'chmod');
	// core feature: if(!$indexAdmin->addConfigBoxLine())
	if (substr(decoct(fileperms($Userlog->getConfig('logfilepath'))),2) < 755) {
		$indexAdmin->addConfigBoxLine("<span class='bold red'>" . sprintf(_AM_USERLOG_CONFIG_CHMOD_ERROR,$Userlog->getConfig('logfilepath'),755)  . "</span>", 'default');
		$indexAdmin->addConfigBoxLine("<span class='bold red'>" . sprintf(_AM_USERLOG_CONFIG_CREATE_FOLDER,$Userlog->getConfig('logfilepath')."/". USERLOG_DIRNAME,755)  . "</span>", 'default');
	}
} else {
// if there is file in working check the log folder chmod
	$indexAdmin->addConfigBoxLine(array($Userlog->getConfig('logfilepath') ."/". USERLOG_DIRNAME, 755) , 'chmod');
}
$indexAdmin->addConfigBoxLine("<span class='bold " . ($Userlog->getConfig('status') ? "green" : "red") . "'>" . _MI_USERLOG_STATUS . " " . ($Userlog->getConfig('status') ? _MI_USERLOG_ACTIVE : _MI_USERLOG_IDLE) . "</span>", 'default');
echo $indexAdmin->addNavigation('index.php');
echo $indexAdmin->renderButton();
echo $indexAdmin->renderIndex();

xoops_cp_footer();