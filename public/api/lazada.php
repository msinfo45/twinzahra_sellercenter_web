<?php

include "../config/lazada/LazopSdk.php";

include "../config/db_connection.php";
include "../config/config_type.php";

$url='https://api.lazada.co.id/rest';

		
$rowsLazada = array();
$rows = [];
    $content = $_GET['request'];
    if (isset($content) && $content != "") {
        
          //Load Models
    include "../config/model.php";

    $db = new Model_user();
        
        
		if ($content == "get_seller") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    //$user_id = $post['UserID'];
					$user_id = 5;
				
					$merchant_name = null;	
	
if (isset($post['merchant_name'])) {
	$merchant_name = $post['merchant_name'];
}

				if (isset($user_id)) {

				$getDataLazada = $db->getDataLazada($user_id , $merchant_name);

				if ($getDataLazada != null) {
							
							
                    while ($rowLazada = $getDataLazada->fetch_assoc()) {	
					$rows[] = $rowLazada;
				
					
					}
					
					foreach ($rows as $obj) {
							
					$appkey =  $obj['AppKey'];
					$appSecret =  $obj['AppSecret'];
					$accessToken =  $obj['AccessToken'];	
					$merchant_name =  $obj['merchant_name'];
					
			
					
					$c = new LazopClient($url,$appkey,$appSecret );
					$request = new LazopRequest('/seller/get','GET');
					$jdecode=json_decode($c->execute($request, $accessToken));
					$data=$jdecode->data;
					
					$dataReturn[] = $data;
			
					}
					
							
						
					
					
					
					
				 $return= array(
                            "status" => 200,
                            "data" => $dataReturn
                        );
				
		
					
				}			
         
                    
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Oops sepertinya ada yang salah!"
                        );
                    }
					

	                   echo json_encode($return);
	


                }
				
				if ($content == "get_orders") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    //$user_id = $post['UserID'];
					$user_id = 5;
				
					$merchant_name = null;	
	
					if (isset($post['merchant_name'])) {
						$merchant_name = $post['merchant_name'];
					}		
					
					if (isset($user_id)) {

				$getDataLazada = $db->getDataLazada($user_id, $merchant_name);

				if ($getDataLazada != null) {
							
                    while ($rowLazada = $getDataLazada->fetch_assoc()) {										
					$rows[] = $rowLazada;
			
					}
						
				
					foreach ($rows as $obj) {
						
			
					
					//if ($objActives == 1 ) {
						
					$appkey =  $obj['AppKey'];
					$appSecret =  $obj['AppSecret'];
					$accessToken =  $obj['AccessToken'];	
					$merchant_name =  $obj['merchant_name'];
				
			
					
						
					$c = new LazopClient($url,$appkey,$appSecret );
					$request = new LazopRequest('/orders/get','GET');
					//$request->addApiParam('status','ready_to_ship');
					$request->addApiParam('status','pending');
					//$request->addApiParam('created_before','2018-02-10T16:00:00+08:00');
					$request->addApiParam('created_after','2020-01-01T09:00:00+08:00');
					//$request->addApiParam('update_before','2018-02-10T16:00:00+08:00');
					$request->addApiParam('sort_direction','DESC');
					$request->addApiParam('offset','0');
					//$request->addApiParam('limit','10');
					$request->addApiParam('update_after','2020-01-01T09:00:00+08:00');
					$request->addApiParam('sort_by','updated_at');
					//var_dump($c->execute($request, $access_token));
					$jdecode=json_decode($c->execute($request, $accessToken));
					//$jencode=json_encode($jdecode, true);
					
					//$dataReturn[] = $data;
			
				//	echo json_encode($jdecode);die;
						
					
						
                      
					
					
					//cek token expire
					if ($jdecode->code == "0") {
						
					$order=$jdecode->data->orders;

					$count=$jdecode->data->count;
					//$count=json_encode(count($order));
				
				
					//echo $count;die;
					
				//if ($count != "0" ){
					
					
					
				
			  foreach($order as $datas)

				{

			   
		$order_id = $datas->order_id;
		$order_number = $datas->order_number;
		$branch_number= $datas->branch_number;
		$warehouse_code= $datas->warehouse_code;
		$customer_first_name= $datas->customer_first_name;
		$customer_last_name= $datas->customer_last_name;
		$price= $datas->price;
		$items_count= $datas->items_count;
		$payment_method= $datas->payment_method;
		$voucher= $datas->voucher;
		$voucher_code= $datas->voucher_code;
		$voucher_platform= $datas->voucher_platform;
		$voucher_seller= $datas->voucher_seller;
		$gift_option= $datas->gift_option;
		$gift_message= $datas->gift_message;
		$shipping_fee= $datas->shipping_fee;
		$shipping_fee_original= $datas->shipping_fee_original;
		$shipping_fee_discount_seller= $datas->shipping_fee_discount_seller;
		$shipping_fee_discount_platform= $datas->shipping_fee_discount_platform;
		$promised_shipping_times= $datas->promised_shipping_times;
		$national_registration_number= $datas->national_registration_number;
		$tax_code= $datas->tax_code;
		$extra_attributes= $datas->extra_attributes;
		$remarks= $datas->remarks;
		$delivery_info= $datas->delivery_info;
		$statuses= $datas->statuses;
		$created_at= $datas->created_at;
		$updated_at= $datas->updated_at;
		//$address_shipping=$datas>address_shipping;
		$address_billing=$datas->address_billing;
		
					

				
		  		$return[] = array(
					"order_id" => $order_id,
					"order_number" => $order_number,
					"marketplace" => "LAZADA",
					"merchant_name" => $merchant_name,
					"branch_number"=>$branch_number,
					"warehouse_code"=>$warehouse_code,
					"customer_first_name" => $customer_first_name,
					"customer_last_name" => $customer_first_name,
					"price"=>$price,
					"items_count"=>$items_count,
					"payment_method"=>$payment_method,
					"voucher"=>$voucher,
					"voucher_code"=>$voucher_code,
					"voucher_platform"=>$voucher_platform,
					"voucher_seller"=>$voucher_seller,
					"gift_option"=>$gift_option,
					"gift_message"=>$gift_message,
					"shipping_fee"=>$shipping_fee,
					"shipping_fee_discount_seller"=>$shipping_fee_discount_seller,
					"shipping_fee_discount_platform"=>$shipping_fee_discount_platform,
					"promised_shipping_times"=>$promised_shipping_times,
					"national_registration_number"=>$national_registration_number,
					"tax_code"=>$tax_code,
					"extra_attributes"=>$extra_attributes,
					"remarks"=>$remarks,
					"delivery_info"=>$delivery_info,
					"statuses"=>$statuses,
					"created_at"=>$created_at,
					"updated_at"=>$updated_at,
					
				///	"address_shipping"=>array(
						//			"order_id"=> $order_id,
						//			"first_name"=> $address_shipping->first_name,
						//			"last_name"=> $address_shipping->last_name,
							//		"country"=> $address_shipping->country,
							//		"phone"=> $address_shipping->phone,
							//		"phone2"=> $address_shipping->phone2,
							//		"address1"=> $address_shipping->address1,
							//		"address2"=> $address_shipping->address2,
							//		"address3"=> $address_shipping->address3,
								//	"address4"=> $address_shipping->address4,
								//	"address5"=> $address_shipping->address5,
								//	"city"=> $address_shipping->city,
								//	"post_code"=> $address_shipping->post_code
					//),
					
					"address_billing"=>array(
									"order_id"=> $order_id,
									"first_name"=> $address_billing->first_name,
									"last_name"=> $address_billing->last_name,
									"country"=> $address_billing->country,
									"phone"=> $address_billing->phone,
									"phone2"=> $address_billing->phone2,
									"address1"=> $address_billing->address1,
									"address2"=> $address_billing->address2,
									"address3"=> $address_billing->address3,
									"address4"=> $address_billing->address4,
									"address5"=> $address_billing->address5,
									"city"=> $address_billing->city,
									"post_code"=> $address_billing->post_code
					
					)
					

                   );
				   
				   }
				   
				//}else{
					
					 //$return = array(
                      //      "status" => 404,
                          //  "message" => "Belum ada data"
                      //  );
					
				
	
			//	}
				
						
					}else{
						
						 $return = array(
                            "status" => 404,
                            "message" => $jdecode->message
                        );
						
					}
					
					//}else {
                      //  $return = array(
                       //     "status" => 404,
                        //    "message" => "Akun lazada belum ada yang aktif"
                        //);
                    //}
					
					}
				
			  
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Akun lazada belum diatur"
                        );
                    }
					   
		
							
					
         

			
                    
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Oops sepertinya ada yang salah!"
                        );
                    }

                    echo json_encode($return);
                }
				
				if ($content == "get_order") {
                    $post = json_decode(file_get_contents("php://input"), true);
					
                    $order_id = $post['order_id'];
					$merchant_name = $post['merchant_name'];
					//$order_id = 438142151846411 ; 
					$user_id = 5;
				
					$order_id = 441008605032192 ;
				$shipping_provider = "LEX ID" ;
				$delivery_type = "dropship" ;
				$merchant_name = "Twinzahra Shop"; 
				
					if (isset($user_id)) {

				$getDataLazada = $db->getDataLazadaByMerchant($user_id , $merchant_name);

				if ($getDataLazada != null) {
							
                    while ($rowLazada = $getDataLazada->fetch_assoc()) {										
					$rows[] = $rowLazada;
			
					}
						
				
					foreach ($rows as $obj) {
						
					
			
						
					
					
					//if ($objActives == 1 ) {
						
					$appkey =  $obj['AppKey'];
					$appSecret =  $obj['AppSecret'];
					$accessToken =  $obj['AccessToken'];	
					$merchant_name =  $obj['merchant_name'];
				
			
					
						
					$c = new LazopClient($url,$appkey,$appSecret );
					$request = new LazopRequest('/order/get','GET');
					$request->addApiParam('order_id',$order_id);;
					$jdecode=json_decode($c->execute($request, $accessToken));
					//$jencode=json_encode($jdecode, true);
					
					//$dataReturn[] = $data;
			
					
						
					
						
                      
					
					
					//cek token expire
					if ($jdecode->code == "0") {
						
					$data=$jdecode->data;
					$address_shipping=$data ->address_shipping;
					$address_billing=$data->address_billing;
					$count=$data->count;
					
				
				
				


			

		
		$order_id = $data->order_id;
		$order_number = $data->order_number;
		$branch_number= $data->branch_number;
		$warehouse_code= $data->warehouse_code;
		$customer_first_name= $data->customer_first_name;
		$customer_last_name= $data->customer_last_name;
		$price= $data->price;
		$items_count= $data->items_count;
		$payment_method= $data->payment_method;
		$voucher= $data->voucher;
		$voucher_code= $data->voucher_code;
		$voucher_platform= $data->voucher_platform;
		$voucher_seller= $data->voucher_seller;
		$gift_option= $data->gift_option;
		$gift_message= $data->gift_message;
		$shipping_fee= $data->shipping_fee;
		$shipping_fee_original= $data->shipping_fee_original;
		$shipping_fee_discount_seller= $data->shipping_fee_discount_seller;
		$shipping_fee_discount_platform= $data->shipping_fee_discount_platform;
		$promised_shipping_times= $data->promised_shipping_times;
		$national_registration_number= $data->national_registration_number;
		$tax_code= $data->tax_code;
		$extra_attributes= $data->extra_attributes;
		$remarks= $data->remarks;
		$delivery_info= $data->delivery_info;
		$statuses= $data->statuses;
		$created_at= $data->created_at;
		$updated_at= $data->updated_at;
		
		
					

				
		  		$return = array(
					"order_id" => $order_id,
					"order_number" => $order_number,
					"marketplace" => "LAZADA",
					"merchant_name" => $merchant_name,
					"branch_number"=>$branch_number,
					"warehouse_code"=>$warehouse_code,
					"customer_first_name" => $customer_first_name,
					"customer_last_name" => $customer_first_name,
					"price"=>$price,
					"items_count"=>$items_count,
					"payment_method"=>$payment_method,
					"voucher"=>$voucher,
					"voucher_code"=>$voucher_code,
					"voucher_platform"=>$voucher_platform,
					"voucher_seller"=>$voucher_seller,
					"gift_option"=>$gift_option,
					"gift_message"=>$gift_message,
					"shipping_fee"=>$shipping_fee,
					"shipping_fee_discount_seller"=>$shipping_fee_discount_seller,
					"shipping_fee_discount_platform"=>$shipping_fee_discount_platform,
					"promised_shipping_times"=>$promised_shipping_times,
					"national_registration_number"=>$national_registration_number,
					"tax_code"=>$tax_code,
					"extra_attributes"=>$extra_attributes,
					"remarks"=>$remarks,
					"delivery_info"=>$delivery_info,
					"statuses"=>$statuses,
					"created_at"=>$created_at,
					"updated_at"=>$updated_at,
					
					"address_shipping"=>array(
									"order_id"=> $order_id,
									"first_name"=> $address_shipping->first_name,
									"last_name"=> $address_shipping->last_name,
									"country"=> $address_shipping->country,
									"phone"=> $address_shipping->phone,
									"phone2"=> $address_shipping->phone2,
									"address1"=> $address_shipping->address1,
									"address2"=> $address_shipping->address2,
									"address3"=> $address_shipping->address3,
									"address4"=> $address_shipping->address4,
									"address5"=> $address_shipping->address5,
									"city"=> $address_shipping->city,
									"post_code"=> $address_shipping->post_code
					),
					
					"address_billing"=>array(
									"order_id"=> $order_id,
									"first_name"=> $address_billing->first_name,
									"last_name"=> $address_billing->last_name,
									"country"=> $address_billing->country,
									"phone"=> $address_billing->phone,
									"phone2"=> $address_billing->phone2,
									"address1"=> $address_billing->address1,
									"address2"=> $address_billing->address2,
									"address3"=> $address_billing->address3,
									"address4"=> $address_billing->address4,
									"address5"=> $address_billing->address5,
									"city"=> $address_billing->city,
									"post_code"=> $address_billing->post_code
					
					)
					

                   );
		
	
				
				
						
					}else{
						
						 $return = array(
                            "status" => 404,
                            "message" => $jdecode->message
                        );
						
					}
					
					//}else {
                      //  $return = array(
                       //     "status" => 404,
                        //    "message" => "Akun lazada belum ada yang aktif"
                        //);
                    //}
					
					}
				
			  
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Akun lazada belum diatur"
                        );
                    }
					   
		
							
					
         

			
                    
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Oops sepertinya ada yang salah!"
                        );
                    }

                    echo json_encode($return);
                }
				
				
				if ($content == "get_order_items") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    //$user_id = $post['UserID'];
					$user_id = 5;
					$order_id = $post['order_id'];;
						

					$merchant_name = null;	
	
					if (isset($post['merchant_name'])) {
						$merchant_name = $post['merchant_name'];
					}
					
					
				//	$order_id = 483725973795096;
					//$merchant_name="Twinzahra Shop";

					if (isset($user_id)) {

				$getDataLazada = $db->getDataLazada($user_id , $merchant_name);


					if ($getDataLazada != null) {
							
                    while ($rowLazada = $getDataLazada->fetch_assoc()) {										
					$rows[] = $rowLazada;
			
					}
					
					
			
					foreach ($rows as $obj) {
							
					$appkey =  $obj['AppKey'];
					$appSecret =  $obj['AppSecret'];
					$accessToken =  $obj['AccessToken'];	
					$merchant_name =  $obj['merchant_name'];
					
                      
					$c = new LazopClient($url,$appkey,$appSecret );

					$request = new LazopRequest('/order/items/get','GET');
					$request->addApiParam('order_id',$order_id);

					$jdecode=json_decode($c->execute($request, $accessToken));
					
				
			

//echo json_encode($jdecode);die;

//cek token expire
					if ($jdecode->code == "0") {
						
					$data=$jdecode->data;

					
					
  foreach ($data as $datas) {
	  
		$order_item_id = $datas->order_item_id;  
		$order_id = $datas->order_id;
		$purchase_order_id= $datas->purchase_order_id;
		$purchase_order_number= $datas->purchase_order_number;
		$invoice_number= $datas->invoice_number;
		$sla_time_stamp= $datas->sla_time_stamp;
		$package_id= $datas->package_id;
		$shop_id= $datas->shop_id;
		$order_type= $datas->order_type;
		$shop_sku= $datas->shop_sku;
		$sku= $datas->sku;
		$name= $datas->name;
		$variation= $datas->variation;
		$item_price= $datas->item_price;
		$paid_price= $datas->paid_price;
		$currency= $datas->currency;
		$tax_amount= $datas->tax_amount;
		$product_main_image= $datas->product_main_image;
		$product_detail_url= $datas->product_detail_url;
		$shipment_provider= $datas->shipment_provider;
		$tracking_code_pre= $datas->tracking_code_pre;
		$tracking_code= $datas->tracking_code;
		$shipping_type= $datas->shipping_type;
		$shipping_provider_type= $datas->shipping_provider_type;
		$shipping_fee_original= $datas->shipping_fee_original;
		$shipping_service_cost= $datas->shipping_service_cost;
		//$shipping_fee_discount_seller= $datas->shipping_fee_discount_seller;
		$shipping_amount= $datas->shipping_amount;
		$is_digital= $datas->is_digital;
		$voucher_amount= $datas->voucher_amount;
		$voucher_seller= $datas->voucher_seller;
		$voucher_code_seller= $datas->voucher_code_seller;
		$voucher_code= $datas->voucher_code;
		//$voucher_code_platform->voucher_code_platform;
		//$voucher_platform= $voucher_platform;
		$order_flag= $datas->order_flag;
		$promised_shipping_time= $datas->promised_shipping_time;
		$digital_delivery_info= $datas->digital_delivery_info;
		$extra_attributes= $datas->extra_attributes;
		$cancel_return_initiator= $datas->cancel_return_initiator;
		$reason= $datas->reason;
		$reason_detail= $datas->reason_detail;
		$stage_pay_status= $datas->stage_pay_status;
		$warehouse_code= $datas->warehouse_code;
		$return_status= $datas->return_status;
		$status= $datas->status;
		$created_at= $datas->created_at;
		$updated_at= $datas->updated_at;

		
				$return[] = array(
					
			"order_item_id" => $order_item_id,
			"order_id" => $order_id,
			"purchase_order_id" =>$purchase_order_id ,
			"purchase_order_number" =>$purchase_order_number,
			"invoice_number" => $invoice_number,
			"sla_time_stamp" => $sla_time_stamp,
			"package_id" =>$package_id,
			"shop_id" =>$shop_id,
			"order_type" => $order_type,
			"shop_sku" =>$shop_sku ,
			"sku" =>$sku,
			"name" =>$name,
			"variation" =>$variation,
			"item_price" =>$item_price,
			"paid_price" =>$paid_price,
			"currency" =>$currency,
			"tax_amount" => $tax_amount,
			"product_main_image" =>$product_main_image,
			"product_detail_url" =>$product_detail_url,
			"shipment_provider" =>$shipment_provider,
			"tracking_code_pre" =>$tracking_code_pre,
			"tracking_code" =>$tracking_code,
			"shipping_type" =>$shipping_type,
			"shipping_provider_type" =>$shipping_provider_type,
            "shipping_fee_original" =>$shipping_fee_original,
			"shipping_service_cost " =>$shipping_service_cost,
            //"shipping_fee_discount_seller" =>$shipping_fee_discount_seller,
            "shipping_amount" =>$shipping_amount,
			"is_digital" =>$is_digital,
			"voucher_amount" =>$voucher_amount,
			"voucher_seller" =>$voucher_seller,
            "voucher_code_seller" =>$voucher_code_seller,
            "voucher_code" =>$voucher_code,
			//"voucher_code_platform" =>$voucher_code_platform,
			//"voucher_platform" =>$voucher_platform,
			"order_flag" =>$order_flag,
			"promised_shipping_time" =>$promised_shipping_time,
			"digital_delivery_info" =>$digital_delivery_info,
            "extra_attributes" =>$extra_attributes,
			"cancel_return_initiator" =>$cancel_return_initiator,
            "reason" =>$reason,
			"reason_detail" =>$reason_detail,
            "stage_pay_status" =>$stage_pay_status,
            "warehouse_code" =>$warehouse_code,
            "return_status" =>$return_status,
            "status" =>$status,
            "created_at" =>$created_at,
            "updated_at" =>$updated_at
                   );
	
			}
			   
					
					
			}else{
						
						 $return = array(
                          "status" => 404,
                           "message" => $jdecode->message
                        );
						
					}
							
				}			
         
					
			   } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Token Belum di setting"
                        );
                    }
					   
					   
                    
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Oops sepertinya ada yang salah!"
                        );
                    }
					

	                   echo json_encode($return);
	


                }
				
				
					if ($content == "get_shipment_providers") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    //$user_id = $post['UserID'];
					$user_id = 5;
					$merchant_name = null;	
	
