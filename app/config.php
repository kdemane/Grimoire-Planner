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

require_once SERVER_ROOT . '../config_local.php';
?>
