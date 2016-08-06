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
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author          trabis <lusopoemas@gmail.com>
 * @version         $Id: Plugin.php 10605 2012-12-29 14:19:09Z trabis $
 */
// irmtfan copy from xoops26 xoops_lib/Xoops/Module/plugin.php class
// change XoopsLoad -> self
// change $xoops -> $GLOBALS['xoops']
// change  Userlog_Module_Plugin_Abstract , Userlog_Module_Plugin
// change  $xoops->getActiveModules() -> xoops_getActiveModules()
class Userlog_Module_Plugin
{
    /**
     * @param string $dirname
     * @param string $pluginName
     * @param bool $force
     *
     * @return bool|Xoops_Module_Plugin_Abstract false if plugin does not exist
     */
    static function getPlugin($dirname, $pluginName = 'system', $force = false)
    {
        $inactiveModules = false;
        if ($force) {
            $inactiveModules = array($dirname);
        }
        $available = self::getPlugins($pluginName, $inactiveModules);
        if (!in_array($dirname, array_keys($available))) {
            return false;
        }
        return $available[$dirname];
    }

    /**
     * @param string $pluginName
     * @param array|bool $inactiveModules
     *
     * @return mixed
     */
    static function getPlugins($pluginName = 'system', $inactiveModules = false)
    {
        static $plugins = array();
        if (!isset($plugins[$pluginName])) {
            $plugins[$pluginName] = array();
            //$xoops = Xoops::getInstance();

            //Load interface for this plugin
            if (!self::loadFile($GLOBALS['xoops']->path("modules/{$pluginName}/class/plugin/interface.php"))) {
                return $plugins[$pluginName];
            }

            $dirnames = xoops_getActiveModules();
            if (is_array($inactiveModules)) {
                $dirnames = array_merge($dirnames, $inactiveModules);
            }
            foreach ($dirnames as $dirname) {
                if (self::loadFile($GLOBALS['xoops']->path("modules/{$dirname}/class/plugin/{$pluginName}.php")) ||
					self::loadFile($GLOBALS['xoops']->path("modules/{$pluginName}/class/plugin/{$dirname}.php"))) {
                    $className = ucfirst($dirname) . ucfirst($pluginName) . 'Plugin';
                    $interface = ucfirst($pluginName) . 'PluginInterface';
                    $class = new $className($dirname);
                    if ($class instanceof Userlog_Module_Plugin_Abstract && $class instanceof $interface) {
                        $plugins[$pluginName][$dirname] = $class;
                    }
                }
            }
        }
        return $plugins[$pluginName];
    }
    public static function loadFile($file, $once = true)
    {
        self::_securityCheck($file);
        if (self::fileExists($file)) {
            if ($once) {
                include_once $file;
            } else {
                include $file;
            }
            return true;
        }
        return false;
    }
	public static function fileExists($file)
    {
        static $included = array();
        if (!isset($included[$file])) {
            $included[$file] = file_exists($file);
        }
        return $included[$file];
    }
    protected static function _securityCheck($filename)
    {
        /**
         * Security check
         */
        if (preg_match('/[^a-z0-9\\/\\\\_.:-]/i', $filename)) {
            exit('Security check: Illegal character in filename');
        }
    }
}