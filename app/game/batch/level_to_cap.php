<?php
require_once preg_replace("/(.*\/Grimoire\-Planner\/).*/", "$1", __FILE__) . 'app/config.php';
require_once SERVER_ROOT . 'game/api/character.php';

// random stuff to start testing...
// going for a bangaa scrapper
$race = 'Bangaa';

$stat_priorities = array(0 => 'Atk',
                         1 => 'Spd',
                         2 => 'Def');

$c = new character($race, $stat_priorities);

$c->make();
?>
