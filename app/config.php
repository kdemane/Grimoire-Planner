<?
define('GP_ROOT', '/var/www/html/Grimoire-Planner/');

define('SERVER_ROOT', GP_ROOT . 'app/');

define('SMARTY_CACHING',           FALSE);
define('SMARTY_DEBUG',             FALSE);
define('SMARTY_VERSION',           '3.1.11');
define('SMARTY_APP_ROOT',          SERVER_ROOT . 'external/smarty/Smarty-' . SMARTY_VERSION . '/');
define('SMARTY_TPL_ROOT',          GP_ROOT . 'tpl_cache/');
define('SMARTY_TPL_CACHE_ROOT',    SMARTY_TPL_ROOT . 'static/');
define('SMARTY_TPL_TEMPLATE_ROOT', SMARTY_TPL_ROOT . 'compiled/');

// has DB credentials
require_once SERVER_ROOT . '../config_local.php';
require_once SERVER_ROOT . 'utilities/api/database.php';

$db = new db(DB_HOST, DB_SCHEMA, DB_USER, DB_PASSWORD);
?>
