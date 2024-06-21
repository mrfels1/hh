<?php
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
defined('SITE_ROOT') ? null : define('SITE_ROOT', DS . 'wamp64' . DS . 'www' . DS . 'hh');

defined('INC_PATH') ? null : define('INC_PATH', SITE_ROOT . DS . 'includes');
defined('CORE_PATH') ? null : define('CORE_PATH', SITE_ROOT . DS . 'core');
defined('DB_PATH') ? null : define('DB_PATH', SITE_ROOT . DS . 'db');

require_once(DB_PATH . DS . "createtable.php");
require_once(INC_PATH . DS . "config.php");
require_once(CORE_PATH . DS . "user.php");
require_once(CORE_PATH . DS . "perm_group.php");
