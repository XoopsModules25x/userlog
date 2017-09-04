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
 * @package         userlog include
 * @since           1
 * @author          irmtfan (irmtfan@yahoo.com)
 * @author          XOOPS Project <www.xoops.org> <www.xoops.ir>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

define('USERLOG_DIRNAME', basename(dirname(__DIR__)));
define('USERLOG_URL', XOOPS_URL . '/modules/' . USERLOG_DIRNAME);
define('USERLOG_ADMIN_URL', USERLOG_URL . '/admin');
define('USERLOG_ROOT_PATH', XOOPS_ROOT_PATH . '/modules/' . USERLOG_DIRNAME);

require_once USERLOG_ROOT_PATH . '/class/helper.php';
//require_once USERLOG_ROOT_PATH . '/class/request.php';
require_once USERLOG_ROOT_PATH . '/class/setting.php';
require_once USERLOG_ROOT_PATH . '/class/log.php';
require_once USERLOG_ROOT_PATH . '/class/stats.php';
require_once USERLOG_ROOT_PATH . '/class/query.php';

xoops_load('xoopsuserutility');
xoops_load('XoopsCache');
xoops_load('XoopsFile');
