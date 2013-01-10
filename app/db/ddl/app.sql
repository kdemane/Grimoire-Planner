DROP TABLE IF EXISTS character_level;
DROP TABLE IF EXISTS character;
DROP TABLE IF EXISTS clan_note;
DROP TABLE IF EXISTS clan;
DROP TABLE IF EXISTS user;

CREATE TABLE user (
    id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(64) NOT NULL,
    password VARCHAR(32),
    email VARCHAR(100),
    date_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE clan (
    id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INTEGER UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    date_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT clan_user_fk FOREIGN KEY (user_id) REFERENCES user (id)
) ENGINE=InnoDB;

CREATE TABLE clan_note (
    id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INTEGER UNSIGNED NOT NULL,
    clan_id INTEGER UNSIGNED NOT NULL,
    date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    note TEXT NOT NULL,
    CONSTRAINT clan_note_user_fk FOREIGN KEY (user_id) REFERENCES user (id),
    CONSTRAINT clan_note_clan_fk FOREIGN KEY (clan_id) REFERENCES clan (id)
) ENGINE=InnoDB;

CREATE TABLE char (
    id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
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
    CONSTRAINT character_race_fk FOREIGN KEY (race_id) REFERENCES race (id)
) ENGINE=InnoDB;

CREATE TABLE guy_level (
    guy_id INTEGER UNSIGNED NOT NULL,
    level TINYINT(1) UNSIGNED NOT NULL,
    job_id TINYINT(1) UNSIGNED NOT NULL,
    CONSTRAINT character_level_character_fk FOREIGN KEY (character_id) REFERENCES character (id),
    CONSTRAINT character_level_job_fk FOREIGN KEY (job_id) REFERENCES job (id)
) ENGINE=InnoDB;