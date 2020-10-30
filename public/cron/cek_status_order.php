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
					
					$getDataLazada = $db->getDataLazada($user_id);
					
			

				if ($getDataLazada != null) {
							
					$getDataHistoryOrders = $db->getHistoryOrder($user_id);	
					
					if ($getDataHistoryOrders != null) {
						
					while ($rowHistory = $getDataHistoryOrders->fetch_assoc()) {										
					$rowsHistory[] = $rowHistory['order_id'];
					
					}	 
					
					
                    while ($rowLazada = $getDataLazada->fetch_assoc()) {										
					$rows[] = $rowLazada;
			
					}
						
				
					foreach ($rows as $obj) {
						
		
						
					$appkey =  $obj['AppKey'];
					$appSecret =  $obj['AppSecret'];
					$accessToken =  $obj['AccessToken'];	
					$merchant_name =  $obj['merchant_name'];
					


			//echo json_encode($rowsHistory);die;
			$c = new LazopClient($url,$appkey,$appSecret);
			$request = new LazopRequest('/orders/items/get','GET');
			$request->addApiParam('order_ids', json_encode($rowsHistory));
			$jdecode=json_decode($c->execute($request, $accessToken));
			$data=$jdecode->data;
			//$order_items=$jdecode->data->order_items;
			
			//echo json_encode($jdecode);die;

			foreach ($data as $datas) {
			$rowsLazada[] = $datas;	
			$order_id= $datas -> order_id;
			$order_number = $datas->order_number;	

			
			foreach ($datas -> order_items as $order_items) {
				
			$status= $order_items -> status;
			$created_at =  $order_items->created_at;
			$name = $order_items->name;
			$sku =  $order_items->sku;
			$paid_price = $order_items->paid_price;
			$shipment_provider =  $order_items->shipment_provider;
			$shipping_amount =  $order_items->shipping_amount;
			

			///update status data 

				if ($status == "shipped") {
					
				$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'http://localhost/twinzahra/public/api/orders.php?request=set_ship');
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

				}else if ($status == "delivered") {
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'http://localhost/twinzahra/public/api/orders.php?request=set_delivery');
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
				
					$status = "delivered";
			
				}else{



				}
				
	
					
				$dataOrders[]= array(
				
					"order_id" => $order_id,
					"order_number" => $order_number,
					//"msg" => $resultLazada,
					"status" => $status
					

                   );
				   
				
				   
			}
			
			
			}
			
			



		
			
				
		  		$return= array(
					"status" => 200,
                    "message" => "",
					"total_rows" => count($dataOrders),
					"data" => $dataOrders
					

                   );


				}
				 } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Belum ada History"
                        );
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