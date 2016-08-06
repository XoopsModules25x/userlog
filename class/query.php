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
 * @package         userlog class
 * @since           1.16
 * @author          irmtfan (irmtfan@yahoo.com)
 * @author          The XOOPS Project <www.xoops.org> <www.xoops.ir>
 * @version         $Id: query.php 1.16 2013/05/08 16:25:04Z irmtfan $
 */
// Important note: use $eleNamePrefix = "options" because it is hard-coded in XOOPS CORE > BLOCKS

defined('XOOPS_ROOT_PATH') or die('Restricted access');
include_once dirname(dirname(__FILE__)) . '/include/common.php';

xoops_loadLanguage("admin",USERLOG_DIRNAME);
xoops_load('XoopsFormLoader');
xoops_loadLanguage('user');
xoops_loadLanguage('findusers');

class UserlogQuery
{
    public $userlog = null;

    protected function __construct()
    {
        $this->userlog = Userlog::getInstance();
    }

    static function &getInstance()
    {
        static $instance = false;
        if (!$instance) {
            $instance = new self();
        }
        return $instance;
    }
	// args[0] - number of items to show in block. the default is 10
	// args[1] - login or register or both radio select
	// args[2] - failed or successful or both radio select
	// args[3] - inactive or active or both
	// args[4] - never login before or login before or both
	// args[5] - Order - DESC, ASC default: DESC
	public function loginregHistoryShow($args)
	{
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('uid', 0), "AND");
		$criteria->add(new Criteria('post', "%pass%" , "LIKE"), "AND"); // login or register
		$criteria->add(new Criteria('post', "%login_patch%" , "LIKE"), "AND"); // login/register was patched
		$opt[0] = 0;
		$opt[1] = "NOT LIKE";
		$opt[2] = "LIKE";

