<?php


class Model_Marketplace
{

  private $conn;

  // constructor
  function __construct()
  {
    include "../config/db_connection.php";
    include "../config/config_type.php";

    $this->conn = $conn;

  }

  // destructor
  function __destruct()
  {

  }

  public function getDataToko($user_id)
  {
    $query = $this->conn->query("SELECT * FROM marketplace WHERE user_id = '" . $user_id . "' 
     order by active desc");

    if (mysqli_num_rows($query) > 0) {
      return $query;
    } else {
      return false;
    }
  }



}
