<?php

function array_map_by_first($array, $keys_are_unique = TRUE, $preserve_first_value = FALSE)
{
    if (!is_array($array) || empty($array))
    {
        return $array;
    }

    if (key($tmp = reset($array)) === 0) // array
    {
        foreach ($array as $row)
        {
            _map_by_first_array($map, $row, $keys_are_unique, $preserve_first_value);
        }
    }
    else // hash
    {
        foreach ($array as $row)
        {
            _map_by_first_assoc($map, $row, $keys_are_unique, $preserve_first_value);
        }
    }

    return $map;
}

function _map_by_first_array(&$map, $row, $keys_are_unique, $preserve_first_value)
{
    if ($preserve_first_value)
        $key = $row[array_shift(array_keys($row))];
    else
        $key = array_shift($row);

    if ($keys_are_unique)
        $map[$key]   = count($row) == 1 ? $row[0] : $row;
    else
        $map[$key][] = count($row) == 1 ? $row[0] : $row;
}

function _map_by_first_assoc(&$map, $row, $keys_are_unique, $preserve_first_value)
{
    if ($preserve_first_value)
        $key = $row[array_shift(array_keys($row))];
    else
        $key = array_shift($row);

    if ($keys_are_unique)
        $map[$key]   = $row;
    else
        $map[$key][] = $row;
}

function array_make_dictionary(&$input_array, $key_name = "id", $value_name = "name", $append_enum_round_robin = NULL, $trim_values = TRUE)
{
    if(!is_array($input_array))
        return NULL;

    $return_array = array();

    if($append_enum_round_robin)
    {
        $count = 1;
        $num_enums = count($append_enum_round_robin);
    }

    foreach ($input_array as $row)
    {
        $key = $trim_values ? trim($row[$key_name]) : $row[$key_name];

        $return_array[$key] = trim($row[$value_name] . (isset($num_enums)
                                                        ? $append_enum_round_robin[$count % $num_enums]
                                                        : ""));

        if(isset($count))
            $count ++;
    }

    return $return_array;
}

?>
