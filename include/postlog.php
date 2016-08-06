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
 * @since           1.1
 * @author          irmtfan (irmtfan@yahoo.com)
 * @author          The XOOPS Project <www.xoops.org> <www.xoops.ir>
 * @version         $Id: postlog.php 1.1 2013-04-26 16:25:04Z irmtfan $
 */
defined('XOOPS_ROOT_PATH') or die('Restricted access');
require_once dirname(__FILE__) . '/common.php';
$Userlog = Userlog::getInstance(false);

if(!empty($_POST) && $Userlog->getConfig("postlog")) {
	include dirname(__FILE__) . '/log.php';
}
