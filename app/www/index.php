<?php
require_once '../config.php';
require_once SERVER_ROOT . 'utilities/api/core.php';
require_once SERVER_ROOT . 'utilities/api/smarty.php';

// login stuff
define('USERID',   FALSE);
define('USERNAME', FALSE);

$smarty = initialize_smarty();

$control = isset($_GET['control']) ? $_GET['control'] : 'home';

$file = SERVER_ROOT . $control . '/control.php';

if (file_exists($file))
{
    require_once $file;

    if (isset($_GET['control_function']) &&
        function_exists($_GET['control_function']))
    {
        call_user_func($_GET['control_function']);
    }
}
else
    header_code(404);

?>
