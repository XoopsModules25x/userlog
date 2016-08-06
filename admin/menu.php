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
 * @package         userlog admin
 * @since           1
 * @author          irmtfan (irmtfan@yahoo.com)
 * @author          XOOPS Project <www.xoops.org> <www.xoops.ir>
 */

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');
include_once dirname(__DIR__) . '/include/common.php'; // after installtion it will included before admin_header.php
$Userlog    = Userlog::getInstance();
$pathIcon32 = $Userlog->getModule()->getInfo('icons32');

xoops_loadLanguage('admin', USERLOG_DIRNAME);

$i = 0;

// Index
$adminmenu[$i]['title'] = _AM_USERLOG_ADMENU_INDEX;
$adminmenu[$i]['link']  = 'admin/index.php';
$adminmenu[$i]['icon']  = '../../' . $pathIcon32 . '/home.png';
++$i;

$adminmenu[$i]['title'] = _AM_USERLOG_ADMENU_SETTING;
$adminmenu[$i]['link']  = 'admin/setting.php';
$adminmenu[$i]['icon']  = '../../' . $pathIcon32 . '/administration.png';

++$i;
$adminmenu[$i]['title'] = _AM_USERLOG_ADMENU_LOGS;
$adminmenu[$i]['link']  = 'admin/logs.php';
$adminmenu[$i]['icon']  = '../../' . $pathIcon32 . '/content.png';

++$i;
$adminmenu[$i]['title'] = _AM_USERLOG_ADMENU_FILE;
$adminmenu[$i]['link']  = 'admin/file.php';
$adminmenu[$i]['icon']  = '../../' . $pathIcon32 . '/compfile.png';

++$i;
$adminmenu[$i]['title'] = _AM_USERLOG_ADMENU_STATS;
$adminmenu[$i]['link']  = 'admin/stats.php';
$adminmenu[$i]['icon']  = '../../' . $pathIcon32 . '/stats.png';

++$i;
$adminmenu[$i]['title'] = _AM_USERLOG_ABOUT;
$adminmenu[$i]['link']  = 'admin/about.php';
$adminmenu[$i]['icon']  = '../../' . $pathIcon32 . '/about.png';
// add js, css, toggle_cookie to admin pages
global $xoTheme;
$xoTheme->addScript('modules/' . USERLOG_DIRNAME . '/assets/js/scripts.js');
$xoTheme->addStylesheet('modules/' . USERLOG_DIRNAME . '/assets/css/style.css');
$toggle_script = "var toggle_cookie=\"" . $Userlog->cookiePrefix . 'TOGGLE' . "\";";
$xoTheme->addScript(null, array('type' => 'text/javascript'), $toggle_script);
