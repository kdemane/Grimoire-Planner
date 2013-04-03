<?php
require_once SERVER_ROOT . 'game/api/constants.php';
require_once SERVER_ROOT . 'utilities/api/array.php';

class character
{
    private
        $db,                    // utility class instances

        $race,                  // config vars (although race may be NULL)
        $stat_priorities,

        $level,                 // operation vars
        $HP,
        $MP,
        $Spd,
        $Atk,
        $Def,
        $Mag,
        $Res,

        $race_map,              // ref maps
        $stat_map;

    /**
     * I want to allow for this class to be instantiated in the general use
     * case (race, stats), but I don't want it to be annoying for cases where
     * you are getting the next level for a guy already in progress, as that
     * should be:
     *
     * $c = new character();
     * $c->set_level(15);
     * etc
     **/
    public function __construct($race = NULL, $stat_priorities = NULL)
    {
        global $db;

        $this->db = $db;

        $this->race = $race;
        $this->stat_priorities = $stat_priorities;

        ksort($this->stat_priorities);

        $this->_initialize();
    }

    private function _initialize()
    {
        $this->_init_ref_data();
    }

    private function _init_ref_data()
    {
        $this->race_map =
            array_make_dictionary(
                $this->db->select("SELECT name, id FROM race"),
                'name',
                'id');

        $this->stat_map =
            array_make_dictionary(
                $this->db->select("SELECT name, id FROM stat"),
                'name',
                'id');
    }

    /**
     * Start the character. In most cases, for the ability to "cheese" rolls
     * many times at one moment, characters are recruited at level 30. I will
     * assume this to be the case, although it is possible to recruit at lower levels
     *
     * Any currently set ops vars are getting thrown out in here
     **/
    public function roll($level = 30)
    {
        if (!$this->_validate())
            return FALSE;

        $this->_clear();

        $this->_choose_initial_job();
    }

    // build query to analyze job stats and what not based on stat priorities
    private function _choose_initial_job()
    {
        $params = array();

        // set up static parts of query before we iterate over stat priorities

        // all this weird spacing is so it looks nice when it gets spat out
        // for debugging/errors
        $sql_outer_select = "
                SELECT v.id rj_id
                     , (";

        $sql_select = "SELECT rj.id";

        $sql_from = "
                          FROM race_job rj";

        $sql_where = "
                         WHERE ";

        $sql_outer_order = ") v
              ORDER BY (";

        $i = 0; // counter for ANDs and &&'s and stuff
        foreach ($this->stat_priorities as $priority => $stat)
        {
            if (!$i)
                $top_priority_stat = $stat;

            // there are a bunch of special rules for speed
            $is_Spd = ($this->stat_map[$stat] == SPD);

            $sql_outer_select .= (($i ? " + " : "") . "v.r_" . $stat);

            $sql_select_addition = ("(rjs_" . $stat . ".growth / (("
                            . ($is_Spd ? "150" : "999")
                            . " - rjs_" . $stat . ".initial) / 98)");

            // speed is weighted due to it's being capped at 150 instead of
            // "250" (999 is the real cap but for calculations in the game
            // this is quartered and rounded down)
            if ($is_Spd)
                $sql_select_addition = ("((" . $sql_select_addition . " * 5) / 3)");

            $sql_select .= ("
                             , " . $sql_select_addition . ") r_" . $stat);

            $sql_from .= "
                          JOIN race_job_stat rjs_" . $stat . "
                            ON rj.id = rjs_" . $stat . ".race_job_id";

            $sql_where .= (($i ? "
                           AND " : "") . "rjs_" . $stat . ".stat_id = :" . $stat);

            $params[$stat] = $this->stat_map[$stat];

            $sql_outer_order .= (($i ? " && " : "") . "FLOOR(r_" . $stat . ")");

            $i++;
        }

        $sql_outer_select .= ") r_prio
                  FROM (";

        if ($this->race)
        {
            $sql_where .= "
                           AND rj.race_id = :race";

            $params['race'] = $this->race_map[$this->race];
        }

        // wrap up parens and aliases and stuff
        $sql_outer_order .= ") DESC
                     , FLOOR(r_" . $top_priority_stat . ") DESC
                     , r_prio DESC";

        // pull it together
        $sql = $sql_outer_select
             . $sql_select
             . $sql_from
             . $sql_where
             . $sql_outer_order;

        die($sql . "\n\n");
    }

    private function _validate()
    {
        $ok = TRUE;

        if (($this->race !== NULL) &&
            !isset($this->race_map[$this->race]))
        {
            $this->_err('Race "' . $this->race . "\" not found.\n");
            $ok = FALSE;
        }

        if (!$this->_validate_stat_priorities())
            $ok = FALSE;

        return $ok;
    }

    private function _validate_stat_priorities()
    {
        if (!is_array($this->stat_priorities))
            return false;

        $priority_sentinel = -1;

        foreach ($this->stat_priorities as $priority => $stat)
        {
            if ($priority < $priority_sentinel)
            {
                $this->_err(
                    "To ensure predicted results, stats must be in ascending priority order\n");

                return FALSE;
            }

            if (!$this->_validate_stat($stat))
                return FALSE;

            $priority_sentinel = $priority;
        }

        // if everything is ok and Speed is not present, add it
        if (!in_array('Spd', $this->stat_priorities))
            $this->stat_priorities[$priority + 1] = 'Spd';

        return TRUE;
    }

    // this is almost definitely going to have to get moved somewhere else
    // once other modules have to do it...
    private function _validate_stat($name)
    {
        return isset($this->stat_map[$name]);
    }

    private function _clear()
    {
        $this->level =
        $this->HP    =
        $this->MP    =
        $this->Spd   =
        $this->Atk   =
        $this->Def   =
        $this->Mag   =
        $this->Res   = NULL;
    }

    private function _err($error)
    {
        print $error;
    }
}

?>
