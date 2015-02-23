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
 * @version         $Id: logs.php 1 2013-02-26 16:25:04Z irmtfan $
 */

include_once dirname(__FILE__) . '/admin_header.php';
include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
xoops_cp_header();

$Userlog = Userlog::getInstance(false);
// Where do we start ?
$startentry = UserlogRequest::getInt('startentry',0);
$limitentry = UserlogRequest::getInt('limitentry',$Userlog->getConfig("logs_perpage"));
$sortentry = UserlogRequest::getString('sortentry','log_id');
$orderentry = UserlogRequest::getString('orderentry','DESC');

$options = UserlogRequest::getArray("options");
$engine = UserlogRequest::getString('engine',$Userlog->getConfig("engine"));
$file = UserlogRequest::getArray('file', $Userlog->getConfig("file"));
$opentry = UserlogRequest::getString('op', '', 'post');
$log_id = UserlogRequest::getArray('log_id', 0 , 'post');
$logsetObj = UserlogSetting::getInstance();
// START build Criteria for database
// get var types int, text, bool , ...
$type_vars = $logsetObj->getOptions("", "type");
//$query_types = array("="=>"",">"=>"GT", "<"=>"LT");
$criteria = new CriteriaCompo();
foreach($options as $key=>$val) {
	// deal with greater than and lower than
	$tt = substr($key, -2);
	switch ($tt) {
		case "GT":
			$op = substr($key,0, -2);
			$t = ">";
			break;
		case "LT":
			$op = substr($key,0, -2);
			$t = "<";
			break;
		default:
			$op = $key;
			$t = "=";
			break;
	}
	$criteria_q[$key] = new CriteriaCompo();
	$val_arr = explode(",", $val);
	$query_array[$key] = "options[{$key}]={$val}"; // to keep options in url. very important
	// if type is text
	if ($type_vars[$op] == "text") {
		foreach($val_arr as $qry) {
			// if !QUERY eg: !logs.php,views.php
			if (substr($qry,0,1) == "!") {
				$criteria_q[$key]->add(new Criteria($op, "%" . substr($qry,1) . "%", "NOT LIKE"), "AND");
			} else {
				$criteria_q[$key]->add(new Criteria($op, "%" . $qry . "%", "LIKE"), "OR");
			}
		}
	} else {
		// if there is one value - deal with =, > ,<
		if (count($val_arr) == 1) {
			$val_int = $val_arr[0];
			if($op == "log_time" || $op == "last_login") $val_int = time() - $Userlog->getSinceTime($val_int);
			// query is one int $t (=, < , >)
			$criteria_q[$key]->add(new Criteria($op, $val_int, $t));
		} else {
			// query is an array of int separate with comma. use OR ???
			$criteria_q[$key]->add(new Criteria($op, "(" . $val . ")", "IN"));
		}
	}
	// add criteria
	$criteria->add($criteria_q[$key]);
}
// END build Criteria for database

// parse query page
if ( !empty($query_array)  ) {
	$query_page = implode("&amp;", array_values($query_array));
}
// create query entry
$query_entry = "&amp;engine=" . $engine . "&amp;limitentry=" . $limitentry . "&amp;sortentry=" . $sortentry . "&amp;orderentry=" . $orderentry;
if ($engine == "file") {
	foreach($file as $oneFile) {
		$query_entry .= "&amp;file[]=" . $oneFile;
	}
}

// START delete/purge
$confirm = UserlogRequest::getString('confirm',0, 'post');
if ($opentry == "del" && !empty($confirm)) {
	if( $engine == 'db' ) {
		// delete logs in database
		$statsObj = UserlogStats::getInstance();
		if(is_numeric($log_id[0])) {
			$criteriaLogId = new CriteriaCompo();
			$criteriaLogId->add(new Criteria("log_id", "(" . implode(",",$log_id) . ")", "IN"));
			$numDel = $statsObj->delete('log', 0, 0, $criteriaLogId);
            redirect_header("logs.php?op=" . $query_entry . (!empty($query_page) ? "&amp;" . $query_page : ''), 1, sprintf(_AM_USERLOG_LOG_DELETE_SUCCESS, $numDel));
		} elseif($log_id[0] == "bulk") {
			$numDel = $statsObj->delete('log', 0, 0, $criteria);
			redirect_header("logs.php?op=" . $query_entry , 10, sprintf(_AM_USERLOG_LOG_DELETE_SUCCESS_QUERY, $numDel, $query_page) );
		}
        redirect_header("logs.php?op=" . $query_entry . (!empty($query_page) ? "&amp;" . $query_page : ''), 1, _AM_USERLOG_LOG_DELETE_ERROR);
	// for file	
	} else {
        redirect_header("logs.php?op=" . $query_entry . (!empty($query_page) ? "&amp;" . $query_page : ''), 1, _AM_USERLOG_LOG_DELETE_ERROR);
	}	
}
// END delete/purge

