<?php

include "../config/lazada/LazopSdk.php";
include "../config/db_connection.php";
include "../config/config_type.php";
include "../config/model.php";


$url='https://api.lazada.co.id/rest';
$db = new Model_user();
$order_id = array();
$order_id_lazada = array();
$user_id = 5;
$merchant_name = null;

if (isset($post['merchant_name'])) {
  $merchant_name = $post['merchant_name'];
}


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/public/api/shopee.php?request=get_orders');
$payload = json_encode( array(
  "status"=> 1,
  "merchant_name"=> $merchant_name) );
curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$contentOrders = curl_exec($ch);
curl_close($ch);
$resultOrders=json_decode($contentOrders);


foreach($resultOrders->data as $dataOrders)
{

  $order_id = $dataOrders-> order_id;
  $merchant_name = $dataOrders->merchant_name ;
  $marketplace = $dataOrders->marketplace ;

  $chItems = curl_init();
  curl_setopt($chItems, CURLOPT_URL, $base_url . '/public/api/orders.php?request=cek_history');
  $payloadItem = json_encode( array( "order_id"=> $order_id ) );

  curl_setopt( $chItems, CURLOPT_POSTFIELDS, $payloadItem );
  curl_setopt( $chItems, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
  curl_setopt($chItems, CURLOPT_RETURNTRANSFER, 1);
  $contentItem = curl_exec($chItems);
  curl_close($chItems);

  $resultItem=json_decode($contentItem);



 if ($resultItem->status == "404") {

   $chItems = curl_init();
   curl_setopt($chItems, CURLOPT_URL, $base_url . '/public/api/orders.php?request=created_order2');
   $payloadItem = json_encode(array(
     "data_order" => base64_encode(json_encode($dataOrders)),
     "merchant_name" => $merchant_name,
     "marketplace" => $marketplace
   ));


   curl_setopt($chItems, CURLOPT_POSTFIELDS, $payloadItem);
   curl_setopt($chItems, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
   curl_setopt($chItems, CURLOPT_RETURNTRANSFER, 1);
   $contentItem = curl_exec($chItems);
   curl_close($chItems);
   $resultItem = json_decode($contentItem, true);


   if ($resultItem['status'] == "200") {

     $status = "sukses";
     $subject = "Pesanan " . $order_id . " telah berhasil dikonfirmasi";
     $message = $resultItem['message'];
     $sendEmail = $db->send_email($subject , $message);

   } else {

     $status = "gagal";
     $subject = "Pesanan " . $order_id . " gagal dikonfirmasi";
     $message = $resultItem['message'];

    $sendEmail = $db->send_email($subject , $message);


   }

   $dataOrder[] = array(
     "merchant_name" => $merchant_name,
     "order_id" => $order_id,
     "status" => $status,
     "message" => $message
   );


 }else{


  $dataOrder = null;

 }




}

if ($dataOrder != null) {

  $return = array(
    "status" => 200,
    "total_rows" => COUNT($dataOrder),
    "message" => "Berhasil",
    "data" => $dataOrder
  );

}else{

  $return = array(
    "status" => 404,
    "message" => "Belum ada data",
    "total_rows" => 0,
    "data" => []
  );

}

echo json_encode($return);

?>
