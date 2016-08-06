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
 * @version         $Id: stats.php 1 2013-02-26 16:25:04Z irmtfan $
 */

defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");
include_once dirname(dirname(__FILE__)) . '/include/common.php';
xoops_loadLanguage("admin",USERLOG_DIRNAME);

class UserlogStats extends XoopsObject
{
	
    /**
     * @var string
     */
    public $userlog = null;
	public $_period = array("all" => 0, "today" => 1, "week" => 7, "month" => 30);
	public $_type = array("log" => _AM_USERLOG_STATS_LOG,
					   "logdel" => _AM_USERLOG_STATS_LOGDEL,	
					   "set" => _AM_USERLOG_STATS_SET,
					   "file" => _AM_USERLOG_STATS_FILE,
					   "fileall" => _AM_USERLOG_STATS_FILEALL,
					   "referral" => _AM_USERLOG_STATS_REFERRAL,
					   "browser" => _AM_USERLOG_STATS_BROWSER,
					   "OS" => _AM_USERLOG_STATS_OS,
					   "views" => _AM_USERLOG_STATS_VIEWS);
	
    public function __construct()
    {
        $this->userlog = Userlog::getInstance();
        $this->initVar("stats_id", XOBJ_DTYPE_INT, null, false);
        $this->initVar("stats_type", XOBJ_DTYPE_TXTBOX, null, false, 10);
        $this->initVar("stats_link", XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar("stats_value", XOBJ_DTYPE_INT, null, false);
        $this->initVar("stats_period", XOBJ_DTYPE_INT, null, false);
        $this->initVar("time_update", XOBJ_DTYPE_INT, null, false);
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
            $instance = new UserlogStats();
        }
        return $instance;
    }
    public function time_update()
    {
        return $this->userlog->formatTime($this->getVar('time_update'));
    }
	// $type = null or array() => get all types
	public function getAll($type = array(), $start = 0, $limit = 0, $sort = "stats_value", $order = "DESC", $otherCriteria = null)
	{
		$criteria = new CriteriaCompo();
		if(!empty($type)) {
			$typeArr = is_array($type) ? $type : array($type);
			foreach($typeArr as $tt) {
				$criteria->add(new Criteria("stats_type", $tt), "OR");
			}
		}
		if(!empty($otherCriteria)) {
			$criteria->add($otherCriteria);	
		}
        $criteria->setStart($start);
        $criteria->setLimit($limit);
        $criteria->setSort($sort);
        $criteria->setOrder($order);
		$statsObj = $this->userlog->getHandler('stats')->getAll($criteria);
		if (empty($statsObj)) return false; // if no result nothing in database
		foreach ($statsObj as $sObj) {
			$link = $sObj->stats_link();
			// if there is a link and only one type just index link
			$index1 = (!empty($link) && count($type) == 1) ? $link : $sObj->stats_type().$link;
			$index2 = $sObj->stats_period();
			if (!isset($ret[$index1])) $ret[$index1] = array();
			if (!isset($ret[$index1][$index2])) $ret[$index1][$index2] = array();
			$ret[$index1][$index2]["value"]=$sObj->stats_value();
			$ret[$index1][$index2]["time_update"]=$sObj->time_update();
		}
		return $ret;
	}
	
