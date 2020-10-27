 
 <?php
 
 	function getRts(){
		

	$halaman = 1;
	$page = isset($_GET["halaman"]) ? (int)$_GET["halaman"] : 1;
	$mulai = ($page>1) ? ($page * $halaman) - $halaman : 0;
	
	$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, base_url('public/api/orders.php?request=get_rts'));
		//$payload = json_encode( array( "Page"=> "1" ) );
		$payload = json_encode( array( "UserID"=> "5",
										"status_id"=> "2"	
										) );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$content = curl_exec($ch);
		curl_close($ch);
		$result=json_decode($content,true);
		return $result;
		
			}			
 
 
 	function getItemsRts($DataProduct){
		
					$chItems = curl_init();
						curl_setopt($chItems, CURLOPT_URL, base_url('public/api/orders.php?request=get_rts_items'));
	
					$payloadItem = json_encode( array( "order_id"=> $DataProduct ) );
					//$payloadItem = json_encode( array( "order_id" => 45 ) );
					//$payloadItem = json_encode( array( "UserID"=> "5" ) );
					curl_setopt( $chItems, CURLOPT_POSTFIELDS, $payloadItem );
					curl_setopt( $chItems, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
					curl_setopt($chItems, CURLOPT_RETURNTRANSFER, 1);
					$contentItem = curl_exec($chItems);
					curl_close($chItems);

					//mengubah data json menjadi data array asosiatif
					$resultItem=json_decode($contentItem,true);
					
					return $resultItem;
 
	}
	

	
		
		
		
		

		//mengubah data json menjadi data array asosiatif
		
  
 

  $result = getRts();

 echo' 



				<table id="TableOrders" class="table table-striped table-hover">
					<thead>
						<tr>
							<th>
								<span class="custom-checkbox">
									<input type="checkbox" id="selectAll">
									<label for="selectAll"></label>
								</span>
							</th>
							<th>Produk</th>
							<th>No Resi</th>						
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>';
					
					if (count($result['data']) > 0) {
					
					  foreach($result['data'] as $DataProduct)

				{
		

					
				//Set Variable History Orders
				$order_id = $DataProduct['order_id'] ;
				$order_number = $DataProduct['order_number'] ;
				$user_id = 5 ;
				$marketplace = $DataProduct['marketplace'] ;
				$branch_number = $DataProduct['branch_number'] ;
				$warehouse_code = $DataProduct['warehouse_code'] ;
				$customer_first_name = $DataProduct['customer_first_name'] ;
				$customer_last_name = $DataProduct['customer_last_name'] ;
				$price = $DataProduct['price'] ;
				$items_count = $DataProduct['items_count'] ;
				$payment_method = $DataProduct['payment_method'] ;
				$voucher = $DataProduct['voucher'] ;
				$voucher_code = $DataProduct['voucher_code'] ;
				$voucher_platform = $DataProduct['voucher_platform'] ;
				$voucher_seller = $DataProduct['voucher_seller'] ;
				$gift_option = $DataProduct['gift_option'] ;
				$gift_message = $DataProduct['gift_message'] ;
				$shipping_fee = $DataProduct['shipping_fee'] ;
				$shipping_fee_discount_seller = $DataProduct['shipping_fee_discount_seller'] ;
				$shipping_fee_discount_platform = $DataProduct['shipping_fee_discount_platform'] ;
				$promised_shipping_times = $DataProduct['promised_shipping_times'] ;
				$national_registration_number = $DataProduct['national_registration_number'] ;
				$tax_code = $DataProduct['tax_code'] ;
				$extra_attributes = $DataProduct['extra_attributes'] ;
				$remarks = $DataProduct['remarks'] ;
				$delivery_info = $DataProduct['delivery_info'] ;
				$statuses = $DataProduct['statuses'] ;
				$created_at = $DataProduct['created_at'] ;
				$updated_at = $DataProduct['updated_at'] ;
				
				

				
	
								
				
		
			echo '<tr>';
										 

											
									
				
			
							echo'<td>
								<span class="custom-checkbox">
									<input type="checkbox" id="checkbox1" name="options[]" value="1">
									<label for="checkbox1"></label>
								</span>
							</td>';
		
		
			
										  
							echo '<td> ';
							
						
										  
							echo'<div class="col-xs-6">
							
							<h6 class="css-11v3zrg">
						'.$DataProduct['marketplace'].	'	
						</h6>
						
						'.$order_id.	'
						'.$customer_first_name.	'	
						'.$customer_last_name.	'								
						</div>';
					
					
							$resultItem = getItemsRts($order_id);
						
					  	
									  foreach($resultItem['data'] as $DataOrderItems)

									  {
										  
										  
					
									
									  
									  
							
							echo'<div  class="css-1cagh9d">';
							
							echo'
							<img class="img-product" width="80px" height="80px" src='.$DataOrderItems['product_main_image'].'>';
							
							echo '<div class="css-gjyepm">';
							
							echo '<a href='.$DataOrderItems['product_detail_url'].'  target="_blank"> 
									<div class="styPLCProductNameInfo">';

							echo mb_strimwidth($DataOrderItems['name'], 0, 40, "...");	
							echo '</div></a>';
							
							echo '<div class="css-11v3zrg">';
							echo $DataOrderItems['order_item_id'];
							echo'</div>';
							
							echo '<div class="css-11v3zrg">';
							echo $DataOrderItems['sku'];
							echo'</div>';				
						
							echo '<div class="css-11v3zrg">';
							echo $DataOrderItems['paid_price'];
							echo'</div>';
							
							echo '</div></div>';
							
}
							echo'</td>';
							
							
						
							echo'<td>';
							
							if ($DataOrderItems['tracking_code'] != null ) {
								
							echo $DataOrderItems['tracking_code'];
								
							}
							
							echo '</td>';
							
							
							
									  

	

							echo '<td >';

							if ($DataOrderItems['tracking_code'] != null ) {
								
							echo'<a  target="_blank" title="Add this item"  class="btn btn-primary" href="https://cekresi.com/?v=573182064&noresi='.$DataOrderItems['tracking_code'].'">Kirim</a>';
							 
								
							}
					
							
							 
							 
?>

	 

					
					
					
					<?php
					echo '</td>';
					
					

						
 
						
						echo'</tr>';
						}

	
	
	
	}else{
		
	echo json_encode($result['message']);
	
	}
	

					 
					echo'</tbody>
				</table>';
				
				
				
				?>


