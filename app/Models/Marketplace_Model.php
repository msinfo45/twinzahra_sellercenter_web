<?php


class Marketplace_Model
{

  private $conn;

  // constructor
  function __construct()
  {

    include "public/config/db_connection.php";
    include "public/config/config_type.php";


    $this->conn = $conn;

  }

  // destructor
  function __destruct()
  {

  }

  public function insertDataToko($user_id,$marketplace_name, $account,$seller_id, $access_token, $refresh_token)
  {
      $insert = $this
          ->conn
          ->query("INSERT INTO toko 
                                  (user_id,
                                  marketplace_name, 
                                  account, 
                                  seller_id, 
                                  access_token,
                                  refresh_token,
                                  active
                                  ) 
                              VALUES 
                                  ('" . $user_id . "', 
                                      '" . $marketplace_name . "', 
                                  '" . $account . "',
                                  '" . $seller_id . "',
                                  '" . $access_token . "',
                                  '" . $refresh_token . "',
                                  1
                                  ) ");

      if ($insert)
      {
          return true;
      }
      else
      {
          return false;
      }
  }

  public function insertToko($user_id)
  {
    $query = $this->conn->query("SELECT * FROM toko WHERE user_id = '" . $user_id . "' 
     order by active desc");

    if (mysqli_num_rows($query) > 0) {
      return $query;
    } else {
      return false;
    }
  }

  public function getDataMarketplace($marketplace)
  {
  
      $query = $this->conn->query(" SELECT * from marketplace
                                      where  marketplace_name = '" . $marketplace . "'
                                     ");

      if (mysqli_num_rows($query) > 0)
      {
          return $query;
      }
      else
      {
          return null;
      }


    
      }


}