	public function updateAll($type="log", $prob = 11)
	{
		if(!$this->userlog->probCheck($prob)) return false;
		switch ($type) {
			case "set":
				// total
				$sets = $this->userlog->getHandler('setting')->getCount();
				$this->update("set", 0, $sets);
				break;
			case "file":
				list($allFiles,$totalFiles) = $this->userlog->getAllLogFiles();
				foreach($allFiles as $path=>$files) {
					$log_file =  $path . '/' . $this->userlog->getConfig('logfilename') . "." . $this->userlog->logext;
					$this->update("file" ,0 ,count($files) ,false ,$log_file); // update working file in all paths (now 2)
				}
				// update all files in db link='all'
				$this->update("file" ,0 ,$totalFiles ,false ,'all');
				break;
			case "views":
				break;
			case "log":
				// if logs exceed the maxlogsperiod delete them
				if ($this->userlog->getConfig('maxlogsperiod') != 0) {
					$criteriaDel = new CriteriaCompo();
					$until = time() - $this->userlog->getSinceTime($this->userlog->getConfig('maxlogsperiod'));
					$criteriaDel->add(new Criteria('log_time', $until, "<" ), "AND");
					$numDelPeriod = $this->delete("log" ,0 ,0 ,$criteriaDel); // all time = maxlogsperiod
				}
				foreach ($this->_period as $per) {
					$criteria = new CriteriaCompo();
					if(!empty($per)) {
						// today, week, month
						$since = $this->userlog->getSinceTime($per);
						$criteria->add(new Criteria('log_time', time() - $since, ">" ), "AND");
					}
					$logs = $this->userlog->getHandler('log')->getLogsCount($criteria);
					$exceed = $logs - $this->userlog->getConfig('maxlogs');
					// if logs exceed the maxlogs delete them
					if ($exceed > 0) {
						$numDel = $this->delete("log",$per, $exceed, null, true);
						$logs -= $numDel;
					}
 					$this->update("log", $per, $logs);
				}
				break;
			case "referral":
				$criteria = new CriteriaCompo();
				$criteria->add(new Criteria("referer", XOOPS_URL . "%", "NOT LIKE"));
				$criteria->setGroupby("referer");
				$outsideReferers = $this->userlog->getHandler('log')->getCounts($criteria);
				$referrals = array();
				foreach($outsideReferers as $ref=>$views) {
					if(empty($ref)) continue;
					$outRef = parse_url($ref, PHP_URL_HOST);
					if(!isset($referrals[$outRef])) $referrals[$outRef] = 0;
					$referrals[$outRef] += $views;
				}
				foreach($referrals as $ref=>$views) {
 					$this->update("referral", 0, $views, false, $ref);
				}
				$this->deleteExpiredStats("referral");
				break;
			case "browser":
			case "OS":
				$criteria = new CriteriaCompo();
				$criteria->setGroupby("user_agent");
				$agents = $this->userlog->getHandler('log')->getCounts($criteria);
				$browsers = array();
				$OSes = array();
				foreach($agents as $agent=>$views) {
					if(empty($agent)) continue;
					$browserArr = $this->userlog->getBrowsCap()->getBrowser($agent, true);
					$browserParent = !empty($browserArr["Parent"]) ? (!empty($browserArr["Crawler"]) ? "crawler: " : "") . $browserArr["Parent"] : "unknown";
					if(!isset($browsers[$browserParent])) $browsers[$browserParent] = 0;
					$browsers[$browserParent] += $views;
					if(!isset($OSes[$browserArr["Platform"]])) $OSes[$browserArr["Platform"]] = 0;
					$OSes[$browserArr["Platform"]] += $views;
				}
				foreach($browsers as $browser=>$views) {
 					$this->update("browser", 0, $views, false, $browser);
				}
				foreach($OSes as $OS=>$views) {
 					$this->update("OS", 0, $views, false, $OS);
				}
				$this->deleteExpiredStats(array("browser", "OS"));
				break;
		}
		return true;
	}
	public function delete($type = 'log',$period = 0, $limitDel = 0, $criteria = null, $asObject = false)
	{
		switch ($type) {
			case "log":
				if ($asObject) {
					$logsObj = $this->userlog->getHandler('log')->getLogs($limitDel,0,$criteria,"log_id","ASC");
					$numDel = 0;
					foreach (array_keys($logsObj) as $key) {
						$numDel += $this->userlog->getHandler('log')->delete($logsObj[$key], true) ? 1 : 0;
					}
					if ($numDel > 0) {
						$this->update("logdel", $period, $numDel, true); // increment
					}
					unset($logsObj);
					return $numDel;
				}
				$numDel = $this->userlog->getHandler('log')->deleteAll($criteria, true, $asObject);
				if ($numDel > 0) {
					$this->update("logdel", $period, $numDel, true); // increment
				}
				return $numDel;
				break;
		}
	}
	
