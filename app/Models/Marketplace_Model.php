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


  public function insertDataToko($user_id,$marketplace, $name , $location , $account, $seller_id, $access_token, $refresh_token , $code)

  {

      $getDataToko = $this->getDataToko($user_id ,$seller_id );
      if ($getDataToko == false) {

          $insert = $this->conn->query("INSERT INTO toko 
                                  (user_id,
                                  marketplace_name,
                                  merchant_name,
                                  location, 
                                  account, 
                                  seller_id, 
                                  access_token,
                                  refresh_token,
                                  code,
                                  created_date,
                                  active
                                  ) 
                              VALUES 
                                  ('" . $user_id . "', 
                                  '" . $marketplace . "', 
                                  '" . $name . "', 
                                  '" . $location . "', 
                                  '" . $account . "',
                                  '" . $seller_id . "',
                                  '" . $access_token . "',
                                  '" . $refresh_token . "',
                                   '" . $code . "',
                                    '" .date("Y-m-d H:i:s") . "',
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

      }else{

          $update = $this->conn->query("UPDATE toko SET 
								merchant_name = '" . $name . "',
                      			location = '" . $location . "',
                  				account = '" . $account . "',
              					access_token = '" . $access_token . "',
								refresh_token = '" . $refresh_token . "',
								code = '" . $code . "',
								update_date = '" . date("Y-m-d H:i:s") . "'
								WHERE 
								user_id = '" . $user_id . "' AND seller_id = '" . $seller_id . "' AND marketplace_name = '" . $marketplace . "' ");

          if ($update)
          {
              return true;
          }
          else
          {
              return false;
          }

      }

  }

  public function getDataToko($user_id , $seller_id)
  {

   if ($seller_id == null)    {

       $query = $this->conn->query("SELECT * FROM toko WHERE user_id = '" . $user_id . "'  and seller_id = '" . $seller_id . "' 
     order by active desc");

       if (mysqli_num_rows($query) > 0) {
           return $query;
       } else {
           return false;
       }

   }else{

       $query = $this->conn->query("SELECT * FROM toko WHERE user_id = '" . $user_id . "' 
     order by active desc");

       if (mysqli_num_rows($query) > 0) {
           return $query;
       } else {
           return false;
       }
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
