-- I would have called this and affiliated tables "character" but it's a
-- reserved word and the backticks are freaking annoying in the MySQL monitor
-- Just going to have to deal with that code/data naming schism : /

DROP TABLE IF EXISTS toon_level;
DROP TABLE IF EXISTS toon;

CREATE TABLE toon (
    id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    clan_id INTEGER UNSIGNED NOT NULL,
    race_id TINYINT(1) UNSIGNED NOT NULL,
    name VARCHAR(64) NOT NULL,
    description TEXT,
    level TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    HP TINYINT(1) UNSIGNED NOT NULL,
    MP TINYINT(1) UNSIGNED NOT NULL,
    Spd TINYINT(1) UNSIGNED NOT NULL,
    Atk TINYINT(1) UNSIGNED NOT NULL,
    Def TINYINT(1) UNSIGNED NOT NULL,
    Mag TINYINT(1) UNSIGNED NOT NULL,
    Res TINYINT(1) UNSIGNED NOT NULL,
    CONSTRAINT toon_clan_fk FOREIGN KEY (clan_id) REFERENCES clan (id),
    CONSTRAINT toon_race_fk FOREIGN KEY (race_id) REFERENCES race (id)
) ENGINE=InnoDB;

CREATE TABLE toon_level (
    toon_id INTEGER UNSIGNED NOT NULL,
    level TINYINT(1) UNSIGNED NOT NULL,
    job_id TINYINT(1) UNSIGNED NOT NULL,
    CONSTRAINT toon_level_character_fk FOREIGN KEY (toon_id) REFERENCES toon (id),
    CONSTRAINT toon_level_job_fk FOREIGN KEY (job_id) REFERENCES job (id)
) ENGINE=InnoDB;
