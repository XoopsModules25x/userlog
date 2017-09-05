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
 * @subpackage      addon
 * @since           1
 * @author          irmtfan (irmtfan@yahoo.com)
 * @author          XOOPS Project <www.xoops.org> <www.xoops.ir>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access.');
// Here you can set ADDITIONAL permission in file for webmasters in your website, ONLY if you want to limit the access to userlog module for some of them.

// Webmasters that dont have access cannot:
// 1- go to the userlog > admin
// 2- go to the userlog > preferences
// 3- install, uninstall or update userlog
// 4- dump any table in system -> maintenance -> dump

// empty array means nothing.
// Note: you can delete this file if you dont need it.

// if you add uid of webmasters or those users who have admin permissions in userlog module, other admins will not have permission anymore.
// e.g.: $perm["super"]["user"] = array(1,234,23451); // it means only users with uid=1,234,23451 have access and other webmasters dont have access.
$perm['super']['uid'] = [];

// if you add groups with admin permission in  whole site (webmasters) or admin permission in userlog module, other admin groups dont have access.
// e.g.: $perm["super"]["group"] = array(1,7,9); // it means only groups 1,7,9 have access and other groups dont have access.
$perm['super']['group'] = [];

return $perm;
