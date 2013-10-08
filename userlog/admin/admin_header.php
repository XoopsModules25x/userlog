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
 * @version         $Id: admin_header.php 1 2013-02-26 16:25:04Z irmtfan $
 */

include_once dirname(dirname(dirname(dirname(__FILE__)))) . '/mainfile.php';
include_once dirname(dirname(__FILE__)) . '/include/common.php';
include_once XOOPS_ROOT_PATH . '/include/cp_header.php';
xoops_load('XoopsFormLoader');
xoops_loadLanguage('modinfo', USERLOG_DIRNAME);
if ( file_exists($GLOBALS['xoops']->path('/Frameworks/moduleclasses/moduleadmin/moduleadmin.php'))){
    include_once $GLOBALS['xoops']->path('/Frameworks/moduleclasses/moduleadmin/moduleadmin.php');
}else{
    echo xoops_error('/Frameworks/moduleclasses/moduleadmin/ is required!!!');
}