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

include_once __DIR__ . '/admin_header.php';
include_once XOOPS_ROOT_PATH . '/class/pagenav.php';

xoops_cp_header();

$Userlog = Userlog::getInstance(false);
$op      = XoopsRequest::getString('op');
// Where do we start ?
$set_id    = XoopsRequest::getInt('set_id', 0);
$logsetObj = $set_id ? $Userlog->getHandler('setting')->get($set_id) : UserlogSetting::getInstance();
if ($set_id && !is_object($logsetObj)) {
    redirect_header('setting.php', 1, _AM_USERLOG_SET_ERROR);
}
$name  = XoopsRequest::getString('name', '', 'post');
$logby = XoopsRequest::getString('logby', '', 'post');
if ($logby === 'ip') {
    $unique_id = XoopsRequest::getString('unique_id', -1, 'post');
    $unique_id = ip2long($unique_id);
} else {
    $unique_id = XoopsRequest::getInt('unique_id', -1, 'post');
}
$option = XoopsRequest::getArray('option', '', 'post');

$scope = XoopsRequest::getArray('scope', '', 'post');

$startentry = XoopsRequest::getInt('startentry', 0);

switch ($op) {
    case 'del':
        if (empty($set_id)) {
            redirect_header('setting.php', 1, _AM_USERLOG_SET_ERROR);
        }
        $confirm = XoopsRequest::getString('confirm', 0, 'post');
        if ($confirm) {
            if ($logsetObj->deleteFile($logsetObj->logby(), $logsetObj->getVar('unique_id'))) { //use getVar to get IP long
                $msgDel = _AM_USERLOG_SET_CLEANCACHE_SUCCESS;
            }
            if (!$Userlog->getHandler('setting')->delete($logsetObj)) {
                redirect_header('setting.php', 1, sprintf(_AM_USERLOG_SET_DELETE_ERROR, $logsetObj->name()));
            }
            $msgDel .= '<br>' . sprintf(_AM_USERLOG_SET_DELETE_SUCCESS, $logsetObj->name());
            redirect_header('setting.php', 1, sprintf($msgDel, 1)); // one cache file deleted
        } else {
            xoops_confirm(array('op' => 'del', 'set_id' => $logsetObj->set_id(), 'confirm' => 1), 'setting.php', sprintf(_AM_USERLOG_SET_DELETE_CONFIRM, $logsetObj->name()), _DELETE);
            xoops_cp_footer();
        }
        break;

    case 'addsetting':
        $message = _AM_USERLOG_SET_EDIT;
        // check to insure only one (logby and unique_id) added to database
        if (!$set_id) {
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('logby', $logby));
            $criteria->add(new Criteria('unique_id', $unique_id));
            $logsetObj = $Userlog->getHandler('setting')->getObjects($criteria);
            if ($logsetObj) {
                $logsetObj = $logsetObj[0];
                $message   = _AM_USERLOG_SET_UPDATE;
            } elseif ($logby != '') {
                $logsetObj = $Userlog->getHandler('setting')->create();
                $message   = _AM_USERLOG_SET_CREATE;
            } else {
                redirect_header('setting.php', 1, _AM_USERLOG_SET_ERROR);
            }
        }
        $logsetObj->setVar('name', $name);
        $logsetObj->setVar('logby', $logby);
        $logsetObj->setVar('unique_id', $unique_id);
        // select views means store uid, groups, script name, pagetitle, pageadmin, module, module_name, item name, item id in Database
        if (in_array('views', $option)) {
            $option = array_merge(array(
                                      'uid',
                                      'groups',
                                      'script',
                                      'pagetitle',
                                      'pageadmin',
                                      'module',
                                      'module_name',
                                      'item_name',
                                      'item_id'
                                  ), $option);
        }
        // always log id and time
        if (!empty($option[0])) {
            $option = array_merge(array('log_id', 'log_time'), $option);
        }
        $options_arr = $logsetObj->getOptions($option, 'key');// empty means all. sanitize options
        $logsetObj->setVar('options', implode(',', $options_arr));
        $logsetObj->setVar('scope', implode(',', $scope));
        if ($logsetObj->storeSet(true)) {
            $message .= '<br>' . _AM_USERLOG_SET_CACHE;
        } else {
            redirect_header('setting.php', 1, _AM_USERLOG_SET_ERROR);
        }
        redirect_header('setting.php', 1, sprintf($message, $logsetObj->name()));
        break;
    case 'cancel':
        redirect_header('setting.php', 1, _AM_USERLOG_SET_CANCEL);

    case 'cleanCash':
        // delete all settings caches
        if ($numfiles = $logsetObj->cleanCache()) {
            redirect_header('setting.php', 1, sprintf(_AM_USERLOG_SET_CLEANCACHE_SUCCESS, $numfiles));
        } else {
            redirect_header('setting.php', 1, _AM_USERLOG_SET_CLEANCACHE_NOFILE);
        }
        break;
    case 'default':
    default:
        // get all dirnames for scope
        $dirNames = $Userlog->getModules();
        // unset userlog
        //unset($dirNames[USERLOG_DIRNAME]);
        // get all settings as array
        $sets      = $Userlog->getHandler('setting')->getSets($Userlog->getConfig('sets_perpage'), $startentry, null, 'set_id', 'DESC', null, false);
        $totalSets = $Userlog->getHandler('setting')->getCount();
        $pagenav   = new XoopsPageNav($totalSets, $Userlog->getConfig('sets_perpage'), $startentry, 'startentry');
        // check set arrays
        foreach ($sets as $id => $set) {
            // ip to string
            if ($set['logby'] === 'ip') {
                $sets[$id]['unique_id'] = long2ip($set['unique_id']);
            }
            // logby to title
            $sets[$id]['logby'] = $logsetObj->all_logby[$set['logby']];

            // options to title
            $options              = $logsetObj->getOptions($set['options'], 'title');
            $sets[$id]['active']  = !empty($options['active']); // add active option to smarty var
            $sets[$id]['options'] = implode(',', $options);

            // modules to name
            if (empty($set['scope'])) {
                $sets[$id]['scope'] = _ALL; // no scope means all
                continue;
            }
            $scope   = explode(',', $set['scope']);
            $dir_str = '';
            foreach ($scope as $sc) {
                $dir_str .= ',' . $dirNames[$sc];
            }
            $sets[$id]['scope'] = $dir_str;
        }
        // buttons
        $indexAdmin = new ModuleAdmin();
        if ($totalSets > 0) {
            $indexAdmin->addItemButton(_AM_USERLOG_SET_CLEANCACHE_ALL, 'setting.php?op=cleanCash', 'delete');
        }
        if ($set_id) { // if in edit mode add a button
            $indexAdmin->addItemButton(_AM_USERLOG_SET_ADD, 'setting.php');
        }
        // template
        $template_main = USERLOG_DIRNAME . '_admin_sets.tpl';
        // form
        $form    = new XoopsThemeForm($set_id ? _EDIT . ' ' . $logsetObj->name() : _AM_USERLOG_SET_ADD, 'setting', 'setting.php?op=addsetting', 'post', true);
        $nameEle = new XoopsFormText(_AM_USERLOG_SET_NAME, 'name', 10, 20, $logsetObj->name());
        $nameEle->setDescription(_AM_USERLOG_SET_NAME_DSC);

        $logbyEle = new XoopsFormSelect(_AM_USERLOG_SET_LOGBY, 'logby', $logsetObj->logby());
        $logbyEle->addOptionArray($logsetObj->all_logby);
        $logbyEle->setDescription(_AM_USERLOG_SET_LOGBY_DSC);

        $unique_idEle = new XoopsFormText(_AM_USERLOG_SET_UNIQUE_ID, 'unique_id', 10, 20, $logsetObj->unique_id());
        $unique_idEle->setDescription(_AM_USERLOG_SET_UNIQUE_ID_DSC);

        $options_arr        = explode(',', $logsetObj->options());
        $optionEle          = new XoopsFormCheckBox(_AM_USERLOG_SET_OPTIONS, 'option[]', $options_arr);
        $optionEle->columns = 4;
        $headers            = $logsetObj->getOptions('', 'title');
        // always log id and time
        unset($headers['log_id'], $headers['log_time']);
        $optionEle->addOptionArray($headers);
        //$optionEle->isRequired();
        //$optionEle->renderValidationJS();
        $check_all = _ALL . ": <input type=\"checkbox\" name=\"option_check\" id=\"option_check\" value=\"0\" onclick=\"xoopsCheckGroup('setting', 'option_check','option[]');\" />";
        //$optiontrayEle = new XoopsFormElementTray(_AM_USERLOG_SET_OPTIONS, "<br\>", 'tray');
        $optionEle = new XoopsFormLabel(_AM_USERLOG_SET_OPTIONS, $check_all . "<br\>" . $optionEle->render());
        $optionEle->setDescription(_AM_USERLOG_SET_OPTIONS_DSC);

        $scope_arr         = explode(',', $logsetObj->scope());
        $scopeEle          = new XoopsFormCheckBox(_AM_USERLOG_SET_SCOPE, 'scope[]', $scope_arr);
        $scopeEle->columns = 4;
        $scopeEle->addOptionArray($dirNames);
        $check_all = _ALL . ": <input type=\"checkbox\" name=\"scope_check\" id=\"scope_check\" value=\"1\" onclick=\"xoopsCheckGroup('setting', 'scope_check','scope[]');\" />";
        $scopeEle  = new XoopsFormLabel(_AM_USERLOG_SET_SCOPE, $check_all . "<br\>" . $scopeEle->render());
        $scopeEle->setDescription(_AM_USERLOG_SET_SCOPE_DSC);

        $submitEle = new XoopsFormButton('', 'post', _SUBMIT, 'submit');
        $set_idEle = new XoopsFormHidden('set_id', $set_id);

        $form->addElement($nameEle, true);
        $form->addElement($logbyEle);
        $form->addElement($unique_idEle, true);
        $form->addElement($optionEle);
        $form->addElement($scopeEle);
        $form->addElement($set_idEle);
        $form->addElement($submitEle);

        break;
}
if (!empty($form)) {
    $GLOBALS['xoopsTpl']->assign('form', $form->render());
}
if (!empty($sets)) {
    //add set arrays to template
    $GLOBALS['xoopsTpl']->assign('sets', $sets);
}
if (!empty($pagenav)) {
    $GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav());
}
if (!empty($indexAdmin)) {
    $GLOBALS['xoopsTpl']->assign('addset', $indexAdmin->renderButton('left'));
    $GLOBALS['xoopsTpl']->assign('logo', $indexAdmin->addNavigation(basename(__FILE__)));
}
if (!empty($template_main)) {
    $GLOBALS['xoopsTpl']->display("db:{$template_main}");
}
xoops_cp_footer();
