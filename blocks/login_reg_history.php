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
 * @package         userlog blocks
 * @since           1.11
 * @author          irmtfan (irmtfan@yahoo.com)
 * @author          The XOOPS Project <www.xoops.org> <www.xoops.ir>
 * @version         $Id: login_reg_history.php 1.11 2013-04-26 16:25:04Z irmtfan $
 */

defined('XOOPS_ROOT_PATH') or die('Restricted access');
include_once dirname(dirname(__FILE__)) . '/include/common.php';

if (defined('USERLOG_BLOCK_LOGIN_REG_HISTORY_DEFINED')) return;
define('USERLOG_BLOCK_LOGIN_REG_HISTORY_DEFINED',true);
xoops_loadLanguage("admin",USERLOG_DIRNAME);
// options[0] - number of items to show in block. the default is 10
// options[1] - login or register or both radio select
// options[2] - failed or successful or both radio select
// options[3] - inactive or active or both
// options[4] - never login before or login before or both
// options[5] - Order - DESC, ASC default: DESC

function userlog_login_reg_history_show($options)
{
	$queryObj = UserlogQuery::getInstance();
	return $queryObj->loginregHistoryShow($options);
}

function userlog_login_reg_history_edit($options)
{
	$queryObj = UserlogQuery::getInstance();
	return $queryObj->loginregHistoryForm($options);
}