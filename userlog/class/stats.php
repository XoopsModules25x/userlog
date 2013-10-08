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
	public function getAll()
	{
		$statsObj = $this->userlog->getHandler('stats')->getAll();
		if (empty($statsObj)) return false; // if no result nothing in database
		foreach ($statsObj as $sObj) {
			$index1 = $sObj->stats_type().$sObj->stats_link();
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
		// if there is nothing to add to db
		if (empty($value) && !empty($increment)) return false;
		// for file we should have a link
		if ($type == "file" && empty($link)) return false;
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('stats_type', $type), "AND");
		$criteria->add(new Criteria('stats_period', $period), "AND");
		
		if ($type == "file") {
			$criteria->add(new Criteria('stats_link', $link), "AND");
		}
		$statsObj = $this->userlog->getHandler('stats')->getAll($criteria);
		if(empty($statsObj)) {
			$statsObj = $this->userlog->getHandler('stats')->create();
		}
		$statsObj = is_array($statsObj) ? $statsObj : array($statsObj);
		foreach($statsObj as $sObj) {
			$sObj->setVar("stats_type", $type);
			$sObj->setVar("stats_period", $period);
			$sObj->setVar("stats_link", $link);
			$sObj->setVar("stats_value", empty($increment) ? $value : $sObj->stats_value() + $value); // increment value if increment is true
			$sObj->setVar("time_update", time());
			$ret = $this->userlog->getHandler('stats')->insert($sObj);
		}
		$this->unsetNew();			
		return $ret;
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
         parent::__construct($db, "mod_userlog_stats", 'UserlogStats', "stats_id", "stats_type");
	}
    public function &get($id)
	{
	    static $stats;
        if (isset($stats[$id])) {
            return $stats[$id];
        }
        $obj = parent::get($id);
        $stats[$id] = $obj;
        return $obj;

	}
	
}