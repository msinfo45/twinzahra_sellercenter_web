<?php

include "../config/lazada/LazopSdk.php";

include "../config/db_connection.php";
include "../config/config_type.php";

$url='https://api.lazada.co.id/rest';
//Load Models
include "../config/model.php";

$db = new Model_user();

$order_id = array();
$order_id_lazada = array();

$user_id = 5;
$merchant_name = null;
$marketplace = "LAZADA";

if (isset($post['merchant_name'])) {
  $merchant_name = $post['merchant_name'];
}

$getDataLazada = $db->getDataLazada($user_id, $merchant_name);



if ($getDataLazada != null) {



//echo json_encode($rowsHistory);die;
    while ($rowLazada = $getDataLazada->fetch_assoc()) {
      $rows[] = $rowLazada;

    }


    foreach ($rows as $obj) {

      $appkey =  $obj['AppKey'];
      $appSecret =  $obj['AppSecret'];
      $accessToken =  $obj['AccessToken'];
      $merchant_name =  $obj['merchant_name'];

  $getDataHistoryOrders = $db->getHistoryOrder($user_id , $marketplace);

  if ($getDataHistoryOrders != null) {

    while ($rowHistory = $getDataHistoryOrders->fetch_assoc()) {
      $order_id = $rowHistory['order_id'];
      $statuses = (int)$rowHistory['statuses'];
	  
     
      $c = new LazopClient($url,$appkey,$appSecret);
      $request = new LazopRequest('/orders/items/get','GET');
      $request->addApiParam('order_ids', json_encode(array($order_id)));
      $jdecode=json_decode($c->execute($request, $accessToken));
      //$data=$jdecode->data;





      if (isset($jdecode->data)) {

        foreach ($jdecode->data as $datas) {
          $rowsLazada[] = $datas;
          $order_id= $datas -> order_id;
          $order_number = $datas->order_number;


          foreach ($datas -> order_items as $order_items) {
        

            if ($order_items -> status == "pending") {

               $status = 1;
         
             }else if ($order_items -> status == "ready_to_ship") {
         
               $status = 10;
         
             }else if ($order_items -> status == "shipped") {
         
               $status = 3;
         
             }else if ($order_items -> status == "delivered") {
         
               $status = 4;
         
         
             }else if ($order_items -> status == "canceled") {
         
               $status = 6;
         
             }else if ($order_items -> status == "failed") {
         
               $status = 7;
         
             }else if ($order_items -> status == "returned") {
         
               $status = 5;
         
             }else if ($order_items -> status == "unpaid") {
         
               $status = 8;
         
         
             }else{
         
             //  $status = "ALL";
         
             }

             
            $created_at =  $order_items->created_at;
            $name = $order_items->name;
            $sku =  $order_items->sku;
            $paid_price = $order_items->paid_price;
            $shipment_provider =  $order_items->shipment_provider;
            $shipping_amount =  $order_items->shipping_amount;
            $variation =  $order_items->variation;
            $product_main_image = $order_items->product_main_image;
            $product_detail_url= $order_items->product_detail_url;

            ///update status data
           
            if ($statuses != $status ) {
				
				
				if ($status == 3){
					
				$ch = curl_init();
              curl_setopt($ch, CURLOPT_URL, 'https://localhost/twinzahra_sellercenter/public/api/orders.php?request=set_ship');
              $payload = json_encode( array( "order_id"=> $order_id ,
                "created_at"=> $created_at,
                "name"=> $name,
                "sku"=> $sku,
                "paid_price"=> $paid_price,
                "shipment_provider"=> $shipment_provider,
                "shipping_amount"=> $shipping_amount) );
              curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
              curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
              $lazadacontent = curl_exec($ch);
              curl_close($ch);

              $resultLazada=json_decode($lazadacontent,true);

              $status = "shipped";
	
					
					
			}else if ($status == 4) {
					
					
			$ch = curl_init();
              curl_setopt($ch, CURLOPT_URL, 'https://localhost/twinzahra_sellercenter/public/api/orders.php?request=set_delivery');
              $payload = json_encode( array( "order_id"=> $order_id) );
              curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
              curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
              $lazadacontent = curl_exec($ch);
              curl_close($ch);

              $resultLazada=json_decode($lazadacontent,true);

//echo json_encode($resultLazada);die;
              $status = "delivered";
	
					
					
				}

            }



            $dataOrders[]= array(

              "order_id" => $order_id,
              "order_number" => $order_number,
              //"msg" => $resultLazada,
              "status" => $status

            );



          }


        }

      }
    }






  

      $return= array(
        "status" => 200,
        "message" => "",
        "total_rows" => count($dataOrders),
        "data" => $dataOrders


      );


    }
	}

} else {
  $return = array(
    "status" => 404,
    "message" => "Akun lazada belum diatur"
  );
}

//
echo json_encode($return);




?>
