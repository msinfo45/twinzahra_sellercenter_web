<?php

include "public/config/db_connection.php";
include "public/config/lazada/LazopSdk.php";


$rows = [];


include "App/Models/Marketplace_Model.php";



$db = new Marketplace_Model();


$code = $_GET['code'];
$shop_id = $_GET['shop_id'];
$marketplace = "SHOPEE";
$user_id = "5";



if (isset($code)){

    $getDataMarketplace = $db->getDataMarketplace($marketplace);

    if ($getDataMarketplace !=null ) {

        while ($rowMarketplace = $getDataMarketplace->fetch_assoc()) {

            $app_key =  $rowMarketplace['app_key'];
            $appSecret =  $rowMarketplace['app_secret'];


        }

        $tgl = "Y-m-d";
        $waktu = "H:i:s";
        $waktu_sekarang = date("$tgl $waktu");
        $ditambah_5_menit = date("$tgl $waktu", strtotime('+5 minutes'));

        $url = "https://partner.shopeemobile.com/api/v1/shop/get";
        $timestamp = strtotime($ditambah_5_menit);

        $convertJson = array(
            "partner_id" => (int)$app_key,
            "shopid" => (int)$shop_id,
            "timestamp" => $timestamp);

        $base_string = $url . "|" . json_encode($convertJson);
        $hmac = hash_hmac('sha256', $base_string, $appSecret);

        $ch = curl_init($url);
        $payload = json_encode($convertJson);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json',
            'Authorization: ' . $hmac . ''));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $jsonDecode = json_decode($result);



        if (isset($jsonDecode->error)) {


            $return = array(
                "status" => 404,
                "message" => $jsonDecode->msg,
                "data" => []

            );


        }else{

            $name = $jsonDecode->shop_name;
            $location = $jsonDecode->country;
           
            $create = $db->insertDataToko($user_id,$marketplace, $name , $location , "", $shop_id, "", "" , $code);
          
           
            if($create) {

         header("Location: ".base_url('marketplace'));die;

            }else{

                $return = array(
                    "status" => 404,
                    "message" => "ERROR",
                    "data" => []

                );
            }



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