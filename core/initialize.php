<?php
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
defined('SITE_ROOT') ? null : define('SITE_ROOT', DS . 'var' . DS . 'www' . DS . 'html');

defined('INC_PATH') ? null : define('INC_PATH', SITE_ROOT . DS . 'includes');
defined('CORE_PATH') ? null : define('CORE_PATH', SITE_ROOT . DS . 'core');
defined('DB_PATH') ? null : define('DB_PATH', SITE_ROOT . DS . 'db');
defined('VENDOR_PATH') ? null : define('VENDOR_PATH', SITE_ROOT . DS . 'vendor');
defined('DB_NAME') ? null : define('DB_NAME', 'hhdb');
defined('DB_USER') ? null : define('DB_USER', 'root');
defined('DB_PASSWORD') ? null : define('DB_PASSWORD', '');


require_once(DB_PATH . DS . "createtable.php");
require_once(INC_PATH . DS . "config.php");
require_once(CORE_PATH . DS . "user.php");
require_once(CORE_PATH . DS . "perm_group.php");
require_once(VENDOR_PATH . DS . "autoload.php");
