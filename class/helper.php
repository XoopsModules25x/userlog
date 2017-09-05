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

use Xmf\Request;

defined('XOOPS_ROOT_PATH') || exit('Restricted access.');
require_once __DIR__ . '/phpbrowscap/Browscap.php';

// The Browscap class is in the phpbrowscap namespace, so import it
use phpbrowscap\Browscap;

/**
 * Class Userlog
 */
class Userlog extends \Xmf\Module\Helper
{
    public $logmodule;
    public $user;
    public $debugArray   = [];
    public $logext       = 'log';
    public $cookiePrefix = '';
    public $groupList;
    public $browscap;

    /**
     * @param $debug
     */
    protected function __construct($debug)
    {
        $this->debug        = $debug;
        $this->dirname      = USERLOG_DIRNAME;
        $this->cookiePrefix = USERLOG_DIRNAME . '_' . ($this->getUser() ? $this->getUser()->getVar('uid') : '');
    }

    /**
     * @param bool $debug
     *
     * @return Userlog
     */
    public static function getInstance($debug = false)
    {
        static $instance;
        if (null === $instance) {
            $instance = new static($debug);
        }

        return $instance;
    }

    /**
     * @return null|\XoopsModule
     */
    public function getLogModule()
    {
        if (null === $this->logmodule) {
            $this->initLogModule();
        }

        return $this->logmodule;
    }

