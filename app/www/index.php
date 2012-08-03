<?php
require_once '../config.php';
require_once SERVER_ROOT . 'utilities/api/database.php';
require_once SERVER_ROOT . 'utilities/api/smarty.php';

$db = new db(DB_HOST, DB_SCHEMA, DB_USER, DB_PASSWORD);

$smarty = initialize_smarty();
?>
