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
 * @package         userlog blocks
 * @since           1
 * @author          irmtfan (irmtfan@yahoo.com)
 * @author          The XOOPS Project <www.xoops.org> <www.xoops.ir>
 * @version         $Id: views.php 1 2013-02-26 16:25:04Z irmtfan $
 */

defined('XOOPS_ROOT_PATH') or die('Restricted access');
include_once dirname(dirname(__FILE__)) . '/include/common.php';

if (defined('USERLOG_BLOCK_VIEWS_DEFINED')) return;
define('USERLOG_BLOCK_VIEWS_DEFINED',true);
xoops_loadLanguage("admin",USERLOG_DIRNAME);

// options[0] - number of items to show in block. the default is 10
// options[1] - items to select in Where claus
// options[2] - Time period - default: 1 day
// options[3] - Uid in WHERE claus: select some users to only count views by them -1 -> all (by default)
// options[4] - Gid in WHERE claus: select some groups to only count views by them 0 -> all (by default)
// options[5] - Sort - views, module dirname, module name, module views default: views
// options[6] - Order - DESC, ASC default: DESC

function userlog_views_show($options)
{
    $loglogObj = UserlogLog::getInstance();
    $module=array();
    if (!empty($options[1])) {
        $options_views = explode(',', $options[1]); // item views in where claus eg: news-storyid, newbb-topic_id, news-storytopic
        foreach ($options_views as $key=>$item) {
            $module_script_item = explode('-', $item); // news:article.php-storyid news:index.php-storytopic => $module["news"]=array("storyid","storytopic");
            $module_script = explode(':', $module_script_item[0]); // 	news:article.php => $module_script = array(news,article.php);
            if (!isset($module[$module_script[0]])) {
                $module[$module_script[0]]["item_name"] = array();
                $module[$module_script[0]]["script"] = array_slice($module_script,1);
            }
            $module[$module_script[0]]["script"] = array_unique(array_merge($module[$module_script[0]]["script"], array_slice($module_script,1)));
            $module[$module_script[0]]["item_name"][] = $module_script_item[1];
        }
    }
    $users = ($options[3] != -1) ? explode(",",$options[3]) : array();
    $groups = !empty($options[4]) ? explode(",",$options[4]) : array();
    
    $items = $loglogObj->getViews($options[0] , 0, $options[5], $options[6], $module, $options[2], $users, $groups);
    $block = array();
    $block["items"] = $items;
    $block["sort"] = $options[5];

    return $block;
}

