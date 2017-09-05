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
 * @package         userlog
 * @since           1
 * @author          irmtfan (irmtfan@yahoo.com)
 * @author          XOOPS Project <www.xoops.org> <www.xoops.ir>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

$moduleDirName = basename(__DIR__);

// ------------------- Informations ------------------- //
$modversion = [
    'version'             => 1.17,
    'module_status'       => 'Beta 1',
    'release_date'        => '2017/09/04', //yyyy/mm/dd
    'name'                => _MI_USERLOG_NAME,
    'description'         => _MI_USERLOG_DSC,
    'official'            => 0, //1 indicates supported by XOOPS Dev Team, 0 means 3rd party supported
    'author'              => 'xoops.org (irmtfan)',
    'nickname'            => 'irmtfan',
    'author_mail'         => 'author-email',
    'author_website_url'  => 'https://xoops.org',
    'author_website_name' => 'XOOPS',
    'credits'             => 'XOOPS Project Team, trabis, irmtfan, mamba, tatane, cesagonchu, zyspec, blackrx, timgno, chefry',
    'license'             => 'GPL 2.0 or later',
    'license_url'         => 'www.gnu.org/licenses/gpl-2.0.html/',
    'help'                => 'page=help',
    //
    'release_info'        => 'Changelog',
    'release_file'        => XOOPS_URL . "/modules/{$moduleDirName}/docs/changelog file",
    //
    'manual'              => 'link to manual file',
    'manual_file'         => XOOPS_URL . "/modules/{$moduleDirName}/docs/install.txt",
    // images
    'image'               => 'assets/images/logoModule.png',
    'iconsmall'           => 'assets/images/iconsmall.png',
    'iconbig'             => 'assets/images/iconbig.png',
    'dirname'             => "{$moduleDirName}",
    // Local path icons
    'modicons16'          => 'assets/images/icons/16',
    'modicons32'          => 'assets/images/icons/32',
    //About
    //    'release'             => '2015-04-04',
    'demo_site_url'       => 'https://xoops.org',
    'demo_site_name'      => 'XOOPS Demo Site',
    'support_url'         => 'https://xoops.org/modules/newbb/viewforum.php?forum=28/',
    'support_name'        => 'Support Forum',
    'module_website_url'  => 'www.xoops.org',
    'module_website_name' => 'XOOPS Project',
    // ------------------- Min Requirements -------------------
    'min_php'             => '5.5',
    'min_xoops'           => '2.5.9',
    'min_admin'           => '1.2',
    'min_db'              => ['mysql' => '5.5'],
    // ------------------- Admin Menu -------------------
    'system_menu'         => 1,
    'hasAdmin'            => 1,
    'adminindex'          => 'admin/index.php',
    'adminmenu'           => 'admin/menu.php',
    // ------------------- Main Menu -------------------
    'hasMain'             => 1,
    // ------------------- Search ---------------------------
    'hasSearch'           => 0,
    //    'search'              => [
    //        'file'   => 'include/search.inc.php',
    //        'func'   => 'XXXX_search'],
    // ------------------- Comments -------------------------
    'hasComments'         => 0,
    //    'comments'              => array(
    //        'pageName'   => 'index.php',
    //        'itemName'   => 'id'),

    // Install/Update
    'onInstall'           => 'include/oninstall.php',
    'onUninstall'         => 'include/onuninstall.php',
    'onUpdate'            => 'include/onupdate.php',
    // ------------------- Mysql -----------------------------
    'sqlfile'             => ['mysql' => 'sql/mysql.sql'],
    // ------------------- Tables ----------------------------
    'tables'              => [
        $moduleDirName . '_log',
        $moduleDirName . '_set',
        $moduleDirName . '_stats'
    ],
];

// ------------------- Help files ------------------- //
$modversion['helpsection'] = [
    ['name' => _MI_USERLOG_OVERVIEW, 'link' => 'page=help'],
    ['name' => _MI_USERLOG_DISCLAIMER, 'link' => 'page=disclaimer'],
    ['name' => _MI_USERLOG_LICENSE, 'link' => 'page=license'],
    ['name' => _MI_USERLOG_SUPPORT, 'link' => 'page=support'],
];

// ------------------- Templates ------------------- //

xoops_loadLanguage('admin', $modversion['dirname']);

// Templates - if you don't define 'type' it will be 'module' | '' -> templates
$modversion['templates'] [] = [
    [
        'file'        => $modversion['dirname'] . '_admin_sets.tpl',
        'type'        => 'admin', // $type = 'blocks' -> templates/blocks , 'admin' -> templates/admin , 'module' | '' -> templates
        'description' => 'list of userlog setting'
    ],
];

