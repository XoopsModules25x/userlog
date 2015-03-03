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
 * @package         userlog preloads
 * @since           1
 * @author          irmtfan (irmtfan@yahoo.com)
 * @author          The XOOPS Project <www.xoops.org> <www.xoops.ir>
 * @version         $Id: core.php 1 2013-02-26 16:25:04Z irmtfan $
 */

defined('XOOPS_ROOT_PATH') or die('Restricted access');
class UserlogCorePreload extends XoopsPreloadItem
{
    // to log main part of modules
    function eventCoreFooterStart($args)
    {
        include dirname(dirname(__FILE__)) . '/include/log.php';
    }

    // to log redirects because usually prorammers use exit() after redirect_header function.
    function eventCoreIncludeFunctionsRedirectheader($args)
    {
        include dirname(dirname(__FILE__)) . '/include/log.php';
    }

    // in XOOPS255/index.php (homepage) when no module is set for start page there is a bug in XOOPS255/header.php exit() should be commented
    /*$xoopsPreload->triggerEvent('core.header.checkcache');
    if ($xoTheme->checkCache()) {
        $xoopsPreload->triggerEvent('core.header.cacheend');
        //exit();
    } */

    // to log admin part of modules (must use moduleadmin class)
    function eventSystemClassGuiHeader($args)
    {
        include dirname(dirname(__FILE__)) . '/include/log.php';
    }
}