// get logs from engine: 1- db 2- file
$loglogObj = UserlogLog::getInstance();
if( $engine == 'db' ) {
	$logs = $Userlog->getHandler('log')->getLogs($limitentry,$startentry,$criteria,$sortentry,$orderentry ,null, false);
	$totalLogs = $Userlog->getHandler('log')->getLogsCount($criteria);
} else {
	list($logs, $totalLogs) = $loglogObj->getLogsFromFiles($file, $limitentry, $startentry, $options, $sortentry,$orderentry);
}

// pagenav to template
$pagenav = new XoopsPageNav($totalLogs, $limitentry, $startentry, 'startentry', $query_entry . (!empty($query_page) ? "&amp;" . $query_page : ''));
$GLOBALS['xoopsTpl']->assign("pagenav", !empty($pagenav) ? $pagenav->renderNav() : '');

// options/entries to template
$GLOBALS['xoopsTpl']->assign('options', $options);
$GLOBALS['xoopsTpl']->assign('totalLogs', $totalLogs);
$GLOBALS['xoopsTpl']->assign('pages', ceil($totalLogs/$limitentry));
$GLOBALS['xoopsTpl']->assign('status', sprintf(_AM_USERLOG_LOG_STATUS,$totalLogs));

$GLOBALS['xoopsTpl']->assign('startentry', $startentry);
$GLOBALS['xoopsTpl']->assign('limitentry', $limitentry);
$GLOBALS['xoopsTpl']->assign('sortentry', $sortentry);
$GLOBALS['xoopsTpl']->assign('orderentry', $orderentry);

// skip these headers because we can merge it to request method column 
$skips = array("get", "post", "request", "files", "env");
// prepared for display. timestamps and var_export
$logs = $loglogObj->arrayToDisplay($logs);

// logs to template
$GLOBALS['xoopsTpl']->assign('logs', $logs);

// query page
$GLOBALS['xoopsTpl']->assign('query_page', !empty($query_page) ? $query_page : '');

// query entry
$GLOBALS['xoopsTpl']->assign('query_entry', !empty($query_entry) ? $query_entry : '');

// var types to template
$GLOBALS['xoopsTpl']->assign('types', $type_vars);

// START main form
// form, elements, headers
list($form, $elements, $headers) = $logsetObj->logForm($options);
// START export
if (substr($opentry,0,6) == "export") {
	list($opentry,$export) = explode("-",$opentry);
	// if it is not bulk export get the actual logs in the page
	if(is_numeric($log_id[0])) {
		$logs = $Userlog->getFromKeys($logs,$log_id);
	}
	$totalLogsExport = count($logs);
	switch ($export) {
		case 'csv':
			if( $csvFile = $loglogObj->exportLogsToCsv($logs, $headers, "engine_" . $engine . "_total_" . $totalLogsExport,";")) {
				redirect_header("logs.php?op=" . $query_entry . (!empty($query_page) ? "&amp;" . $query_page : '') . "&amp;limitentry=" . (empty($limitentry) ? $Userlog->getConfig("logs_perpage") : $limitentry),
							7,
							sprintf(_AM_USERLOG_LOG_EXPORT_SUCCESS,$totalLogsExport, $csvFile) );
			}
			redirect_header("logs.php?op=" . $query_entry . (!empty($query_page) ? "&amp;" . $query_page : '') . "&amp;limitentry=" . (empty($limitentry) ? $Userlog->getConfig("logs_perpage") : $limitentry),
							1, 
							_AM_USERLOG_LOG_EXPORT_ERROR);
			break;
		default :
			break;
	}
}
// END export

