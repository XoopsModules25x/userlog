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
 * @package         userlog class
 * @since           1
 * @author          irmtfan (irmtfan@yahoo.com)
 * @author          XOOPS Project <www.xoops.org> <www.xoops.ir>
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access.');
require_once __DIR__ . '/../include/common.php';

xoops_loadLanguage('admin', USERLOG_DIRNAME);
xoops_load('XoopsFormLoader');

/**
 * Class UserlogSetting
 */
class UserlogSetting extends XoopsObject
{
    /**
     * @var string
     */
    public $all_logby = ['uid' => _AM_USERLOG_UID, 'gid' => _AM_USERLOG_SET_GID, 'ip' => _AM_USERLOG_SET_IP];

    public $userlog = null;

    /**
     * constructor
     */
    public function __construct()    {

        $this->userlog = Userlog::getInstance();
        $this->initVar('set_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('logby', XOBJ_DTYPE_TXTBOX, null, true, 10);
        $this->initVar('unique_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('options', XOBJ_DTYPE_TXTAREA, '', false);
        $this->initVar('scope', XOBJ_DTYPE_TXTAREA, '', false);
    }

    /**
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        $arg = isset($args[0]) ? $args[0] : null;

        return $this->getVar($method, $arg);
    }

    /**
     * @return UserlogSetting
     */
    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * @return mixed|string
     */
    public function unique_id()
    {
        if ('ip' === $this->getVar('logby')) {
            return long2ip($this->getVar('unique_id'));
        }

        return $this->getVar('unique_id');
    }

    /**
     * @param bool $force
     *
     * @return bool
     */
    public function storeSet($force = true)
    {
        if ($this->setDb(true)) {
            // use $this->getVar('unique_id') (int ip) instead of $this->unique_id() (string ip)
            if ($this->setFile($this->logby(), $this->getVar('unique_id'), [$this->options(), $this->scope()])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array|bool
     */
    public function getSet()
    {
        $options = '';
        // if uid setting exist in File
        $unique_uid = $this->userlog->getUser() ? $this->userlog->getUser()->getVar('uid') : 0;
        if ($options = $this->getFile('uid', $unique_uid)) {
            return $options;
        }

        // if gid setting exist in File
        $unique_gid = $this->userlog->getUser() ? $this->userlog->getUser()->getGroups() : [XOOPS_GROUP_ANONYMOUS];
        foreach ($unique_gid as $gid) {
            if ($options = $this->getFile('gid', $gid)) {
                return $options;
            }
        }
        // if ip setting exist in File
        $unique_ip = XoopsUserUtility::getIP(); // ip as int
        if ($options = $this->getFile('ip', $unique_ip)) {
            return $options;
        }
        // if all exist in File
        if ($options = $this->getFile('all', 0)) {
            return $options;
        }
        ///////////////////////////////////////////////////////////
        // check probability
        if (!$this->userlog->probCheck($this->userlog->getConfig('probset'))) {
            return false;
        }
        // database get All is better for performance???
        $logsetsObj = $this->userlog->getHandler('setting')->getAll();
        if (empty($logsetsObj)) {
            return false;
        } // if not set in db return false
        $uid_unique_uid = 'uid' . $unique_uid;
        foreach ($unique_gid as $key => $gid) {
            $gid_unique_gid[$key] = 'gid' . $gid;
        }
        $ip_unique_ip = 'ip' . $unique_ip;
        foreach ($logsetsObj as $setObj) {
            $sLogby     = $setObj->logby();
            $sUnique_id = $setObj->getVar('unique_id');
            $sLogbyId   = $sLogby . $sUnique_id;
            // if uid setting exist in db return it
            if ($sLogbyId == $uid_unique_uid
                || // if gid setting exist in db return it
                in_array($sLogbyId, $gid_unique_gid)
                || // if ip setting exist in db return it
                $sLogbyId == $ip_unique_ip
            ) {
                $sets = [$setObj->options(), $setObj->scope()];
                $this->setFile($sLogby, $sUnique_id, $sets); // build cache

                return $sets;
            }
            // if all exist in db
            if (0 == $sUnique_id) {
                $sets = [$setObj->options(), $setObj->scope()];
                $this->setFile('all', 0, $sets); // build cache

                return $sets;
            }
        }

        return false;
    }

    /**
     * @param bool $force
     *
     * @return mixed
     */
    public function setDb($force = true)
    {
        $ret = $this->userlog->getHandler('setting')->insert($this, $force);
        $this->unsetNew();

        return $ret;
    }

    public function getDb()
    {
    }

    /**
     * @param string $logby
     * @param        $unique_id
     * @param        $options
     *
     * @return bool
     */
    public function setFile($logby = 'uid', $unique_id, $options)
    {
        return $this->_createCacheFile($options, "setting_{$logby}_{$unique_id}");
    }

    /**
     * @param string $logby
     * @param        $unique_id
     *
     * @return bool|mixed
     */
    public function getFile($logby = 'uid', $unique_id)
    {
        return $this->_loadCacheFile("setting_{$logby}_{$unique_id}");
    }

    /**
     * @param string $logby
     * @param        $unique_id
     *
     * @return bool
     */
    public function deleteFile($logby = 'uid', $unique_id)
    {
        return $this->_deleteCacheFile("setting_{$logby}_{$unique_id}");
    }

    /**
     * @param        $data
     * @param null   $name
     * @param string $root_path
     *
     * @return bool
     */
    private function _createCacheFile($data, $name = null, $root_path = XOOPS_CACHE_PATH)
    {
        $name = $name ?: (string)time();
        $key  = USERLOG_DIRNAME . "_{$name}";

        //$cacheHandler = XoopsCache::config($key, array('path' => XOOPS_VAR_PATH . '/caches/xoops_cache/userlog'));
        return XoopsCache::write($key, $data);
    }

    /**
     * @param null   $name
     * @param string $root_path
     *
     * @return bool|mixed
     */
    private function _loadCacheFile($name = null, $root_path = XOOPS_CACHE_PATH)
    {
        if (empty($name)) {
            return false;
        }
        $key = USERLOG_DIRNAME . "_{$name}";

        return XoopsCache::read($key);
    }

    /**
     * @param null   $name
     * @param string $root_path
     *
     * @return bool
     */
    private function _deleteCacheFile($name = null, $root_path = XOOPS_CACHE_PATH)
    {
        if (empty($name)) {
            return false;
        }
        $key = USERLOG_DIRNAME . "_{$name}";

        return XoopsCache::delete($key);
    }

    /**
     * @param null   $option
     * @param string $V
     *
     * @return array
     */
    public function getOptions($option = null, $V = 'value')
    {
        $V = strtolower($V);

        if ($this->userlog->getUser()) {
            $uid        = $this->userlog->getUser()->getVar('uid');
            $uname      = $this->userlog->getUser()->getVar('uname');
            $last_login = $this->userlog->getUser()->getVar('last_login');
            $admin      = $this->userlog->getUser()->isAdmin();
            $groups     = 'g' . implode('g', array_unique($this->userlog->getUser()->getGroups())); // g1g2
        } else {
            $uid        = 0;
            $uname      = '';
            $last_login = 0;
            $admin      = 0;
            $groups     = 'g' . XOOPS_GROUP_ANONYMOUS; // g3
        }
        $tempUserLog = explode('/', $_SERVER['PHP_SELF']);
        $options = [
            'log_id'         => [
                'type'  => 'int',
                'title' => _AM_USERLOG_LOG_ID,
                'value' => null // null for now
            ],
            'log_time'       => [
                'type'  => 'int',
                'title' => _AM_USERLOG_LOG_TIME,
                'value' => time()
            ],
            'uid'            => [
                'type'  => 'int',
                'title' => _AM_USERLOG_UID,
                'value' => $uid
            ],
            'uname'          => [
                'type'  => 'text',
                'title' => _AM_USERLOG_UNAME,
                'value' => $uname
            ],
            'admin'          => [
                'type'  => 'bool',
                'title' => _AM_USERLOG_ADMIN,
                'value' => $admin
            ],
            'groups'         => [
                'type'  => 'text',
                'title' => _AM_USERLOG_GROUPS,
                'value' => $groups
            ],
            'last_login'     => [
                'type'  => 'int',
                'title' => _AM_USERLOG_LAST_LOGIN,
                'value' => $last_login
            ],
            'user_ip'        => [
                'type'  => 'text',
                'title' => _AM_USERLOG_USER_IP,
                'value' => $_SERVER['REMOTE_ADDR']
            ],
            'user_agent'     => [
                'type'  => 'text',
                'title' => _AM_USERLOG_USER_AGENT,
                'value' => $_SERVER['HTTP_USER_AGENT']
            ],
            'url'            => [
                'type'  => 'text',
                'title' => _AM_USERLOG_URL,
                'value' => $_SERVER['REQUEST_URI']
            ],
            'script'         => [
                'type'  => 'text',
                'title' => _AM_USERLOG_SCRIPT,
                'value' => end($tempUserLog)
            ],
            'referer'        => [
                'type'  => 'text',
                'title' => _AM_USERLOG_REFERER,
                'value' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''
            ],
            'pagetitle'      => [
                'type'  => 'text',
                'title' => _AM_USERLOG_PAGETITLE,
                'value' => isset($GLOBALS['xoopsTpl']) ? $GLOBALS['xoopsTpl']->get_template_vars('xoops_pagetitle') : ''
            ],
            'pageadmin'      => [
                'type'  => 'bool',
                'title' => _AM_USERLOG_PAGEADMIN,
                'value' => (isset($GLOBALS['xoopsOption']['pagetype'])
                            && 'admin' === $GLOBALS['xoopsOption']['pagetype']) ? 1 : 0
            ],
            'module'         => [
                'type'  => 'text',
                'title' => _AM_USERLOG_MODULE,
                'value' => $this->userlog->getLogModule()->getVar('dirname')
            ],
            'module_name'    => [
                'type'  => 'text',
                'title' => _AM_USERLOG_MODULE_NAME,
                'value' => $this->userlog->getLogModule()->getVar('name')
            ],
            'item_name'      => [
                'type'  => 'text',
                'title' => _AM_USERLOG_ITEM_NAME,
                'value' => null
            ],
            'item_id'        => [
                'type'  => 'int',
                'title' => _AM_USERLOG_ITEM_ID,
                'value' => null
            ],
            // user data input method
            'request_method' => [
                'type'  => 'text',
                'title' => _AM_USERLOG_REQUEST_METHOD,
                'value' => $_SERVER['REQUEST_METHOD']
            ],
            'zget'           => [
                'type'  => 'text',
                'title' => _AM_USERLOG_GET,
                'value' => $_GET
            ],
            'post'           => [
                'type'  => 'text',
                'title' => _AM_USERLOG_POST,
                'value' => $_POST
            ],
            'request'        => [
                'type'  => 'text',
                'title' => _AM_USERLOG_REQUEST,
                'value' => $_REQUEST
            ],
            'files'          => [
                'type'  => 'text',
                'title' => _AM_USERLOG_FILES,
                'value' => $_FILES
            ],
            'env'            => [
                'type'  => 'text',
                'title' => _AM_USERLOG_ENV,
                'value' => $_ENV
            ],
            'session'        => [
                'type'  => 'text',
                'title' => _AM_USERLOG_SESSION,
                'value' => $_SESSION
            ],
            'cookie'         => [
                'type'  => 'text',
                'title' => _AM_USERLOG_COOKIE,
                'value' => $_COOKIE
            ],
            'header'         => [
                'type'  => 'text',
                'title' => _AM_USERLOG_HEADER,
                'value' => headers_list()
            ],
            'logger'         => [
                'type'  => 'text',
                'title' => _AM_USERLOG_LOGGER,
                'value' => $GLOBALS['xoopsLogger']->errors
            ],
            // settings will not be logged
            'active'         => [
                'type'  => 'int',
                'title' => _AM_USERLOG_SET_ACTIVE,
                'value' => 1
            ],
            'inside'         => [
                'type'  => 'int',
                'title' => _AM_USERLOG_INSIDE,
                'value' => 1
            ],
            'outside'        => [
                'type'  => 'int',
                'title' => _AM_USERLOG_OUTSIDE,
                'value' => 1
            ],
            'unset_pass'     => [
                'type'  => 'int',
                'title' => _AM_USERLOG_UNSET_PASS,
                'value' => 1
            ],
            'store_file'     => [
                'type'  => 'int',
                'title' => _AM_USERLOG_STORE_FILE,
                'value' => 1
            ],
            'store_db'       => [
                'type'  => 'int',
                'title' => _AM_USERLOG_STORE_DB,
                'value' => 1
            ],
            'views'          => [
                'type'  => 'int',
                'title' => _AM_USERLOG_VIEWS,
                'value' => 1 // for item_name and item_id
            ]
        ];
        $ret     = $this->userlog->getFromKeys($options, $option);
        // patch Login/Register History
        if (isset($ret['post']['value'])) {
            $ret['post']['value'] = $this->userlog->patchLoginHistory($ret['post']['value'], $uid, !empty($ret['unset_pass']['value']));
        }
        if (empty($V)) {
            return $ret;
        }
        if ('key' === $V) {
            return array_keys($ret);
        }
        $ret2     = [];
        $emptyAll = 'value' === $V; // check if all values are empty
        foreach ($ret as $option => $val) {
            $ret2[$option] = $val[$V];
            // if there is a value || exceptions continue
            if (!$emptyAll
                || in_array($option, [
                    'log_id',
                    'log_time',
                    'active',
                    'inside',
                    'outside',
                    'unset_pass',
                    'store_file',
                    'store_db',
                    'views'
                ])
            ) {
                continue;
            }
            // check values
            if (!empty($val[$V])) {
                $emptyAll = false;
            }
        }

        return $emptyAll ? [] : $ret2;
    }

    /**
     * @param null $options
     *
     * @return array
     */
    public function logForm($options = null)
    {
        $form    = new XoopsThemeForm(_AM_USERLOG_LOGFORM, 'logs', 'logs.php', 'get');
        $headers = $this->getOptions('', 'title');
        unset($headers['active'], $headers['inside'], $headers['outside'], $headers['unset_pass'], $headers['store_db'], $headers['store_file'], $headers['views']);
        $el          = [];
        $query_types = ['=' => '', '>' => 'GT', '<' => 'LT'];
        foreach ($headers as $ele => $def) {
            switch ($ele) {
                case 'pageadmin':
                case 'admin':
                    $defEl    = '_AM_USERLOG_' . strtoupper($ele); // if constant is defined in translation - it is good for now
                    $el[$ele] = new XoopsFormRadio(constant($defEl), "options[{$ele}]", isset($options[$ele]) ? $options[$ele] : '');
                    $el[$ele]->addOption(1, _YES);
                    $el[$ele]->addOption(0, _NO);
                    $el[$ele]->addOption('', _ALL);
                    $el[$ele]->setDescription(constant($defEl . '_FORM'));
                    $form->addElement($el[$ele]);
                    break;
                default:
                    foreach ($query_types as $type) {
                        $defEl = '_AM_USERLOG_' . strtoupper($ele . $type); // if constant is defined in translation - it is good for now
                        if (defined($defEl . '_FORM')) {
                            $el[$ele . $type] = new XoopsFormText(constant($defEl), "options[{$ele}{$type}]", 10, 255, !empty($options[$ele . $type]) ? $options[$ele . $type] : null);
                            $defEle           = '_AM_USERLOG_' . strtoupper($ele);
                            $el[$ele . $type]->setDescription(sprintf(constant($defEl . '_FORM'), constant($defEle), constant($defEle)));
                            $form->addElement($el[$ele . $type]);
                        }
                    }
                    break;
            }
        }
        // http://stackoverflow.com/questions/8029532/how-to-prevent-submitting-the-html-forms-input-field-value-if-it-empty
        // http://stackoverflow.com/questions/2617480/how-to-get-all-elements-which-name-starts-with-some-string
        $el['log_id']->customValidationCode[] = "preventSubmitEmptyInput('options[');"; // check all input tags

        return [$form, $el, $headers];
    }

    /**
     * @return int
     */
    public function cleanCache()
    {
        $files = glob(XOOPS_VAR_PATH . '/caches/xoops_cache/*' . USERLOG_DIRNAME . '*.*');
        foreach ($files as $filename) {
            unlink($filename);
        }

        return count($files);
    }
}

/**
 * Class UserlogSettingHandler
 */
class UserlogSettingHandler extends XoopsPersistableObjectHandler
{
    public $userlog = null;

    /**
     * @param null|XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    {
        $this->userlog = Userlog::getInstance();
        parent::__construct($db, USERLOG_DIRNAME . '_set', 'UserlogSetting', 'set_id', 'logby');
    }

    /**
     * @param int    $limit
     * @param int    $start
     * @param null   $otherCriteria
     * @param string $sort
     * @param string $order
     * @param null   $fields
     * @param bool   $asObject
     * @param bool   $id_as_key
     *
     * @return mixed
     */
    public function getSets(
        $limit = 0,
        $start = 0,
        $otherCriteria = null,
        $sort = 'set_id',
        $order = 'DESC',
        $fields = null,
        $asObject = true,
        $id_as_key = true
    ) {
        $criteria = new CriteriaCompo();
        if (!empty($otherCriteria)) {
            $criteria->add($otherCriteria);
        }
        $criteria->setLimit($limit);
        $criteria->setStart($start);
        $criteria->setSort($sort);
        $criteria->setOrder($order);
        $ret = $this->getAll($criteria, $fields, $asObject, $id_as_key);

        return $ret;
    }
}