$modversion['templates'] [] = [
    [
        'file'        => $modversion['dirname'] . '_admin_logs.tpl',
        'type'        => 'admin', // $type = 'blocks' -> templates/blocks , 'admin' -> templates/admin , 'module' | '' -> templates
        'description' => 'list of userlog logs'
    ],

];

$modversion['templates'] [] = [
    [
        'file'        => $modversion['dirname'] . '_admin_file.tpl',
        'type'        => 'admin', // $type = 'blocks' -> templates/blocks , 'admin' -> templates/admin , 'module' | '' -> templates
        'description' => 'File manager'
    ],

];

$modversion['templates'] [] = [
    [
        'file'        => $modversion['dirname'] . '_admin_stats.tpl',
        'type'        => 'admin', // $type = 'blocks' -> templates/blocks , 'admin' -> templates/admin , 'module' | '' -> templates
        'description' => 'Logs Statistics'
    ],

];

$modversion['templates'] [] = [
    [
        'file'        => $modversion['dirname'] . '_admin_stats_moduleadmin.tpl',
        'type'        => 'admin', // $type = 'blocks' -> templates/blocks , 'admin' -> templates/admin , 'module' | '' -> templates
        'description' => 'module admin history'
    ],
];
// ------------------- blocks ------------------- //

// options[0] - number of items to show in block. the default is 10
// options[1] - items to select in Where claus
// options[2] - Time period - default: 1 day
// options[3] - Uid in WHERE claus: select some users to only count views by them -1 -> all (by default)
// options[4] - Gid in WHERE claus: select some groups to only count views by them 0 -> all (by default)
// options[5] - Sort - views, module dirname, module name, module views default: views
// options[6] - Order - DESC, ASC default: DESC

$modversion['blocks'][] = [
    'file'        => 'views.php',
    'name'        => _MI_USERLOG_BLOCK_VIEWS,
    'description' => _MI_USERLOG_BLOCK_VIEWS_DSC,
    'show_func'   => $modversion['dirname'] . '_views_show',
    'edit_func'   => $modversion['dirname'] . '_views_edit',
    'options'     => '10|0|1|-1|0|count|DESC',
    'template'    => $modversion['dirname'] . '_block_views.tpl',
];

// options[0] - number of items to show in block. the default is 10
// options[1] - login or register or both radio select
// options[2] - failed or successful or both radio select
// options[3] - inactive or active or both
// options[4] - never login before or login before or both
// options[5] - Order - DESC, ASC default: DESC

$modversion['blocks'][] = [
    'file'        => 'login_reg_history.php',
    'name'        => _AM_USERLOG_LOGIN_REG_HISTORY,
    'description' => _AM_USERLOG_LOGIN_REG_HISTORY,
    'show_func'   => $modversion['dirname'] . '_login_reg_history_show',
    'edit_func'   => $modversion['dirname'] . '_login_reg_history_edit',
    'options'     => '10|0|0|0|0|DESC',
    'template'    => $modversion['dirname'] . '_block_login_reg_history.tpl',
];

// options[0] - number of items to show in block. the default is 10
// options[1] - stats_type - referral (default), browser, OS
// options[2] - Sort - stats_link, stats_value (default), time_update
// options[3] - Order - DESC, ASC default: DESC

$modversion['blocks'][] = [
    'file'        => 'stats_type.php',
    'name'        => _AM_USERLOG_STATS_TYPE,
    'description' => _AM_USERLOG_STATS_TYPE_DSC,
    'show_func'   => $modversion['dirname'] . '_stats_type_show',
    'edit_func'   => $modversion['dirname'] . '_stats_type_edit',
    'options'     => '10|referral|stats_value|DESC',
    'template'    => $modversion['dirname'] . '_block_stats_type.tpl',
];

// Config categories
$modversion['configcat']['logfile']['name']        = _MI_USERLOG_CONFCAT_LOGFILE;
$modversion['configcat']['logfile']['description'] = _MI_USERLOG_CONFCAT_LOGFILE_DSC;
$modversion['configcat']['format']['name']         = _MI_USERLOG_CONFCAT_FORMAT;
$modversion['configcat']['format']['description']  = _MI_USERLOG_CONFCAT_FORMAT_DSC;
$modversion['configcat']['pagenav']['name']        = _MI_USERLOG_CONFCAT_PAGENAV;
$modversion['configcat']['pagenav']['description'] = _MI_USERLOG_CONFCAT_PAGENAV_DSC;
$modversion['configcat']['logdb']['name']          = _MI_USERLOG_CONFCAT_LOGDB;
$modversion['configcat']['logdb']['description']   = _MI_USERLOG_CONFCAT_LOGDB_DSC;
$modversion['configcat']['prob']['name']           = _MI_USERLOG_CONFCAT_PROB;
$modversion['configcat']['prob']['description']    = _MI_USERLOG_CONFCAT_PROB_DSC;

