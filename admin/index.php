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
 * @package         userlog admin
 * @since           1
 * @author          irmtfan (irmtfan@yahoo.com)
 * @author          XOOPS Project <www.xoops.org> <www.xoops.ir>
 */

require_once __DIR__ . '/admin_header.php';

xoops_cp_header();

$adminObject = \Xmf\Module\Admin::getInstance();
$userlog = Userlog::getInstance();

// update all time stats
$statsObj = UserlogStats::getInstance();
$statsObj->updateAll('log', 100); // prob = 100
$statsObj->updateAll('set', 100); // prob = 100
$statsObj->updateAll('file', 100); // prob = 100

$stats = $statsObj->getAll(['log', 'logdel', 'set', 'file']);
// if no set in database - start with a setting!
if (isset($stats['set'][0]) && 0 == $stats['set'][0]['value']) {
    $adminObject->addItemButton(_AM_USERLOG_SET_ADD, 'setting.php');
} else {
    $adminObject->addInfoBox(_AM_USERLOG_SUMMARY);
    $adminObject->addInfoBoxLine('<a href="logs.php?options[referer]=del&options[request_method]=POST">' . _AM_USERLOG_SUMMARY_DELETED . '</a>');
    $adminObject->addInfoBoxLine('<a href="logs.php?options[admin]=1">' . _AM_USERLOG_SUMMARY_ADMIN . '</a>');
    $adminObject->addInfoBoxLine('<a href="logs.php?options[referer]=google.com">' . _AM_USERLOG_SUMMARY_GOOGLE . '</a>');
}
$adminObject->addInfoBox(_AM_USERLOG_STATS_ABSTRACT);
$periods = array_flip($statsObj->period);
$types   = $statsObj->type;
foreach ($stats as $type => $arr) {
    if (strlen($type) > 10) {
        continue;
    }
    foreach ($arr as $period => $arr2) {
        // use sprintf in moduleadmin: sprintf($text, "<span style='color : " . $color . "; font-weight : bold;'>" . $value . "</span>")
        $adminObject->addInfoBoxLine(
            sprintf(
            sprintf(_AM_USERLOG_STATS_TYPE_PERIOD, '%1$s', $types[$type], constant('_AM_USERLOG_' . strtoupper($periods[$period]))) . ' ' . _AM_USERLOG_STATS_TIME_UPDATE . ' ' . $arr2['time_update'],
            $arr2['value']
            ),
            '',
            $arr2['value'] ? 'GREEN' : 'RED'
        );
    }
}
// if there is no file in working check the parent folder chmod
if ((isset($stats['fileall'][0]) && 0 == $stats['fileall'][0]['value']) || (0 == $stats['file' . $userlog->getWorkingFile()][0]['value'])) {
    $adminObject->addConfigBoxLine([$userlog->getConfig('logfilepath'), 755], 'chmod');
    // core feature: if(!$adminObject->addConfigBoxLine())
    if (substr(decoct(fileperms($userlog->getConfig('logfilepath'))), 2) < 755) {
        $adminObject->addConfigBoxLine("<span class='bold red'>" . sprintf(_AM_USERLOG_CONFIG_CHMOD_ERROR, $userlog->getConfig('logfilepath'), 755) . '</span>', 'default');
        $adminObject->addConfigBoxLine("<span class='bold red'>" . sprintf(_AM_USERLOG_CONFIG_CREATE_FOLDER, $userlog->getConfig('logfilepath') . '/' . USERLOG_DIRNAME, 755) . '</span>', 'default');
    }
} else {
    // if there is file in working check the log folder chmod
    $adminObject->addConfigBoxLine([$userlog->getConfig('logfilepath') . '/' . USERLOG_DIRNAME, 755], 'chmod');
}
$adminObject->addConfigBoxLine("<span class='bold " . ($userlog->getConfig('status') ? 'green' : 'red') . "'>" . _MI_USERLOG_STATUS . ' ' . ($userlog->getConfig('status') ? _MI_USERLOG_ACTIVE : _MI_USERLOG_IDLE) . '</span>', 'default');

$adminObject->displayNavigation(basename(__FILE__));
$adminObject->displayButton('left');
$adminObject->displayIndex();

xoops_cp_footer();
