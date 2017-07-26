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
 * @package         userlog class plugin
 * @since           1.16
 * @author          irmtfan (irmtfan@yahoo.com)
 * @author          XOOPS Project <www.xoops.org> <www.xoops.ir>
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Class NewbbUserlogPlugin
 */
class NewbbUserlogPlugin extends Userlog_Module_Plugin_Abstract implements UserlogPluginInterface
{
    /**
     * @param string $subscribe_from Name of the script
     *
     * 'name' => 'thread';
     * 'title' => _MI_NEWBB_THREAD_NOTIFY;
     * 'description' => _MI_NEWBB_THREAD_NOTIFYDSC;
     * 'subscribe_from' => 'viewtopic.php';
     * 'item_name' => 'topic_id';
     * 'allow_bookmark' => 1;
     *
     * publisher:
     * 'name' = 'category_item';
     * 'title' = _MI_PUBLISHER_CATEGORY_ITEM_NOTIFY;
     * 'description' = _MI_PUBLISHER_CATEGORY_ITEM_NOTIFY_DSC;
     * 'subscribe_from' = array('index.php', 'category.php', 'item.php');
     * 'item_name' = 'categoryid';
     * 'allow_bookmark' = 1;
     *
     * empty($subscribe_from):
     * @return array $script_arr["item_name"] name of the item = array("subscribe_from1", "subscribe_from2") Name of the script
     *
     * !empty($subscribe_from):
     * @return array $item["item_name"] name of the item, $item["item_id"] id of the item
     */
    public function item($subscribe_from)
    {
        if (empty($subscribe_from)) {
            $script_arr             = array();
            $script_arr['topic_id'] = array('viewtopic.php');
            $script_arr['forum']    = array('viewforum.php');

            return $script_arr;
        }

        switch ($subscribe_from) {
            case 'viewtopic.php':
                $topicHandler = xoops_getModuleHandler('topic', 'newbb');
                $post_id      = !empty($_REQUEST['post_id']) ? (int)$_REQUEST['post_id'] : 0;
                $move         = isset($_GET['move']) ? strtolower($_GET['move']) : '';
                $topic_id     = !empty($_REQUEST['topic_id']) ? (int)$_REQUEST['topic_id'] : 0;
                if (!empty($post_id)) {
                    $topic_obj = $topicHandler->getByPost($post_id);
                    $topic_id  = $topic_obj->getVar('topic_id');
                } elseif (!empty($move)) {
                    $forum_id  = !empty($_REQUEST['forum_id']) ? (int)$_REQUEST['forum_id'] : 0;
                    $topic_obj = $topicHandler->getByMove($topic_id, ($move === 'prev') ? -1 : 1, $forum_id);
                    $topic_id  = $topic_obj->getVar('topic_id');
                }

                return array('item_name' => 'topic_id', 'item_id' => $topic_id);
                break;
            case 'viewforum.php':
                $forum_id = !empty($_REQUEST['forum']) ? (int)$_REQUEST['forum'] : 0;

                return array('item_name' => 'forum', 'item_id' => $forum_id);
                break;
        }

        return false;
    }
}
