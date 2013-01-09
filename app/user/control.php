<?
function register()
{
    global $smarty;

    // submitting
    if (isset($_POST['register_submit']) && _validate_registration())
    {

    }

    $smarty->display('user/view/register.tpl');
}

function _validate_registration()
{
    require_once SERVER_ROOT . '/utilities/api/validator.php';

    global $_POST;

    $v = new validator();
}
?>
