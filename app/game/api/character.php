<?php
require_once SERVER_ROOT . 'game/api/constants.php';
require_once SERVER_ROOT . 'utilities/api/array.php';

class character
{
    private
        $db,                    // utility class instances

        $race,                  // config vars (although race may be NULL)
        $race_id,
        $stat_priorities,

        $race_job_id,           // operation vars
        $job,
        $job_id,
        $level,
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

        $this->_initialize();

        if ($race !== NULL)
        {
            $this->race = $race;
            $this->race_id = $this->race_map[$race];
        }


        $this->stat_priorities = $stat_priorities;

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

    public function display()
    {
        print "------------------------------\n";
        print "Level " . $this->level . " " . $this->race . " " . $this->job . "\n";
        print "HP   " . $this->HP . "\n";
        print "MP   " . $this->MP . "\n";
        print "Spd  " . $this->Spd . "\n";
        print "Atk  " . $this->Atk . "\n";
        print "Def  " . $this->Def . "\n";
        print "Mag  " . $this->Mag . "\n";
        print "Res  " . $this->Res . "\n";
        print "------------------------------\n";
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

        if (!$this->_choose_initial_job())
            $this->_err("Did not find a job, restrictions must be too tight");

        // ok now that we have chosen our job and potentially our race, we
        // need to go through the iterative process of leveling up that many
        // times to actually incur the randomness
        for ($i = 0; $i < ($level - 1); $i++)
            $this->_level();
    }

