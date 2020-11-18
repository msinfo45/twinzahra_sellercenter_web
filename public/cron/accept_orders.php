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
curl_setopt($ch, CURLOPT_URL, 'https://twinzahra.masuk.id/public/api/lazada.php?request=get_orders');
$payload = json_encode( array(
  "status"=> 1,
  "merchant_name"=> $merchant_name) );
curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$contentOrders = curl_exec($ch);
curl_close($ch);
$resultOrders=json_decode($contentOrders,true);


Foreach($resultOrders['data'] as $dataOrders)
{

  $order_id = $dataOrders['order_id'] ;
  $merchant_name = $dataOrders['merchant_name'] ;
  $marketplace = $dataOrders['marketplace'] ;
 

 
  foreach($dataOrders['order_items'] as $DataOrderItems)
  {
    $sku = $DataOrderItems['sku'];
  }
    //echo json_encode($sku);die;
    $chItems = curl_init();
    curl_setopt($chItems, CURLOPT_URL,'https://twinzahra.masuk.id/public/api/products.php?request=cek_stok');
    $payloadItem = json_encode( array( "sku"=> $sku ) );
    curl_setopt( $chItems, CURLOPT_POSTFIELDS, $payloadItem );
    curl_setopt( $chItems, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($chItems, CURLOPT_RETURNTRANSFER, 1);
    $contentItem = curl_exec($chItems);
    curl_close($chItems);

    $resultItem=json_decode($contentItem,true);

    $stock =$resultItem['data'];

   // echo json_encode($stock);die;

    if ($stock > 0) {


      $chItems = curl_init();
      curl_setopt($chItems, CURLOPT_URL,'https://twinzahra.masuk.id/public/api/orders.php?request=accept_order');
      $payloadItem = json_encode( array(
        "order_id"=> $order_id ,
        "merchant_name"=> $merchant_name ,
        "marketplace"=> $marketplace,
        "shipping_provider"=> "dropship" ,
        "delivery_type"=> "dropship"
      ));

      //echo $payloadItem;die;

      curl_setopt( $chItems, CURLOPT_POSTFIELDS, $payloadItem );
      curl_setopt( $chItems, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
      curl_setopt($chItems, CURLOPT_RETURNTRANSFER, 1);
      $contentItem = curl_exec($chItems);
     curl_close($chItems);
      $resultItem=json_decode($contentItem,true);

      //echo json_encode($resultItem);die;

      if ($resultItem['status'] == "200"){

        $status = "sukses";
        $subject  = "Pesanan " . $order_id . " telah berhasil dikonfirmasi" ;
        $message = "Pesanan di lazada berhasil dikonfirmasi";
       $sendEmail = $db->send_email($subject , $message);

      }else{

        $status = "gagal";
        $subject  = "Pesanan " . $order_id . " gagal dikonfirmasi" ;
        $message = "Pesanan gagal dikonfirmasi ada masalah di accept orders";

       //$sendEmail = $db->send_email($subject , $message);

      }

     }else if ($stock == null) {

      $status = "gagal";
      $subject  = "Pesanan " . $order_id . " belum terdaftar didatabase" ;
      $message = "Pesanan gagal dikonfirmasi karna produk belum terdaftar didatabase";
      $sendEmail = $db->send_email($subject , $message);
	   
    }else{

      $status = "gagal";
      $subject  = "Pesanan " . $order_id . " stok kosong" ;
      $message = "Pesanan gagal dikonfirmasi karna stok kosong";
      $sendEmail = $db->send_email($subject , $message);

      //
    }





    $dataOrder[] = array(
      "marketplace" => $marketplace,
      "merchant_name" => $merchant_name,
      "order_id" => $order_id,
      "sku" => $sku,
      "status" => $status,
      "message" => $message
    );



  //}
}


$return = array(
  "status" => 200,
  "data" => $dataOrder
);





echo json_encode($return);

?>
