DROP FUNCTION IF EXISTS diminishing_returns;

DELIMITER $

CREATE FUNCTION diminishing_returns
(
    p_val       TINYINT(1) UNSIGNED,
    p_scale     TINYINT(1) UNSIGNED
)
RETURNS FLOAT UNSIGNED
BEGIN
    DECLARE v_mult      FLOAT UNSIGNED;
    DECLARE v_trinum    FLOAT UNSIGNED;

    SET v_mult := p_val / p_scale;

    SET v_trinum := (SQRT(8 * v_mult + 1) - 1) / 2;

    RETURN v_trinum;
END$

DELIMITER ;