// Config Settings (only for modules that need config settings generated automatically)
################### Log file ####################
$modversion['log_paths'] = [
    'XOOPS_VAR_PATH'    => XOOPS_VAR_PATH,
    'XOOPS_UPLOAD_PATH' => XOOPS_UPLOAD_PATH
];

$modversion['config'][] = [
    'name'        => 'status',
    'title'       => '_MI_USERLOG_STATUS',
    'description' => '_MI_USERLOG_STATUS_DSC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 1,
    'options'     => [
        _MI_USERLOG_ACTIVE => 1,
        _MI_USERLOG_IDLE   => 0
    ],
];

$modversion['config'][] = [
    'name'        => 'postlog',
    'title'       => '_MI_USERLOG_POSTLOG',
    'description' => '_MI_USERLOG_POSTLOG_DSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'logfile',
    'title'       => '_MI_USERLOG_CONFCAT_LOGFILE_DSC',
    'description' => '',
    'formtype'    => 'line_break',
    'valuetype'   => 'textbox',
    'default'     => 'odd',
    'category'    => 'logfile',
];

$modversion['config'][] = [
    'name'        => 'maxlogfilesize',
    'title'       => '_MI_USERLOG_MAXLOGFILESIZE',
    'description' => '_MI_USERLOG_MAXLOGFILESIZE_DSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 1000000, // bytes below 1MB because some servers have limitations
    'category'    => 'logfile',
];

$modversion['config'][] = [
    'name'        => 'logfilepath',
    'title'       => '_MI_USERLOG_LOGFILEPATH',
    'description' => '_MI_USERLOG_LOGFILEPATH_DSC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => XOOPS_VAR_PATH,
    'options'     => $modversion['log_paths'],
    'category'    => 'logfile',
];

$modversion['config'][] = [
    'name'        => 'logfilename',
    'title'       => '_MI_USERLOG_LOGFILENAME',
    'description' => '_MI_USERLOG_LOGFILENAME_DSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => 'userlognav',
    'category'    => 'logfile',
];

$modversion['config'][] = [
    'name'        => 'format',
    'title'       => '_MI_USERLOG_CONFCAT_FORMAT_DSC',
    'description' => '',
    'formtype'    => 'line_break',
    'valuetype'   => 'textbox',
    'default'     => 'even',
    'category'    => 'format',
];

$modversion['config'][] = [
    'name'        => 'format_date',
    'title'       => '_MI_USERLOG_DATEFORMAT',
    'description' => '_MI_USERLOG_DATEFORMAT_DSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => 'd-M-Y H:i',
    'category'    => 'format',
];

$modversion['config'][] = [
    'name'        => 'format_date_history',
    'title'       => '_MI_USERLOG_DATEFORMAT_HISTORY',
    'description' => '_MI_USERLOG_DATEFORMAT_DSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => 'elapse',
    'category'    => 'format',

];

$modversion['config'][] = [
    'name'        => 'pagenav',
    'title'       => '_MI_USERLOG_CONFCAT_PAGENAV_DSC',
    'description' => '',
    'formtype'    => 'line_break',
    'valuetype'   => 'textbox',
    'default'     => 'odd',
    'category'    => 'pagenav',
];

$modversion['config'][] = [
    'name'        => 'sets_perpage',
    'title'       => '_MI_USERLOG_SETS_PERPAGE',
    'description' => '_MI_USERLOG_SETS_PERPAGE_DSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 20,
    'category'    => 'pagenav',
];

$modversion['config'][] = [
    'name'        => 'logs_perpage',
    'title'       => '_MI_USERLOG_LOGS_PERPAGE',
    'description' => '_MI_USERLOG_LOGS_PERPAGE_DSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 100,
    'category'    => 'pagenav',
];

$modversion['config'][] = [
    'name'        => 'engine',
    'title'       => '_MI_USERLOG_ENGINE',
    'description' => '_MI_USERLOG_ENGINE_DSC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 'db',
    'options'     => [
        _AM_USERLOG_ENGINE_DB   => 'db',
        _AM_USERLOG_ENGINE_FILE => 'file'
    ],
    'category'    => 'pagenav',
];

$modversion['config'][] = [
    'name'        => 'file',
    'title'       => '_MI_USERLOG_FILE',
    'description' => '_MI_USERLOG_FILE_DSC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => '0',
    'options'     => [
        _AM_USERLOG_FILE_WORKING  => '0',
        _AM_USERLOG_STATS_FILEALL => 'all'
    ],
    'category'    => 'pagenav',
];