// engine element
$engineEl = new XoopsFormSelect(_AM_USERLOG_ENGINE,"engine", $engine);
$engineEl->addOption("db", _AM_USERLOG_ENGINE_DB);
$engineEl->addOption("file",  _AM_USERLOG_ENGINE_FILE);
$engineEl->setDescription(_AM_USERLOG_ENGINE_DSC);
// file element
if ($engine == "file") {
	$fileEl = $loglogObj->buildFileSelectEle($file, true);// multiselect = true
	$fileEl->setDescription(_AM_USERLOG_FILE_DSC);
}
// limit, sort, order
$limitEl = new XoopsFormText(_AM_USERLOG_LOGS_PERPAGE, "limitentry", 10, 255, $limitentry);
$limitEl->setDescription(sprintf(_AM_USERLOG_LOGS_PERPAGE_DSC, $Userlog->getConfig("logs_perpage")));
$sortEl = new XoopsFormSelect(_AM_USERLOG_SORT,"sortentry", $sortentry);
$sortEl->addOptionArray($headers);
$sortEl->setDescription(_AM_USERLOG_SORT_DSC);
$orderEl = new XoopsFormSelect(_AM_USERLOG_ORDER,"orderentry", $orderentry);
$orderEl->addOption("DESC", _DESCENDING);
$orderEl->addOption("ASC",  _ASCENDING);
$orderEl->setDescription(_AM_USERLOG_ORDER_DSC);
// submit logs
$submitEl = new XoopsFormButton(_SUBMIT, 'submitlogs', _SUBMIT, 'submit');
// add elements
$form->addElement($engineEl);
if ($engine == "file") {
	$form->addElement($fileEl);
}
$form->addElement($limitEl);
$form->addElement($sortEl);
$form->addElement($orderEl);
$form->addElement($submitEl);
$GLOBALS['xoopsTpl']->assign('form', $form->render());
// END main form
// START form navigation
// formNav in the upper section
include_once USERLOG_ROOT_PATH . '/class/form/simpleform.php';
$formNav = new UserlogSimpleForm('','logsnav','logs.php', 'get');
foreach($elements as $key=>$ele) {
	$ele->setClass("hidden");
	$formNav->addElement($elements[$key]);
}
if ($engine == "file") {
	$fileEl->setClass("floatleft left");
	$fileEl->setExtra("onchange=\"document.forms.logsnav.submitlogsnav.click()\"");
	$formNav->addElement($fileEl);
}
$engineEl->setClass("floatleft left");
$engineEl->setExtra("onchange=\"document.forms.logsnav.submitlogsnav.click()\"");
$formNav->addElement($engineEl);
$limitEl->setClass("floatleft left");
$formNav->addElement($limitEl);
$sortEl->setExtra("onchange=\"document.forms.logsnav.submitlogsnav.click()\"");
$sortEl->setClass("floatleft left");
$formNav->addElement($sortEl);
$orderEl->setExtra("onchange=\"document.forms.logsnav.submitlogsnav.click()\"");
$orderEl->setClass("floatleft left");
$formNav->addElement($orderEl);
$submitEl = new XoopsFormButton('', 'submitlogsnav', _GO, 'submit');
$submitEl->setClass("floatleft left");
$formNav->addElement($submitEl);
$formNav->setExtra("onsubmit=\"preventSubmitEmptyInput('options[');\"");
$GLOBALS['xoopsTpl']->assign('formNav', $formNav->render());
// END form navigation
// START form head
// use _class = array("hidden") to reset element class
$formHead = new UserlogSimpleForm(_AM_USERLOG_LOGFORM,'logshead','logs.php', 'get');
foreach($elements as $key=>$ele) {
	$ele->_class = array("floatleft", "left");
	$formHead->addElement($elements[$key]);
}
// add class hidden to formHead
if ($engine == "file") {
	$fileEl->_class = array("hidden");
	$formHead->addElement($fileEl);
}
$engineEl->_class = array("hidden");
$formHead->addElement($engineEl);
$limitEl->_class = array("hidden");
$formHead->addElement($limitEl);
$sortEl->_class = array("hidden");
$formHead->addElement($sortEl);
$orderEl->_class = array("hidden");
$formHead->addElement($orderEl);
// add submit to formHead
$submitEl = new XoopsFormButton('', 'submitlogshead', _SUBMIT, 'submit');
$submitEl->setClass("floatleft left");
$formHead->addElement($submitEl);
$formHead->setExtra("onsubmit=\"preventSubmitEmptyInput('options[');\"");
$GLOBALS['xoopsTpl']->assign('formHead', $formHead->render());
// END form head

$indexAdmin = new ModuleAdmin(); // add this just to include the css file to template
$GLOBALS['xoopsTpl']->assign('logo',$indexAdmin->addNavigation('logs.php'));

//headers skip then to template
foreach($skips as $option) {
	unset($headers[$option]);
}
$GLOBALS['xoopsTpl']->assign('headers', $headers);
// get TOGGLE cookie
$toggles = $Userlog->getCookie("TOGGLE");
$expand = (count($toggles) > 0) ? ( (in_array('formhead', $toggles)) ? false : true ) : true;
if ($expand) {
	$formHeadToggle["toggle"] = "toggle_block";
	$formHeadToggle["icon"]  = "green";
	$formHeadToggle["alt"] = _AM_USERLOG_HIDE_FORM;
} else {
	$formHeadToggle["toggle"] = "toggle_none";
	$formHeadToggle["icon"]  = "green_off";
	$formHeadToggle["alt"] = _AM_USERLOG_SHOW_FORM;
}
$xoopsTpl->assign('formHeadToggle', $formHeadToggle);
// template
$template_main = "userlog_admin_logs.html";
if ( !empty($template_main)  ) {
    $GLOBALS['xoopsTpl']->display("db:{$template_main}");
}
xoops_cp_footer();