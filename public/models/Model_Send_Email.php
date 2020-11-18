<?php

class Model_Send_Email
{

    private $conn;

    // constructor
    function __construct()
    {
        include "../../config/db_connection.php";
        include "../../config/config_type.php";
        $this->conn = $conn;

    }

    // destructor
    function __destruct()
    {

    }






}