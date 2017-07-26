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
 * @package         userlog preloads
 * @since           1
 * @author          irmtfan (irmtfan@yahoo.com)
 * @author          XOOPS Project <www.xoops.org> <www.xoops.ir>
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Class UserlogCorePreload
 */
class UserlogCorePreload extends XoopsPreloadItem
{
    // to log main part of modules
    /**
     * @param $args
     */
    public static function eventCoreFooterStart($args)
    {
        include dirname(__DIR__) . '/include/log.php';
    }

    // to log redirects because usually prorammers use exit() after redirect_header function.
    /**
     * @param $args
     */
    public static function eventCoreIncludeFunctionsRedirectheader($args)
    {
        include dirname(__DIR__) . '/include/log.php';
    }

    /**
     * @param $args
     */
    public static function eventCoreIncludeCommonEnd($args)
    {
        include dirname(__DIR__) . '/include/postlog.php';
    }
    // in XOOPS255/index.php (homepage) when no module is set for start page there is a bug in XOOPS255/header.php exit() should be commented
    /*$xoopsPreload->triggerEvent('core.header.checkcache');
    if ($xoTheme->checkCache()) {
        $xoopsPreload->triggerEvent('core.header.cacheend');
        //exit();
    } */

    // to log admin part of modules (must use moduleadmin class)
    /**
     * @param $args
     */
    public static function eventSystemClassGuiHeader($args)
    {
        include dirname(__DIR__) . '/include/log.php';
    }
}
