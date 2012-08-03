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

define('DB_HOST',     'localhost');
define('DB_SCHEMA',   'GP');
define('DB_USER',     'kdemane');
define('DB_PASSWORD', 'gr1m01r3');
?>
