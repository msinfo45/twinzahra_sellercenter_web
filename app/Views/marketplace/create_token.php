<?php

include "public/config/db_connection.php";
include "public/config/lazada/LazopSdk.php";



$urlAuth='https://auth.lazada.com/rest';
$urlApi='https://api.lazada.co.id/rest';

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


      $c = new LazopClient($urlAuth,$app_key,$appSecret);
      $request = new LazopRequest('/auth/token/create','GET');
      $request->addApiParam('code', $code);
      $jdecode=json_decode($c->execute($request));
    
    // echo json_encode($jdecode);die;

      if ($jdecode->code== "0") {


          $cRefresh = new LazopClient($urlAuth,$app_key,$appSecret );
          $requestRefresh = new LazopRequest('/auth/token/refresh','GET');
          $requestRefresh->addApiParam('refresh_token', $jdecode->refresh_token);
          $jdecodeRefresh=json_decode($c->execute($requestRefresh));

          $access_token = $jdecodeRefresh->access_token;
          $refresh_token = $jdecodeRefresh->refresh_token;
          $account = $jdecodeRefresh->account;

          foreach ($jdecodeRefresh->country_user_info as $user_info) {

              $seller_id = $user_info->seller_id;

          }

          $cSeller = new LazopClient($urlApi,$app_key,$appSecret );
          $requestSeller = new LazopRequest('/seller/get','GET');
          $jdecodeSeller=json_decode($cSeller->execute($requestSeller, $access_token));

          $name = $jdecodeSeller->data->name;
          $location = $jdecodeSeller->data->location;

        $create = $db->insertDataToko($user_id,$marketplace, $name , $location , $account, $seller_id, $access_token, $refresh_token);

        if($create) {
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