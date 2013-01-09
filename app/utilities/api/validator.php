<?
class validator
{
    private $errors;

    function __construct()
    {
        $this->errors = array();
    }

    function __call($f, $args)
    {
        return $this->_validate($args, $f);
    }

    private function _validate($input, $ruleset)
    {
        switch($ruleset)
        {
        case 'username':
            require_once SERVER_ROOT . '/user/api/constants.php';

            $l = strlen($input);

            if (!is_string($input)
                && ($l > 0)
                && ($l <= USER_MAX_USERNAME_LENGTH)
                && preg_match('/^[0-9a-zA-Z_]+$/', $input);

            break;
        }
    }
}
?>
