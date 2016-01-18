# userlog module database structure
# version 1
# irmtfan irmtfan@yahoo.com
# --------------------------------------------------------
CREATE TABLE mod_userlog_log (
  log_id mediumint(8) unsigned NOT NULL auto_increment,
  log_time int(11) unsigned NOT NULL default 0,
  uid mediumint(8) unsigned NOT NULL default 0,
  uname varchar(50) NOT NULL default '',
  admin smallint(1) unsigned NOT NULL default 0,
  groups varchar(100) NOT NULL default '',
  last_login int(11) unsigned NOT NULL default 0,
  user_ip varchar(15) NOT NULL default '',
  user_agent varchar(255) NOT NULL default '',
  url varchar(255) NOT NULL default '',
  script varchar(20) NOT NULL default '',
  referer varchar(255) NOT NULL default '',
  pagetitle varchar(255) NOT NULL default '',
  pageadmin smallint(1) unsigned NOT NULL default 0,
  module varchar(25) NOT NULL default '',
  module_name varchar(50) NOT NULL default '',
  item_name varchar(10) NOT NULL default '',
  item_id int(11) unsigned NOT NULL default 0,
  request_method varchar(20) NOT NULL default '',
  get TEXT NOT NULL,
  post LONGTEXT NOT NULL,
  request TEXT NOT NULL,
  files TEXT NOT NULL,
  env TEXT NOT NULL,
  session TEXT NOT NULL,
  cookie TEXT NOT NULL,
  header TEXT NOT NULL,
  logger TEXT NOT NULL,
  PRIMARY KEY  (log_id),
  KEY log_id_uid (log_id, uid),
  KEY uid (uid, uname, user_ip),
  KEY views (uid, groups, script, pagetitle(20),pageadmin, module, item_name, item_id)
) ENGINE=MyISAM;
# --------------------------------------------------------

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

CREATE TABLE mod_userlog_stats (
  stats_id mediumint(8) unsigned NOT NULL auto_increment,
  stats_type varchar(10) NOT NULL default '',
  stats_link varchar(255) NOT NULL default '',
  stats_value int(11) unsigned NOT NULL default 0,
  stats_period mediumint(8) NOT NULL default 0,
  time_update int(11) unsigned NOT NULL default 0,
  PRIMARY KEY  (stats_id),
  KEY stats_type_link_period (stats_type, stats_link, stats_period)
) ENGINE=MyISAM;