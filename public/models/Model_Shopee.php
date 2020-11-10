<?php


class Model_Shopee
{

  private $conn;

  // constructor
  function __construct()
  {
    include "../config/db_connection.php";
    include "../config/config_type.php";

    $this->conn = $conn;
    $this->uploaddir = $UPLOAD_DIR_2;
    $this->smsuserkey = $SMS_USERKEY;
    $this->smspasskey = $SMS_PASSKEY;
  }

  // destructor
  function __destruct()
  {

  }

  public function getDataShopee($user_id , $merchant_name)
  {

    if ($merchant_name != null) {

      $query = $this->conn->query(" SELECT * from shopee
										where (user_id = '" . $user_id . "' and merchant_name = '" . $merchant_name . "') and active=1
                                       ");

      if (mysqli_num_rows($query) > 0)
      {
        return $query;
      }
      else
      {
        return null;
      }


    }else{

      $query = $this->conn->query(" SELECT * from shopee
										where user_id = '" . $user_id . "' and active=1
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






}

?>
