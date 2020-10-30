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
					
				
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'http://localhost/twinzahra/public/api/orders.php?request=get_orders');
					$payload = json_encode( array( 
					"status"=> "pending") );
					curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
					curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$lazadacontent = curl_exec($ch);
					curl_close($ch);
					
					$resultLazada=json_decode($lazadacontent,true);
		
					

//cek stok 
							
			//if (count($result['data']) > 0) {

			foreach($resultLazada['data'] as $DataProduct)
			{

			$order_id = $DataProduct['order_id'] ;


					$chItems = curl_init();
					curl_setopt($chItems, CURLOPT_URL,'http://localhost/twinzahra/public/api/orders.php?request=get_order_items');
					$payloadItem = json_encode( array( "order_id"=> $order_id ) );
					//$payloadItem = json_encode( array( "order_id" => 45 ) );
					//$payloadItem = json_encode( array( "UserID"=> "5" ) );
					curl_setopt( $chItems, CURLOPT_POSTFIELDS, $payloadItem );
					curl_setopt( $chItems, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
					curl_setopt($chItems, CURLOPT_RETURNTRANSFER, 1);
					$contentItem = curl_exec($chItems);
					curl_close($chItems);

					//mengubah data json menjadi data array asosiatif
					$resultItem=json_decode($contentItem,true);

			//echo json_encode($resultItem);die;

foreach($resultItem['data'] as $DataOrderItems)
			{
				$sku = $DataOrderItems['sku'];


///cek stok sku
				$chItems = curl_init();
					curl_setopt($chItems, CURLOPT_URL,'http://localhost/twinzahra/public/api/products.php?request=cek_stok');
					$payloadItem = json_encode( array( "sku"=> $sku ) );

					curl_setopt( $chItems, CURLOPT_POSTFIELDS, $payloadItem );
					curl_setopt( $chItems, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
					curl_setopt($chItems, CURLOPT_RETURNTRANSFER, 1);
					$contentItem = curl_exec($chItems);
					//$resultItem=json_encode($contentItem,true);
					curl_close($chItems);

					//mengubah data json menjadi data array asosiatif
					$resultItem=json_decode($contentItem,true);
										
					$stock =$resultItem['data'];

			//echo json_encode($stock);die;

			if ($stock > 0) {
			$chItems = curl_init();
					curl_setopt($chItems, CURLOPT_URL,'http://localhost/twinzahra/public/api/orders.php?request=accept_order');
					$payloadItem = json_encode( array( 
						"order_id"=> $order_id ,
						"merchant_name"=> "Twinzahra Shop" ,
						"shipping_provider"=> "dropship" ,
						"delivery_type"=> "dropship" 
					) );

					curl_setopt( $chItems, CURLOPT_POSTFIELDS, $payloadItem );
					curl_setopt( $chItems, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
					curl_setopt($chItems, CURLOPT_RETURNTRANSFER, 1);
					$contentItem = curl_exec($chItems);
					//$resultItem=json_encode($contentItem,true);
					curl_close($chItems);

					//mengubah data json menjadi data array asosiatif
					$resultItem=json_decode($contentItem,true);


					//echo json_encode($resultItem);die;
		
		if ($resultItem['status'] == "200"){

		$status = "sukses";
		$message = "Pesanan Berhasil di Konfirmasi";

		$sendEmail = $db->send_email($order_id , $sku , $status , $message);

		}else{

		$status = "gagal";
		$message = "Pesanan Gagal di Konfirmasi";

	//	$sendEmail = $db->send_email($order_id , $sku , $status , $message);

		}


		}else{

		$status = "gagal";
		$message = "Produk di database kosong";

		$sendEmail = $db->send_email($order_id , $sku , $status , $message);
		

		}



			}


				$dataOrders[]= array(
				
					"order_id" => $order_id,
					"sku" => $sku,
					"status" => $status,
					"message" => $message
					
					

                   );


			}




					$return = array(

                            "status" => 200,
                            "data" => $dataOrders
                        );




	//	}

	
					
				
			
			
			



		
			
				
			  
                 
            
            //
            echo json_encode($return);
             
				
				
				
				?>