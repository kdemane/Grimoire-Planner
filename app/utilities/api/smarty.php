<?php

function initialize_smarty()
{
    require_once SMARTY_APP_ROOT . '/libs/Smarty.class.php';

    $smarty = new Smarty();
    $smarty->config_dir = SMARTY_APP_ROOT . '/configs'; // doesn't seem to be used
    $smarty->compile_dir = SMARTY_TPL_TEMPLATE_ROOT;
    $smarty->cache_dir = SMARTY_TPL_CACHE_ROOT;
    $smarty->use_sub_dirs = TRUE;
	$smarty->template_dir = SERVER_ROOT;
    $smarty->debugging = SMARTY_DEBUG;

    if (!SMARTY_CACHING)
    {
        $smarty->clearAllCache();  // clear the entire cache
        $smarty->cache_lifetime = 0; //in seconds, 0 generate everytime
    }

    $smarty->caching = 2; // lifetime is per cache

    return $smarty;
}
