<?php

require_once dirname(__FILE__) . '/../core/main.inc.php';

/**
 * This class encapsulates the simple connect, disconnect and query
 * functionalty of the mysql class.
 */
class MysqlDBHandler implements IDBHandler {

    /**
     * @var mysql_link Contains the link to the DB server.
     */
    protected $DB;

    /**
     * Initializes the MysqlDBHandler object. It also is able to create connect
     * automatically to the DB. The parameters are not persisted, there just used
     * to connect to the server.
     * @param string The host address to the DB server
     * @param string The user name
     * @param string The password of the DB user.
     */
    public function __construct($host = null, $user = null, $password = null) {
        if($host != null && $user != null && $password != null) {
            $this->Connect($host, $user, $password);
        }
    }

    /**
     * Connects to the given server address with the given credentials. If an
     * error occours it throws and DBException.
     * @param string The host address to the DB server
     * @param string The user name
     * @param string The password of the DB user.
     */
    public function Connect($host, $user, $password) {
        $this->DB = mysql_connect ($host, $user, $password);

        if(!$this->DB) {

            $errorMsg = mysql_error();
            throw new DBException("The DB connection failed! \n {$errorMsg}", mysql_errno());
        }
    }

    /**
     * Queries against the connected DB and returns the result set.
     * @param string The DB query
     * @return mixed The result set of the query.
     */
    public function Query($query) {
        return mysql_query($query, $this->DB);
    }

    /**
     * Closes the current DB connection if it is still alive.
     * @return void
     */
    public function  Close() {
        if($this->DB) {
            mysql_close($this->DB);
        }
    }

    /**
     * Disposes this object. If the DB link hasn't been disconnect, the
     * destructor disconnects.
     */
    public function  __destruct() {
        $this->Close();
    }
}
?>