$modversion['config'][] = [
    'name'        => 'logdb',
    'title'       => '_MI_USERLOG_CONFCAT_LOGDB_DSC',
    'description' => '',
    'formtype'    => 'line_break',
    'valuetype'   => 'textbox',
    'default'     => 'even',
    'category'    => 'logdb',

];

$modversion['config'][] = [
    'name'        => 'maxlogs',
    'title'       => '_MI_USERLOG_MAXLOGS',
    'description' => '_MI_USERLOG_MAXLOGS_DSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 10000,
    'category'    => 'logdb',
];

$modversion['config'][] = [
    'name'        => 'maxlogsperiod',
    'title'       => '_MI_USERLOG_MAXLOGSPERIOD',
    'description' => '_MI_USERLOG_MAXLOGSPERIOD_DSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 0,
    'category'    => 'logdb',
];

$modversion['config'][] = [
    'name'        => 'prob',
    'title'       => '_MI_USERLOG_CONFCAT_PROB_DSC',
    'description' => '',
    'formtype'    => 'line_break',
    'valuetype'   => 'textbox',
    'default'     => 'odd',
    'category'    => 'prob',
];

$modversion['config'][] = [
    'name'        => 'probset',
    'title'       => '_MI_USERLOG_PROBSET',
    'description' => '_MI_USERLOG_PROBSET_DSC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 20,
    'options'     => array_combine(range(1, 100), range(1, 100)),
    'category'    => 'prob',
];

$modversion['config'][] = [
    'name'        => 'probstats',
    'title'       => '_MI_USERLOG_PROBSTATS',
    'description' => '_MI_USERLOG_PROBSTATS_DSC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 10,
    'options'     => range(0, 100),
    'category'    => 'prob',
];

$modversion['config'][] = [
    'name'        => 'probstatsallhit',
    'title'       => '_MI_USERLOG_PROBSTATSALLHIT',
    'description' => '_MI_USERLOG_PROBSTATSALLHIT_DSC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 1,
    'options'     => range(0, 100),
    'category'    => 'prob',
];

// START add webmaster permission from file to add additional permission check for all webmasters
global $xoopsOption, $xoopsModule;
// effective only in admin side
if (isset($xoopsOption['pagetype']) && 'admin' === $xoopsOption['pagetype'] && is_object($xoopsModule)) {
    // get dirname
    $dirname = $xoopsModule->getVar('dirname');
    // START if dirname is system
    if ('system' === $dirname && isset($_REQUEST['fct'])) {
        $hModule = xoops_getHandler('module');
        // if we are in preferences of modules
        if ('preferences' === $_REQUEST['fct'] && isset($_REQUEST['mod'])) {
            $mod     = (int)$_REQUEST['mod'];
            $module  = $hModule->get($mod);
            $dirname = $module->getVar('dirname');
        }
        // if we are in modules admin - can be done with onuninstall and onupdate???
        if ('modulesadmin' === $_REQUEST['fct'] && isset($_REQUEST['module'])) {
            $dirname = $_REQUEST['module'];
        }
        // if we are in maintenance - now all modules - how to do it for only one module?
        if ('maintenance' === $_REQUEST['fct']) {
            $dump_modules = isset($_REQUEST['dump_modules']) ? $_REQUEST['dump_modules'] : false;
            $dump_tables  = isset($_REQUEST['dump_tables']) ? $_REQUEST['dump_tables'] : false;
            if (true === $dump_tables || true === $dump_modules) {
                $dirname = $modversion['dirname'];
            }
        }
    }
    // END if dirname is system

    // now check permission from file
    if ($dirname == $modversion['dirname']) {
        if (file_exists($permFile = XOOPS_ROOT_PATH . '/modules/' . $modversion['dirname'] . '/admin/addon/perm.php')) {
            $perm = include $permFile;
            if (count($perm['super']['uid']) > 0 || count($perm['super']['group']) > 0) {
                global $xoopsUser;
                if (is_object($xoopsUser) && !in_array($xoopsUser->getVar('uid'), $perm['super']['uid'])
                    && 0 == count(array_intersect($xoopsUser->getGroups(), $perm['super']['group']))) {
                    $modversion['hasAdmin']    = 0;
                    $modversion['system_menu'] = 0;
                    $modversion['tables']      = null;
                    redirect_header(XOOPS_URL . '/modules/system/help.php?mid=' . (!empty($mod) ? $mod : $xoopsModule->getVar('mid', 's')) . '&amp;page=help', 1, sprintf(_MI_USERLOG_WEBMASTER_NOPERM, implode(',', $perm['super']['uid']), implode(',', $perm['super']['group'])));
                }
            }
        }
    }
}
// END add webmaster permission from file to add additional permission check for all webmasters
