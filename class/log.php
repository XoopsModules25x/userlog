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

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');
include_once dirname(__DIR__) . '/include/common.php';

/**
 * Class UserlogLog
 */
class UserlogLog extends XoopsObject
{
    /**
     * @var string
     */
    public $userlog = null;

    public $store = 0; // store: 0,1->db 2->file 3->both

    public $sourceJSON = array(
        'zget',
        'post',
        'request',
        'files',
        'env',
        'session',
        'cookie',
        'header',
        'logger'
    );// json_encoded fields

    /**
     * constructor
     */
    public function __construct()
    {
        $this->userlog = Userlog::getInstance();
        $this->initVar('log_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('log_time', XOBJ_DTYPE_INT, null, true);
        $this->initVar('uid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('uname', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('admin', XOBJ_DTYPE_INT, null, false);
        $this->initVar('groups', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('last_login', XOBJ_DTYPE_INT, null, true);
        $this->initVar('user_ip', XOBJ_DTYPE_TXTBOX, null, true, 15);
        $this->initVar('user_agent', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('url', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('script', XOBJ_DTYPE_TXTBOX, null, true, 50);
        $this->initVar('referer', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('pagetitle', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('pageadmin', XOBJ_DTYPE_INT, null, false);
        $this->initVar('module', XOBJ_DTYPE_TXTBOX, null, true, 25);
        $this->initVar('module_name', XOBJ_DTYPE_TXTBOX, null, true, 50);
        $this->initVar('item_name', XOBJ_DTYPE_TXTBOX, null, false, 10);
        $this->initVar('item_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('request_method', XOBJ_DTYPE_TXTBOX, null, false, 20);
        $this->initVar('zget', XOBJ_DTYPE_SOURCE);
        $this->initVar('post', XOBJ_DTYPE_SOURCE);
        $this->initVar('request', XOBJ_DTYPE_SOURCE);
        $this->initVar('files', XOBJ_DTYPE_SOURCE);
        $this->initVar('env', XOBJ_DTYPE_SOURCE);
        $this->initVar('session', XOBJ_DTYPE_SOURCE);
        $this->initVar('cookie', XOBJ_DTYPE_SOURCE);
        $this->initVar('header', XOBJ_DTYPE_SOURCE);
        $this->initVar('logger', XOBJ_DTYPE_SOURCE);
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
     * @return UserlogLog
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
     * @return mixed
     */
    public function log_time()
    {
        return $this->userlog->formatTime($this->getVar('log_time'));
    }

    /**
     * @return bool|string
     */
    public function last_login()
    {
        return $this->userlog->formatTime($this->getVar('last_login'));
    }

    /**
     * @return array|mixed
     */
    public function post()
    {
        $post = $this->getVar('post');

        return is_array($post) ? $post : json_decode($post, true);
    }

    /**
     * @param int    $limit
     * @param int    $start
     * @param string $sort
     * @param string $order
     * @param array  $modules
     * @param int    $since
     * @param array  $users
     * @param array  $groups
     *
     * @return array
     */
    public function getViews(
        $limit = 10,
        $start = 0,
        $sort = 'count',
        $order = 'DESC',
        $modules = array(),
        $since = 0,
        $users = array(),
        $groups = array()
    ) {
        if (!empty($modules)) {
            $criteriaModule = new CriteriaCompo();
            foreach ($modules as $module_dir => $items) {
                $criteriaItem = new CriteriaCompo();
                $criteriaItem->add(new Criteria('module', $module_dir));
                $criteriaItemName = new CriteriaCompo();
                if (!empty($items['item_name'])) {
                    foreach ($items['item_name'] as $item_name) {
                        // why we cannot use this $criteriaItemName->add(new Criteria('item_name', $items, "IN"));
                        $criteriaItemName->add(new Criteria('item_name', $item_name), 'OR');
                    }
                }
                $criteriaItem->add($criteriaItemName);
                $criteriaScript = new CriteriaCompo();
                if (!empty($items['script'])) {
                    foreach ($items['script'] as $script_name) {
                        $criteriaScript->add(new Criteria('script', $script_name), 'OR');
                    }
                }
                $criteriaItem->add($criteriaScript);
                $criteriaModule->add($criteriaItem, 'OR');
                unset($criteriaItem, $criteriaItemName, $criteriaScript);
            }
        }

        if (!empty($since)) {
            $starttime     = time() - $this->userlog->getSinceTime($since);
            $criteriaSince = new CriteriaCompo();
            $criteriaSince->add(new Criteria('log_time', $starttime, '>'));
        }

        if (!empty($users)) {
            $criteriaUser = new CriteriaCompo();
            $criteriaUser->add(new Criteria('uid', '(' . implode(',', $users) . ')', 'IN'));
        }
        if (!empty($groups)) {
            $criteriaGroup = new CriteriaCompo();
            foreach ($groups as $group) {
                $criteriaGroup->add(new Criteria('groups', '%g' . $group . '%', 'LIKE'), 'OR');
            }
        }

        // add all criterias
        $criteria = new CriteriaCompo();
        if (!empty($criteriaModule)) {
            $criteria->add($criteriaModule);
        }
        if (!empty($criteriaSince)) {
            $criteria->add($criteriaSince);
        }
        if (!empty($criteriaUser)) {
            $criteria->add($criteriaUser);
        }
        if (!empty($criteriaGroup)) {
            $criteria->add($criteriaGroup);
        }
        $criteria->setLimit($limit);
        $criteria->setStart($start);
        $sortItem = ($sort === 'module_count') ? 'module_name' : $sort;
        $criteria->setSort($sortItem);
        $criteria->setOrder($order);
        $fields = array(
            'uid',
            'groups',
            'pagetitle',
            'pageadmin',
            'module',
            'module_name',
            'script',
            'item_name',
            'item_id'
        );
        $criteria->setGroupBy('pageadmin, module, script, item_name, item_id');

        list($loglogsObj, $itemViews) = $this->userlog->getHandler('log')->getLogsCounts($criteria, $fields);
        $criteria->setGroupBy('module');
        $criteria->setSort(($sort === 'module_count') ? 'count' : 'module');
        $moduleViews = $this->userlog->getHandler('log')->getCounts($criteria);
        unset($criteria);
        // initializing
        $items = array(); // very important!!!
        foreach ($loglogsObj as $key => $loglogObj) {
            $module_dirname = $loglogObj->module();
            $item_id        = $loglogObj->item_id();
            if (!empty($item_id)) {
                $link = 'modules/' . $module_dirname . '/' . $loglogObj->script() . '?' . $loglogObj->item_name() . '=' . $item_id;
            } elseif ($module_dirname !== 'system-root') {
                $link = 'modules/' . $module_dirname . (($module_dirname !== 'system'
                                                         && $loglogObj->pageadmin()) ? '/admin/' : '/') . $loglogObj->script();
            } else {
                $link = $loglogObj->script();
            }
            $items[$link]                 = array();
            $items[$link]['count']        = $itemViews[$key];
            $items[$link]['pagetitle']    = $loglogObj->pagetitle();
            $items[$link]['module']       = $module_dirname;
            $items[$link]['module_name']  = $loglogObj->module_name();
            $items[$link]['module_count'] = $moduleViews[$module_dirname];
        }
        foreach ($items as $link => $item) {
            $col1[$link] = $item[$sort];
            $col2[$link] = $item['count'];//second sort by
        }
        if (!empty($items)) {
            array_multisort($col1, ($order === 'ASC') ? SORT_ASC : SORT_DESC, $col2, SORT_DESC, $items);
        }

        return $items;
    }

    /**
     * @param      $tolog
     * @param bool $force
     *
     * @return bool
     */
    public function store($tolog, $force = true)
    {
        if ($this->store > 1) {
            $this->storeFile($tolog);
        } // store file
        if ($this->store == 2) {
            return true;
        } // do not store db
        $this->storeDb($tolog, $force);

        return null;
    }

    /**
     * @param      $tolog
     * @param bool $force
     *
     * @return mixed
     */
    public function storeDb($tolog, $force = true)
    {
        // set vars
        foreach ($tolog as $option => $logvalue) {
            if (!empty($logvalue)) {
                // value array to string. use json_encode
                if (is_array($logvalue) && count($logvalue) > 0) {
                    $logvalue = json_encode($logvalue, (phpversion() > '5.4.0') ? JSON_UNESCAPED_UNICODE : 0);
                }
                switch ($option) {
                    // update referral in stats table
                    case 'referer':
                        if (strpos($logvalue, XOOPS_URL) === false) {
                            $statsObj = UserlogStats::getInstance();
                            $statsObj->update('referral', 0, 1, true, parse_url($logvalue, PHP_URL_HOST)); // auto increment 1
                        }
                        break;
                    // update browser and OS in stats table
                    case 'user_agent':
                        $statsObj   = UserlogStats::getInstance();
                        $browserArr = $this->userlog->getBrowsCap()->getBrowser($logvalue, true);
                        $statsObj->update('browser', 0, 1, true, !empty($browserArr['Parent']) ? (!empty($browserArr['Crawler']) ? 'crawler: ' : '') . $browserArr['Parent'] : 'unknown'); // auto increment 1
                        $statsObj->update('OS', 0, 1, true, $browserArr['Platform']); // auto increment 1
                        break;
                }
                $this->setVar($option, $logvalue);
            }
        }
        $ret = $this->userlog->getHandler('log')->insert($this, $force);
        $this->unsetNew();

        return $ret;
    }

    /**
     * @param       $logs
     * @param array $skips
     *
     * @return mixed
     */
    public function arrayToDisplay($logs, $skips = array())
    {
        foreach ($logs as $log_id => $log) {
            $logs[$log_id]['log_time']   = $this->userlog->formatTime($logs[$log_id]['log_time']);
            $logs[$log_id]['last_login'] = $this->userlog->formatTime($logs[$log_id]['last_login']);
            if (!empty($logs[$log_id]['groups'])) {
                // change g1g2 to Webmasters, Registered Users
                $groups                  = explode('g', substr($logs[$log_id]['groups'], 1)); // remove the first "g" from string
                $userGroupNames          = $this->userlog->getFromKeys($this->userlog->getGroupList(), $groups);
                $logs[$log_id]['groups'] = implode(',', $userGroupNames);
            }
            foreach ($this->sourceJSON as $option) {
                // if value is not string it was decoded in file
                if (!is_string($logs[$log_id][$option])) {
                    continue;
                }
                $logArr = json_decode($logs[$log_id][$option], true);
                if ($logArr) {
                    $logs[$log_id][$option] = var_export($logArr, true);
                }
            }
            // merge all request_method to one column - possibility to log methods when user dont set to log request_method itself
            $logs[$log_id]['request_method'] = empty($logs[$log_id]['request_method']) ? '' : $logs[$log_id]['request_method'] . "\n";
            foreach ($this->sourceJSON as $option) {
                if (!empty($logs[$log_id][$option])) {
                    $logs[$log_id]['request_method'] .= "\$_" . strtoupper($option) . ' ' . $logs[$log_id][$option] . "\n";
                }
                if ($option === 'env') {
                    break;
                } // only $sourceJSON = array("zget","post","request","files","env"
            }
            foreach ($skips as $option) {
                unset($logs[$log_id][$option]);
            }
        }

        return $logs;
    }

    /**
     * @param $tolog
     *
     * @return bool
     */
    public function storeFile($tolog)
    {
        $log_file = $this->userlog->getWorkingFile();
        // file create/open/write
        $fileHandler = XoopsFile::getHandler();
        $fileHandler->__construct($log_file, false);
        if ($fileHandler->size() > $this->userlog->getConfig('maxlogfilesize')) {
            $log_file_name = $this->userlog->getConfig('logfilepath') . '/' . USERLOG_DIRNAME . '/' . $this->userlog->getConfig('logfilename');
            $old_file      = $log_file_name . '_' . date('Y-m-d_H-i-s') . '.' . $this->userlog->logext;
            if (!$result = rename($log_file, $old_file)) {
                $this->setErrors("ERROR renaming ({$log_file})");

                return false;
            }
        }
        // force to create file if not exist
        if (!$fileHandler->exists()) {
            if (!$fileHandler->__construct($log_file, true)) { // create file and folder
                // Errors Warning: mkdir() [function.mkdir]: Permission denied in file /class/file/folder.php line 529
                $this->setErrors("Cannot create folder/file ({$log_file})");

                return false;
            }
            $this->setErrors("File was not exist create file ({$log_file})");
            // update the new file in database
            $statsObj = UserlogStats::getInstance();
            $statsObj->update('file', 0, 0, false, $log_file); // value = 0 to not auto increment
            // update old file if exist
            if (!empty($old_file)) {
                $statsObj->update('file', 0, 0, false, $old_file); // value = 0 to not auto increment
            }
            $statsObj->updateAll('file', 100); // prob = 100
            $data = '';
        } else {
            $data = "\n";
        }
        $data .= json_encode($tolog, (phpversion() > '5.4.0') ? JSON_UNESCAPED_UNICODE : 0);
        if ($fileHandler->open('a') === false) {
            $this->setErrors("Cannot open file ({$log_file})");

            return false;
        }
        if ($fileHandler->write($data) === false) {
            $this->setErrors("Cannot write to file ({$log_file})");

            return false;
        }
        $fileHandler->close();

        return true;
    }

    /**
     * @param array  $log_files
     * @param        $headers
     * @param string $csvNamePrefix
     * @param string $delimiter
     *
     * @return bool|string
     */
    public function exportFilesToCsv($log_files = array(), $headers, $csvNamePrefix = 'list_', $delimiter = ';')
    {
        $log_files = $this->parseFiles($log_files);
        if (($totalFiles = count($log_files)) == 0) {
            $this->setErrors(_AM_USERLOG_FILE_SELECT_ONE);

            return false;
        }
        list($logs, $totalLogs) = $this->getLogsFromFiles($log_files);
        $logs          = $this->arrayToDisplay($logs);
        $csvNamePrefix = basename($csvNamePrefix);
        if ($csvFile == $this->exportLogsToCsv($logs, $headers, $csvNamePrefix . 'from_file_total_' . $totalLogs, $delimiter)) {
            return $csvFile;
        }

        return false;
    }

    /**
     * @param        $logs
     * @param        $headers
     * @param string $csvNamePrefix
     * @param string $delimiter
     *
     * @return bool|string
     */
    public function exportLogsToCsv($logs, $headers, $csvNamePrefix = 'list_', $delimiter = ';')
    {
        $csvFile = $this->userlog->getConfig('logfilepath') . '/' . USERLOG_DIRNAME . '/export/csv/' . $csvNamePrefix . '_' . date('Y-m-d_H-i-s') . '.csv';
        // file create/open/write
        $fileHandler = XoopsFile::getHandler();
        $fileHandler->__construct($csvFile, false);
        // force to create file if not exist
        if (!$fileHandler->exists()) {
            $fileHandler->__construct($csvFile, true); // create file and folder
            $this->setErrors("File was not exist create file ({$csvFile})");
        }
        if ($fileHandler->open('a') === false) {
            $this->setErrors("Cannot open file ({$csvFile})");

            return false;
        }
        if (!fputcsv($fileHandler->handler, $headers, $delimiter)) {
            return false;
        }
        foreach ($logs as $thisRow) {
            if (!fputcsv($fileHandler->handler, $thisRow, $delimiter)) {
                return false;
            }
        }
        $fileHandler->close();

        return $csvFile;
    }

    /**
     * @param array  $log_files
     * @param int    $limit
     * @param int    $start
     * @param null   $options
     * @param string $sort
     * @param string $order
     *
     * @return array
     */
    public function getLogsFromFiles(
        $log_files = array(),
        $limit = 0,
        $start = 0,
        $options = null,
        $sort = 'log_time',
        $order = 'DESC'
    ) {
        $logs    = array();
        $logsStr = $this->readFiles($log_files);
        // if no logs return empty array and total = 0
        if (empty($logsStr)) {
            return array(array(), 0);
        }
        foreach ($logsStr as $id => $log) {
            $logArr = json_decode($log, true);
            // check if data is correct in file before do anything more
            if (!is_array($logArr) || !array_key_exists('log_id', $logArr)) {
                continue;
            }
            foreach ($logArr as $option => $logvalue) {
                // value array to string
                $logs[$id][$option] = is_array($logvalue) ? ((count($logvalue) > 0) ? var_export($logvalue, true) : '') : $logvalue;
            }
        }
        // START Criteria in array
        foreach ($options as $key => $val) {
            // if user input an empty variable unset it
            if (empty($val)) {
                continue;
            }
            // deal with greater than and lower than
            $tt = substr($key, -2);
            switch ($tt) {
                case 'GT':
                    $op = substr($key, 0, -2);
                    break;
                case 'LT':
                    $op = substr($key, 0, -2);
                    break;
                default:
                    $op = $key;
                    break;
            }
            $val_arr = explode(',', $val);
            // if type is text
            if (!empty($val_arr[0]) && (int)$val_arr[0] == 0) {
                foreach ($logs as $id => $log) {
                    if (is_array($log[$op])) {
                        $log[$op] = json_encode($log[$op], (phpversion() > '5.4.0') ? JSON_UNESCAPED_UNICODE : 0);
                    }
                    foreach ($val_arr as $qry) {
                        // if !QUERY eg: !logs.php,views.php
                        if (substr($qry, 0, 1) === '!') {
                            $flagStr = true;
                            if (strpos($log[$op], substr($qry, 1)) !== false) {
                                $flagStr = false; // have that delete
                                break; // means AND
                            }
                        } else {
                            $flagStr = false;
                            if (strpos($log[$op], $qry) !== false) {
                                $flagStr = true; // have that dont delete
                                break; // means OR
                            }
                        }
                    }
                    if (!$flagStr) {
                        unset($logs[$id]);
                    }
                }
            } else {
                // if there is one value - deal with =, > ,<
                if (count($val_arr) == 1) {
                    $val_int = $val_arr[0];
                    if ($op === 'log_time' || $op === 'last_login') {
                        $val_int = time() - $this->userlog->getSinceTime($val_int);
                    }
                    // query is one int $t (=, < , >)
                    foreach ($logs as $id => $log) {
                        switch ($tt) {
                            case 'GT':
                                if ($log[$op] <= $val_int) {
                                    unset($logs[$id]);
                                }
                                break;
                            case 'LT':
                                if ($log[$op] >= $val_int) {
                                    unset($logs[$id]);
                                }
                                break;
                            default:
                                if ($log[$op] != $val_int) {
                                    unset($logs[$id]);
                                }
                                break;
                        }
                    }
                } else {
                    // query is an array of int separate with comma. use OR ???
                    foreach ($logs as $id => $log) {
                        if (!in_array($log[$op], $val_arr)) {
                            unset($logs[$id]);
                        }
                    }
                }
            }
        }
        // END Criteria in array
        // if no logs return empty array and total = 0
        if (empty($logs)) {
            return array(array(), 0);
        }

        // sort order array. multisort is possible :D
        if (!empty($sort)) {
            // log_id is just the same as log_time
            if ($sort === 'log_id') {
                $sort = 'log_time';
            }
            // $typeFlag = is_numeric($logs[0][$sort]) ? SORT_NUMERIC : SORT_STRING;
            // Obtain a list of columns
            foreach ($logs as $key => $log) {
                $col[$key] = $log[$sort];
                //$col2[$key]  = $log[$sort2];
            }
            // Add $logs as the last parameter, to sort by the common key
            array_multisort($col, ($order === 'ASC') ? SORT_ASC : SORT_DESC, $logs);
        }
        // get count
        $total = count($logs);
        // now slice the array with desired start and limit
        if (!empty($limit)) {
            $logs = array_slice($logs, $start, $limit);
        }

        return array($logs, $total);
    }

    /**
     * @param array $log_files
     *
     * @return array
     */
    public function readFiles($log_files = array())
    {
        $log_files = $this->parseFiles($log_files);
        if (($totalFiles = count($log_files)) == 0) {
            return $this->readFile();
        }
        $logs = array();
        foreach ($log_files as $file) {
            $logs = array_merge($logs, $this->readFile($file));
        }

        return $logs;
    }

    /**
     * @param array $log_files
     * @param null  $mergeFileName
     *
     * @return bool|string
     */
    public function mergeFiles($log_files = array(), $mergeFileName = null)
    {
        $log_files = $this->parseFiles($log_files);
        if (($totalFiles = count($log_files)) == 0) {
            $this->setErrors(_AM_USERLOG_FILE_SELECT_ONE);

            return false;
        }
        $logs          = array();
        $logsStr       = $this->readFiles($log_files);
        $data          = implode("\n", $logsStr);
        $mergeFile     = $this->userlog->getConfig('logfilepath') . '/' . USERLOG_DIRNAME . '/';
        $mergeFileName = basename($mergeFileName, '.' . $this->userlog->logext);
        if (empty($mergeFileName)) {
            $mergeFile .= $this->userlog->getConfig('logfilename') . '_merge_' . count($log_files) . '_files_' . date('Y-m-d_H-i-s');
        } else {
            $mergeFile .= $mergeFileName;
        }
        $mergeFile .= '.' . $this->userlog->logext;

        // file create/open/write
        $fileHandler = XoopsFile::getHandler();
        $fileHandler->__construct($mergeFile, false); //to see if file exist
        if ($fileHandler->exists()) {
            $this->setErrors("file ({$mergeFile}) is exist");

            return false;
        }
        $fileHandler->__construct($mergeFile, true); // create file and folder
        if ($fileHandler->open('a') === false) {
            $this->setErrors("Cannot open file ({$mergeFile})");

            return false;
        }
        if ($fileHandler->write($data) === false) {
            $this->setErrors("Cannot write to file ({$mergeFile})");

            return false;
        }
        $fileHandler->close();

        return $mergeFile;
    }

    /**
     * @param null $log_file
     *
     * @return array
     */
    public function readFile($log_file = null)
    {
        if (!$log_file) {
            $log_file = $this->userlog->getWorkingFile();
        }
        // file open/read
        $fileHandler = XoopsFile::getHandler();
        // not create file if not exist
        $fileHandler->__construct($log_file, false);
        if (!$fileHandler->exists()) {
            $this->setErrors("Cannot open file ({$log_file})");

            return array();
        }

        if (($data = $fileHandler->read()) === false) {
            $this->setErrors("Cannot read file ({$log_file})");

            return array();
        }
        $fileHandler->close();
        $logs = explode("\n", $data);

        return $logs;
    }

    /**
     * @param array $log_files
     *
     * @return int
     */
    public function deleteFiles($log_files = array())
    {
        $log_files = $this->parseFiles($log_files);
        if (($totalFiles = count($log_files)) == 0) {
            $this->setErrors(_AM_USERLOG_FILE_SELECT_ONE);

            return false;
        }
        $deletedFiles = 0;
        // file open/read
        $fileHandler = XoopsFile::getHandler();
        foreach ($log_files as $file) {
            $fileHandler->__construct($file, false);
            if (!$fileHandler->exists()) {
                $this->setErrors("({$file}) is a folder or is not exist");
                continue;
            }
            if (($ret = $fileHandler->delete()) === false) {
                $this->setErrors("Cannot delete ({$file})");
                continue;
            }
            ++$deletedFiles;
        }
        $fileHandler->close();

        return $deletedFiles;
    }

    /**
     * @param null $log_file
     * @param null $newFileName
     *
     * @return bool|string
     */
    public function renameFile($log_file = null, $newFileName = null)
    {
        if (!is_string($log_file)) {
            $this->setErrors(_AM_USERLOG_FILE_SELECT_ONE);

            return false;
        }
        // check if file exist
        $fileHandler = XoopsFile::getHandler();
        $fileHandler->__construct($log_file, false);
        if (!$fileHandler->exists()) {
            $this->setErrors("({$log_file}) is a folder or is not exist");

            return false;
        }

        $newFileName = basename($newFileName, '.' . $this->userlog->logext);
        if (empty($newFileName)) {
            $newFileName = $fileHandler->name() . '_rename_' . date('Y-m-d_H-i-s');
        }
        $newFile = dirname($log_file) . '/' . $newFileName . '.' . $this->userlog->logext;
        // check if new file exist => return false
        $fileHandler->__construct($newFile, false);
        if ($fileHandler->exists()) {
            $this->setErrors("({$newFile}) is exist");

            return false;
        }
        if (!@rename($log_file, $newFile)) {
            $this->setErrors("Cannot rename ({$log_file})");

            return false;
        }
        $fileHandler->close();

        return $newFile;
    }

    /**
     * @param null $log_file
     * @param null $newFileName
     *
     * @return bool|string
     */
    public function copyFile($log_file = null, $newFileName = null)
    {
        if (!is_string($log_file)) {
            $this->setErrors(_AM_USERLOG_FILE_SELECT_ONE);

            return false;
        }
        // check if file exist
        $fileHandler = XoopsFile::getHandler();
        $fileHandler->__construct($log_file, false);
        if (!$fileHandler->exists()) {
            $this->setErrors("({$log_file}) is a folder or is not exist");

            return false;
        }

        $newFileName = basename($newFileName, '.' . $this->userlog->logext);
        if (empty($newFileName)) {
            $newFileName = $fileHandler->name() . '_copy_' . date('Y-m-d_H-i-s');
        }
        $newFile = dirname($log_file) . '/' . $newFileName . '.' . $this->userlog->logext;
        // check if new file exist => return false
        $fileHandler->__construct($newFile, false);
        if ($fileHandler->exists()) {
            $this->setErrors("({$newFile}) is exist");

            return false;
        }
        if (!@copy($log_file, $newFile)) {
            $this->setErrors("Cannot copy ({$log_file})");

            return false;
        }
        $fileHandler->close();

        return $newFile;
    }

    /**
     * @param array $folders
     *
     * @return array
     */
    public function getFilesFromFolders($folders = array())
    {
        list($allFiles, $totalFiles) = $this->userlog->getAllLogFiles();
        if (empty($totalFiles)) {
            return array();
        }
        $pathFiles = array();
        $getAll    = false;
        if (in_array('all', $folders)) {
            $getAll = true;
        }
        foreach ($allFiles as $path => $files) {
            if ($getAll || in_array($path, $folders)) {
                foreach ($files as $file) {
                    $pathFiles[] = $path . '/' . $file;
                }
            }
        }

        return $pathFiles;
    }

    /**
     * @param array $log_files
     *
     * @return array
     */
    public function parseFiles($log_files = array())
    {
        $pathFiles = $this->getFilesFromFolders($log_files);
        $log_files = array_unique(array_merge($log_files, $pathFiles));
        // file open/read
        $fileHandler = XoopsFile::getHandler();
        foreach ($log_files as $key => $file) {
            $fileHandler->__construct($file, false);
            if (!$fileHandler->exists()) {
                $this->setErrors("({$file}) is a folder or is not exist");
                unset($log_files[$key]);
                continue;
            }
        }
        $fileHandler->close();

        return $log_files;
    }

    /**
     * @param array $log_files
     * @param null  $zipFileName
     *
     * @return string
     */
    public function zipFiles($log_files = array(), $zipFileName = null)
    {
        $log_files = $this->parseFiles($log_files);
        if (($totalFiles = count($log_files)) == 0) {
            $this->setErrors('No file to zip');

            return false;
        }
        //this folder must be writeable by the server
        $zipFolder     = $this->userlog->getConfig('logfilepath') . '/' . USERLOG_DIRNAME . '/zip';
        $folderHandler = XoopsFile::getHandler('folder', $zipFolder, true);// create if not exist
        $zipFileName   = basename($zipFileName, '.zip');
        if (empty($zipFileName)) {
            $zipFileName = $this->userlog->getConfig('logfilename') . '_zip_' . $totalFiles . '_files_' . date('Y-m-d_H-i-s') . '.zip';
        } else {
            $zipFileName = $zipFileName . '.zip';
        }
        $zipFile = $zipFolder . '/' . $zipFileName;

        $zip = new ZipArchive();

        if ($zip->open($zipFile, ZipArchive::CREATE) !== true) {
            $this->setErrors("Cannot open ({$zipFile})");

            return false;
        }
        foreach ($log_files as $file) {
            if (!$zip->addFile($file, basename($file))) {
                $this->setErrors("Cannot zip ({$file})");
            }
        }
        // if there are some files existed in zip file and/or some files overwritten
        if ($totalFiles != $zip->numFiles) {
            $this->setErrors("Number of files operated in zipped file: ({$zip->numFiles})");
        }
        //$this->setErrors("Zip file name: ({$zip->filename})");
        $zip->close();

        return $zipFile;
    }

    /**
     * @param array $currentFile
     * @param bool  $multi
     * @param int   $size
     *
     * @return XoopsFormSelect
     */
    public function buildFileSelectEle($currentFile = array(), $multi = false, $size = 3)
    {
        // $modversion['config'][$i]['options'] = array(_AM_USERLOG_FILE_WORKING=>'0',_AM_USERLOG_STATS_FILEALL=>'all');
        if (count($currentFile) == 0 || $currentFile[0] == '0') {
            $currentFile = $this->userlog->getWorkingFile();
        }
        $fileEl = new XoopsFormSelect(_AM_USERLOG_FILE, 'file', $currentFile, $size, $multi);
        list($allFiles, $totalFiles) = $this->userlog->getAllLogFiles();
        if (empty($totalFiles)) {
            return $fileEl;
        }
        $log_file_name = $this->userlog->getConfig('logfilename');
        $working_file  = $log_file_name . '.' . $this->userlog->logext;
        $fileEl->addOption('all', _AM_USERLOG_STATS_FILEALL);
        foreach ($allFiles as $path => $files) {
            $fileEl->addOption($path, '>' . $path);
            foreach ($files as $file) {
                $fileEl->addOption($path . '/' . $file, '-----' . $file . (($file == $working_file) ? '(' . _AM_USERLOG_FILE_WORKING . ')' : ''));
            }
        }

        return $fileEl;
    }

    /**
     * @return bool
     */
    public function setItem()
    {
        // In very rare occasions like newbb the item_id is not in the URL $_REQUEST
        include_once __DIR__ . '/plugin/plugin.php';
        include_once __DIR__ . '/plugin/Abstract.php';
        if ($plugin = Userlog_Module_Plugin::getPlugin($this->userlog->getLogModule()->getVar('dirname'), USERLOG_DIRNAME, true)) {
            /*
            // get all module scripts can accept an item_name to check if this script is exist
            $scripts = $plugin->item();
            $ii = 0;
            $len_script = count($scripts);
            foreach ($scripts as $item_name=>$script_arr) {
                ++$ii;
                $script_arr = is_array($script_arr) ? $script_arr : array($script_arr);
                if(in_array($this->script(), $script_arr)) break;
                if($ii == $len_script) return false;
            }
            */
            $item = $plugin->item($this->script());
            if (empty($item['item_id'])) {
                return false;
            }
            $this->setVar('item_name', $item['item_name']);
            $this->setVar('item_id', $item['item_id']);

            return true;
        }
        // if no plugin use notifications
        $not_config = $this->userlog->getLogModule()->getInfo('notification');
        if (!empty($not_config)) {
            foreach ($not_config['category'] as $category) {
                // if $item_id != 0 ---> return true
                if (!empty($category['item_name'])
                    && in_array($this->script(), is_array($category['subscribe_from']) ? $category['subscribe_from'] : array($category['subscribe_from']))
                    && $item_id = XoopsRequest::getInt($category['item_name'], 0)
                ) {
                    $this->setVar('item_name', $category['item_name']);
                    $this->setVar('item_id', $item_id);

                    return true;
                }
            }
        }

        return false;
    }
}

/**
 * Class UserlogLogHandler
 */
class UserlogLogHandler extends XoopsPersistableObjectHandler
{
    public $userlog = null;

    /**
     * @param null|object|XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    {
        $this->userlog = Userlog::getInstance();
        parent::__construct($db, USERLOG_DIRNAME . '_log', 'UserlogLog', 'log_id', 'log_time');
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
    public function getLogs(
        $limit = 0,
        $start = 0,
        $otherCriteria = null,
        $sort = 'log_id',
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
        $ret =& $this->getAll($criteria, $fields, $asObject, $id_as_key);

        return $ret;
    }

    /**
     * @param null $criteria
     * @param null $fields
     * @param bool $asObject
     * @param bool $id_as_key
     *
     * @return array
     */
    public function getLogsCounts($criteria = null, $fields = null, $asObject = true, $id_as_key = true)
    {
        if (is_array($fields) && count($fields) > 0) {
            if (!in_array($this->keyName, $fields)) {
                $fields[] = $this->keyName;
            }
            $select = implode(',', $fields);
        } else {
            $select = '*';
        }
        $limit = null;
        $start = null;
        $sql   = "SELECT {$select}, COUNT(*) AS count FROM {$this->table}";
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ($groupby = $criteria->getGroupby()) {
                $sql .= !strpos($groupby, 'GROUP BY') ? " GROUP BY {$groupby}" : $groupby;
            }
            if ($sort = $criteria->getSort()) {
                $sql .= " ORDER BY {$sort} " . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result   = $this->db->query($sql, $limit, $start);
        $ret      = array();
        $retCount = array();
        if ($asObject) {
            while (($myrow = $this->db->fetchArray($result)) !== false) {
                if ($id_as_key) {
                    $retCount[$myrow[$this->keyName]] = array_pop($myrow);
                } else {
                    $retCount[] = array_pop($myrow);
                }
                $object = $this->create(false);
                $object->assignVars($myrow);
                if ($id_as_key) {
                    $ret[$myrow[$this->keyName]] = $object;
                } else {
                    $ret[] = $object;
                }
                unset($object);
            }
        } else {
            $object = $this->create(false);
            while (($myrow = $this->db->fetchArray($result)) !== false) {
                if ($id_as_key) {
                    $retCount[$myrow[$this->keyName]] = array_pop($myrow);
                } else {
                    $retCount[] = array_pop($myrow);
                }
                $object->assignVars($myrow);
                if ($id_as_key) {
                    $ret[$myrow[$this->keyName]] = $object->getValues(array_keys($myrow));
                } else {
                    $ret[] = $object->getValues(array_keys($myrow));
                }
            }
            unset($object);
        }

        return array($ret, $retCount);
    }

    /**
     * @param null   $otherCriteria
     * @param string $notNullFields
     *
     * @return int
     */
    public function getLogsCount($otherCriteria = null, $notNullFields = '')
    {
        $criteria = new CriteriaCompo();
        if (!empty($otherCriteria)) {
            $criteria->add($otherCriteria);
        }

        return $this->getCount($criteria, $notNullFields);
    }

    /**
     * Change Field in a table
     *
     * @access public
     *
     * @param string $field     - name of the field eg: "my_field"
     * @param string $structure - structure of the field eg: "VARCHAR(50) NOT NULL default ''"
     * @param        bool
     *
     * @return bool
     */
    public function changeField($field = null, $structure = null)
    {
        $sql = "ALTER TABLE {$this->table} CHANGE {$field} {$field} {$structure}";
        if (!$result = $this->db->queryF($sql)) {
            xoops_error($this->db->error() . '<br>' . $sql);

            return false;
        }

        return true;
    }

    /**
     * Show Fields in a table - one field or all fields
     *
     * @access   public
     *
     * @param string $field - name of the field eg: "my_field" or null for all fields
     *
     * @internal param array $ret [my_field] = Field    Type    Null    Key        Default        Extra
     *
     * @return array|bool
     */
    public function showFields($field = null)
    {
        $sql = "SHOW FIELDS FROM {$this->table}";
        if (isset($field)) {
            $sql .= " LIKE '{$field}'";
        }
        if (!$result = $this->db->queryF($sql)) {
            xoops_error($this->db->error() . '<br>' . $sql);

            return false;
        }
        $ret = array();
        while ($myrow = $this->db->fetchArray($result)) {
            $ret[$myrow['Field']] = $myrow;
        }

        return $ret;
    }

    /**
     * Add Field in a table
     *
     * @access public
     *
     * @param string $field     - name of the field eg: "my_field"
     * @param string $structure - structure of the field eg: "VARCHAR(50) NOT NULL default '' AFTER item_id"
     * @param        bool
     *
     * @return bool
     */
    public function addField($field = null, $structure = null)
    {
        if (empty($field) || empty($structure)) {
            return false;
        }
        if ($this->showFields($field)) {
            return false;
        } // field is exist
        $sql = "ALTER TABLE {$this->table} ADD {$field} {$structure}";
        if (!$result = $this->db->queryF($sql)) {
            xoops_error($this->db->error() . '<br>' . $sql);

            return false;
        }

        return true;
    }

    /**
     * Drop Field in a table
     *
     * @access public
     *
     * @param string $field - name of the field
     * @param        bool
     *
     * @return bool
     */
    public function dropField($field = null)
    {
        if (empty($field)) {
            return false;
        }
        if (!$this->showFields($field)) {
            return false;
        } // field is not exist
        $sql = "ALTER TABLE {$this->table} DROP {$field}";
        if (!$result = $this->db->queryF($sql)) {
            xoops_error($this->db->error() . '<br>' . $sql);

            return false;
        }

        return true;
    }

    /**
     * Show index in a table
     *
     * @access   public
     *
     * @param string $index - name of the index (will be used in KEY_NAME)
     * @internal param array $ret = Table    Non_unique    Key_name    Seq_in_index    Column_name        Collation    Cardinality        Sub_part    Packed    Null    Index_type    Comment    Index_comment
     *
     * @return array|bool
     */
    public function showIndex($index = null)
    {
        $sql = "SHOW INDEX FROM {$this->table}";
        if (isset($index)) {
            $sql .= " WHERE KEY_NAME = '{$index}'";
        }
        if (!$result = $this->db->queryF($sql)) {
            xoops_error($this->db->error() . '<br>' . $sql);

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
     *
     * @param string $index      - name of the index
     * @param array  $fields     - array of table fields should be in the index
     * @param string $index_type - type of the index array("INDEX", "UNIQUE", "SPATIAL", "FULLTEXT")
     * @param        bool
     *
     * @return bool
     */
    public function addIndex($index = null, $fields = array(), $index_type = 'INDEX')
    {
        if (empty($index) || empty($fields)) {
            return false;
        }
        if ($this->showIndex($index)) {
            return false;
        } // index is exist
        $index_type = strtoupper($index_type);
        if (!in_array($index_type, array('INDEX', 'UNIQUE', 'SPATIAL', 'FULLTEXT'))) {
            return false;
        }
        $fields = is_array($fields) ? implode(',', $fields) : $fields;
        $sql    = "ALTER TABLE {$this->table} ADD {$index_type} {$index} ( {$fields} )";
        if (!$result = $this->db->queryF($sql)) {
            xoops_error($this->db->error() . '<br>' . $sql);

            return false;
        }

        return true;
    }

    /**
     * Drop index in a table
     *
     * @access public
     *
     * @param string $index - name of the index
     * @param        bool
     *
     * @return bool
     */
    public function dropIndex($index = null)
    {
        if (empty($index)) {
            return false;
        }
        if (!$this->showIndex($index)) {
            return false;
        } // index is not exist
        $sql = "ALTER TABLE {$this->table} DROP INDEX {$index}";
        if (!$result = $this->db->queryF($sql)) {
            xoops_error($this->db->error() . '<br>' . $sql);

            return false;
        }

        return true;
    }

    /**
     * Change Index = Drop index + Add Index
     *
     * @access public
     *
     * @param string $index      - name of the index
     * @param array  $fields     - array of table fields should be in the index
     * @param string $index_type - type of the index array("INDEX", "UNIQUE", "SPATIAL", "FULLTEXT")
     * @param        bool
     *
     * @return bool
     */
    public function changeIndex($index = null, $fields = array(), $index_type = 'INDEX')
    {
        if ($this->showIndex($index) && !$this->dropIndex($index)) {
            return false;
        } // if index is exist but cannot drop it

        return $this->addIndex($index, $fields, $index_type);
    }

    /**
     * Show if the object table or any other table is exist in database
     *
     * @access   public
     *
     * @param string $table or $db->prefix("{$table}") eg: $db->prefix("bb_forums") or "bb_forums" will return same result
     * @internal param bool $found
     *
     * @return bool
     */
    public function showTable($table = null)
    {
        if (empty($table)) {
            $table = $this->table;
        } // the table for this object
        // check if database prefix is not added yet and then add it!!!
        if (strpos($table, $this->db->prefix() . '_') !== 0) {
            $table = $this->db->prefix("{$table}");
        }
        $result = $this->db->queryF("SHOW TABLES LIKE '{$table}'");
        $found  = $this->db->getRowsNum($result);

        return empty($found) ? false : true;
    }

    /**
     * Rename an old table to the current object table in database
     *
     * @access public
     *
     * @param string $oldTable or $db->prefix("{$oldTable}") eg: $db->prefix("bb_forums") or "bb_forums" will return same result
     * @param        bool
     *
     * @return bool
     */
    public function renameTable($oldTable)
    {
        if ($this->showTable() || !$this->showTable($oldTable)) {
            return false;
        } // table is current || oldTable is not exist
        // check if database prefix is not added yet and then add it!!!
        if (strpos($oldTable, $this->db->prefix() . '_') !== 0) {
            $oldTable = $this->db->prefix("{$oldTable}");
        }
        if (!$result = $this->db->queryF("ALTER TABLE {$oldTable} RENAME {$this->table}")) {
            xoops_error($this->db->error() . '<br>' . $sql);

            return false;
        }

        return true;
    }
}
