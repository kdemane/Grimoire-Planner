<?
function validate_username($username)
{
    require_once SERVER_ROOT . 'user/api/constants.php';

    $l = strlen($username);

    return is_string($username)
        && ($l > 0)
        && ($l <= USER_MAX_USERNAME_LENGTH)
        && preg_match('/^[0-9a-zA-Z_]+$/', $username);
}
?>
