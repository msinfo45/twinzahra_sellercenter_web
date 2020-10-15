<?php

class Model_Core
{
    private $conn;

// constructor
    function __construct()
    {
        include "db_connection.php";
        include "config_type.php";
        $this->conn = $conn;
    }

// destructor
    function __destruct()
    {

    }

    public function getBaseURL() {

        $query = $this->conn->query("
                                    SELECT 
                                    baseurl 
                                    FROM 
                                    site_settings
                                     WHERE active = 1"
        );

        if (mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return false;
        }

    }

}
?>