    /**
     * build query to analyze job stats and what not based on stat priorities
     *
     * Ok I think this query (and the code to assemble it) has reached
     * sufficient complexity to warrant an example of the complete query and
     * what it does so it's easier to follow construction below. There comes a
     * point where I believe it is best to transition to query "templates"
     * included whole for different sets of circumstances, but we're not there
     * yet, this isn't that hard, the code just looks messy but the final
     * query looks beautiful!
     *
     * Below is an example "roll query" for a Bangaa scrapper AKA melee DPS.
     * For this build the important stats in priority order are Atk, Spd and
     * Def.
     *
     * The innermost query (starting at 3.) is to find each job available to
     * the specified race (in this case), or just every possible job in the
     * case of no race being specificed, and their "ratios" (thus the r_
     * prefix) for each stat that has been input as a priority. The ratio is
     * the average growth in that stat for that job compared to the amount of
     * growth that is required, given that particular job's starting amount in
     * that stat, to max out that stat at level 99 if all levels were taken in
     * this job. If the ratio is greater than 1, it is theoretically possible
     * (barring terrible luck on the random variation) for that job to max
     * that stat. I believe these to be the most important factors in
     * determining the ideal initial job. One thing to note is that Speed is a
     * special attribute with special rules, and since it is capped at 150
     * instead of 250 it is weighted by that ratio (5/3).
     * -------------------------------------------------------------------------
     * The middle query (starting at 2.) is to pick the best job out of all of
     * these jobs that we have determined are available to our character in
     * the inner query. There are 3 steps of ordering, the top two of which
     * are binary on/off filters basically.
     *
     * - At 4. there is an "and" of floors for all priority stat ratios. As I
     *   said earlier, if a ratio is above 1, that job should be able to max
     *   that stat. So if all desired stats can be maxed by this job, there is
     *   basically no reason to find another job (unless there are several
     *   that can do that - this is exceedingly rare). So any jobs that can
     *   max all desired stats should automatically outrank any jobs that
     *   cannot.
     *
     * - At 5. we take the floor of the #1 priority stat and put jobs that can
     *   at least max that stat automatically above jobs that cannot. If you
     *   don't end up with the stat you want the most maxed out, that seems
     *   like kind of a fail. On the other hand, if you are going for some
     *   weird build where your race cannot max your primary stat, you still
     *   want to get a job back and not have the query come back empty, which
     *   is why it is not in the WHERE clause
     *
     * - At 6. we have the sum of all weighted ratios for priority stats. This
     *   is basically the total payload of stats that you care about, so
     *   within the list of jobs that are on the highest "tier" in terms of
     *   being able to max out all priority stats/the top priority stat/no
     *   priority stats, let's get the one that will net us the most bang for
     *   our buck in the areas that we care about. I don't think it's
     *   appropriate to calculate the tiers with weighted ratios, so the
     *   weighting only comes into play here where we are sorting between jobs
     *   within the same tier. Coefficients could change based on test data
     *   but for now I am detracting 5% from the ratio weight for each step
     *   down in the priority chain.
     * -------------------------------------------------------------------------
     * The outer query (starting at 1.) is to actually "hydrate" our
     * character's stats. It takes the final winner of the race/job contest
     * that we have decided is best in the middle query, and actually joins
     * out to the stats tables to get all of the stats (not just priority
     * stats) to find out exactly where we will be in terms of statistical
     * data when we simulate this character being created at level 1 as this
     * race doing this job.
     *
     * 1. SELECT v2.race_job_id
     *         , v2.race_id
     *         , v2.race
     *         , v2.job_id
     *         , v2.job
     *         , s.name stat
     *         , rjs.initial
     * 2.   FROM (SELECT v.race_job_id
     *                 , v.race_id
     *                 , v.race
     *                 , v.job_id
     *                 , v.job
     *                 , (v.r_Atk + (v.r_Spd * 0.95) + (v.r_Def * 0.9)) r_prio
     * 3.           FROM (SELECT rj.id race_job_id
     *                         , rj.race_id
     *                         , r.name race
     *                         , rj.job_id
     *                         , j.name job
     *                         , (rjs_Atk.growth /
     *                            ((999 - rjs_Atk.initial) / 98)) r_Atk
     *                         , (rjs_Spd.growth /
     *                            ((150 - rjs_Spd.initial) / 98) * 5 / 3) r_Spd
     *                         , (rjs_Def.growth /
     *                            ((999 - rjs_Def.initial) / 98)) r_Def
     *                      FROM race_job rj
     *                      JOIN race r
     *                        ON rj.race_id = r.id
     *                      JOIN job j
     *                        ON rj.job_id = j.id
     *                      JOIN race_job_stat rjs_Atk
     *                        ON rj.id = rjs_Atk.race_job_id
     *                      JOIN race_job_stat rjs_Spd
     *                        ON rj.id = rjs_Spd.race_job_id
     *                      JOIN race_job_stat rjs_Def
     *                        ON rj.id = rjs_Def.race_job_id
     *                     WHERE rjs_Atk.stat_id = 4
     *                       AND rjs_Spd.stat_id = 3
     *                       AND rjs_Def.stat_id = 5
     *                       AND rj.race_id = 1) v
     * 4.       ORDER BY (FLOOR(r_Atk) && FLOOR(r_Spd) && FLOOR(r_Def)) DESC
     * 5.              , FLOOR(r_Atk) DESC
     * 6.              , r_prio DESC
     *             LIMIT 1) v2
     *      JOIN race_job_stat rjs
     *        ON v2.race_job_id = rjs.race_job_id
     *      JOIN stat s
     *        ON rjs.stat_id = s.id;
     **/
    private function _choose_initial_job()
    {
        $sql = array();

        // set up static parts of query before we iterate over stat priorities
        $this->_build_initial_job_sql_static($sql);

        $params = array();

        // build dynamic query parts
        $i = 0; // counter for ANDs and +'s and stuff
        foreach ($this->stat_priorities as $priority => $stat)
        {
            // need this later
            if (!$i)
                $top_priority_stat = $stat;

            // there are a bunch of special rules for speed
            $is_Spd = ($this->stat_map[$stat] == SPD);

            $ratio_segment = "v.r_" . $stat;

            if ($i)
            {
                // weighting
                $ratio_segment = ("(" . $ratio_segment
                                  . " * " . (1 - (0.05 * $i)) . ")");
            }

            $sql['middle_select'] .= (($i ? " + " : "") . $ratio_segment);

            $sql['inner_select'] .= ("
                                     , (rjs_" . $stat . ".growth /
                                        ((" . ($is_Spd ? "150" : "999")
                                  . " - rjs_" . $stat . ".initial) / 98)"
                                  . ($is_Spd ? " * 5 / 3" : "")
                                  . ") r_" . $stat);

            $sql['inner_from'] .= "
                                  JOIN race_job_stat rjs_" . $stat . "
                                    ON rj.id = rjs_" . $stat . ".race_job_id";

            $sql['inner_where'] .= (($i ? "
                                   AND " : "") . "rjs_" . $stat . ".stat_id = :" . $stat);

            $params[$stat] = $this->stat_map[$stat];

            $sql['middle_end'] .= (($i ? " && " : "") . "FLOOR(r_" . $stat . ")");

            $i++;
        }

        // wrap up parens and aliases and stuff
        $sql['middle_select'] .= ") r_prio
                          FROM (";

        if ($this->race)
        {
            $sql['inner_where'] .= "
                                   AND rj.race_id = :race";

            $params['race'] = $this->race_id;
        }

        $sql['middle_end'] .= ") DESC
                             , FLOOR(r_" . $top_priority_stat . ") DESC
                             , r_prio DESC
                         LIMIT 1";

        // bring it together
        $sql_final = $sql['outer_start']
                   . $sql['middle_select']
                   . $sql['inner_select']
                   . $sql['inner_from']
                   . $sql['inner_where']
                   . $sql['middle_end']
                   . $sql['outer_end'];

        if ($rows = $this->db->select($sql_final, $params))
        {
            foreach ($rows as $row)
                $this->{$row['stat']} = $row['initial'];

            $this->race_job_id = $row['race_job_id'];

            if ($this->race)
            {
                $this->race    = $row['race'];
                $this->race_id = $row['race_id'];
            }

            $this->job    = $row['job'];
            $this->job_id = $row['job_id'];
            $this->level  = 1;

            return TRUE;
        }

        return FALSE;
    }

    private function _build_initial_job_sql_static(&$sql)
    {
        $sql['outer_start'] = "
                SELECT v2.race_job_id
                     , v2.race_id
                     , v2.race
                     , v2.job_id
                     , v2.job
                     , s.name stat
                     , rjs.initial
                  FROM (";

        $sql['middle_select'] = "SELECT v.race_job_id
                             , v.race_id
                             , v.race
                             , v.job_id
                             , v.job
                             , (";

        $sql['inner_select'] = "SELECT rj.id race_job_id
                                     , rj.race_id
                                     , r.name race
                                     , rj.job_id
                                     , j.name job";

        $sql['inner_from'] = "
                                  FROM race_job rj
                                  JOIN race r
                                    ON rj.race_id = r.id
                                  JOIN job j
                                    ON rj.job_id = j.id";

        $sql['inner_where'] = "
                                 WHERE ";

        $sql['middle_end'] = ") v
                      ORDER BY (";

        $sql['outer_end'] = ") v2
                  JOIN race_job_stat rjs
                    ON v2.race_job_id = rjs.race_job_id
                  JOIN stat s
                    ON rjs.stat_id = s.id";
    }

    /**
     * Leveling is basically just growing each stat by the appropriate amount
     * for the current job and then incrementing level.
     **/
    private function _level()
    {
        // so let's get our growth
        $sql = "
                SELECT s.name stat
                     , rjs.growth
                  FROM race_job_stat rjs
                  JOIN stat s
                    ON rjs.stat_id = s.id
                 WHERE rjs.race_job_id = :rj_id";

        $stats = array_make_dictionary(
            $this->db->select($sql,
                              array('rj_id' => $this->race_job_id)),
            'stat',
            'growth');

        foreach ($stats as $stat => $growth)
            $this->_level_stat($stat, $growth);

        $this->level++;
    }

    /**
     * once again, different rules for speed... speed growth is given as a %
     * chance to gain 1 point of speed, it is not guaranteed like the other
     * stats
     *
     * for all other stats, you add the growth to your current stat value, and
     * then apply the random variance, which works as follows:
     *
     * 1/3 chance: no variance
     * 1/3 chance: positive variance
     * 1/3 chance: negative variance
     *
     * where if variance is applied in either direction, it is equal to:
     *
     * floor((growth + 9) / 10)
     **/
    private function _level_stat($stat, $growth)
    {
        if ($this->stat_map[$stat] == SPD)
        {
            $this->Spd += ((100 * $growth) >= mt_rand(1, 100)) ? 1 : 0;
        }
        else
        {
            $this->{$stat} += $growth + $this->_get_variance($growth);
        }
    }

    private function _get_variance($growth)
    {
        $variance = floor(($growth + 9) / 10);

        switch(mt_rand(1, 3))
        {
        case 1:
            return $variance;
        case 2:
            return (0 - $variance);
        case 3:
            return 0;
        }
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
