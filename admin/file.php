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
 * @version         $Id: file.php 1 2013-02-26 16:25:04Z irmtfan $
 */

include_once dirname(__FILE__) . '/admin_header.php';
include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
xoops_cp_header();
$Userlog = Userlog::getInstance(false);
$loglogObj = UserlogLog::getInstance();

$indexAdmin = new ModuleAdmin();

// Where do we start ?
$opentry = UserlogRequest::getString('op', '');
$file = UserlogRequest::getArray('file', !empty($opentry) ? '' : $Userlog->getConfig("file"));
$filename = UserlogRequest::getString('filename', '');
$confirm = UserlogRequest::getString('confirm',0, 'post');
$file = $loglogObj->parseFiles($file);
$totalFiles = count($file);
if(!empty($opentry) && ($confirm == 0 || $totalFiles == 0)) {
	redirect_header("file.php", 5, sprintf(_AM_USERLOG_ERROR,""));
}
switch ($opentry) {
	case "del":
		if ($deleteFiles = $loglogObj->deleteFiles($file)) {
			$msgDel = sprintf(_AM_USERLOG_FILE_DELETE_SUCCESS, $deleteFiles) . "<br\>" . implode("<br\>",$loglogObj->getErrors());
			redirect_header("file.php", 5, $msgDel);
		}
		redirect_header("file.php", 5, sprintf(_AM_USERLOG_ERROR,implode("<br\>",$loglogObj->getErrors())));
        break;
	case "rename":
		// only one file. 0 file or more than one file => error
		if ($totalFiles != 1) {
			redirect_header("file.php", 5, sprintf(_AM_USERLOG_ERROR,_AM_USERLOG_FILE_SELECT_ONE));
		}
		if ($newFile = $loglogObj->renameFile($file[0], $filename)) {
			redirect_header("file.php", 5, sprintf(_AM_USERLOG_FILE_RENAME_SUCCESS,$file[0], $newFile));
		}
		redirect_header("file.php", 5, sprintf(_AM_USERLOG_ERROR,implode("<br\>",$loglogObj->getErrors())));
        break;
	case "copy":
		// only one file. 0 file or more than one file => error
		if ($totalFiles != 1) {
			redirect_header("file.php", 5, sprintf(_AM_USERLOG_ERROR,_AM_USERLOG_FILE_SELECT_ONE));
		}
		if ($newFile = $loglogObj->copyFile($file[0], $filename)) {
			redirect_header("file.php", 5, sprintf(_AM_USERLOG_FILE_COPY_SUCCESS,$file[0], $newFile));
		}
		redirect_header("file.php", 5, sprintf(_AM_USERLOG_ERROR,implode("<br\>",$loglogObj->getErrors())));
        break;
	case "merge":
		if ($mergeFile = $loglogObj->mergeFiles($file, $filename)) {
			redirect_header("file.php", 5, sprintf(_AM_USERLOG_FILE_MERGE_SUCCESS, $totalFiles,$mergeFile));
		}
		redirect_header("file.php", 5, sprintf(_AM_USERLOG_ERROR,implode("<br\>",$loglogObj->getErrors())));
        break;
	case "zip":
		if ($zipFile = $loglogObj->zipFiles($file, $filename)) {
			$msgZip = sprintf(_AM_USERLOG_FILE_ZIP_SUCCESS,$totalFiles, $zipFile ) . "<br\>" . implode("<br\>",$loglogObj->getErrors());
			redirect_header("file.php", 5, $msgZip);
		}
		redirect_header("file.php", 5, sprintf(_AM_USERLOG_ERROR, implode("<br\>",$loglogObj->getErrors())));
        break;
	case "export-csv":
		$logsetObj = UserlogSetting::getInstance();
		$headers = $logsetObj->getOptions("","title");
		unset($headers["store_db"], $headers["store_file"], $headers["views"]);
		if($csvFile = $loglogObj->exportFilesToCsv($file, $headers, $filename,";")) {
			$msgCsv = sprintf(_AM_USERLOG_FILE_EXOPORT_SUCCESS,$totalFiles, $csvFile );
			redirect_header("file.php", 5, $msgCsv);
		}
		redirect_header("file.php", 5, sprintf(_AM_USERLOG_ERROR, implode("<br\>",$loglogObj->getErrors())));
        break;
}
$form = new XoopsThemeForm(_AM_USERLOG_ADMENU_FILE,'filemanager','file.php', 'post');
$fileEl = $loglogObj->buildFileSelectEle($file, true, 10);// multiselect = true, size=10
$form->addElement($fileEl);
$actionEl = new XoopsFormSelect(_AM_USERLOG_FILE_ACTION,"op", $opentry);
$actions = array(	"zip" =>_AM_USERLOG_FILE_ZIP,
					"del"=>_DELETE,
					"rename"=>_AM_USERLOG_FILE_RENAME,
					"copy"=>_AM_USERLOG_FILE_COPY,
					"merge"=>_AM_USERLOG_FILE_MERGE,
					"export-csv"=>_AM_USERLOG_FILE_EXPORT_CSV
				);
$actionEl->addOptionArray($actions);
$actionEl->setExtra("onchange=\"var el = document.forms.filemanager.filename.parentElement.parentElement; el.className = ''; if(this.value == 'del') { el.className = 'hidden'}\"");
$form->addElement($actionEl);
$filenameEl = new XoopsFormText(_AM_USERLOG_FILE_FILENAME, "filename", 50, 255, '');
$filenameEl->setDescription(_AM_USERLOG_FILE_FILENAME_DSC);
$form->addElement($filenameEl);
$submitEl = new XoopsFormButton(_SUBMIT, 'submitfilemanager', _SUBMIT, 'submit');
$form->addElement($submitEl);
$confirmEl = new XoopsFormHidden("confirm",0);
$confirmEl->customValidationCode[]="if(confirm('" . _AM_USERLOG_FILE_CONFIRM . " ' + myform.op.options[myform.op.selectedIndex].innerHTML + '\\n " . _AM_USERLOG_FILE . ": ' + myform.file.value)) {myform.confirm.value = 1;} else {return false;};";
$form->addElement($confirmEl);
$GLOBALS['xoopsTpl']->assign('form', $form->render());
$GLOBALS['xoopsTpl']->assign('logo',$indexAdmin->addNavigation('file.php'));
// template
$template_main = USERLOG_DIRNAME . "_admin_file.html";
if ( !empty($template_main)  ) {
    $GLOBALS['xoopsTpl']->display("db:{$template_main}");
}
xoops_cp_footer();