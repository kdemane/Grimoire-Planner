<?

class db
{
    // Handles
    private $dbh;
    private $sth;

    // Connection info
    private $user;
    private $password;
    private $schema;
    private $host;

    // Configuration
    private $persist;

    public function __construct($host, $schema, $user, $password, $persist = TRUE)
    {
        $this->host     = $host;
        $this->schema   = $schema;
        $this->user     = $user;
        $this->password = $password;

        $this->persist = $persist;

        $this->connect();
    }

    function connect()
    {
        $mods = $this->persist ? array(PDO::ATTR_PERSISTENT => TRUE) : NULL;

        try
        {
            $this->dbh = new PDO('mysql:host=' . $this->host . '; dbname=' . $this->schema,
                                 $this->user,
                                 $this->password,
                                 $mods);
        }
        catch (Exception $e)
        {
            $conn_info = 'mysql: host= ' . $this->host .
                ' dbname= ' . $this->schema .
                ' user= ' . $this->user .
                ' pass= ' . $this->password .
                ' mods= ' . print_r($mods, TRUE);

            print "\nUnable to connect to the database: \n";
            print $conn_info . "\n\n";

            throw new Exception("Unable to connect to the database");
        }
    }

    function select($sql,
                    $params = NULL,
                    $return_array_of_ids = FALSE)
    {
        $this->errors = NULL;

        $limit = $this->check_params($params);

        if (isset($limit["count"]) &&
            isset($limit["start"]))
            $sql .= " " . make_limit_sql(array($limit["start"], $limit["count"]));
        elseif (isset($limit["count"]))
            $sql .= " " . make_limit_sql($limit["count"]);

        if ($result = $this->prepare($sql, $params) &&
            $result = $this->execute($sql, $params))
        {
        	if ($return_array_of_ids)
            {
	        	while ($row = $this->sth->fetch(PDO::FETCH_ASSOC))
	            	$rows[] = $row["id"]; // Rreturns an array of IDs only instead of a multi-dimensional array
        	}
            else
            {
        		while ($row = $this->sth->fetch(PDO::FETCH_ASSOC))
	            	$rows[] = $row;
        	}

            if (!(isset($rows) && is_array($rows)))
                $rows = array();
        }
        elseif (isset($result) &&
                !$result)
        {
            if ($this->_prepare_retry_query())
                return $this->select($sql, $params, $return_array_of_ids);

            $this->err(array($sql, $params));
            return FALSE;
        }

        if (isset($rows) &&
            !is_array($rows))
        {
            $this->err(array($sql, $params, "returned result was not an array"));
            return FALSE;
        }

        $this->num_rows = @count($rows);

        if (count(@$rows) == 0)
        {
            unset($this->sth);
            return NULL;
        }

        unset($this->sth);
        return $rows;
    }

    function select_one($sql, $params = NULL)
    {
        unset($this->sth);

        $row = $this->fetch($sql, $params);

        unset($this->sth);
        return $row;
    }

    function query($sql, $params = null, $return_num_affected_rows = FALSE)
    {
        $this->errors = NULL;

        if (!$this->prepare($sql, $params))
            return FALSE;

        if (is_scalar($params))
            $params = array($params);

        if (!$this->execute($sql, $params))
        {
            if ($this->_prepare_retry_query())
                return $this->query($sql, $params, $return_num_affected_rows);

            $this->err(array($sql, $params));
            return FALSE;
        }

        if ($return_num_affected_rows === TRUE)
        {
            $num_affected_rows = $this->sth->rowCount();
            unset($this->sth);
            return $num_affected_rows;
        }

        unset($this->sth);
        return TRUE;
    }

    function fetch($sql,
                   $params = null,
                   $return_sth = FALSE)
    {
        if (is_object($sql))
            $this->sth = $sql;

        if (!isset($this->sth))
        {
            if (!$this->prepare($sql, $params))
                return FALSE;

            $this->check_params($params);

            if (!$this->execute($sql, $params))
            {
                if ($this->_prepare_retry_query())
                    return $this->fetch($sql, $params, $return_sth);

                $this->err(array($sql, $params));
                return FALSE;
            }
        }

        if ($return_sth === TRUE)
            return $this->sth;

        if ($row = $this->sth->fetch(PDO::FETCH_ASSOC))
        {
            if (!is_array($row))
            {
                $this->err(array($sql, $params, "returned result was not an array"));
                return FALSE;
            }

            return $row;
        }
        else
        {
            unset($this->sth);

            return NULL;
        }
    }

    private function _prepare_retry_query()
    {
        $error_info = $this->sth->errorInfo();

        if (!isset($error_info[1]) ||
            $this->num_connects > 2 ||
            $error_info[1] != self::ERROR_MYSQL_GONE_AWAY)
        {
            return FALSE;
        }

        try
        {
            $this->sth = NULL;
            $this->dbh = NULL;
            $this->connect();
            return TRUE;
        }
        catch(Exception $e)
        {
            return FALSE;
        }
    }

    private function prepare(&$sql, &$params)
    {
        $sql = trim($sql);

        $this->errors = NULL;

        if (!$this->sth = $this->dbh->prepare($sql))
            return FALSE;

        return TRUE;
    }

    private function execute(&$sql, &$params)
    {
        $result = $this->sth->execute($params);
        return $result;
    }

    private function err($error)
    {
        $this->errors["error"] = $error;

        if (@is_object($this->sth))
        {
            $sth_errors = $this->sth->errorInfo();

            if (is_scalar($sth_errors))
            {
                $this->errors["sql_error"] = $sth_errors;
            }
            elseif (is_array($sth_errors))
            {
                foreach ($sth_errors as $e)
                    $this->errors["sql_error"] = $e;
            }
        }
        else
        {
            $exception_error = $this->dbh->errorInfo();

            if (is_scalar($exception_error))
            {
                $this->errors["sql_error"] = $exception_error;
            }
            elseif (is_array($exception_error))
            {
                foreach ($exception_error as $e)
                    $this->errors["sql_error"] = $e;
            }
        }

        $this->errors["trace"]       = $this->draw_debug_trace(debug_backtrace());
        $this->errors["request_uri"] = @$_SERVER["REQUEST_URI"];
        $this->errors["db_host"]     = $this->host;
        $this->errors["user_agent"]  = @$_SERVER["HTTP_USER_AGENT"];

        print(print_r($this->errors, TRUE) . "\n\n" . $this->debug . "\n\n" . $this->db_log_file);

        return TRUE;
    }

    function draw_debug_trace($trace, $depth = 3)
    {
        if (count($trace) < $depth)
            $depth = count($trace);

        $pre = "";
        $trace_text = "";
        for(;$depth > 0; $depth--)
        {
            $trace_text .= $pre . @$trace[$depth]["file"] . '[' . @$trace[$depth]["line"] . '] : ' . @$trace[$depth]["function"];
            $pre = "\n            => ";
        }

        return $trace_text;
    }

    private function check_params(&$params)
    {
        if (is_scalar($params))
            $params = array($params);

        $limit = NULL;

        if (isset($params["start"]))
        {
            $limit["start"] = $params["start"];
            unset($params["start"]);
        }

        if (isset($params["count"]))
        {
            $limit["count"] = $params["count"];
            unset($params["count"]);
        }

        return $limit;
    }
}
