developers help for userlog module.

1- log user activities structure.
I assume we need to gather below information from user. If you have any idea about them please let me know.
i dont sanitize them because i want to log them as they are. what is your idea. Im a very basic programmer. please help me.

in userlog/class/setting.php

[code]
	public function getOptions($option = null, $V = "value")
	{
		$V = strtolower($V);
		
		if ($this->userlog->getUser()) {
			$uid = $this->userlog->getUser()->getVar('uid');
			$uname = $this->userlog->getUser()->getVar('uname');
			$last_login = $this->userlog->getUser()->getVar('last_login');
			$admin = $this->userlog->getUser()->isAdmin();
			$groups = "g" . implode("g",$this->userlog->getUser()->getGroups()); // g1g2
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
		$ret2 = null;
		foreach ($ret as $option=>$val) {
			$ret2[$option] = $val[$V];
		}
		return $ret2;
	}
[/code]

2- setting structure.
I think we just need to log users by getting "uid" or "gid" or "ip" and it will cover all possibilities. please let me know your idea.

the table structure is:
in userlog/sql/mysql.sql
[code]
CREATE TABLE mod_userlog_set (
  set_id mediumint(8) unsigned NOT NULL auto_increment,
  name varchar(100) NOT NULL default '',
  logby varchar(10) NOT NULL default '',
  unique_id int(11) unsigned NOT NULL default 0,
  options TEXT NOT NULL,
  scope TEXT NOT NULL,
  PRIMARY KEY  (set_id),
  UNIQUE logby_id (logby, unique_id)
) ENGINE=MyISAM;
[/code]

The above is clear. more information in userlog > help.

3- performance.
it checks every hit to see if the visitor have a setting or not. for the best performance i wrote a getSet function like this.
in userlog/class/setting.php
[code]
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
			$sLobgy = $setObj->logby();
			$sUnique_id	= $setObj->unique_id();
			$sLogbyId = $sLobgy . $sUnique_id;
			// if uid setting exist in db return it			
			if($sLogbyId == $uid_unique_uid ||
			// if gid setting exist in db return it
			   in_array($sLogbyId, $gid_unique_gid) ||
			// if ip setting exist in db return it
			   $sLogbyId == $ip_unique_ip) {
					$sets = array($setObj->options(), $setObj->scope());
					$this->setFile($sLobgy, $sUnique_id, $sets); // build cache
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
[/code]
as you can see it create cache files for each setting. IMO it will help to reduce queries for users have a setting.
for getting settings from db at first I used criteria but then i decide to get all settings at once and then find if the user have setting or not. IMO it has the better performance. please let me know your opinion. what is the best performance? this function is important for your review.
Also i add a probability check to access to database in random.

4- views
now every module have a counter for its item views. eg: news, publisher, page in xoops 2.6
but this counter is useless. we dont need forever views. we need views in a specific time period.
in every news agency the first and top block is "today hot news" and it means news that had been most viewed today.

for a long time that was an unsolved issue for me and I think in the whole xoops we have this problem in all modules.
after starting userlog for a different purpose I suddenly reach to an innovative idea for gathering all views in all modules.
I used "notification" functionality because every module item that should have views have a notification too.
There we have all needed information about items. so i wrote this nice and tricky function:
[code]
	public function setItem()
	{
	    $not_config = $this->userlog->getLogModule()->getInfo('notification');
		if (!empty($not_config)) {
			foreach ($not_config['category'] as $category) {
				// if $item_id != 0 ---> return true
				if (!empty($category['item_name']) && $item_id = UserlogRequest::getInt($category['item_name'], 0)){
					$this->setVar('item_name', $category['item_name']);
					$this->setVar('item_id', $item_id);
					return true;
				}
			}
		}
		return false;		
	}
[/code]

so for example in news we only have these typical URLs:

news/article.php?storyid=ITEM_ID
news/index.php:article.php?storytopic=ITEM_ID

as you can see i just need to store item_name and item_id because i have the module dirname and script name!!!

IMO we should stick to this idea to have an "overall item views" in the XOOPS and drop all counters in all modules.
I need your idea about this.