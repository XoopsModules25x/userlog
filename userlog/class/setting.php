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
 * @since           1
 * @author          irmtfan (irmtfan@yahoo.com)
 * @author          The XOOPS Project <www.xoops.org> <www.xoops.ir>
 * @version         $Id: setting.php 1 2013-02-26 16:25:04Z irmtfan $
 */

defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");
include_once dirname(dirname(__FILE__)) . '/include/common.php';

xoops_loadLanguage("admin",USERLOG_DIRNAME);
xoops_load('XoopsFormLoader');

class UserlogSetting extends XoopsObject
{
    /**
     * @var string
     */
	public $all_logby = array("uid"=>_AM_USERLOG_UID,"gid"=>_AM_USERLOG_SET_GID,"ip"=>_AM_USERLOG_SET_IP);
	
    public $userlog = null;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->userlog = Userlog::getInstance();
        $this->initVar("set_id", XOBJ_DTYPE_INT, null, false);
        $this->initVar("name", XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar("logby", XOBJ_DTYPE_TXTBOX, null, true, 10);
        $this->initVar("unique_id", XOBJ_DTYPE_INT, null, false);
        $this->initVar("options", XOBJ_DTYPE_TXTAREA, '', false);
        $this->initVar("scope", XOBJ_DTYPE_TXTAREA, '', false);
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
	
    static function &getInstance()
    {
        static $instance = false;
        if (!$instance) {
            $instance = new UserlogSetting();
        }
        return $instance;
    }
	public function unique_id()
	{
		if ($this->getVar("logby") == "ip") return long2ip($this->getVar('unique_id'));
		return $this->getVar('unique_id');
	}
    public function storeSet($force = true)
	{
		if($this->setDb(true)) {
			// use $this->getVar('unique_id') (int ip) instead of $this->unique_id() (string ip)
			if($this->setFile($this->logby(), $this->getVar('unique_id'), array($this->options(), $this->scope()))) return true;
		}
		return false;
	}
    public function getSet()
	{
		// if uid setting exist in File
		$unique_uid = ($this->userlog->getUser()) ? $this->userlog->getUser()->getVar('uid') : 0;
		if ($options = $this->getFile('uid', $unique_uid)) {
			return $options;
		}

		// if gid setting exist in File
		$unique_gid = ($this->userlog->getUser()) ? $this->userlog->getUser()->getGroups() :  array(XOOPS_GROUP_ANONYMOUS);
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
		if(!$this->userlog->probCheck($this->userlog->getConfig("probset"))) return false;
		// database get All is better for performance???
        $logsetsObj = $this->userlog->getHandler('setting')->getAll();
		if (empty($logsetsObj)) return false; // if not set in db return false
		$uid_unique_uid = "uid" . $unique_uid;
		foreach($unique_gid as $key=>$gid) {
			$gid_unique_gid[$key] = "gid" . $gid;
		}
		$ip_unique_ip = "ip" . $unique_ip;
		foreach($logsetsObj as $setObj) {
			$sLogby = $setObj->logby();
			$sUnique_id	= $setObj->getVar('unique_id');
			$sLogbyId = $sLogby . $sUnique_id;
			// if uid setting exist in db return it			
			if($sLogbyId == $uid_unique_uid ||
			// if gid setting exist in db return it
			   in_array($sLogbyId, $gid_unique_gid) ||
			// if ip setting exist in db return it
			   $sLogbyId == $ip_unique_ip) {
					$sets = array($setObj->options(), $setObj->scope());
					$this->setFile($sLogby, $sUnique_id, $sets); // build cache
					return $sets;
			}
			// if all exist in db
			if($sUnique_id == 0) {
				$sets = array($setObj->options(), $setObj->scope());
				$this->setFile('all', 0, $sets); // build cache
				return $sets;
			}
		}
		return false;
	}
	
    public function setDb($force = true)
	{
        $ret = $this->userlog->getHandler('setting')->insert($this, $force);
        $this->unsetNew();
        return $ret;
	}
    public function getDb()
	{
	}
	
    public function setFile($logby = "uid", $unique_id, $options)
	{
	    return $this->_createCacheFile($options, "setting_{$logby}_{$unique_id}");
	}
    public function getFile($logby = "uid", $unique_id)
	{
		return $this->_loadCacheFile("setting_{$logby}_{$unique_id}");
	}
    public function deleteFile($logby = "uid", $unique_id)
	{
		return $this->_deleteCacheFile("setting_{$logby}_{$unique_id}");
	}
	private function _createCacheFile($data, $name = null, $root_path = XOOPS_CACHE_PATH)
	{
		$name = ($name) ? $name : strval(time());
		$key = USERLOG_DIRNAME ."_{$name}";
		//$cacheHandler = XoopsCache::config($key, array('path' => XOOPS_VAR_PATH . '/caches/xoops_cache/userlog'));
		return XoopsCache::write($key, $data);
	}
	private function _loadCacheFile($name = null, $root_path = XOOPS_CACHE_PATH)
	{
		if (empty($name)) return false;
		$key = USERLOG_DIRNAME ."_{$name}";
		return XoopsCache::read($key);
	}
	private function _deleteCacheFile($name = null, $root_path = XOOPS_CACHE_PATH)
	{
		if (empty($name)) return false;
		$key = USERLOG_DIRNAME ."_{$name}";
		return XoopsCache::delete($key);
	}
	public function getOptions($option = null, $V = "value")
	{
		$V = strtolower($V);
		
		if ($this->userlog->getUser()) {
			$uid = $this->userlog->getUser()->getVar('uid');
			$uname = $this->userlog->getUser()->getVar('uname');
			$last_login = $this->userlog->getUser()->getVar('last_login');
			$admin = $this->userlog->getUser()->isAdmin();
			$groups = "g" . implode("g",array_unique($this->userlog->getUser()->getGroups())); // g1g2
		} else {
			$uid = 0;
			$uname = '';
			$last_login = 0;
			$admin = 0;
			$groups = "g" . XOOPS_GROUP_ANONYMOUS; // g3
		}
		$options = array(
		"log_id" =>		array(	"type" => "int",
								"title" => _AM_USERLOG_LOG_ID,
								"value" => null // null for now
								),
		"log_time" =>	array(	"type" => "int",
								"title" => _AM_USERLOG_LOG_TIME,
								"value" => time()
								),
		"uid" => 		array(	"type" => "int",
								"title" => _AM_USERLOG_UID,
								"value" => $uid
								),
		"uname" =>		array(	"type" => "text",
								"title" => _AM_USERLOG_UNAME,
								"value" => $uname
								),
		"admin" => 		array(	"type" => "bool",
								"title" => _AM_USERLOG_ADMIN,
								"value" => $admin
								),
		"groups" =>		array(	"type" => "text",
								"title" => _AM_USERLOG_GROUPS,
								"value" => $groups
								),
		"last_login" =>	array(	"type" => "int",
								"title" => _AM_USERLOG_LAST_LOGIN,
								"value" => $last_login
								),
		"user_ip" =>	array(	"type" => "text",
								"title" => _AM_USERLOG_USER_IP,
								"value" => $_SERVER['REMOTE_ADDR']
								),
		"user_agent" => array(	"type" => "text",
								"title" => _AM_USERLOG_USER_AGENT,
								"value" => $_SERVER['HTTP_USER_AGENT']
								),
		"url" => 		array(	"type" => "text",
								"title" => _AM_USERLOG_URL,
								"value" => $_SERVER['REQUEST_URI']
								),
		"script" => 	array(	"type" => "text",
								"title" => _AM_USERLOG_SCRIPT,
								"value" => end(explode('/',$_SERVER['PHP_SELF']))
								),
		"referer" => 	array(	"type" => "text",
								"title" => _AM_USERLOG_REFERER,
								"value" => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ""
								),
		"pagetitle" => 	array(	"type" => "text",
								"title" => _AM_USERLOG_PAGETITLE,
								"value" => isset($GLOBALS['xoopsTpl']) ? $GLOBALS['xoopsTpl']->get_template_vars("xoops_pagetitle") : ""
								),
		"pageadmin" => 	array(	"type" => "bool",
								"title" => _AM_USERLOG_PAGEADMIN,
								"value" => (isset($GLOBALS['xoopsOption']['pagetype']) && $GLOBALS['xoopsOption']['pagetype'] == "admin") ? 1 : 0
								),
		"module" => 	array(	"type" => "text",
								"title" => _AM_USERLOG_MODULE,
								"value" => $this->userlog->getLogModule()->getVar("dirname")
								),
		"module_name" => array(	"type" => "text",
								"title" => _AM_USERLOG_MODULE_NAME,
								"value" => $this->userlog->getLogModule()->getVar("name")
								),
		"item_name" => 	array(	"type" => "text",
								"title" => _AM_USERLOG_ITEM_NAME,
								"value" => null
								),
		"item_id" => 	array(	"type" => "int",
								"title" => _AM_USERLOG_ITEM_ID,
								"value" => null
								),
		// user data input method
		"request_method" => array(	"type" => "text",
									"title" => _AM_USERLOG_REQUEST_METHOD,
									"value" => $_SERVER['REQUEST_METHOD']
									),
		"get" => 		array(	"type" => "text",
								"title" => _AM_USERLOG_GET,
								"value" => $_GET
								),
		"post" => 		array(	"type" => "text",
								"title" => _AM_USERLOG_POST,
								"value" => $_POST
								),
		"request" =>	array(	"type" => "text",
								"title" => _AM_USERLOG_REQUEST,
								"value" => $_REQUEST
								),
		"files" =>		array(	"type" => "text",
								"title" => _AM_USERLOG_FILES,
								"value" => $_FILES
								),
		"env" => 		array(	"type" => "text",
								"title" => _AM_USERLOG_ENV,
								"value" => $_ENV
								),
		"session" => 	array(	"type" => "text",
								"title" => _AM_USERLOG_SESSION,
								"value" => $_SESSION
								),
		"cookie" => 	array(	"type" => "text",
								"title" => _AM_USERLOG_COOKIE,
								"value" => $_COOKIE
								),
		"header" => 	array(	"type" => "text",
								"title" => _AM_USERLOG_HEADER,
								"value" => headers_list()
								),
		"logger" => 	array(	"type" => "text",
								"title" => _AM_USERLOG_LOGGER,
								"value" => $GLOBALS['xoopsLogger']->errors
								),
		// settings will not be logged
		"store_file" => array(	"type" => "int",
								"title" => _AM_USERLOG_STORE_FILE,
								"value" => 1
								),
		"store_db" => 	array(	"type" => "int",
								"title" => _AM_USERLOG_STORE_DB,
								"value" => 1
								),
		"views" => 		array(	"type" => "int",
								"title" => _AM_USERLOG_VIEWS,
								"value" => 1 // for item_name and item_id
								),
		);
		$ret = $this->userlog->getFromKeys($options, $option);
		if (empty($V)) return $ret;
		if ($V == "key") return array_keys($ret);
		$ret2 = array();
		$emptyAll = ($V == "value") ? true : false; // check if all values are empty
		foreach ($ret as $option=>$val) {
			$ret2[$option] = $val[$V];
			// if there is a value || exceptions continue
			if (!$emptyAll || in_array($option, array("log_id","log_time","store_file","store_db","views"))) continue;
			// check values
			if (!empty($val[$V])) $emptyAll = false;
		}
		return $emptyAll ? array() : $ret2;
	}
	public function logForm($options = null)
	{
		$form = new XoopsThemeForm(_AM_USERLOG_LOGFORM,'logs','logs.php', 'get');
		$headers = $this->getOptions("","title");
		unset($headers["store_db"], $headers["store_file"], $headers["views"]);
		$el= array();
		$query_types = array("="=>"",">"=>"GT", "<"=>"LT");
		foreach($headers as $ele=>$def) {
			switch ($ele) {
				case "pageadmin":
				case "admin":
					$defEl = "_AM_USERLOG_". strtoupper($ele); // if constant is defined in translation - it is good for now
					$el[$ele] = new XoopsFormRadio(constant($defEl), "options[{$ele}]",isset($options[$ele]) ? $options[$ele] : '');
					$el[$ele]->addOption(1,_YES);
					$el[$ele]->addOption(0,_NO);
					$el[$ele]->addOption('',_ALL);
					$el[$ele]->setDescription(constant($defEl . "_FORM"));
					$form->addElement($el[$ele]);
					break;
				default:
					foreach($query_types as $type) {
						$defEl = "_AM_USERLOG_". strtoupper($ele.$type); // if constant is defined in translation - it is good for now
						if (defined($defEl . "_FORM")) {
							$el[$ele.$type] = new XoopsFormText(constant($defEl), "options[{$ele}{$type}]", 10, 255, !empty($options[$ele.$type]) ? $options[$ele.$type] : null);
							$defEle = "_AM_USERLOG_". strtoupper($ele);
							$el[$ele.$type]->setDescription(sprintf(constant($defEl . "_FORM"), constant($defEle), constant($defEle)));
							$form->addElement($el[$ele.$type]);
						}
					}
					break;
			}
		}
		// http://stackoverflow.com/questions/8029532/how-to-prevent-submitting-the-html-forms-input-field-value-if-it-empty
		// http://stackoverflow.com/questions/2617480/how-to-get-all-elements-which-name-starts-with-some-string
		$el["log_id"]->customValidationCode[]="preventSubmitEmptyInput('options[');"; // check all input tags
		return array($form ,$el ,$headers);
	}
	function cleanCache() {
		$files = glob(XOOPS_VAR_PATH.'/caches/xoops_cache/*' . USERLOG_DIRNAME . '*.*');
		foreach ($files as $filename) {
			unlink($filename);
		}
		return count($files);
	}
}

class UserlogSettingHandler extends XoopsPersistableObjectHandler
{
   public $userlog = null;

   /**
     * @param null|object $db
     */
    public function __construct(&$db)
    {
        $this->userlog = Userlog::getInstance();
         parent::__construct($db, "mod_userlog_set", 'UserlogSetting', "set_id", "logby");
	}
	
	public function getSets($limit = 0, $start = 0, $otherCriteria = null, $sort = 'set_id', $order = 'DESC', $fields = null, $asObject = true, $id_as_key = true)
	{
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
	
    public function &get($id)
	{
	    static $sets;
        if (isset($sets[$id])) {
            return $sets[$id];
        }
        $obj = parent::get($id);
        $sets[$id] = $obj;
        return $obj;
	}
}