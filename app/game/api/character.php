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
    }

    private function _validate()
    {
        $ok = TRUE;

        // if (($this->race !== NULL) &&
        //     !$this->

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