	public function update($type = 'log', $period = 0, $value = null, $increment = false, $link = '')
	{
		// check if version is 115 => unique index is added
		if($this->userlog->getModule()->getVar("version") < 115) return false;
		// if there is nothing to add to db
		if (empty($value) && !empty($increment)) return false;
		// for file,referral,browser,OS we should have a link
		if (in_array($type, array("file","referral","browser","OS")) && empty($link)) return false;
		$statsObj = $this->userlog->getHandler('stats')->create();
		
		$statsObj->setVar("stats_type", $type);
		$statsObj->setVar("stats_period", $period);
		$statsObj->setVar("stats_link", $link);
		$statsObj->setVar("stats_value", $value);
		$statsObj->setVar("time_update", time());
		// increment value if increment is true
		$ret = $this->userlog->getHandler('stats')->insertUpdate($statsObj, array("stats_value"=>(empty($increment) ? $value : "stats_value + {$value}"), "time_update"=>time()));
		$this->unsetNew();			
		return $ret;
	}
	/**
     * Delete expired statistics for types when time_update < expire time
     *
     * @access public
     * @param array $types - types ($this->_type)
     * @param int $expire - delete all records exist in the table before expire time - positive for days and negatice for hours - 0 = never expired
     * @return int count of deleted rows
     */
	public function deleteExpiredStats($types = array('browser'), $expire = 1)
	{
		if(empty($expire)) return false; // if $expire = 0 dont delete
		$criteriaDel = new CriteriaCompo();
		$until = time() - $this->userlog->getSinceTime($expire);
		if(!empty($types)) {
			$criteriaTypes = new CriteriaCompo();
			$types = is_array($types) ? $types : array($types);
			foreach($types as $type) {
				$criteriaTypes->add(new Criteria('stats_type', $type, "=" ), "OR");
			}
			$criteriaDel->add($criteriaTypes, "AND");
		}
		$criteriaTime = new CriteriaCompo();
		$criteriaTime->add(new Criteria('time_update', $until, "<" ), "AND");
		$criteriaDel->add($criteriaTime, "AND");
		return $this->userlog->getHandler('stats')->deleteAll($criteriaDel); // function deleteAll($criteria = null, $force = true, $asObject = false)
	}
}
class UserlogStatsHandler extends XoopsPersistableObjectHandler
{
   public $userlog = null;

