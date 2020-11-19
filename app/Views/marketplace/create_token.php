<?php

include "public/config/db_connection.php";
include "public/config/lazada/LazopSdk.php";



$url='https://auth.lazada.com/rest';


$rowsLazada = array();
$rows = [];


include "App/Models/Marketplace_Model.php";



  $db = new Marketplace_Model();


$code = $_GET['code'];
$marketplace = "LAZADA";
$user_id = "5";
	//echo $code;die;

	if (isset($code)){
		
    $getDataLazada = $db->getDataMarketplace($marketplace);

    if ($getDataLazada !=null ) {

      while ($rowLazada = $getDataLazada->fetch_assoc()) {

        $app_key =  $rowLazada['app_key'];
        $appSecret =  $rowLazada['app_secret'];
 

      }
      //echo $code;die;

      $c = new LazopClient($url,$app_key,$appSecret);
      $request = new LazopRequest('/auth/token/create','GET');
      $request->addApiParam('code', $code);
      $jdecode=json_decode($c->execute($request));
    
    // echo json_encode($jdecode);die;

      if ($jdecode->code== "0") {
        $access_token = $jdecode->access_token;
        $refresh_token = $jdecode->refresh_token;
        $account = $jdecode->account;

        foreach ($jdecode->country_user_info as $user_info) {

            $seller_id = $user_info->seller_id;

        }
        $create = $db->insertDataToko($user_id,$marketplace, $account, $seller_id, $access_token, $refresh_token);

        if($create) {
          //  header('Location: http://localhost/twinzahra_sellercenter/marketplace');die;
           header("Location: ".base_url('marketplace'));die;

        }else{

            $return = array(
                "status" => 404,
                "message" => "ERROR",
                "data" => []
        
              );
        }
       
        
    }else{


        $return = array(
          "status" => 404,
          "message" => $jdecode->message,
          "data" => []
  
        );
  
      }

     
	  
	}

    }else{


      $return = array(
        "status" => 404,
        "message" => "ERROR",
        "data" => []

      );

    }



    //
    echo json_encode($return);



?>