function userlog_views_edit($options)
{
    // include_once XOOPS_ROOT_PATH . "/class/blockform.php"; //reserve for 2.6
    xoops_load('XoopsFormLoader');
    // $form = new XoopsBlockForm(); //reserve for 2.6
    $form = new XoopsThemeForm(_AM_USERLOG_VIEW,'views','');
    
    $module_handler =& xoops_gethandler('module');
    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('hasnotification', 1));
    $criteria->add(new Criteria('isactive', 1));
    $modules = $module_handler->getObjects($criteria, true);
    $hasviews = array();
    foreach ($modules as $module) {
        $not_config = $module->getInfo('notification');
        foreach ($not_config['category'] as $category) {
            if (!empty($category['item_name'])) {
                $script = is_array($category["subscribe_from"]) ? implode(":", $category["subscribe_from"]) : $category["subscribe_from"];
                $hasviews[$module->dirname(). ":" . $script . "-" . $category['item_name']] = $module->dirname()."/" . $script ."?".$category['item_name']."=ITEM_ID";
            }
        }
    }
    
    $i=0;
    // number of items to display element
    $numitemsEle = new XoopsFormText(_AM_USERLOG_ITEMS_NUM, "options[{$i}]", 10, 255, intval($options[$i]));

    $i++;
    // views element
    $options_views = explode(',', $options[$i]);
    $viewsEle = new XoopsFormCheckBox(_AM_USERLOG_ITEMS, "options[{$i}][]", $options_views);
    $viewsEle->columns = 3;
    $viewsEle->addOptionArray($hasviews);
    $check_all = _ALL . ": <input type=\"checkbox\" name=\"item_check\" id=\"item_check\" value=\"1\" onclick=\"xoopsCheckGroup('blockform', 'item_check','options[{$i}][]');\" />"; // blockform is the main form
    $viewsEle = new XoopsFormLabel(_AM_USERLOG_ITEMS, $check_all ."<br\>". $viewsEle->render());
    $viewsEle->setDescription(_AM_USERLOG_ITEMS_DSC);
    
    $i++;
    $timeEle = new XoopsFormText(_AM_USERLOG_LOG_TIMEGT, "options[{$i}]", 10, 255, $options[$i]);
    $timeEle->setDescription(_AM_USERLOG_LOG_TIMEGT_FORM);

    $i++;
    $userRadioEle = new XoopsFormRadio(_AM_USERLOG_UID, "options[{$i}]", $options[$i]);
    $userRadioEle->addOption(-1,_ALL);
    $userRadioEle->addOption(($options[$i] != -1) ? $options[$i] : 0,_SELECT); // if no user in selection box it select uid=0 anon users
    $userRadioEle->setExtra("onchange=\"var el=document.getElementById('options[{$i}]'); el.disabled=(this.id == 'options[{$i}]1'); if (!el.value) {el.value= this.value}\""); // if user dont select any option it select "all"
    $userSelectEle = new XoopsFormSelectUser(_AM_USERLOG_UID, "options[{$i}]", true, explode(',', $options[$i]), 3, true);
    $userEle = new XoopsFormLabel(_AM_USERLOG_UID, $userRadioEle->render().$userSelectEle->render());

    $i++;
    $groupRadioEle = new XoopsFormRadio(_AM_USERLOG_GROUPS, "options[{$i}]", !empty($options[$i]));
    $groupRadioEle->addOption(0,_ALL);
    $groupRadioEle->addOption(!empty($options[$i]) ? $options[$i] : 2,_SELECT); // if no group in selection box it select gid=2 registered users
    $groupRadioEle->setExtra("onchange=\"var el=document.getElementById('options[{$i}]'); el.disabled=(this.id == 'options[{$i}]1'); if (!el.value) {el.value= this.value}\""); // if group dont select any option it select "all"
    $groupSelectEle = new XoopsFormSelectGroup(_AM_USERLOG_GROUPS, "options[{$i}]", true, explode(',', $options[$i]), 3, true);
    $groupEle = new XoopsFormLabel(_AM_USERLOG_GROUPS, $groupRadioEle->render().$groupSelectEle->render());

    $i++;
    $sortEle = new XoopsFormSelect(_AM_USERLOG_SORT, "options[{$i}]", $options[$i]);
    $sortEle->addOptionArray(array(
                                "count"=>_AM_USERLOG_VIEW,
                                "module"=>_AM_USERLOG_MODULE,
                                "module_name"=>_AM_USERLOG_MODULE_NAME,
                                "module_count"=>_AM_USERLOG_VIEW_MODULE
                                ));
    $sortEle->setDescription(_AM_USERLOG_SORT_DSC);
    
    $i++;
    $orderEle = new XoopsFormSelect(_AM_USERLOG_ORDER,"options[{$i}]", $options[$i]);
    $orderEle->addOption("DESC", _DESCENDING);
    $orderEle->addOption("ASC",  _ASCENDING);
    $orderEle->setDescription(_AM_USERLOG_ORDER_DSC);
    
    // add all elements to form
    $form->addElement($numitemsEle);
    $form->addElement($viewsEle);
    $form->addElement($timeEle);
    $form->addElement($userEle);
    $form->addElement($groupEle);
    $form->addElement($sortEle);
    $form->addElement($orderEle);
    
    return $form->render();
}
