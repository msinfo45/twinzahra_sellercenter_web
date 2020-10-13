 <?php
  
  include "../classes/class.phpmailer.php";
  include "../config/lazada/LazopSdk.php";

include "../config/db_connection.php";
include "../config/config_type.php";

$url='https://api.lazada.co.id/rest';
     //Load Models
    include "../config/model.php";

    $db = new Model_user();
	
 
  $user_id = 5;

				$rowProducts = array();
				$ProductID = array();
					
					 if (isset($post['ProductID'])) {
                        $product_id = $post['ProductID'];
                    }

                
							//Mencari konfigurasi lazada by user id
						$getDataLazada = $db->getDataLazada($user_id);
					
                        while ($rowLazada = $getDataLazada->fetch_assoc()) {										
				
						$app_key =  $rowLazada['AppKey'];
						$appSecret =  $rowLazada['AppSecret'];
						$access_token =  $rowLazada['AccessToken'];	
			
						}
					
						//Mencari ProductID by user id
						$getDataProduct = $db->getProductIDByUserID($user_id);
							
                   
							
                        while ($rowProduct = $getDataProduct->fetch_assoc()) {	
						
						$rowProducts[] = $rowProduct['ProductID'];
						$ProductID = $rowProducts;

	
	}
	
		foreach($rowProducts as $item) {
		
		   
						$getDataVariant = $db->getDataSync($user_id, $item);	
	
	
						while ($rowVariant = $getDataVariant->fetch_assoc()) {										
							
							
						
							$SkuID = $rowVariant['SkuID'];
							$Stock  = $rowVariant['Stock'];
							$PriceRetail  = $rowVariant['PriceRetail'];
							$PriceSale = $rowVariant['PriceSale'];
							
		 			
	
		$c = new LazopClient($url,$app_key,$appSecret);
			$request = new LazopRequest('/product/price_quantity/update');
			$request->addApiParam('payload','<Request>
		  <Product>
			<Skus>
		<Sku>
			<SellerSku>' . $SkuID . '</SellerSku>
			<Price>' . $PriceRetail .'</Price>
			<SalePrice>' . $PriceSale .'</SalePrice>
			<SaleStartDate>2020-07-23</SaleStartDate>
			<SaleEndDate>2020-07-31</SaleEndDate>
			<Quantity>' . $Stock .'</Quantity>
			</Sku> 
			</Skus>
		  </Product>
		</Request>');
			
			
			$jdecode=json_decode($c->execute($request, $access_token));
			$code = $jdecode->code;
			$message = $jdecode->message;
			$resultData = json_encode($jdecode , true);
			
		if ($code == 0) {
				
				$status = "Sukses";
				
				
			}else{
				$status = "Gagal";
				
			
						
						
			}	
			
			$dataResult[]= array (
						"SkuID"=>$SkuID,
						"Status"=>$status,
						"Msg"=>$message,
						"Code"=>$code,
						);
					
		
			
					}
							
					
					}
						


		$total = mysqli_num_rows($getDataProduct);

						
		$dataResult = json_encode($dataResult , true);

		$return = array(
		"status" => 200,
		"message" => "Sync ke marketplace berhasil",
		"total_rows" => $total,
		"data" => $dataResult
			  
               );

			//sendEmail (json_encode($return));
			
          echo json_encode($return);
					
					
?>	