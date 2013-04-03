<?php
require_once preg_replace("/(.*\/Grimoire-Planner\/).*/", "$1", __FILE__) . 'app/config.php';
require_once SERVER_ROOT . '/character/api/character.php';

$c = new character();
?>
