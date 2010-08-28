<?php

/**
 * This interface defines which methods a very simple DB handler should look
 * like. This handler are used to give the DB handler an interface to query
 * against.
 */
interface IDBHandler {
    public function Connect($host, $user, $password);

    public function Query($query);

    public function Close();
}

/**
 * The DB helper encapsulates methods for create simple secure querys.
 */
class DatabaseHelper {
    /**
     * @var $DBHandler Contains the DB handler object which is used for queries.
     */
    protected $DBHandler;

    /**
     * Initializes the DatabaseHelper object with the given DB handler. If the
     * parameter isn't a IDBHandler type the constructer throws an exception.
     */
    public function  __construct($dbHandler) {
        if(!($dbHandler instanceof IDBHandler))
            throw new WrongTypeException ("DatabaseHelper needs a IDBHandler object!");

        $this->DBHandler = $dbHandler;
    }

    /**
     * Creates an query with the given query string and the parameters. The parameters
     * get quoted and if there are strings bordered with ' (The parameters get escaped
     * with the mysql_real_escape function)
     * @return string query
     */
    public function GetSelectQuery($query, $params) {
        foreach($params as $key => $value) {
            if($this->IsValidParameter($key))
                throw new InvalidArgsException("The parameter of the query wasn't proper formatted. The name of the key must start with an @ and have more than one char.");

            $query = str_replace($query, $key, $this->FormatParameter($value));
        }
    }

    /**
     * Checks if the given parameter is a string longer then 1 char and starts
     * with an @.
     * @return boolean
     */
    private function IsValidParameter($parameter) {
        if($parameter === null)
            return false;

        if(strlen($parameter) > 1)
            return false;

        if($parameter{0} != '@')
            return false;

        return true;
    }

    /**
     * Checks if the given value is a string and if it is so border it with ticks (')
     * It additionally escapes it with mysql_real_escape.
     */
    private function FormatParameter($value) {
        if(strcmp(gettype($value), "string")) {
            $value = mysql_real_escape_string($value);
            return "'{$value}'";
        }
        else {
            return mysql_real_escape_string($value);
        }
    }
}
?>
