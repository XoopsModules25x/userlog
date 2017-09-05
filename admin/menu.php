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

use Xmf\Module\Admin;
use Xmf\Module\Helper;

// defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

//$path = dirname(dirname(dirname(__DIR__)));
//require_once $path . '/mainfile.php';
require_once __DIR__ . '/../include/common.php'; // after installation it will be included before admin_header.php
$userlog    = Userlog::getInstance();
//$pathIcon32 = $userlog->getModule()->getInfo('icons32');
$moduleDirName = basename(dirname(__DIR__));
if (false !== ($moduleHelper = \Xmf\Module\Helper::getHelper($moduleDirName))) {
} else {
    $moduleHelper = \Xmf\Module\Helper::getHelper('system');
}
$pathIcon32    = \Xmf\Module\Admin::menuIconPath('');
$pathModIcon32 = $moduleHelper->getModule()->getInfo('modicons32');

xoops_loadLanguage('modinfo', $moduleDirName);
xoops_loadLanguage('admin', $moduleDirName);
// xoops_loadLanguage('admin', USERLOG_DIRNAME);

$i = 0;

// Index
$adminmenu[] = [
'title' =>  _AM_USERLOG_ADMENU_INDEX,
'link' =>  'admin/index.php',
'icon' =>  $pathIcon32 . '/home.png',
];

$adminmenu[] = [
'title' =>  _AM_USERLOG_ADMENU_SETTING,
'link' =>  'admin/setting.php',
'icon' =>  $pathIcon32 . '/administration.png',
];

$adminmenu[] = [
'title' =>  _AM_USERLOG_ADMENU_LOGS,
'link' =>  'admin/logs.php',
'icon' =>  $pathIcon32 . '/content.png',
];

$adminmenu[] = [
'title' =>  _AM_USERLOG_ADMENU_FILE,
'link' =>  'admin/file.php',
'icon' =>  $pathIcon32 . '/compfile.png',
];

$adminmenu[] = [
'title' =>  _AM_USERLOG_ADMENU_STATS,
'link' =>  'admin/stats.php',
'icon' =>  $pathIcon32 . '/stats.png',
];

$adminmenu[] = [
'title' =>  _AM_USERLOG_ABOUT,
'link' =>  'admin/about.php',
'icon' =>  $pathIcon32 . '/about.png',
];

// add js, css, toggle_cookie to admin pages
global $xoTheme;
$xoTheme->addScript('modules/' . USERLOG_DIRNAME . '/assets/js/scripts.js');
$xoTheme->addStylesheet('modules/' . USERLOG_DIRNAME . '/assets/css/style.css');
$toggle_script = 'var toggle_cookie="' . $userlog->cookiePrefix . 'TOGGLE' . '";';
$xoTheme->addScript(null, ['type' => 'text/javascript'], $toggle_script);
