 <?php

include "../include/classes/class.phpmailer.php";
include "../config/lazada/LazopSdk.php";
include "../config/db_connection.php";
include "../config/config_type.php";
include "../config/model.php";

	$url='https://api.lazada.co.id/rest';
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
						
						$rowProducts[] = $rowProduct;
						//$ProductID = $rowProducts;

					}
	
		foreach($rowProducts as $item) {

			$ProductID = $item['ProductID'];
			$ProductName = $item['ProductName'];
			$Description = $item['Description'];


			$xml_output = '<?xml version="1.0" encoding="UTF-8" ?>';
			$xml_output .= "<Request>\n";

			$xml_output .= "\t<Product>\n";


		 $xml_output .= "\t<Attributes>\n";

		 $xml_output .= "\t\t<name>" . $ProductName . "</name>\n";
			// $xml_output .= "\t\t<short_description>" . $Description . "</short_description>\n";


		 $xml_output .= "\t</Attributes>\n";

			$xml_output .= "\t<Skus>\n";

			$getDataVariant = $db->getDataProductVariants2($user_id, $ProductID);

			while ($rowVariant = $getDataVariant->fetch_assoc()) {


				$SkuID = $rowVariant['SkuID'];
				$Stock = $rowVariant['Stock'];
				$PriceRetail = $rowVariant['PriceRetail'];
				$PriceReseller = $rowVariant['PriceReseller'];

				$xml_output .= "\t<Sku>\n";
				$xml_output .= "\t\t<SellerSku>" . $rowVariant['SkuID'] . "</SellerSku>\n";
				$xml_output .= "\t\t<quantity>" . $rowVariant['Stock'] . "</quantity>\n";
				$xml_output .= "\t\t<price>" . $rowVariant['PriceRetail'] . "</price>\n";
				$xml_output .= "\t</Sku>\n";

			}



			$xml_output .= "\t</Skus>\n";
			$xml_output .= "\t</Product>\n";

			$xml_output .= "</Request>";

			$c = new LazopClient($url,$app_key,$appSecret);
			$request = new LazopRequest('/product/update');
			$request->addApiParam('payload', $xml_output);
			
			
			$jdecode=json_decode($c->execute($request, $access_token));
			$code = $jdecode->code;

			$resultData = json_encode($jdecode , true);
			
		if ($code == 0) {
				
				$status = "Sukses";
			$message = 'null';
				
			}else{
				$status = "Gagal";
			$message = $jdecode->message;
			
						
						
			}	
			
			$dataResult[]= array (
						"ProductName"=>$ProductName,
						"Status"=>$status,
						"Msg"=>$message,
						"Code"=>$code,
						);


		}
				//	}


//}
						


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