   /**
     * @param null|object $db
     */
    public function __construct(&$db)
    {
        $this->userlog = Userlog::getInstance();
         parent::__construct($db, "mod_" . USERLOG_DIRNAME . "_stats", 'UserlogStats', "stats_id", "stats_type");
	}
	public function insertUpdate(&$object, $duplicate = array(), $force = true) 
	{
        $handler = $this->loadHandler('write');
	
        if (!$object->isDirty()) {
            trigger_error("Data entry is not inserted - the object '" . get_class($object) . "' is not dirty", E_USER_NOTICE);
            return $object->getVar($this->keyName);
        }
        if (!$handler->cleanVars($object)) {
            trigger_error("Insert failed in method 'cleanVars' of object '" . get_class($object) . "'", E_USER_WARNING);
            return $object->getVar($this->keyName);
        }
        $queryFunc = empty($force) ? "query" : "queryF";

        if ($object->isNew()) {
            $sql = "INSERT INTO {$this->table}";
            if (!empty($object->cleanVars)) {
                $keys = array_keys($object->cleanVars);
                $vals = array_values($object->cleanVars);
                $sql .= " (" . implode(", ", $keys) . ") VALUES (" . implode(",", $vals) . ")";
            } else {
                trigger_error("Data entry is not inserted - no variable is changed in object of '" . get_class($object) . "'", E_USER_NOTICE);
                return $object->getVar($this->keyName);
            }
			// START ON DUPLICATE KEY UPDATE
			if(!empty($duplicate)) {
				$sql .= " ON DUPLICATE KEY UPDATE";
				$keys = array();
				foreach($duplicate as $keyD=>$valD) {
					$keys[] = " {$keyD} = {$valD} ";
				}
				$sql .= implode(", ", $keys);
			}
			// END ON DUPLICATE KEY UPDATE
            if (!$result = $this->db->{$queryFunc}($sql)) {
                return false;
            }
            if (!$object->getVar($this->keyName) && $object_id = $this->db->getInsertId()) {
                $object->assignVar($this->keyName, $object_id);
            }
        } else if (!empty($object->cleanVars)) {
            $keys = array();
            foreach ($object->cleanVars as $k => $v) {
                $keys[] = " `{$k}` = {$v}";
            }
            $sql = "UPDATE `" . $this->table . "` SET " . implode(",", $keys) . " WHERE `" . $this->keyName . "` = " . $this->db->quote($object->getVar($this->keyName));
            if (!$result = $this->db->{$queryFunc}($sql)) {
                return false;
            }
        }
        return $object->getVar($this->keyName);
	}
	/**
     * Show index in a table
     *
     * @access public
     * @param string $index - name of the index (will be used in KEY_NAME)
     * @param array $ret = Table	Non_unique	Key_name	Seq_in_index	Column_name		Collation	Cardinality		Sub_part	Packed	Null	Index_type	Comment	Index_comment
     */
	public function showIndex($index = null)
	{
        $sql = "SHOW INDEX FROM {$this->table}";
		if (isset($index)) {
			$sql .= " WHERE KEY_NAME = '{$index}'";
		}
        if (!$result = $this->db->queryF($sql)) {
    		xoops_error($this->db->error().'<br />'.$sql);
    		return false;
    	}
        $ret = array();
        while ($myrow = $this->db->fetchArray($result)) {
			$ret[] = $myrow;
		}
	    return $ret;
	}
	/**
     * Add Index to a table
     *
     * @access public
     * @param string $index - name of the index
     * @param array $fields - array of table fields should be in the index
     * @param string $index_type - type of the index array("INDEX", "UNIQUE", "SPATIAL", "FULLTEXT")
     * @param bool
     */
	public function addIndex($index = null, $fields = array(), $index_type = "INDEX")
	{
		if(empty($index) || empty($fields)) return false;
		if($this->showIndex($index)) return false; // index is exist
		$index_type = strtoupper($index_type);
		if(!in_array($index_type, array("INDEX", "UNIQUE", "SPATIAL", "FULLTEXT"))) return false;
		$fields = is_array($fields) ? implode("," , $fields) : $fields;
        $sql = "ALTER TABLE {$this->table} ADD {$index_type} {$index} ( {$fields} )";
        if (!$result = $this->db->queryF($sql)) {
    		xoops_error($this->db->error().'<br />'.$sql);
    		return false;
    	}
	    return true;
	}
	/**
     * Drop index in a table
     *
     * @access public
     * @param string $index - name of the index
     * @param bool
     */
	public function dropIndex($index = null)
	{
		if(empty($index)) return false;
		if(!$this->showIndex($index)) return false; // index is not exist
        $sql = "ALTER TABLE {$this->table} DROP INDEX {$index}";
        if (!$result = $this->db->queryF($sql)) {
    		xoops_error($this->db->error().'<br />'.$sql);
    		return false;
    	}
	    return true;
	}
	/**
     * Change Index = Drop index + Add Index
     *
     * @access public
     * @param string $index - name of the index
     * @param array $fields - array of table fields should be in the index
     * @param string $index_type - type of the index array("INDEX", "UNIQUE", "SPATIAL", "FULLTEXT")
     * @param bool
     */
	public function changeIndex($index = null, $fields = array(), $index_type = "INDEX")
	{
		if($this->showIndex($index) && !$this->dropIndex($index)) return false; // if index is exist but cannot drop it
		return $this->addIndex($index, $fields, $index_type);
	}
}