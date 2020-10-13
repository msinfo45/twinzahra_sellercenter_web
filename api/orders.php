<?php

include "../config/db_connection.php";

include "../config/lazada/LazopSdk.php";

$rows = array();
$rows2 = array();
$rowsLazada = array();

$current_app_version_code = "1"; //App Version Code
$current_app_version_name = "0.1.0"; //App Version Name

$token_header = ""; //Header Token

$version_code_header = ""; //Header Version Code
$version_name_header = ""; //Header Version Name
$version_name_header = ""; //Header Version Name
$userid_header = "";
$modeHeader = 1;


$url='https://api.lazada.co.id/rest';
	

//-------------------------------------- API Function Start Here ----------------------------------------//

//Check request content
$content = $_GET['request'];

if (isset($content) && $content != "") {

    //Load Models
    include "../config/model.php";

    $db = new Model_user();


	
	 if ($content == "add_cart_detail") {
        $modeHeader = 0;   
        $post = json_decode(file_get_contents("php://input"), true);
        $user_id = $post['UserID'];	
		$sku_id = $post['SkuID'];	
		//$sku_id = "WK-HTM-AB-S-HTM-39";
	//Get Value For History Order details from post
		
		
		
       if (isset($sku_id)) {

			//cek user id
            $getData = $db->checkProductBySKU($sku_id);
			
			if ($getData != null) {

                        while ($row = $getData->fetch_assoc()) {
                           $rows[] = $row;
							
							
							
						}


						foreach ($rows as $obj) {
							
							$product_id =$obj['ProductID'];
							$price =$obj['PriceRetail'];
							$stock =$obj['Stock'];
							$quantity = "1";
							$product_variant_id =$obj['ProductVariantID'];
							$product_variant_detail_id =$obj['ProductVariantDetailID'];
							
							if ($stock > 0) {
	
							$create = $db->createCartDetail($user_id, $sku_id ,$product_id , $price , $quantity , $product_variant_id, $product_variant_detail_id);
						
							}
						}
							
							
							 
							 //jika produk berhasil
							  if ($create) {
								   
								 $return = array(
											 "status" => 200,
												"total_rows" => 1,
											 "message" => "Berhasil",
												"data" => $price
										   );
								
								
								//jika produk gagal	
								} else {
								  $return = array(
									   "status" => 404,
									 "message" => "Stok Kosong"
									);
								}
							

					
					
                       
							
						
			
            
				
				
				
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Produk tidak ada di database"
                        );
                    }
					
			
		//Jika user id tidak ada//	
       } else {
           $return = array(
               "status" => 404,
               "message" => "Sku tidak boleh kosong"
            );
        }
        echo json_encode($return);
    }
				
			if ($content == "get_cart_details") {
					$modeHeader = 0;
                    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
                    $user_id = null;
				
					$page = null;
					
                    $limit = 0;
					
					 if (isset($post['UserID'])) {
                        $user_id = $post['UserID'];
                    }
					
					
                    if (isset($post['Page'])) {
                        $page = $post['Page'];
                    }
					
				
					
						

                        $getData = $db->getDataCartDetail($user_id, $page, $limit);
                        if ($getData != null) {

                            while ($row = $getData->fetch_assoc()) {										
						
                                $rows[] = $row;				
	
							
                            }
							
	

                            $total = mysqli_num_rows($getData);


                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "total_rows" => $total,
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 200,
								"total_rows" => 0,
                                "message" => "Belum ada Produk",
								"data" => []
                            );
                        }
                  

                    echo json_encode($return);
                }		
				
					if ($content == "delete_cart_details") {
					$modeHeader = 0;
                    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
                    $user_id = 5;
					$CartDetailID = $post['CartDetailID'];
		
		

                        $getData = $db->deleteCartDetailByUserID($user_id, $CartDetailID);
                        if ($getData) {

                   


                            $return = array(
                                "status" => 200,
                                "message" => "Berhasil menghapus item",
                                "total_rows" => $total,
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 200,
								"total_rows" => 0,
                                "message" => "ERROR",
								"data" => []
                            );
                        }
                  

                    echo json_encode($return);
                }		
				
				
				
				if ($content == "get_orders") {
					$modeHeader = 0;
                    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
                    $user_id = 5;
					$status_id = 1;
					
					$page = null;
					
                    $limit = 0;
					
					 if (isset($post['UserID'])) {
                        $user_id = $post['UserID'];
                    }
					
					
                    if (isset($post['Page'])) {
                        $page = $post['Page'];
                    }
					
					if (isset($post['status_id'])) {
                        $status_id = $post['status_id'];
                    }
					
				
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://sellercenter.twinzahra.com/api/lazada.php?request=get_orders');
					//$payload = json_encode( array( "order_number"=> $DataProduct['order_number'] ) );
					//$payload = json_encode( array( "UserID"=> "5" ) );
					//curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
					curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$lazadacontent = curl_exec($ch);
					curl_close($ch);
					
					$resultLazada=json_decode($lazadacontent,true);
					
						

                        $getData = $db->getDataOrders($user_id, $page, $limit , $status_id);
						
						if ($getData != null && $resultLazada['status'] != 404) {
							
							
							   while ($row = $getData->fetch_assoc()) {										
						
                                $rows[] = $row;				
	
								
                            }
							
									$r = [];
									$r = array_merge($rows,$resultLazada) ;
					


                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "data" => $r
                            );
							
						}else if ($getData != null) {


                            while ($row = $getData->fetch_assoc()) {										
						
                                $rows[] = $row;				
	
								
                            }
							

							$result=json_decode($content,true);
							//get new orders lazada

	
                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "data" => $rows
                            );
							
							
						}else if (($resultLazada['status'] != 404)) {
							
			
						$return = array(
                        "status" => 200,
                        "message" => "ok lazada",
                        "data" => $resultLazada
						);	
									
								
								
								
                        } else {
							
                            $return = array(
                                "status" => 404,
                                "message" => "Belum ada Data",
								"data" => []
                            );
                        }
                  

                    echo json_encode($return);
                }		
				
				
					

				
				if ($content == "get_order") {
					$modeHeader = 0;
                    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
                    $user_id = 5;
					$order_id = $post['order_id'];
					
					$page = null;
					
                    $limit = 0;
					
					 if (isset($post['UserID'])) {
                        $user_id = $post['UserID'];
                    }
					
					
                    if (isset($post['Page'])) {
                        $page = $post['Page'];
                    }
					
		
					
				
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://sellercenter.twinzahra.com/api/lazada.php?request=get_order');
					//$payload = json_encode( array( "order_number"=> $DataProduct['order_number'] ) );
					$payload = json_encode( array( "order_id"=> $order_id ) );
					curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
					curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$lazadacontent = curl_exec($ch);
					curl_close($ch);
					
					$resultLazada=json_decode($lazadacontent,true);
					
						

                        $getData = $db->getDataOrder($user_id, $page, $limit , $order_id);
						
						if ($getData != null && $lazadacontent != null) {
							
							
							   while ($row = $getData->fetch_assoc()) {										
						
                                $rows[] = $row;				
	
								
                            }
							
									$r = [];
									$r = array_merge($rows,$resultLazada) ;
					


                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "data" => $r
                            );
							
						}else if ($getData != null) {


                            while ($row = $getData->fetch_assoc()) {										
						
                                $rows[] = $row;				
	
								
                            }
							

							$result=json_decode($content,true);
							//get new orders lazada

	
                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "data" => $rows
                            );
							
							
						}else if ($lazadacontent != null) {
							
							 $return = array(
                                "status" => 200,
                                "message" => "ok lazada",
                                "data" => $resultLazada
                            );
							
					
							
								
                        } else {
                            $return = array(
                                "status" => 200,
                                "message" => "Belum ada Data",
								"data" => []
                            );
                        }
                  

                    echo json_encode($return);
                }		
				
				 if ($content == "set_ship") {
				$modeHeader = 0;   
				$post = json_decode(file_get_contents("php://input"), true);
		
				$order_id = $post['order_id'] ;
				$user_id = 5;	
				//$order_id = "2008046PJSNM2B" ;
				$created_at = $post['created_at'] ;
				$name = $post['name'] ;
				$sku = $post['sku'] ;
				$paid_price = $post['paid_price'] ;
				$shipment_provider = $post['shipment_provider'] ;
				$shipping_amount = $post['shipping_amount'] ;
		
				
       if (isset($order_id) && isset($user_id)) {


			 
			//cek order id and user id
            $getData = $db->checkHistoryOrderByOrder($order_id , $user_id);
			
	
			if ($getData != false) {

            while ($row = $getData->fetch_assoc()) {										
						
			$rows = $row;				
			$status = 	$row['statuses'];	
			
            }      
			
			if ($status == 3) {
				
                $return = array(
				"status" => 200,
				"message" => "Pesanan masih diperjalanan"
				);
				
			}else{
				
				$create = $db->setShip($user_id, $order_id);
	
               //jika produk berhasil
			  if ($create) {
				  
				 $to = "twinzahrashop@gmail.com";
				$subject = "Pesanan Order ID ".$order_id." dalam perjalanan";
				 
				 $message = "<b>Hai Admin</b>";
				 $message = "<p>Pesanan kamu dalam perjalanan</p>
				<b>Rincian Pesanan</b>
				 <p>No Pesanan : ".$order_id." </p>
				  <p>Tanggal Pesanan : ".$created_at." </p>
				  <p>Nama Produk : ".$name." </p>
				   <p>Sku : ".$sku." </p>
				   <p>Paid Price : ".$paid_price." </p>
				  <p>Jasa Pengiriman : ".$shipment_provider." </p>
				   <p>Ongkos Kirim: ".$shipping_amount." </p>";
				 $header = "From:no_replay@twinzahra.com \r\n";
				 $header .= "Cc:no_replay@twinzahra.com \r\n";
				 $header .= "MIME-Version: 1.0\r\n";
				 $header .= "Content-type: text/html\r\n";
				 
				 $retval = mail ($to,$subject,$message,$header);
         
				if( $retval == true ) {
		
				$return = array(
				"status" => 200,
				"message" => "Pesanan telah dirubah menjadi Dalam Perjalan"
				);
				}else {
					
					
			
			$return = array(
				"status" => 404,
				"message" => "Gagal mengirim Email"
				);
				}  
				
			  //jika produk gagal	
                } else {
					
					$return = array(
				"status" => 404,
				"message" => "error"
				);
		
                }
                
			}
				
				
			
			

                } else {
				$return = array(
				"status" => 404,
				"message" => "No Order tidak ditemukan"
           );
      }
					
			
		//Jika user id tidak ada//	
       } else {
          $return = array(
               "status" => 504,
              "message" => "ERROR"
           );
      }
        echo json_encode($return);
    }
	
	 if ($content == "set_delivery") {
        $modeHeader = 0;   
        $post = json_decode(file_get_contents("php://input"), true);
		

				$order_id = $post['order_id'] ;
				$user_id = 5;	
				//$order_id = 1 ;
				$created_at = $post['created_at'] ;
				$name = $post['name'] ;
				$sku = $post['sku'] ;
				$paid_price = $post['paid_price'] ;
				$shipment_provider = $post['shipment_provider'] ;
				$shipping_amount = $post['shipping_amount'] ;
				
       if (isset($order_id) && isset($user_id)) {


			 
			//cek order id and user id
            $getData = $db->checkHistoryOrderByOrder($order_id , $user_id);
			
	
			if ($getData != false) {

            while ($row = $getData->fetch_assoc()) {										
						
			$rows = $row;				
			$status = 	$row['statuses'];	
			
            }      
			
			if ($status == 4) {
				
			 // $return = "Pesanan";
                 
				
			}else{

				
				$create = $db->setDelivery($user_id, $order_id);
	
               //jika produk berhasil
			  if ($create) {
				 $to = "twinzahrashop@gmail.com";
				$subject = "Pesanan Order ID ".$order_id." sudah sampai";
				 
				 $message = "<b>Hai Admin</b>";
				 $message = "<p>Pesanan kamu telah diantar kurir</p>
				<b>Rincian Pesanan</b>
				 <p>No Pesanan : ".$order_id." </p>
				  <p>Tanggal Pesanan : ".$created_at." </p>
				  <p>Nama Produk : ".$name." </p>
				   <p>Sku : ".$sku." </p>
				   <p>Paid Price : ".$paid_price." </p>
				  <p>Jasa Pengiriman : ".$shipment_provider." </p>
				   <p>Ongkos Kirim: ".$shipping_amount." </p>";
				 $header = "From:no_replay@twinzahra.com \r\n";
				 $header .= "Cc:no_replay@twinzahra.com \r\n";
				 $header .= "MIME-Version: 1.0\r\n";
				 $header .= "Content-type: text/html\r\n";
				 
				 $retval = mail ($to,$subject,$message,$header);
         
				if( $retval == true ) {
				$return ="Pesanan telah dirubah menjadi Selesai";

				}else {
				$return ="Pesanan telah dirubah menjadi Selesai!";
	
				}  
				
                          
                
				
				//jika produk gagal	
                } else {
                  $return = array(
                       "status" => 404,
					   "order_id" => $order_id,
						"message" => "ERROR"
                    );
                }
				
			}
			

                } else {
				$return = array(
				"status" => 404,
				"message" => "No Order tidak ditemukan"
           );
      }
					
			
		//Jika user id tidak ada//	
       } else {
          $return = array(
               "status" => 504,
              "message" => "ERROR"
           );
      }
        echo json_encode($return);
    }
				
				if ($content == "get_order_items") {
					$modeHeader = 0;
                    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
                    //$user_id = 5;
					$order_id =$post['order_id'];
					//$order_id =10;
					$page = null;
					
                    $limit = 0;
					
					 if (isset($post['UserID'])) {
                        $user_id = $post['UserID'];
                    }
					
					
                    if (isset($post['Page'])) {
                        $page = $post['Page'];
                    }
					

					//Get data from database
                    $getData = $db->getDataOrderItems($user_id, $page, $limit , $order_id);
						
					//Get data from lazada	
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://sellercenter.twinzahra.com/api/lazada.php?request=get_order_items');
					$payload = json_encode( array( "order_id"=> $order_id,
													"UserID"=> "5") );
					curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
					curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$lazadacontent = curl_exec($ch);
					curl_close($ch);
					$rowLazada=json_decode($lazadacontent,true);
					
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
							
							
						}else if ($rowLazada != null) {
					

							$total = count($rowLazada);	

                            $return = array(
                                "status" => 200,
								"total_rows" => $total,
                                "message" => "Berhasil",
                                "data" => $rowLazada
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
				
							if ($content == "cek_history") {
					$modeHeader = 0;
                    $post = json_decode(file_get_contents("php://input"), true);

					$order_id = $post['order_id'];
	
	//$order_id = $post['order_id'];
	
                 

                        $getData = $db->getDataHistory($order_id);
                        if ($getData != null) {

                            while ($row = $getData->fetch_assoc()) {										
						
                                $rows = $row;				
	
							
                            }
							
	

                            $total = mysqli_num_rows($getData);


                            $return = array(
                                "status" => 200,
                                "message" => "Berhasil Ditemukan",
                                "total_rows" => $total,
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 404,
								"total_rows" => 0,
                                "message" => "Data produk tidak ditemukan",
								"data" => null
                            );
                        }
                  

                    echo json_encode($return);
                }
				
					if ($content == "get_rts") {
					$modeHeader = 0;
                    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
                    $user_id = 5;
					$status_id = 2;
					
					$page = null;
					
                    $limit = 0;
					
					 if (isset($post['UserID'])) {
                        $user_id = $post['UserID'];
                    }
					
					
                    if (isset($post['Page'])) {
                        $page = $post['Page'];
                    }
					
					if (isset($post['status_id'])) {
                        $status_id = $post['status_id'];
                    }
					
				
					
					
						

                        $getData = $db->getDataRts($user_id, $page, $limit , $status_id);
						
					 if ($getData != null) {


                            while ($row = $getData->fetch_assoc()) {										
						
                                $rows[] = $row;				
	
								
                            }
							

							$result=json_decode($content,true);
							//get new orders lazada

	
                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "data" => $rows
                            );
							
	
								
                        } else {
                            $return = array(
                                "status" => 200,
                                "message" => "Belum ada Data",
								"data" => []
                            );
                        }
                  

                    echo json_encode($return);
                }		
				
				
					if ($content == "get_rts_items") {
					$modeHeader = 0;
                    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
                    //$user_id = 5;
					$order_id =$post['order_id'];
					//$order_id =10;
					$page = null;
					
                    $limit = 0;
					
					 if (isset($post['UserID'])) {
                        $user_id = $post['UserID'];
                    }
					
					
                    if (isset($post['Page'])) {
                        $page = $post['Page'];
                    }
					

					//Get data from database
                    $getData = $db->getDataRtsItems($user_id, $page, $limit , $order_id);
						
	
					
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
				
				
				
				
				 if ($content == "created_order") {
        $modeHeader = 0;   
        $post = json_decode(file_get_contents("php://input"), true);
		
		//Get Value For History Order from post
       
	
		
					$marketplace = $post['marketplace']  ;
					$order_id = $post['order_id']  ;
					$merchant_name = $post['merchant_name']  ;
					$customer_first_name = $post['name']  ;
					$shipping_provider = $post['shipping_provider']  ;
					$tracking_code = $post['tracking_code']  ;
					$shipping_amount = $post['shipping_amount']  ;
					$tracking_code_pre = $post['tracking_code_pre']  ;
					$remarks = $post['remark']  ;
					$action = $post['action']  ;
				
					//$order_id = 1;
					$user_id = 5;
					//$marketplace = "Lazada";
				
				//$order_id = 441008605032192 ;
				//$shipping_provider = "LEX ID" ;
				///$delivery_type = "dropship" ;
				//$merchant_name = "Twinzahra Shop" ;
				
				
					if (isset($user_id) && isset($order_id) && isset($marketplace) ) {
						
					$getDataCartDetail = $db->checkCartDetailByUserID($user_id);
						
					if ($getDataCartDetail != null) {

                    while ($row = $getDataCartDetail->fetch_assoc()) {										
						
                    $rows = $row;				
	
					$variant_details[] = array(
					"order_id" =>$order_id ,
					"name" =>$rows['ProductName'],
					"sku" =>$rows['SKU'] ,
					"paid_price" =>$rows['PriceSale'] ,
					"item_price" =>$rows['PriceSale'] ,
					"Quantity" =>$rows['Quantity'] ,
					"variation" =>$rows['ProductVariantName'] . $rows['ProductVariantDetailName'] ,
					"shipment_provider" =>$shipping_provider, 
					"tracking_code_pre" =>$tracking_code_pre, 
					"tracking_code" =>$tracking_code, 
					"shipping_amount" =>$shipping_amount, 
					"product_main_image" =>$rows['ImageProductVariantName'] ,
					"status" =>2
					);
					
                    }
					}
					
					}else{

	
					$chItems = curl_init();
					curl_setopt($chItems, CURLOPT_URL, 'https://sellercenter.twinzahra.com/api/lazada.php?request=get_order');
					$payloadItem = json_encode( array( "order_id"=> $order_id,
					"merchant_name"=> $merchant_name) );
					curl_setopt( $chItems, CURLOPT_POSTFIELDS, $payloadItem );
					curl_setopt( $chItems, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
					curl_setopt($chItems, CURLOPT_RETURNTRANSFER, 1);
					$contentItem = curl_exec($chItems);
					curl_close($chItems);
					
					$resultItem=json_decode($contentItem,true);
					$order_number = $resultItem['order_number'] ;	
					$marketplace = $resultItem['marketplace'] ;
					$branch_number = $resultItem['branch_number'] ;
					$warehouse_code = $resultItem['warehouse_code'] ;
					$customer_first_name = $resultItem['customer_first_name'] ;
					$customer_last_name = $resultItem['customer_last_name'] ;
					$price = $resultItem['price'] ;
					$items_count = $resultItem['items_count'] ;
					$payment_method = $resultItem['payment_method'] ;
					$voucher = $resultItem['voucher'] ;
					$voucher_code = $resultItem['voucher_code'] ;
					$voucher_platform = $resultItem['voucher_platform'] ;
					$voucher_seller = $resultItem['voucher_seller'] ;
					$gift_option = $resultItem['gift_option'] ;
					$gift_message = $resultItem['gift_message'] ;
					$shipping_fee = $resultItem['shipping_fee'] ;
					$shipping_fee_discount_seller = $resultItem['shipping_fee_discount_seller'] ;
					$shipping_fee_discount_platform = $resultItem['shipping_fee_discount_platform'] ;
					$promised_shipping_times = $resultItem['promised_shipping_times'] ;
					$national_registration_number = $resultItem['national_registration_number'] ;
					$tax_code = $resultItem['tax_code'] ;
					$extra_attributes = $resultItem['extra_attributes'] ;
					$remarks = $resultItem['remarks'] ;
					$delivery_info = $resultItem['delivery_info'] ;
					$statuses = $resultItem['statuses'] ;
					$created_at = $resultItem['created_at'] ;
					$updated_at = $resultItem['updated_at'] ;
					
					//echo json_encode($order_number);die;
					$chItems = curl_init();
					curl_setopt($chItems, CURLOPT_URL, 'https://sellercenter.twinzahra.com/api/orders.php?request=get_order_items');
					$payloadItem = json_encode( array( "order_id"=> $order_id ) );
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
					$order_item_ids[] = $DataOrderItems['order_item_id'];
					//Set array untuk variable history orders
					$variant_details[] = array(
					
					"order_item_id" =>$DataOrderItems['order_item_id'] ,
					"order_id" =>$DataOrderItems['order_id'] ,
					"purchase_order_id" =>$DataOrderItems['purchase_order_id'] ,
					"purchase_order_number" =>$DataOrderItems['purchase_order_number'],
					"invoice_number" => $DataOrderItems['invoice_number'],
					"sla_time_stamp" => $DataOrderItems['sla_time_stamp'],
					"package_id" =>$DataOrderItems['package_id'],
					"shop_id" =>$DataOrderItems['shop_id'],
					"order_type" => $DataOrderItems['order_type'],
					"shop_sku" =>$DataOrderItems['shop_sku'] ,
					"sku" =>$DataOrderItems['sku'],
					"name" =>$DataOrderItems['name'],
					"variation" =>$DataOrderItems['variation'],
					"item_price" =>$DataOrderItems['item_price'],
					"paid_price" =>$DataOrderItems['paid_price'],
					"currency" =>$DataOrderItems['currency'],
					"tax_amount" => $DataOrderItems['tax_amount'],
					"product_main_image" =>$DataOrderItems['product_main_image'],
					"product_detail_url" =>$DataOrderItems['product_detail_url'],
					"shipment_provider" =>$DataOrderItems['shipment_provider'],
					"tracking_code_pre" =>$DataOrderItems['tracking_code_pre'],
					"tracking_code" =>$DataOrderItems['tracking_code'],
					"shipping_type" =>$DataOrderItems['shipping_type'],
					"shipping_provider_type" =>$DataOrderItems['shipping_provider_type'],
					"shipping_fee_original" =>$DataOrderItems['shipping_fee_original'],
					"shipping_service_cost " =>$DataOrderItems['shipping_service_cost'],
					"shipping_fee_discount_seller" =>$DataOrderItems['shipping_fee_discount_seller'],
					"shipping_amount" =>$DataOrderItems['shipping_amount'],
					"is_digital" =>$DataOrderItems['is_digital'],
					"voucher_amount" =>$DataOrderItems['voucher_amount'],
					"voucher_seller" =>$DataOrderItems['voucher_seller'],
					"voucher_code_seller" =>$DataOrderItems['voucher_code_seller'],
					"voucher_code" =>$DataOrderItems['voucher_code'],
					"voucher_code_platform" =>$DataOrderItems['voucher_code_platform'],
					"voucher_platform" =>$DataOrderItems['voucher_platform'],
					"order_flag" =>$DataOrderItems['order_flag'],
					"promised_shipping_time" =>$DataOrderItems['promised_shipping_time'],
					"digital_delivery_info" =>$DataOrderItems['digital_delivery_info'],
					"extra_attributes" =>$DataOrderItems['extra_attributes'],
					"cancel_return_initiator" =>$DataOrderItems['cancel_return_initiator'],
					"reason" =>$DataOrderItems['reason'],
					"reason_detail" =>$DataOrderItems['reason_detail'],
					"stage_pay_status" =>$DataOrderItems['stage_pay_status'],
					"warehouse_code" =>$DataOrderItems['warehouse_code'],
					"return_status" =>$DataOrderItems['return_status'],
					"status" =>$DataOrderItems['status'],
					"created_at" =>$DataOrderItems['created_at'],
					"updated_at" =>$DataOrderItems['updated_at']
					
					);
							
					}
					}	
		
       if (isset($user_id) && isset($order_id) ) {

			
			
			//cek user id
            $getData = $db->checkHistoryOrderByOrder($order_id , $user_id);
			
			if ($getData == null) {
				
				//print_r ($order_number);die;
					
				//Isi History Orders
			
				$createHistoryOrders = $db->createHistoryOrders(
					$order_id,
					$order_number,
					$user_id,
					$marketplace,
					$branch_number,
					$warehouse_code,
					$customer_first_name,
					$customer_last_name,
					$price,
					$items_count,
					$payment_method,
					$voucher,
					$voucher_code,
					$voucher_platform,
					$voucher_seller,
					$gift_option,
					$gift_message,
					$shipping_fee,
					$shipping_fee_discount_seller,
					$shipping_fee_discount_platform,
					$promised_shipping_times,
					$national_registration_number,
					$tax_code,
					$extra_attributes,
					$remarks,
					$delivery_info,
					$statuses,
					$created_at,
					$updated_at);
			 

                  
			
               //jika produk berhasil
			  if ($createHistoryOrders == true) {
				  
			
				$variant_details = json_encode($variant_details, true);
				 
				$createHistoryOrderDetails = $db->createHistoryOrderDetails(
				$order_id , $variant_details);
				 
		
               				 
				if ($createHistoryOrderDetails == true) {
				$qty = 1;		
				$updateStokBySKU = $db->updateStokBySKU($variant_details , $qty);
				
				$deleteCartDetailByUser = $db->deleteCartDetailByUser($user_id);
				   
				// $chItems = curl_init();
					//curl_setopt($chItems, CURLOPT_URL, 'https://sellercenter.twinzahra.com/api/lazada.php?request=sync_marketplace');
					//$payloadItem = json_encode( array( "order_id"=> $order_id ) );
					//curl_setopt( $chItems, CURLOPT_POSTFIELDS, $payloadItem );
					//curl_setopt( $chItems, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
					//curl_setopt($chItems, CURLOPT_RETURNTRANSFER, 1);
					//$contentItem = curl_exec($chItems);
					//curl_close($chItems);

					//mengubah data json menjadi data array asosiatif
					//$resultItem=json_decode($contentItem,true);
					
				 $return = array(
                             "status" => 200,
							"action" => "createHistoryOrderDetails",
                            "message" => "Pesanan berhasil di konfirmasi",
							"data" => $createHistoryOrderDetails
                         );
						   
						   
                }else{
					
							$return = array(
                             "status" => 404,
							"action" => "createHistoryOrderDetails",
                             "message" => "Gagal Menambahkan Variant Produk",
							"data" => []
                           );
					
				}
				
				//jika produk gagal	
                } else {
                  $return = array(
                       "status" => 404,
					   	"action" => " createHistoryOrders",
                     "message" => "Gagal"
                    );
                }
				
				 
				
				
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Pesanan sudah pernah di konfirmasi"
                        );
                    }
					
			
		//Jika user id tidak ada//	
       } else {
           $return = array(
               "status" => 404,
               "message" => "Order Number belum terisi"
            );
        }
        echo json_encode($return);
    }
	
			
					
	 if ($content == "accept_order") {
        $modeHeader = 0;   
        $post = json_decode(file_get_contents("php://input"), true);
		
				
				
			
				

				$order_id = $post['order_id'] ;
				$user_id = 5;	
				$shipping_provider =$post['shipping_provider'];
				$delivery_type = $post['delivery_type'];
				$merchant_name = $post['merchant_name'];
				
				
				//$order_id = 441008605032192 ;
				///$shipping_provider = "LEX ID" ;
				//$delivery_type = "dropship" ;
				//$merchant_name = "Twinzahra Shop" ;
				
		
				
       if (isset($order_id) && isset($user_id)) {

				///Variable variant details
				$variant_details = array();
						
					$chItems = curl_init();
					curl_setopt($chItems, CURLOPT_URL, 'https://sellercenter.twinzahra.com/api/orders.php?request=get_order_items');
					$payloadItem = json_encode( array( "order_id"=> $order_id ) );
					curl_setopt( $chItems, CURLOPT_POSTFIELDS, $payloadItem );
					curl_setopt( $chItems, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
					curl_setopt($chItems, CURLOPT_RETURNTRANSFER, 1);
					$contentItem = curl_exec($chItems);
					curl_close($chItems);

					//mengubah data json menjadi data array asosiatif
					$resultItem=json_decode($contentItem,true);
					
					
				 foreach($resultItem['data'] as $DataOrderItems)

				{
					
				
					
					$order_item_ids[] = $DataOrderItems['order_item_id'];
				
					
					$order_item_id = $DataOrderItems['order_item_id'];
			
				
				
			
			}
				
			//$order_item_ids = array(
					//	$order_item_ids
				//	);
			$order_item_ids = json_encode($order_item_ids, true);
			
				

			
			 
			//cek order id and user id
            $getData = $db->checkHistoryOrderByOrder($order_id , $user_id);
			

			
			//$getData = false;
			//print_r ($order_item_ids);die;
			//print_r ($order_item_id);die;
			if ($getData == true) {

                    

			$create = $db->acceptOrders($user_id, $order_id);
	
               //jika produk berhasil
			  if ($create) {
				   
				 $return = array(
                             "status" => 200,
								"total_rows" => 1,
								"message" => "Pesanan telah dirubah menjadi siap kirim",
								"data" => []
                           );
                
				
				//jika produk gagal	
                } else {
                  $return = array(
                       "status" => 404,
                     "message" => "Pesanan gagal di setting"
                    );
                }
					
		
				
				
               } else {
					
					if($shipping_provider != null && $delivery_type != null ) {
						
						
					//Set Pick						
					$chpick = curl_init();
					curl_setopt($chpick, CURLOPT_URL, 'https://sellercenter.twinzahra.com/api/lazada.php?request=set_pick');
					$payloadItem = json_encode( array( "order_item_ids"=> $order_item_ids,
					"shipping_provider"=> $shipping_provider,
					"delivery_type"=> $delivery_type,
					"user_id"=> $user_id,
					"merchant_name"=> $merchant_name	) );
					curl_setopt( $chpick, CURLOPT_POSTFIELDS, $payloadItem );
					curl_setopt( $chpick, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
					curl_setopt($chpick, CURLOPT_RETURNTRANSFER, 1);
					$contentpick = curl_exec($chpick);					
					curl_close($chpick);
					
					$resultpick=json_decode($contentpick,true);
					//print_r ($resultpick);die;
					//Mencari tracking number
					foreach ($resultpick['data'] as $items) {
						
						$tracking_number = $items['tracking_number'];
					}
					

					
					//print_r ($order_item_id);die;
					
						//jika set pick berhasil
						if ($resultpick['status'] == 200) {
							
					//Set Invoice						
					$chInvoice = curl_init();
					curl_setopt($chInvoice, CURLOPT_URL, 'https://sellercenter.twinzahra.com/api/lazada.php?request=set_invoice');
					$payloadInvoice = json_encode( array( "order_item_id"=> $order_item_id,
					"user_id"=> $user_id,
					"merchant_name"=> $merchant_name		
					) );
					curl_setopt( $chInvoice, CURLOPT_POSTFIELDS, $payloadInvoice );
					curl_setopt( $chInvoice, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
					curl_setopt($chInvoice, CURLOPT_RETURNTRANSFER, 1);
					$contentInvoice = curl_exec($chInvoice);					
					curl_close($chInvoice);
					
					$resultInvoice=json_decode($contentInvoice,true);
					

					//print_r ($resultInvoice);die;

					
					
					
						//jika set invoice berhasil
						if ($resultInvoice['status'] == 200) {
							
						
												//Set Ready to Ship						
					$chrts = curl_init();
					curl_setopt($chrts, CURLOPT_URL, 'https://sellercenter.twinzahra.com/api/lazada.php?request=set_rts');
					$payloadRts = json_encode( array( "order_item_ids"=> $order_item_ids,
					"shipping_provider"=> $shipping_provider,
					"delivery_type"=> $delivery_type,
					"tracking_number"=> $tracking_number,
					"user_id"=> $user_id,
					"merchant_name"=> $merchant_name	) );
					curl_setopt( $chrts, CURLOPT_POSTFIELDS, $payloadRts );
					curl_setopt( $chrts, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
					curl_setopt($chrts, CURLOPT_RETURNTRANSFER, 1);
					$contentRts = curl_exec($chrts);
					curl_close($chrts);

					//mengubah data json menjadi data array asosiatif
					$resultRts=json_decode($contentRts,true);
					
					//print_r ($resultRts);die;
					//jika set RTS sukse
					
					if ($resultRts['status'] == 200) {
						
							// Simpan data ke database
							$data1 = array(
							'order_id'=> $order_id,
							'merchant_name'=> $merchant_name,
							'user_id'=> $user_id 
						);

						$options = array(
						'http' => array(
						'method'  => 'POST',
						'content' => json_encode( $data1 ),
						'header'=>  "Content-Type: application/json\r\n" .
						"Accept: application/json\r\n"
							)
						);

						$context  = stream_context_create( $options );
						$result = file_get_contents( "https://sellercenter.twinzahra.com/api/orders.php?request=created_order", false, $context );
						$response = json_decode($result, true );
						
						//print_r ($response);die;
						
						//jika created order berhasil
						if ($response['status'] == 200) {
							
							$return = array(
                             "status" => 200,
								"total_rows" => 1,
								"message" => $response['message'],
								"data" => $response
                           );
                
				
				//jika tidak
							} else {
							$return = array(
							"status" => 404,
							"message" => $response['message']
							);
							}
						
					}else{
				//Jika Set RTS Gagal
				
								$return = array(
								"status" => 404,
								"total_rows" => 0,
								"message" => $resultRts['message'],
								"data" => $resultRts
								
								   );

					}						
						
					
							

						 //jika respond invoice error  
						}else{
							
							
								$return = array(
								"status" => 404,
								"total_rows" => 9,
								"message" => $resultInvoice['message'],
								"data" => resultInvoice
                           );
							
						}

						 //jika respond invoice error  
						}else{
							
							
								$return = array(
								"status" => 404,
								"total_rows" => 100,
								"message" => $resultpick['message'],
								"data" => $resultpick
                           );
							
						}
						
					}else{
						
						$return = array(
                             "status" => 404,
								"total_rows" => 1,
								"message" => "Kurir belum di pilih",
								"data" => []
                           );
						
					}
						

					
							
						


                      
                    }
					
			
		//Jika user id tidak ada//	
       } else {
          $return = array(
               "status" => 504,
              "message" => "ERROR"
           );
      }
        echo json_encode($return);
    }
	
	
				
			
				
    // ---------------------------------------- API that need token below ------------------------------------------- //
    if ($modeHeader == 1) {
        //Check header token
        $token_header = $_SERVER['HTTP_TOKEN'];
        $userid_header = $_SERVER['HTTP_USER_ID'];
        $version_code_header = $_SERVER['HTTP_VERSION_CODE'];
        $version_name_header = $_SERVER['HTTP_VERSION_NAME'];
        $version_check = 1;
		
		
		
				
				

        // if($current_app_version_code['Value'] == $version_code_header){
        // 	$version_check = 1;
        // }

        // $data = [
        // 	"token_header" 				=> $token_header,
        // 	"userid_header" 		    => $userid_header,
        // 	"version_code_header" 		=> $version_code_header
        // ];

        // echo json_encode($data);
        $nurse_type_header = isset($_SERVER['HTTP_NURSE_TYPE']) ? $_SERVER['HTTP_NURSE_TYPE'] : 1;

        if (isset($token_header) && isset($userid_header) && $token_header != "" && $userid_header != "" && $version_check == 1) {

            $checkLoginGoogle = $db->getUserByID($userid_header);

            $loginGoogle = 0;
            if ($checkLoginGoogle) {
                $checkLoginGoogle = $checkLoginGoogle->fetch_assoc();
                if (isset($checkLoginGoogle['GoogleUserID'])) {
                    $loginGoogle = 1;
                }
            }

            if ($loginGoogle == 0) {
                $checkToken = $db->checkToken($token_header, $userid_header);
            } elseif ($loginGoogle == 1) {
                $checkToken = $db->checkToken2($token_header, $userid_header);
            }
            if ($checkToken) {
               
		
				
			
				
				
				

		
				
				


            } else {
                //Token not match !!!
                $return = array(
                    "status" => 406,
                    "message" => "Anda sudah login di device lain!"
                );

                echo json_encode($return);
            }
        } elseif ($version_check == 0) {
            $return = array(
                "status" => 407,
                "force_update" => $force_update,
                "message" => "Versi app terbaru sudah ada di playstore, harap update app terbaru !"
            );

            echo json_encode($return);
        } else {
            $return = array(
                "status" => 406,
                "message" => "Oops sesi anda sudah habiss!"
            );

            echo json_encode($return);
        }
    }

} else {
    //Aha, what you're looking for !!!
    $return = array(
        "status" => 404,
        "message" => "Method Not Founde!"
    );

    echo json_encode($return);
}

?>
