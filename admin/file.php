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
 * @since           1
 * @author          irmtfan (irmtfan@yahoo.com)
 * @author          XOOPS Project <www.xoops.org> <www.xoops.ir>
 */

use Xmf\Request;

require_once __DIR__ . '/admin_header.php';
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
xoops_cp_header();
$userlog   = Userlog::getInstance();
$loglogObj = UserlogLog::getInstance();

$adminObject = \Xmf\Module\Admin::getInstance();

// Where do we start ?
$opentry    = Request::getString('op', '');
$file       = Request::getArray('file', !empty($opentry) ? '' : $userlog->getConfig('file'));
$filename   = Request::getString('filename', '');
$confirm    = Request::getString('confirm', 0, 'post');
$file       = $loglogObj->parseFiles($file);
$totalFiles = count($file);
if (!empty($opentry) && (0 == $confirm || 0 == $totalFiles)) {
    redirect_header('file.php', 5, sprintf(_AM_USERLOG_ERROR, ''));
}
switch ($opentry) {
    case 'del':
        if ($deleteFiles = $loglogObj->deleteFiles($file)) {
            $msgDel = sprintf(_AM_USERLOG_FILE_DELETE_SUCCESS, $deleteFiles) . "<br\>" . implode("<br\>", $loglogObj->getErrors());
            redirect_header('file.php', 5, $msgDel);
        }
        redirect_header('file.php', 5, sprintf(_AM_USERLOG_ERROR, implode("<br\>", $loglogObj->getErrors())));
        break;
    case 'rename':
        // only one file. 0 file or more than one file => error
        if (1 != $totalFiles) {
            redirect_header('file.php', 5, sprintf(_AM_USERLOG_ERROR, _AM_USERLOG_FILE_SELECT_ONE));
        }
        if ($newFile = $loglogObj->renameFile($file[0], $filename)) {
            redirect_header('file.php', 5, sprintf(_AM_USERLOG_FILE_RENAME_SUCCESS, $file[0], $newFile));
        }
        redirect_header('file.php', 5, sprintf(_AM_USERLOG_ERROR, implode("<br\>", $loglogObj->getErrors())));
        break;
    case 'copy':
        // only one file. 0 file or more than one file => error
        if (1 != $totalFiles) {
            redirect_header('file.php', 5, sprintf(_AM_USERLOG_ERROR, _AM_USERLOG_FILE_SELECT_ONE));
        }
        if ($newFile = $loglogObj->copyFile($file[0], $filename)) {
            redirect_header('file.php', 5, sprintf(_AM_USERLOG_FILE_COPY_SUCCESS, $file[0], $newFile));
        }
        redirect_header('file.php', 5, sprintf(_AM_USERLOG_ERROR, implode("<br\>", $loglogObj->getErrors())));
        break;
    case 'merge':
        if ($mergeFile = $loglogObj->mergeFiles($file, $filename)) {
            redirect_header('file.php', 5, sprintf(_AM_USERLOG_FILE_MERGE_SUCCESS, $totalFiles, $mergeFile));
        }
        redirect_header('file.php', 5, sprintf(_AM_USERLOG_ERROR, implode("<br\>", $loglogObj->getErrors())));
        break;
    case 'zip':
        if ($zipFile = $loglogObj->zipFiles($file, $filename)) {
            $msgZip = sprintf(_AM_USERLOG_FILE_ZIP_SUCCESS, $totalFiles, $zipFile) . "<br\>" . implode("<br\>", $loglogObj->getErrors());
            redirect_header('file.php', 5, $msgZip);
        }
        redirect_header('file.php', 5, sprintf(_AM_USERLOG_ERROR, implode("<br\>", $loglogObj->getErrors())));
        break;
    case 'export-csv':
        $logsetObj = UserlogSetting::getInstance();
        $headers   = $logsetObj->getOptions('', 'title');
        unset($headers['store_db'], $headers['store_file'], $headers['views']);
        if ($csvFile = $loglogObj->exportFilesToCsv($file, $headers, $filename, ';')) {
            $msgCsv = sprintf(_AM_USERLOG_FILE_EXOPORT_SUCCESS, $totalFiles, $csvFile);
            redirect_header('file.php', 5, $msgCsv);
        }
        redirect_header('file.php', 5, sprintf(_AM_USERLOG_ERROR, implode("<br\>", $loglogObj->getErrors())));
        break;
}
$form   = new XoopsThemeForm(_AM_USERLOG_ADMENU_FILE, 'filemanager', 'file.php', 'post', true);
$fileEl = $loglogObj->buildFileSelectEle($file, true, 10);// multiselect = true, size=10
$form->addElement($fileEl);
$actionEl = new XoopsFormSelect(_AM_USERLOG_FILE_ACTION, 'op', $opentry);
$actions  = [
    'zip'        => _AM_USERLOG_FILE_ZIP,
    'del'        => _DELETE,
    'rename'     => _AM_USERLOG_FILE_RENAME,
    'copy'       => _AM_USERLOG_FILE_COPY,
    'merge'      => _AM_USERLOG_FILE_MERGE,
    'export-csv' => _AM_USERLOG_FILE_EXPORT_CSV
];
$actionEl->addOptionArray($actions);
$actionEl->setExtra("onchange=\"var el = document.forms.filemanager.filename.parentElement.parentElement; el.className = ''; if (this.value == 'del') { el.className = 'hidden'}\"");
$form->addElement($actionEl);
$filenameEl = new XoopsFormText(_AM_USERLOG_FILE_FILENAME, 'filename', 50, 255, '');
$filenameEl->setDescription(_AM_USERLOG_FILE_FILENAME_DSC);
$form->addElement($filenameEl);
$submitEl = new XoopsFormButton(_SUBMIT, 'submitfilemanager', _SUBMIT, 'submit');
$form->addElement($submitEl);
$confirmEl                         = new XoopsFormHidden('confirm', 0);
$confirmEl->customValidationCode[] = "if (confirm('" . _AM_USERLOG_FILE_CONFIRM . " ' + myform.op.options[myform.op.selectedIndex].innerHTML + '\\n " . _AM_USERLOG_FILE . ": ' + myform.file.value)) {myform.confirm.value = 1;} else {return false;};";
$form->addElement($confirmEl);
$GLOBALS['xoopsTpl']->assign('form', $form->render());
$GLOBALS['xoopsTpl']->assign('logo', $adminObject->displayNavigation(basename(__FILE__)));
// template
$template_main = USERLOG_DIRNAME . '_admin_file.tpl';
if (!empty($template_main)) {
    $GLOBALS['xoopsTpl']->display("db:{$template_main}");
}
xoops_cp_footer();
