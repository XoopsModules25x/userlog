# userlog module database structure
# version 1
# irmtfan irmtfan@yahoo.com
# --------------------------------------------------------
CREATE TABLE userlog_log (
  log_id         MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  log_time       INT(11) UNSIGNED      NOT NULL DEFAULT 0,
  uid            MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0,
  uname          VARCHAR(50)           NOT NULL DEFAULT '',
  admin          SMALLINT(1) UNSIGNED  NOT NULL DEFAULT 0,
  groups         VARCHAR(100)          NOT NULL DEFAULT '',
  last_login     INT(11) UNSIGNED      NOT NULL DEFAULT 0,
  user_ip        VARCHAR(15)           NOT NULL DEFAULT '',
  user_agent     VARCHAR(255)          NOT NULL DEFAULT '',
  url            VARCHAR(255)          NOT NULL DEFAULT '',
  script         VARCHAR(20)           NOT NULL DEFAULT '',
  referer        VARCHAR(255)          NOT NULL DEFAULT '',
  pagetitle      VARCHAR(255)          NOT NULL DEFAULT '',
  pageadmin      SMALLINT(1) UNSIGNED  NOT NULL DEFAULT 0,
  module         VARCHAR(25)           NOT NULL DEFAULT '',
  module_name    VARCHAR(50)           NOT NULL DEFAULT '',
  item_name      VARCHAR(10)           NOT NULL DEFAULT '',
  item_id        INT(11) UNSIGNED      NOT NULL DEFAULT 0,
  request_method VARCHAR(20)           NOT NULL DEFAULT '',
  zget           TEXT                  NOT NULL,
  post           LONGTEXT              NOT NULL,
  request        TEXT                  NOT NULL,
  files          TEXT                  NOT NULL,
  env            TEXT                  NOT NULL,
  session        TEXT                  NOT NULL,
  cookie         TEXT                  NOT NULL,
  header         TEXT                  NOT NULL,
  logger         TEXT                  NOT NULL,
  PRIMARY KEY (log_id),
  KEY log_time (log_time),
  KEY uid (uid, uname, user_ip),
  KEY views (uid, groups, script, pagetitle(20), pageadmin, module, item_name, item_id)
)
  ENGINE = MyISAM;
# --------------------------------------------------------

CREATE TABLE userlog_set (
  set_id    MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  name      VARCHAR(100)          NOT NULL DEFAULT '',
  logby     VARCHAR(10)           NOT NULL DEFAULT '',
  unique_id INT(11) UNSIGNED      NOT NULL DEFAULT 0,
  options   TEXT                  NOT NULL,
  scope     TEXT                  NOT NULL,
  PRIMARY KEY (set_id),
  UNIQUE logby_id (logby, unique_id)
)
  ENGINE = MyISAM;

CREATE TABLE userlog_stats (
  stats_id     MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  stats_type   VARCHAR(10)           NOT NULL DEFAULT '',
  stats_link   VARCHAR(255)          NOT NULL DEFAULT '',
  stats_value  INT(11) UNSIGNED      NOT NULL DEFAULT 0,
  stats_period MEDIUMINT(8)          NOT NULL DEFAULT 0,
  time_update  INT(11) UNSIGNED      NOT NULL DEFAULT 0,
  PRIMARY KEY (stats_id),
  UNIQUE stats_type_link_period (stats_type, stats_link, stats_period)
)
  ENGINE = MyISAM;