if (isset($post['merchant_name'])) {
	$merchant_name = $post['merchant_name'];
}
					
					if (isset($user_id)) {

				$getDataLazada = $db->getDataLazada($user_id , $merchant_name);

				if ($getDataLazada != null) {
							
							
                    while ($rowLazada = $getDataLazada->fetch_assoc()) {										
					$appkey =  $rowLazada['AppKey'];
					$appSecret =  $rowLazada['AppSecret'];
					$accessToken =  $rowLazada['AccessToken'];	
					}
						
						
						$c = new LazopClient($url,$appkey,$appSecret );

					$request = new LazopRequest('/shipment/providers/get','GET');
					//$request->addApiParam('order_id',$order_id);

					$jdecode=json_decode($c->execute($request, $accessToken));
					$shipment_providers=$jdecode->data->shipment_providers;
		
			
				
				 $return = $shipment_providers;	
					
				}			
         
		 
		 		
				
                    
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Oops sepertinya ada yang salah!"
                        );
                    }
					

	                   echo json_encode($return);
	


                }
				
				
				
					if ($content == "create_product") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    //$user_id = $post['UserID'];
					$user_id = 5;
					
					$Data = $post['Data'];
					
			
				
					$merchant_name = null;	
	
					if (isset($post['merchant_name'])) {
						$merchant_name = $post['merchant_name'];
					}
					
					if (isset($user_id)) {

					$getDataLazada = $db->getDataLazada($user_id, $merchant_name);

				if ($getDataLazada != null) {
							
                   while ($rowLazada = $getDataLazada->fetch_assoc()) {										
					$rows[] = $rowLazada;
			
					}
						
				
					foreach ($rows as $obj) {
						

					$appkey =  $obj['AppKey'];
					$appSecret =  $obj['AppSecret'];
					$accessToken =  $obj['AccessToken'];	
					$merchant_name =  $obj['merchant_name'];
					
					
					
					foreach($Data as $Datas) {
						
						
					
					$PrimaryCategory = $Datas['PrimaryCategory'];
					$name = $Datas['name'];
					$short_description = $Datas['short_description'];
					$brand = $Datas['brand'];
					
					$model = "Polos";
					
					
					
				$images = "http://sg.s.alibaba.lzd.co/original/59046bec4d53e74f8ad38d19399205e6.jpg";
					
					foreach($Datas['variant'] as $Variants) {			
					$SellerSku = $Variants['SellerSku'];
					$color_family = $Variants['color_family'];
					$size = $Variants['size'];
					$quantity = $Variants['quantity'];
					$price = $Variants['price'];
					$package_length = "5";
					$package_height = "5";
					$package_weight = "0.5";
					$package_width = "5";
					$package_content = "Paperbag";
					
					$dataSkus[] =  '<Sku>
				  <SellerSku>'.$SellerSku.'</SellerSku>
				  <color_family>'.$color_family.'</color_family> 
				  <size>'.$size.'</size>
				  <quantity>'.$quantity.'</quantity>
				  <price>'.$price.'</price>
				  <package_length>'.$package_length.'</package_length> 
				  <package_height>'.$package_height.'</package_height>
				  <package_weight>'.$package_weight.'</package_weight> 
				  <package_width>'.$package_width.'</package_width>  
				  <package_content>'.$package_content.'</package_content> 
				  <Images>  
				  <Image>'.$images.'</Image> 
				  </Images> 
				  </Sku>';
				  
				
				
					}
					
				
				echo json_encode(simplexml_load_string($dataSkus));die;
				
				
					
					
					
					$c = new LazopClient($url,$appkey,$appSecret );
					$request = new LazopRequest('/product/create' , 'POST');
					$request->addApiParam('payload','<?xml version="1.0" encoding="UTF-8" ?> 
					<Request>   
				  <Product>
				  <PrimaryCategory>'.$PrimaryCategory.'</PrimaryCategory> 
				  <SPUId></SPUId>
				  <AssociatedSku></AssociatedSku> 
				  
				  <Attributes>
				  <name>'.$name.'</name> 
				  <short_description>sadasdsadsad  asdsadsa</short_description>
				  <brand>'.$brand.'</brand>
				  <model>'.$model.'</model>
				  <kid_years>Kids (6-10yrs)</kid_years> 
				  <delivery_option_sof>No</delivery_option_sof> 
				  </Attributes>
				  
				<Skus>
				
				
				
				  </Skus> 
				  </Product>
				  </Request>
				   
				   ');

					
					$jdecode=json_decode($c->execute($request, $accessToken));
					
					$code = $jdecode->code;
					$msg = $jdecode->message;
					
					$jencode=json_encode($jdecode, true);
					
					//echo $jencode;die;
					
			
			
				
						
							if ($code == 0) {
				
							$status = "Sukses";
							$msg2 = "";
				
				
							}else{
								
							$status = "Gagal";
							$msg2 = "";
							//foreach ($jdecode->detail as $details) {
								
							//	$msg2 = $details['message'];
	
							//}
							
							}
						
                      $dataResult[]= array (
			
						"Nama Toko"=>$merchant_name,	
						"Name Product"=>$name,
						"Status"=>$status,
						"Msg"=>$msg,
						"Msg2"=>$msg2,
						"Code"=>$code,
						);
					
					}
					
					//cek token expire
					if ($code == 0) {
						
					$sku_list=$jdecode->data->sku_list;

					$count=$sku_list->count;
				


			$return = array(
             "status" => 200,
             "message" => "Berhasil menambahkan produk",
			  "dataDecode" => $dataResult
			  
               );
				
						
					}else{
						
						 $return = array(
                            "status" => 404,
                            "message" => $dataResult
                        );
						
					}
					
					
					}
				
			  
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Akun lazada belum diatur"
                        );
                    }
					   
		
							
					
         

			
                    
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Oops sepertinya ada yang salah!"
                        );
                    }

                    echo json_encode($return);
                }
				
				
				
					if ($content == "get_document") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    //$user_id = $post['UserID'];
					$user_id = 5;
				
					$merchant_name = null;	
	
					if (isset($post['merchant_name'])) {
						$merchant_name = $post['merchant_name'];
					}

					if (isset($user_id)) {

				$getDataLazada = $db->getDataLazada($user_id, $merchant_name);

				if ($getDataLazada != null) {
							
							
         while ($rowLazada = $getDataLazada->fetch_assoc()) {
					$appkey =  $rowLazada['AppKey'];
					$appSecret =  $rowLazada['AppSecret'];
					$accessToken =  $rowLazada['AccessToken'];	
					}
						
						
						$c = new LazopClient($url,$appkey,$appSecret );

					$request = new LazopRequest('/order/document/get','GET');
					$request->addApiParam('doc_type','shippingLabel');
					$request->addApiParam('order_item_ids','[438179560123582]');

					$jdecode=json_decode($c->execute($request, $accessToken));
					
					
				

					
					$data=$jdecode->data;
				
					$return = array(
                            "status" => 200,
                            "message" => $jdecode
                        );	
					
				}			
         
                    
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Oops sepertinya ada yang salah!"
                        );
                    }
					

	                   echo json_encode($return);
	


                }
				
				
					if ($content == "set_pick") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    //$user_id = $post['UserID'];
				$user_id = 5;
				$order_item_ids = $post['order_item_ids'];
				$shipping_provider =$post['shipping_provider'];
				$delivery_type = $post['delivery_type'];
				$merchant_name = $post['merchant_name'];
			
				
				
				//print_r ($order_item_ids);die;
				
					if (isset($user_id) && isset($order_item_ids) && isset($shipping_provider) && isset($delivery_type) ) {

				$getDataLazada = $db->getDataLazadaByMerchant($user_id ,$merchant_name);

				if ($getDataLazada != null) {
							
                    while ($rowLazada = $getDataLazada->fetch_assoc()) {										
					$rows[] = $rowLazada;
			
					}
					
					foreach ($rows as $obj) {
							
					$appkey =  $obj['AppKey'];
					$appSecret =  $obj['AppSecret'];
					$accessToken =  $obj['AccessToken'];	
					$merchant_name =  $obj['merchant_name'];
					
						
						
						$c = new LazopClient($url,$appkey,$appSecret );
						
						$request = new LazopRequest('/order/pack');
						$request->addApiParam('shipping_provider',$shipping_provider);
						$request->addApiParam('delivery_type', $delivery_type);
						$request->addApiParam('order_item_ids', $order_item_ids);
					$data=json_decode($c->execute($request, $accessToken));
					
					
				$datas=$data->data->order_items;
			
					
					
				
			if ( $data->code  == "0") {
				
				$return = array(
                            "status" => 200,
							"message" => "Pesanan berhasil dikonfirmasi",
                            "data" => $datas
                        );	
			}else{
				
				$return = array(
                            "status" => 404,
							"message" => $data->message,
                            "data" =>$data
                        );	
				
			}
				
				 
					
				}
				
				} else {
                        $return = array(
                           "status" => 404,
                          "message" => "Toko anda belum diatur",
							"data" => []
                      );
                  }
				
         
                    
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Oops sepertinya ada yang salah!",
							"data" => []
                        );
                    }
					

	                   echo json_encode($return);
	


                }
				
				if ($content == "set_rts") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    //$user_id = $post['UserID'];
				$user_id = 5;
				$order_item_ids = $post['order_item_ids'];
				$shipping_provider =$post['shipping_provider'];
				$delivery_type = $post['delivery_type'];
				$tracking_number = $post['tracking_number'];
				$merchant_name = $post['merchant_name'];
				
				//$order_item_ids = "[441008605132192]";
				//$shipping_provider = "LEX ID" ;
				//$delivery_type = "dropship" ;
				//$merchant_name = "Twinzahra Shop";
				
				
				
				//print_r ($order_item_id);die;
				
				if (isset($user_id) && isset($order_item_ids) && isset($shipping_provider) && isset($delivery_type) && isset($tracking_number) ) {

				$getDataLazada = $db->getDataLazadaByMerchant($user_id , $merchant_name);

				if ($getDataLazada != null) {
							
                    while ($rowLazada = $getDataLazada->fetch_assoc()) {										
					$rows[] = $rowLazada;
			
					}
					
					foreach ($rows as $obj) {
							
					$appkey =  $obj['AppKey'];
					$appSecret =  $obj['AppSecret'];
					$accessToken =  $obj['AccessToken'];	
					$merchant_name =  $obj['merchant_name'];
						
						
						$c = new LazopClient($url,$appkey,$appSecret );
						



						$request = new LazopRequest('/order/rts');
						$request->addApiParam('delivery_type', $delivery_type);
						$request->addApiParam('order_item_ids', $order_item_ids);
						$request->addApiParam("shipment_provider", $delivery_type);
						$request->addApiParam('tracking_number', $tracking_number);
						$request->addApiParam('delivery_type', $delivery_type);
						$data=json_decode($c->execute($request, $accessToken));
					
				
				//$data=$jdecode->data;
				$response = json_encode($data, true );
					
					//print_r ($response);die;
				
			if ( $data->code  == "0") {
				
				$return = array(
                            "status" => 200,
							"message" => $data->message,
                            "data" => $response
                        );	
			}else{
				
				$return = array(
                            "status" => 404,
							"message" => $data->message,
                            "data" =>$response
                        );	
				
			}
			
					}
				
				 
					
					} else {
                        $return = array(
                           "status" => 404,
                          "message" => "Toko anda belum diatur",
							"data" => []
                      );
                  }			
         
                    
                    } else {
                        $return = array(
                           "status" => 404,
                          "message" => "Oops sepertinya ada yang salah!",
							"data" => []
                      );
                  }
					

	                   echo json_encode($return);
	


                }
				
				
					if ($content == "set_invoice") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    //$user_id = $post['UserID'];
				$user_id = 5;
				$order_item_id = $post['order_item_id'];
				$merchant_name = $post['merchant_name'];
				
				
				//$order_item_id = "439088831036995";
				//$shipping_provider = "LEX ID" ;
				//$delivery_type = "dropship" ;
				//$tracking_number = "LXAD-2026285224";
				
			
				
				if (isset($user_id) && isset($order_item_id) ) {

				$invoice_number = $order_item_id;
				
				$getDataLazada = $db->getDataLazadaByMerchant($user_id , $merchant_name);

			if ($getDataLazada != null) {
							
                    while ($rowLazada = $getDataLazada->fetch_assoc()) {										
					$rows[] = $rowLazada;
			
					}
					
					foreach ($rows as $obj) {
							
					$appkey =  $obj['AppKey'];
					$appSecret =  $obj['AppSecret'];
					$accessToken =  $obj['AccessToken'];	
					$merchant_name =  $obj['merchant_name'];
					
				
						$c = new LazopClient($url,$appkey,$appSecret );
						
						$request = new LazopRequest('/order/invoice_number/set');
						$request->addApiParam('order_item_id', $order_item_id);
						$request->addApiParam('invoice_number', $invoice_number);
						$data=json_decode($c->execute($request, $accessToken));
					
				
				//$data=$jdecode->data;
				$response = json_encode($data, true );
					
					//print_r ($response);die;
				
			if ( $data->code  == "0") {
				
				$return = array(
                            "status" => 200,
							"message" => $data->message,
                            "data" => $response
                        );	
			}else{
				
				$return = array(
                            "status" => 404,
							"message" => $data->message,
                            "data" =>$response
                        );	
				
			}
					}
				 
					
				} else {
                        $return = array(
                           "status" => 404,
                          "message" => "Toko anda belum diatur",
							"data" => []
                      );
                  }		
         
                    
                    } else {
                        $return = array(
                           "status" => 404,
                          "message" => "Oops sepertinya ada yang salah?",
							"data" => []
                      );
                  }
					

	                   echo json_encode($return);
	


                }
				
				
				
				
				
				
		  if ($content == "get_products") {
	
					$modeHeader = 0;
                    $post = json_decode(file_get_contents("php://input"), true);
					
					
					$merchant_name = null;	
	
					if (isset($post['merchant_name'])) {
						$merchant_name = $post['merchant_name'];
					}
					//$user_id = $post['UserID'];
					$user_id = 5;
					
			$productArr = [];
            $skusArr = [];
			
				$getDataLazada = $db->getDataLazada($user_id, $merchant_name);

		if ($getDataLazada != null) {
							
          while ($rowLazada = $getDataLazada->fetch_assoc()) {
					$rows[] = $rowLazada;
			
					}
					
foreach ($rows as $obj) {
							
					$appkey =  $obj['AppKey'];
					$appSecret =  $obj['AppSecret'];
					$accessToken =  $obj['AccessToken'];	
					$merchant_name =  $obj['merchant_name'];
					
						
			$c = new LazopClient($url,$appkey,$appSecret);
			$request = new LazopRequest('/products/get','GET');
			$request->addApiParam('filter','all');
			$jdecode=json_decode($c->execute($request, $accessToken));
			$data=$jdecode->data;
			$product=$jdecode->data->products;
								
	//echo json_encode($jdecode);die;
	

	 
 foreach($product as $products)
		{
		
					

	foreach($products -> skus as $skus)
		{
		//$rowSkus [] = $skus	;	
		//$skusArr[$products->item_id]['SkuId'] = $skus['SkuId'];
        //$skusArr[$products->item_id]['Status'] =$skus['Status'];
		//$skusArr[$products->item_id]['SellerSku'] = $skus['SellerSku'];
		//$skusArr[$products->item_id]['ShopSku'] = $skus['ShopSku'];
		//$skusArr[$products->item_id]['color_family'] = $skus['color_family'];
		//$skusArr[$products->item_id]['quantity'] = $skus['quantity'];
		//$skusArr[$products->item_id]['price'] = $skus['price'];
		//$skusArr[$products->item_id]['special_price'] = $skus['special_price'];
		//$skusArr[$products->item_id]['size'] = $skus['size'];	
		//$skusArr[$products->item_id]['Url'] = $skus['Url'];	

		//}	
		
//$resultSkus = array_values($skusArr);
			
		$productArr[$products->item_id]['item_id'] = $products->item_id;
        $productArr[$products->item_id]['merchant_name'] = $merchant_name;
		$productArr[$products->item_id]['name'] = $products->attributes->name;
		$productArr[$products->item_id]['skus'][]= $skus;
		}		
		}
		
		
$result = array_values($productArr);
		
		}	
		//echo json_encode($dataProducts);die;
				
		  		$return= array(
					"status" => 200,
                    "message" => "",
					"total_rows" => count($result),
					"data" => $result
					

                   );



}else{
		$return= array(
					"status" => 404,
                    "message" => "Toko lazada tidak ada yang aktif",
					"total_rows" => 0,
					"data" => []
					

                   );	
			
			
		}
 

	
            
            //
            echo json_encode($return);
			
                }
				
		  if ($content == "get_product_items") {
	
					$modeHeader = 0;
                    $post = json_decode(file_get_contents("php://input"), true);
					
					
					$merchant_name = null;	
	
					if (isset($post['merchant_name'])) {
						$merchant_name = $post['merchant_name'];
					}
				
	
				
						$item_id = $post['item_id'];
					
					
					$seller_sku = null;	
	
					if (isset($post['seller_sku'])) {
						$seller_sku = $post['seller_sku'];
					}
					
					//$user_id = $post['UserID'];
					$user_id = 5;
					
				if ($item_id != null) {
					
				$getDataLazada = $db->getDataLazada($user_id, $merchant_name);

		if ($getDataLazada != null) {
							
                    while ($rowLazada = $getDataLazada->fetch_assoc()) {										
					$rows[] = $rowLazada;
			
					}
					
					foreach ($rows as $obj) {
							
					$appkey =  $obj['AppKey'];
					$appSecret =  $obj['AppSecret'];
					$accessToken =  $obj['AccessToken'];	
					$merchant_name =  $obj['merchant_name'];
					
						
			$c = new LazopClient($url,$appkey,$appSecret);
			$request = new LazopRequest('/product/item/get','GET');
			$request->addApiParam('item_id', $item_id);
			$jdecode=json_decode($c->execute($request, $accessToken));
		
			
	//echo json_encode($jdecode);die;
	

if ($jdecode->code == "0") {
	
		$data=$jdecode->data;
		$primary_category=$data->primary_category;	
		$attributes=$data->attributes;	
			
		foreach($jdecode->data->skus as $skus)
		{
			
		foreach($skus->Images as $Images)		
		{
		 	$dataImage = $Images;	
		}
		
		$dataSkus[] = array("Status" => $skus->Status,
		"SellerSku" =>$skus->SellerSku,
		"ShopSku" => $skus->ShopSku,	
		"SkuId" => $skus->SkuId,
		"color_family" =>$skus->color_family,
		"Url" => $skus->Url,
		"price" => $skus->price,	
		"special_price" => $skus->special_price,			
		"quantity" => $skus->quantity,
		"package_width" => $skus->package_width,
		"package_height" => $skus->package_height,	
		"package_length" => $skus->package_length,
		"package_weight" => $skus->package_weight,
		"image_skus" => array($dataImage)		
		);			
		
		
		$dataItems = array(
		
					"merchant_name" => $merchant_name,
					"item_id" => $item_id,
					"name" => $attributes->name,
					"brand" => $attributes->brand,
					//"short_description" => $attributes->short_description,
					//"description" => $attributes->description,
					"primary_category" => $primary_category,
					"skus" => $dataSkus
				
                   );		
			   
		}
		
}else{
	
		
		  		$return= array(
					"status" => 404,
                    "message" => $jdecode->message,
					"total_rows" => 0,
					"data" => []
					

                   );
	
}
		}	
		//echo json_encode($dataProducts);die;
				
		  		$return= array(
					"status" => 200,
                    "message" => "Sukses",
					"total_rows" => count($dataSkus),
					"data" => $dataItems
					

                   );



}else{
		$return= array(
					"status" => 404,
                    "message" => "Toko lazada tidak ada yang aktif",
					"total_rows" => 0,
					"data" => []
					

                   );	
			
			
		}
 

	}else{
		$return= array(
					"status" => 404,
                    "message" => "Item ID tidak boleh kosong",
					"total_rows" =>0,
					"data" => []
					

                   );	
			
			
		}
            
            //
            echo json_encode($return);
			
                }
								
				  if ($content == "get_skus") {
	
					$modeHeader = 0;
                    $post = json_decode(file_get_contents("php://input"), true);
					$merchant_name = null;	
	
					if (isset($post['merchant_name'])) {
						$merchant_name = $post['merchant_name'];
					}
			
					$user_id = 5;
					
					$getDataLazada = $db->getDataLazada($user_id, $merchant_name);
					
					$addProduct = 0;

				if ($getDataLazada != null) {
							
                    while ($rowLazada = $getDataLazada->fetch_assoc()) {										
					$rows[] = $rowLazada;
			
					}
						
				
					foreach ($rows as $obj) {
						
		
						
					$appkey =  $obj['AppKey'];
					$appSecret =  $obj['AppSecret'];
					$accessToken =  $obj['AccessToken'];	
					$merchant_name =  $obj['merchant_name'];
					


		
			$c = new LazopClient($url,$appkey,$appSecret);
			$request = new LazopRequest('/products/get','GET');
			$jdecode=json_decode($c->execute($request, $accessToken));
			$data=$jdecode->data;
			$product=$jdecode->data->products;

	


		foreach($data->products  as $products)

		{


		
		 foreach($products->skus  as $skus)

		{
			$sku[]= $skus->SellerSku;
			
		
					

		}
		}
				
			
		$dataSkus[] = array(
		$merchant_name => 
		$sku
		
		);		
		
		
			
				
		  		$return= array(
					"status" => 200,
                    "message" => "",
					"total_rows" => count($sku),
					"data" => $dataSkus
					

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
			
                }
				
				  if ($content == "cek_status") {
	
					$modeHeader = 0;
                    $post = json_decode(file_get_contents("php://input"), true);
					
			
					$user_id = 5;
					$merchant_name = null;	
	
					if (isset($post['merchant_name'])) {
						$merchant_name = $post['merchant_name'];
					}

					$getDataLazada = $db->getDataLazada($user_id, $merchant_name);
					
			

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
			$order_items=$jdecode->data->order_items;
			
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
			
				if ($status == "shipped") {
					
				$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://sellercenter.twinzahra.com/api/orders.php?request=set_ship');
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
					curl_setopt($ch, CURLOPT_URL, 'https://sellercenter.twinzahra.com/api/orders.php?request=set_delivery');
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
			
				}
				
	
					
				$dataOrders[]= array(
				
					"order_id" => $order_id,
					"order_number" => $order_number,
					"msg" => $resultLazada,
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
			
          }
				
					if ($content == "sync_products") {
	
					$modeHeader = 0;
                    $post = json_decode(file_get_contents("php://input"), true);
					$user_id = 5;
					
					
					
					$getDataProducts = $db->getDataProductsdanVariant($user_id);	
					
					if ($getDataProducts != null) {
						
					while ($rowProducts = $getDataProducts->fetch_assoc()){
						
					//$rows[] = $rowProducts;
					
					$ProductID = $rowProducts['ProductID'];
					$PrimaryCategory = $rowProducts['CategoryID'];
					$name = $rowProducts['ProductName'];
					$short_description =  $rowProducts['Description'];
					$brand =  $rowProducts['BrandName'];
					
					$color_family = $rowProducts['ProductVariantName'];				

					$SellerSku = $rowProducts['SkuID'];
					$size = $rowProducts['ProductVariantDetailName'];
					$quantity = $rowProducts['Stock'];
					$price = $rowProducts['PriceRetail'];
					
					$package_length = "5";
					$package_height = "5";
					$package_weight = "0.5";
					$package_width = "5";
					$package_content = "Paperbag";
					
					$getImagesProducts = $db->getImagesProducts($ProductID);	
					
					
					if ($getImagesProducts != null) {
						
					while ($rowImages = $getImagesProducts->fetch_assoc()){
						
						//$images[] = $rowImages['ImageProductName'];
						
						$images[] = array ( 
									$rowImages['ImageProductName']
									
									);	

					}}	
					//echo json_encode($images);die;
				
						
									
					$dataVariant = array ( 
									"color_family" => $color_family,	
									"SellerSku" => $SellerSku,
									"size" => $size,
									"quantity" => $quantity,
									"price" => $price,
									"package_length" => $package_length,
									"package_height" => $package_height,
									"package_weight" => $package_weight,
									"package_width" => $package_width,
									"package_content" => $package_content
									);	
						
				
									
	$rows[$rowProducts['ProductID']]['ProductID'] = $rowProducts['ProductID'];
	$rows[$rowProducts['ProductID']]['PrimaryCategory'] = $rowProducts['CategoryID'];
    $rows[$rowProducts['ProductID']]['name'] = $rowProducts['ProductName'];
	$rows[$rowProducts['ProductID']]['short_description'] = $rowProducts['Description'];
	$rows[$rowProducts['ProductID']]['brand'] = $rowProducts['brand'];
	//$rows[$rowProducts['ProductID']]['images']= $images;
    $rows[$rowProducts['ProductID']]['variant'][] = $dataVariant;


		$result = array_values($rows);

					
					
					}	 										
					}
					
					

							//$data[] = array (
							//"PrimaryCategory" => $PrimaryCategory,
							//"name" => $name,
							//"short_description" => $short_description,
							//"brand" => $brand,
							//"variant" =>$dataVariant
							//);				
					
					//echo json_encode($result);die;
				
		$chLazada = curl_init();
		curl_setopt($chLazada, CURLOPT_URL, 'https://sellercenter.twinzahra.com/api/lazada.php?request=create_product');
		$payloadLazada = json_encode( array(
		"UserID"=> 5 ,
		"Data"=> $result
		 ) );
		curl_setopt( $chLazada, CURLOPT_POSTFIELDS, $payloadLazada );
		curl_setopt( $chLazada, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($chLazada, CURLOPT_RETURNTRANSFER, 1);
		$contentLazada = curl_exec($chLazada);
		curl_close($chLazada);

		//mengubah data json menjadi data array asosiatif
		$resultLazada=json_decode($contentLazada,true);
		
		
		

				
		  		$return= array(
					"status" => 200,
                    "message" => "",				
					"data" => $resultLazada
					

                   );


				
				
			  

	
            
            //
            echo json_encode($return);
			
                }
				
				
					if ($content == "sync_marketplace") {
                    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
                    $user_id = 5;

				$rowProducts = array();
				$rowVariants = array ();
				$ProductID = array();
				$xmlString = array();
				
					$product_id = null;
					//$product_id = null;
					
					 if (isset($post['ProductID'])) {
                        $product_id = $post['ProductID'];
                    }

					$merchant_name = null;	
	
					if (isset($post['merchant_name'])) {
						$merchant_name = $post['merchant_name'];
					}

                
							//Mencari konfigurasi lazada by user id
						$getDataLazada = $db->getDataLazada($user_id, $merchant_name);
					
						if ($getDataLazada != null) {
							
						while ($rowLazada = $getDataLazada->fetch_assoc()) {										
						$rows[] = $rowLazada;
			
						}
						
						
						
					
                       foreach ($rows as $obj) {
							
					$appkey =  $obj['AppKey'];
					$appSecret =  $obj['AppSecret'];
					$accessToken =  $obj['AccessToken'];	
					$merchant_name =  $obj['merchant_name'];
					
					
					
						//Mencari ProductID by user id
						$getDataProduct = $db->getProductIDByUserID($user_id);
							
						if ($getDataProduct != null) {
							
                        while ($rowProduct = $getDataProduct->fetch_assoc()) {	
						
						$rowProducts[] = $rowProduct['ProductID'];
						$ProductID = $rowProducts;

						}
	
						foreach($rowProducts as $item) {
		
		   
						$getDataVariant = $db->getDataVariantProduct($user_id, $item);	
						
						if ($getDataVariant != null) {
	
						while ($rowVariant = $getDataVariant->fetch_assoc()) {										
							
							
							$rowsVariant[] = $rowVariant;
							$SkuID = $rowVariant['SkuID'];
							$Stock  = $rowVariant['Stock'];
							$PriceRetail  = $rowVariant['PriceRetail'];
							$PriceSale = $rowVariant['PriceSale'];		

						
						
			$c = new LazopClient($url,$appkey,$appSecret);
			$request = new LazopRequest('/product/price_quantity/update');
			$request->addApiParam('payload','
			<Request>
			<Product>
			<Skus>
			<Sku>
			<SellerSku>' . $SkuID . '</SellerSku>
			<Price>' . $PriceRetail .'</Price>
			<SalePrice>' . $PriceSale .'</SalePrice>
			<SaleStartDate>2020-08-01</SaleStartDate>
			<SaleEndDate>2020-09-30</SaleEndDate>
			<Quantity>' . $Stock .'</Quantity>
			</Sku> 
			</Skus>
			</Product>
			</Request>');
			
			
			$jdecode=json_decode($c->execute($request, $accessToken));
			$code = $jdecode->code;
			$message = $jdecode->message;
			$resultData = json_encode($jdecode , true);
			Thread.sleep(2);
			
						
			
			if ($code == 0) {
				
				$status = "Sukses";
				
				
			}else{
				$status = "Gagal";
				
			
						
						
			}	
			
			$dataResult[]= array (
			
						"Nama Toko"=>$merchant_name,
						"SkuID"=>$SkuID,
						"Status"=>$status,
						"Msg"=>$message,
						"Code"=>$code,
						);
					
						
						}
					
						
					echo json_encode ($dataResult);die;	
		

			
			
		
			
					
					
					
					
					
					
						
					$total = mysqli_num_rows($getDataProduct);
					$dataEncode = json_encode($dataResult , true);
						
			
			$return = array(
             "status" => 200,
             "message" => "Sync ke marketplace berhasil",
             "total_rows" => $total,
           //  "data" => $dataEncode,
			  "dataDecode" => $dataResult
			  
               );
			   
			
			} else {
                        $return = array(
                           "status" => 404,
                          "message" => "Belum ada variant product yang aktif",
							"data" => []
                      );
                  }
			
			}

			
			
			
			} else {
                        $return = array(
                           "status" => 404,
                          "message" => "Belum ada product yang aktif",
							"data" => []
                      );
                  }
			
			
			}
			
			
			
			} else {
                        $return = array(
                           "status" => 404,
                          "message" => "Toko anda belum diatur",
							"data" => []
                      );
            }
				
         
						
						
						
                            
                        
						
                    

                    echo json_encode($return);
                }
				
				
				
				
				
				
				
				
        if ($content == "create_token") {
	
					$modeHeader = 0;
                    $post = json_decode(file_get_contents("php://input"), true);
					$merchant_name = null;	
	
					if (isset($post['merchant_name'])) {
						$merchant_name = $post['merchant_name'];
					}
					$user_id = 5;
				$getDataLazada = $db->getDataLazada($user_id, $merchant_name);
				
				if ($getDataLazada !=null ) {
					
					while ($rowLazada = $getDataLazada->fetch_assoc()) {										
				
						$app_key =  $rowLazada['AppKey'];
						$appSecret =  $rowLazada['AppSecret'];
						$access_token =  $rowLazada['AccessToken'];	
						$code =  $rowLazada['Code'];	
						
						
					}
					

			//var_dump($jdecode);die;
					//print_r($jdecode);die;

					$return = array(
                            "status" => 200,
                            "message" => "ok",
                            "data" => $jdecode
                            
                            );
						
					
				}else{
					
					
						$return = array(
                            "status" => 404,
                            "message" => "Anda belum setting lazada",
                            "data" => []
                            
                            );
					
				}

				
            
            //
            echo json_encode($return);
			
                }




        if ($content == "call_back") {
	 
					$modeHeader = 0;
                    $post = json_decode(file_get_contents("php://input"), true);
					
					$code = $_GET['code'];
					$user_id = $_GET['user_id'];
					if ($code != null && $user_id != null ) {
	
					//Cek UserID
					$cekUserID = $db->cekUserIDLazada($user_id);
					if ($cekUserID != false) {
					
					//Jika userid ada maka data diupdate
					$UpdateData = $db->updateCodeLazada($user_id ,$code);
					
					if($UpdateData != false) {
						
						
						$return = array(
                            "status" => 200,
                            "message" => "Code Berhasil di update",
                            "data" => $UpdateData
                            
                            );
						
					}else{
						
						$return = array(
                            "status" => 404,
                            "message" => "Gagal Update Code Lazada",
                            "data" => []
                            
                            );
						
					}	
						
						
					}else{
						
						
					//Jika tidak maka insert
					//$InsertData = $db->saveCodeLazada($user_id ,$code);
					
					//if($InsertData != false) {
						
						
						//$return = array(
                           // "status" => 200,
                            //"message" => "Code Berhasil di tambahkan",
                            //"data" => $InsertData
                            
                            //);
						
					//}else{
						
						//$return = array(
                           // "status" => 404,
                           // "message" => "Gagal Tambah Code Lazada",
                           // "data" => []
                            
                           // );
						
					//}
					
						
							$return = array(
                            "status" => 404,
                            "message" => "Anda belum mengatur lazada",
                            "data" => []
                            
                            );
	
	
					}
					

	
	
					}else{
	
	
						$return = array(
                            "status" => 404,
                            "message" => "User ID dan Kode harus diisi",
                            "data" => []
                            
                            );
	
					}
		
				



            //
            echo json_encode($return);
			
                }
				
				
				        if ($content == "create_global_product") {
	
					$modeHeader = 0;
                    $post = json_decode(file_get_contents("php://input"), true);
					
					$code = $Get['code'];


$c = new LazopClient($url,$app_key,$appSecret );
$request = new LazopRequest('/product/global/create');
$request->addApiParam('payload',' <?xml version="1.0" encoding="UTF-8"?>

<Request>    
 <Product>      
  <PrimaryCategory>11069</PrimaryCategory>       
  <SPUId/>       
  <AssociatedSku/>       
  <AutoAllocateStock>false</AutoAllocateStock>       
  <Ventures>       
   <Venture>MY</Venture>         
   <Venture>SG</Venture>         
   <Venture>TH</Venture>      
  </Ventures>     
  <Attributes>        
   <name>api create product test sample</name>         
   <short_description>This is a nice product</short_description>         
   <description>This is a nice product description</description>       
   <brand>Remark</brand>         
   <model>asdf</model>         
   <kid_years>Kids (6-10yrs)</kid_years>       
   <package_length>11</package_length>         
   <package_height>22</package_height>         
   <package_weight>1</package_weight>         
   <package_width>44</package_width>         
   <package_content>this is whats in the box</package_content>       
  </Attributes>       
  <Skus>        
   <Sku>          
    <SellerSku>api-create-test1-14</SellerSku>           
    <color_family>Green</color_family>           
    <size>40</size>           
    <quantity>120</quantity>           
    <sg_retail_price>388.50</sg_retail_price>           
    <sg_sales_price>308.50</sg_sales_price>          
    <retail_price>388.50</retail_price>           
    <sales_price>308.50</sales_price>           
    <tax_class>default</tax_class>         
    <Images>            
     <Image>http://imgsrc.baidu.com/imgad/pic/item/37d12f2eb9389b508e646c9b8f35e5dde6116e64.jpg</Image>             
     <Image>http://imgsrc.baidu.com/imgad/pic/item/37d12f2eb9389b508e646c9b8f35e5dde6116e64.jpg</Image>          
    </Images>        
   </Sku>      
  </Skus>    
 </Product>  
</Request>');

var_dump($c->execute($request, $access_token));



		$return = array(
                            "status" => 200,
                            "message" => "ok",
                            "data" => $request
                            
                            );
            
            //
            echo json_encode($return);
			
                }
				

} else {
    //Aha, what you're looking for !!!
    $return = array(
        "status" => 404,
        "message" => "Method Not Found!"
    );

    echo json_encode($return);
}


?>
