<?php

class Model_Config
{

    private $conn;

    // constructor
    function __construct()
    {
        include "config/db_connection.php";
       include "../config/config_type.php";
        $this->conn = $conn;

    }

    // destructor
    function __destruct()
    {

    }
    public function getSiteSettings()
    {


        $query = $this->conn->query("Select * from site_settings
									");



        if (mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return null;
        }

    }


}
?>