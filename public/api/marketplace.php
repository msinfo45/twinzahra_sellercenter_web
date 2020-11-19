<?php

include "../config/db_connection.php";

$rows = array();


$current_app_version_code = "1"; //App Version Code
$current_app_version_name = "0.1.0"; //App Version Name

$token_header = ""; //Header Token
$version_code_header = ""; //Header Version Code
$version_name_header = ""; //Header Version Name
$version_name_header = ""; //Header Version Name
$userid_header = "";
$modeHeader = 1;




//-------------------------------------- API Function Start Here ----------------------------------------//

//Check request content
$content = $_GET['request'];

if (isset($content) && $content != "") {

  //Load Models
  include "../models/Model_Marketplace.php";

  $db = new Model_Marketplace();


  if ($content == "get_toko") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);

    $user_id =5;


    //if (isset($post['UserID'])) {
    //  $user_id = $post['UserID'];
   // }

    //Get data from database
    $getData = $db->getDataToko($user_id);


    if ($getData != null) {

      while ($row = $getData->fetch_assoc()) {

        $rows[] = $row;


      }

      $total = mysqli_num_rows($getData);


      $return = array(
        "status" => 200,
        "total_rows" => $total,
        "message" => "Berhasil",
        "data" => $rows
      );



    } else {
      $return = array(
        "status" => 200,
        "total_rows" => 0,
        "message" => "Belum ada Data",
        "data" => []
      );
    }


    echo json_encode($return);
  }

  if ($content == "get_marketplace") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);

  
  
    $getData = $db->getDataMarketplace();


    if ($getData != null) {

      while ($row = $getData->fetch_assoc()) {

        $rows[] = $row;


      }

      $total = mysqli_num_rows($getData);


      $return = array(
        "status" => 200,
        "total_rows" => $total,
        "message" => "Berhasil",
        "data" => $rows
      );



    } else {
      $return = array(
        "status" => 200,
        "total_rows" => 0,
        "message" => "Belum ada Data",
        "data" => []
      );
    }


    echo json_encode($return);
  }


}



?>
