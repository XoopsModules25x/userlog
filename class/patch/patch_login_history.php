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
 * @package         userlog class patch
 * @since           1.1
 * @author          irmtfan (irmtfan@yahoo.com)
 * @author          The XOOPS Project <www.xoops.org> <www.xoops.ir>
 * @version         $Id: patch_login_history.php 1.1 2013-04-26 16:25:04Z irmtfan $
 */
 
// Important notice: this file will be deleted from your server after patch is done
defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");
if(!is_object($Userlog)) return false;
$criteria = new CriteriaCompo();
$criteria->add(new Criteria('uid', 0), "AND");
$criteria->add(new Criteria('post', "%pass%" , "LIKE"), "AND"); // login or register
$criteria->add(new Criteria('post', "%login_patch%" , "NOT LIKE"), "AND"); // login/register not patched
$loginsForPatch = $Userlog->getHandler('log')->getLogs(100,0,$criteria,"log_id","DESC" ,array("log_id", "post"), true); // true => as object - 100 by 100

if(!empty($loginsForPatch)) {
	foreach($loginsForPatch as $loginObj) {
		$checkUserPost = $loginObj->post(); // dont use getVar("post")
		if (empty($checkUserPost["pass"]) || empty($checkUserPost["uname"])) continue;
		$postPatch["post"] = $Userlog->patchLoginHistory($checkUserPost, 0); // uid=0
		$loginObj->store($postPatch, true);
	}
	if(!empty($postPatch["post"])) return true;
}
unlink(__FILE__);