		$i=1;
		if(!empty($args[$i])) {
			$criteria->add(new Criteria('post', "%vpass%" , $opt[$args[$i]]), "AND"); // login "NOT LIKE" register "LIKE" 
		}
		$i++; //2
		if(!empty($args[$i])) {
			$criteria->add(new Criteria('post', "%success%" , $opt[$args[$i]]), "AND"); // falied "NOT LIKE" success "LIKE" 
		}
		$i++; //3
		if(!empty($args[$i])) {
			$criteria->add(new Criteria('post', "%level%" , $opt[$args[$i]]), "AND"); // inactive "NOT LIKE" active "LIKE" 
		}
		$i++; //4
		if(!empty($args[$i])) {
			$criteria->add(new Criteria('post', "%last_visit%" , $opt[$args[$i]]), "AND"); // never login before "NOT LIKE" login before "LIKE" 
		}
		$loginsObj = $this->userlog->getHandler('log')->getLogs($args[0],0,$criteria,"log_id",$args[5] ,array("log_id", "log_time","post"), true); // true => as Obj
		$block = array();
		if(empty($loginsObj)) return $block;
		foreach($loginsObj as $log_id=>$loginObj) {
			$block[$log_id] = $loginObj->post(); // dont use getVar("post")
			$block[$log_id]["loginOrRegister"] = !empty($block[$log_id]["vpass"]) ? "register" : "login";
			if (!empty($block[$log_id]["success"])) {
				$block[$log_id]["msg"] = _AM_USERLOG_SUCCESS . " ";
				if(empty($block[$log_id]["level"])) {
					$block[$log_id]["msg"] .= _MA_USER_LEVEL_INACTIVE;
					$block[$log_id]["color"] = "YELLOW";
				} else {
					$block[$log_id]["msg"] .= _MA_USER_LEVEL_ACTIVE;
					$block[$log_id]["color"] = "GREEN";
				}
				if(empty($block[$log_id]["last_visit"])) {
					if(($block[$log_id]["loginOrRegister"] == "register")) {
						$block[$log_id]["msg"] .= " ". sprintf(_US_HASJUSTREG,$block[$log_id]["uname"]);
						$block[$log_id]["color"] = "GREEN";
					} else {
						$block[$log_id]["msg"] .= " " . sprintf(_US_CONFMAIL,$block[$log_id]["uname"]);
						$block[$log_id]["color"] = "BROWN";
					}
				}
			} else {
				$block[$log_id]["success"] = 0;
				$block[$log_id]["msg"] = _AM_USERLOG_FAIL . " ";
				$block[$log_id]["msg"] .= ($block[$log_id]["loginOrRegister"] == "register") ? _ERRORS :_US_INCORRECTLOGIN;
				$block[$log_id]["color"] = "RED";
			}
			$this->userlog->setConfig("format_date",$this->userlog->getConfig("format_date_history"));
			$block[$log_id]["log_time"] = $loginObj->log_time();
		}
		unset($block[$log_id]["pass"],$block[$log_id]["vpass"]);
		return $block;
	}
	public function loginregHistoryForm($args, $eleNamePrefix = "options")
	{
		// include_once XOOPS_ROOT_PATH . "/class/blockform.php"; //reserve for 2.6
		xoops_load('XoopsFormLoader');
		// $form = new XoopsBlockForm(); //reserve for 2.6
		$form = new XoopsThemeForm(_AM_USERLOG_LOGIN_REG_HISTORY,'login_reg_history','');
		
		$i=0;
		// number of items to display element
		$numitemsEle = new XoopsFormText(_AM_USERLOG_ITEMS_NUM, "{$eleNamePrefix}[{$i}]", 10, 255, intval($args[$i]));

		$i++;
		$loginRegRadioEle = new XoopsFormRadio(_LOGIN . "|" . _REGISTER, "{$eleNamePrefix}[{$i}]", $args[$i]);
		$loginRegRadioEle->addOption(1,_LOGIN);
		$loginRegRadioEle->addOption(2,_REGISTER);
		$loginRegRadioEle->addOption(0,_ALL);

		$i++;
		$failSucRadioEle = new XoopsFormRadio(_AM_USERLOG_FAIL . "|" . _AM_USERLOG_SUCCESS, "{$eleNamePrefix}[{$i}]", $args[$i]);
		$failSucRadioEle->addOption(1,_LOGIN . "|" . _REGISTER . " " . _AM_USERLOG_FAIL);
		$failSucRadioEle->addOption(2,_LOGIN . "|" . _REGISTER . " " . _AM_USERLOG_SUCCESS);
		$failSucRadioEle->addOption(0,_ALL);

		$i++;
		$inactiveActiveRadioEle = new XoopsFormRadio(_MA_USER_LEVEL_INACTIVE . "|" . _MA_USER_LEVEL_ACTIVE, "{$eleNamePrefix}[{$i}]", $args[$i]);
		$inactiveActiveRadioEle->addOption(1,_MA_USER_LEVEL_INACTIVE);
		$inactiveActiveRadioEle->addOption(2,_MA_USER_LEVEL_ACTIVE);
		$inactiveActiveRadioEle->addOption(0,_ALL);

		$i++;
		$lastVisitRadioEle = new XoopsFormRadio(_AM_USERLOG_LAST_LOGIN, "{$eleNamePrefix}[{$i}]", $args[$i]);
		$lastVisitRadioEle->addOption(1,_NONE);
		$lastVisitRadioEle->addOption(2,_YES);
		$lastVisitRadioEle->addOption(0,_ALL);
		$lastVisitRadioEle->setDescription(_AM_USERLOG_LAST_LOGIN_DSC);
	
		$i++;
		$orderEle = new XoopsFormSelect(_AM_USERLOG_ORDER,"{$eleNamePrefix}[{$i}]", $args[$i]);
		$orderEle->addOption("DESC", _DESCENDING);
		$orderEle->addOption("ASC",  _ASCENDING);
		$orderEle->setDescription(_AM_USERLOG_ORDER_DSC);
	
		// add all elements to form
		$form->addElement($numitemsEle);
		$form->addElement($loginRegRadioEle);
		$form->addElement($failSucRadioEle);
		$form->addElement($inactiveActiveRadioEle);
		$form->addElement($lastVisitRadioEle);
		$form->addElement($orderEle);
	
		return $form->render();
	}
	
	// args[0] - number of items to show in block. the default is 10
	// args[1] - stats_type - referral (default), browser, OS
	// args[2] - Sort - stats_link, stats_value (default), time_update
	// args[3] - Order - DESC, ASC default: DESC
	public function stats_typeShow($args)
	{
		$statsObj = UserlogStats::getInstance();
		$refViews = $statsObj->getAll($args[1], 0, $args[0], $args[2], $args[3]); // getAll($type = array(), $start = 0, $limit = 0, $sort = "stats_value", $order = "DESC", $otherCriteria = null)
		if (empty($refViews)) return false;
		$block = array("stats" => $refViews,"stats_type" => $args[1]);
		return $block;
	}

	public function stats_typeForm($args, $eleNamePrefix = "options")
	{
		// include_once XOOPS_ROOT_PATH . "/class/blockform.php"; //reserve for 2.6
		xoops_load('XoopsFormLoader');
		// $form = new XoopsBlockForm(); //reserve for 2.6
		$form = new XoopsThemeForm(_AM_USERLOG_STATS_TYPE,'stats_type','');
	
		$i=0;
		// number of items to display element
		$numitemsEle = new XoopsFormText(_AM_USERLOG_ITEMS_NUM, "{$eleNamePrefix}[{$i}]", 10, 255, intval($args[$i]));
		$i++;
		$typeEle = new XoopsFormSelect(_AM_USERLOG_STATS_TYPE, "{$eleNamePrefix}[{$i}]", $args[$i]);
		$typeEle->addOptionArray(array(
									"referral"=>_AM_USERLOG_STATS_REFERRAL,
									"browser"=>_AM_USERLOG_STATS_BROWSER,
									"OS"=>_AM_USERLOG_STATS_OS
									));
		$typeEle->setDescription(_AM_USERLOG_STATS_TYPE_DSC);

		$i++;
		$sortEle = new XoopsFormSelect(_AM_USERLOG_SORT, "{$eleNamePrefix}[{$i}]", $args[$i]);
		$sortEle->addOptionArray(array(
									"stats_link"=>_AM_USERLOG_ITEM_NAME,
									"stats_value"=>_AM_USERLOG_VIEW,
									"time_update"=>_AM_USERLOG_STATS_TIME_UPDATE
									));
		$sortEle->setDescription(_AM_USERLOG_SORT_DSC);
		
		$i++;
		$orderEle = new XoopsFormSelect(_AM_USERLOG_ORDER,"{$eleNamePrefix}[{$i}]", $args[$i]);
		$orderEle->addOption("DESC", _DESCENDING);
		$orderEle->addOption("ASC",  _ASCENDING);
		$orderEle->setDescription(_AM_USERLOG_ORDER_DSC);
	
		// add all elements to form
		$form->addElement($numitemsEle);
		$form->addElement($typeEle);
		$form->addElement($sortEle);
		$form->addElement($orderEle);
	
		return $form->render();
	}
	
	// args[0] - number of items to show in block. the default is 10
	// args[1] - module dirname - 0 or empty = all modules
	public function modulesadminShow($args)
	{
		xoops_loadLanguage("admin/modulesadmin","system");
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('module', 'system'), "AND");
		$criteria->add(new Criteria('request_method', "POST"), "AND"); // only POST method
		$refLike = "%modulesadmin&op=%";
		if(!empty($args[1])) {
			$refLike .= "module={$args[1]}"; 
		}
		$criteria->add(new Criteria('referer', "{$refLike}" , "LIKE"), "AND"); // modules admin
	
		$modulesadminObjs = $this->userlog->getHandler('log')->getLogs($args[0],0,$criteria,"log_id","DESC" ,array("log_id", "log_time","referer"), true); // true => as Obj
		if (empty($modulesadminObjs)) return false;
		$block = array();
		foreach($modulesadminObjs as $maObj) {
			$query = parse_url($maObj->referer(),PHP_URL_QUERY);
			parse_str($query, $moduleAdmin);
			$moduleAdmin["op_lang"] = constant("_AM_SYSTEM_MODULES_" . strtoupper($moduleAdmin["op"]));
			$moduleAdmin["log_time"] = $maObj->log_time();
			$block[$maObj->getVar("log_id")] = $moduleAdmin;
		}
		return $block;
	}
}