<?php

class Model_lab
{

    private $conn;

    // constructor
    function __construct()
    {

        include "db_connection.php";
        include "config_type.php";
        $this->conn = $conn;
        $this->uploaddir = $UPLOAD_DIR_2;
        $this->smsuserkey = $SMS_USERKEY;
        $this->smspasskey = $SMS_PASSKEY;
    }

    // destructor
    function __destruct()
    {

    }

    public function checkToken($token, $id)
    {
        $query = $this->conn->query("SELECT * FROM nrz_nurses WHERE Token = '" . $token . "' AND NurseID = '" . $id . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }
}