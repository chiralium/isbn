<?php

/**
 * Class DB
 */
class DB {
    public $connection;

    /**
     * DB constructor.
     * @param $host
     * @param $lgn
     * @param $pwd
     */
        function __construct($host, $lgn, $pwd) {
            $this->connection = new mysqli($host, $lgn, $pwd);
            if ($this->connection->connect_error) die("Connection to the database failed");
        }

    /**
     * @param $query
     * @return array
     * @description performing query and return all of fetched objects
     */
        function execute_query($query) {
            $rows = array();
            if ($result = $this->connection->query($query)) {
                while ($row = $result->fetch_object()) $rows[] = $row;
                $result->close();
            }
            return $rows;
        }
}