    /**
     * @param array $dirnames
     * @param null  $otherCriteria
     * @param bool  $asObj
     *
     * @return mixed
     */
    public function getModules($dirnames = [], $otherCriteria = null, $asObj = false)
    {
        // get all dirnames
        /** @var XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $criteria      = new CriteriaCompo();
        if (count($dirnames) > 0) {
            foreach ($dirnames as $mDir) {
                $criteria->add(new Criteria('dirname', $mDir), 'OR');
            }
        }
        if (!empty($otherCriteria)) {
            $criteria->add($otherCriteria);
        }
        $criteria->add(new Criteria('isactive', 1), 'AND');
        $modules = $moduleHandler->getObjects($criteria, true);
        if ($asObj) {
            return $modules;
        }
        $dirNames['system-root'] = _YOURHOME;
        foreach ($modules as $module) {
            $dirNames[$module->dirname()] = $module->name();
        }

        return $dirNames;
    }

    /**
     * @return null
     */
    public function getUser()
    {
        if (null === $this->user) {
            $this->initUser();
        }

        return $this->user;
    }

    /**
     * @return null|array
     */
    public function getGroupList()
    {
        if (null === $this->groupList) {
            $this->initGroupList();
        }

        return $this->groupList;
    }

    /**
     * @return null
     */
    public function getBrowsCap()
    {
        if (null === $this->browscap) {
            $this->initBrowsCap();
        }

        return $this->browscap;
    }


    /**
     * @param null $name
     * @param null $value
     *
     * @return mixed
     */
    public function setConfig($name = null, $value = null)
    {
        if (null === $this->configs) {
            $this->initConfig();
        }
        $this->configs[$name] = $value;
        $this->addLog("Setting config '{$name}' : " . $this->configs[$name]);

        return $this->configs[$name];
    }

    /**
     * @return array
     */
    public function getAllLogFiles()
    {
        $logPaths    = $this->object->getInfo('log_paths');
        $currentPath = $this->getConfig('logfilepath');
        $allFiles    = [];
        $totalFiles  = 0;
        foreach ($logPaths as $path) {
            $folderHandler                           = XoopsFile::getHandler('folder', $path . '/' . USERLOG_DIRNAME);
            $allFiles[$path . '/' . USERLOG_DIRNAME] = $folderHandler->find('.*' . $this->logext);
            $totalFiles                              += count($allFiles[$path . '/' . USERLOG_DIRNAME]);
        }
        if (empty($totalFiles)) {
            return [[], 0];
        }

        return [$allFiles, $totalFiles];
    }

    /**
     * @return string
     */
    public function getWorkingFile()
    {
        $logFileName = $this->getConfig('logfilepath') . '/' . USERLOG_DIRNAME . '/' . $this->getConfig('logfilename');

        return $logFileName . '.' . $this->logext;
    }

    /**
     * @param      $array
     * @param null $keys
     *
     * @return array
     */
    public function getFromKeys($array, $keys = null)
    {
        if (empty($keys)) {
            return $array;
        } // all keys
        $keyarr = is_string($keys) ? explode(',', $keys) : $keys;
        if (empty($keyarr[0])) {
            return $array;
        } // all keys
        $keyarr = array_intersect(array_keys($array), $keyarr); // keys should be in array
        $ret    = [];
        foreach ($keyarr as $key) {
            $ret[$key] = $array[$key];
        }

        return $ret;
    }

    /**
     * @param int $since
     *
     * @return int
     */
    public function getSinceTime($since = 1) // one day
    {
        if ($since > 0) {
            return (int)$since * 24 * 3600;
        }

        return (int)abs($since) * 3600;
    }

    /**
     * @param null   $intTime
     * @param string $dateFormat
     * @param null|string $timeoffset
     *
     * @return bool|string
     */
    public function formatTime($intTime = null, $dateFormat = 'c', $timeoffset = null)
    {
        if (empty($intTime)) {
            return false;
        }
        if ('custom' === $dateFormat || 'c' === $dateFormat) {
            $dateFormat = $this->getConfig('format_date');
        }
        xoops_load('XoopsLocal');

        return class_exists('XoopsLocal') ? XoopsLocal::formatTimestamp($intTime, $dateFormat, $timeoffset) : XoopsLocale::formatTimestamp($intTime, $dateFormat, $timeoffset); // use XoopsLocale in xoops26
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getCookie($name = 'TOGGLE')
    {
        $toggles = Request::getString($this->cookiePrefix . $name, null, 'cookie');

        return explode(',', $toggles);
    }

    /**
     * @param int $prob
     *
     * @return bool
     */
    public function probCheck($prob = 11)
    {
        mt_srand((double)microtime() * 1000000);
        // check probabillity 11 means 10%, 100 means 100%
        $ret = mt_rand(1, 100) > $prob;

        return $ret;
    }

    /**
     * @param null $post
     * @param int  $uid
     * @param bool $unsetPass
     *
     * @return null
     */
    public function patchLoginHistory($post = null, $uid = 0, $unsetPass = true)
    {
        if ($uid > 0 || empty($post['pass']) || empty($post['uname'])) {
            return $post;
        }
        $postPatch                = $post;
        $postPatch['login_patch'] = 1;
        if ($unsetPass) {
            $postPatch['pass'] = 'unset_pass';
            if (isset($postPatch['vpass'])) {
                $postPatch['vpass'] = 'unset_vpass';
            }
        }
        $memberHandler = xoops_getHandler('member');
        $loginSuccess  = $memberHandler->loginUser($post['uname'], $post['pass']); // check login to find if this user is exist in database
        // only for successful login/register
        if (is_object($loginSuccess)) {
            $postPatch['success'] = 1;
            $postPatch['uid']     = $loginSuccess->getVar('uid');
            if (0 < ($level = $loginSuccess->getVar('level'))) {
                $postPatch['level'] = $level;
            }
            if (0 < ($last_visit = $loginSuccess->getVar('last_login'))) {
                $postPatch['last_visit'] = $last_visit;
            }
        }

        return $postPatch;
    }


    private function initLogModule()
    {
        global $xoopsModule;
        if (isset($xoopsModule) && is_object($xoopsModule)) {
            $this->logmodule = $xoopsModule;
        } else {
            $hModule         = xoops_getHandler('module');
            $this->logmodule = $hModule->getByDirname('system');
            $this->logmodule->setVar('dirname', 'system-root');
        }
        $this->addLog('INIT LOGMODULE');
    }

    private function initUser()
    {
        global $xoopsUser;
        if (isset($xoopsUser) && is_object($xoopsUser)) {
            $this->user = $xoopsUser;
        } else {
            $this->user = null;
        }
        $this->addLog('INIT USER');
    }

    private function initGroupList()
    {
        /** @var \XoopsMemberHandler $groupHandler */
        $groupHandler    = xoops_getHandler('member');
        $this->groupList = $groupHandler->getGroupList();
        $this->addLog('INIT GROUP LIST');
    }

    /**
     * @return bool
     * @throws \phpbrowscap\Exception
     */
    private function initBrowsCap()
    {
        $browscapCache = XOOPS_CACHE_PATH . '/browscap';
        // force to create file if not exist
        $folderHandler = XoopsFile::getHandler('folder', $browscapCache, true);
        if (!$folderHandler->pwd()) {
            // Errors Warning: mkdir() [function.mkdir]: Permission denied in file /class/file/folder.php line 529
            $this->addLog("Cannot create folder ({$browscapCache})");

            return false;
        }
        // Creates a new Browscap object (loads or creates the cache)
        // $bc = new Browscap('path/to/the/cache/dir');
        $this->browscap = new Browscap($browscapCache);
        $this->addLog('INIT BrowsCap');

        return true;
    }
}
