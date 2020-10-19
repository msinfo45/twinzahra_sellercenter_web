 
 <?php
 require_once "../../config/config_type.php";

 	function getOrders(){
		

	$halaman = 1;
	$page = isset($_GET["halaman"]) ? (int)$_GET["halaman"] : 1;
	$mulai = ($page>1) ? ($page * $halaman) - $halaman : 0;
	
	$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'http://localhost/api/orders.php?request=get_orders');
		//$payload = json_encode( array( "Page"=> "1" ) );
		$payload = json_encode( array( "UserID"=> "5",
										"status_id"=> "1"	
										) );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$content = curl_exec($ch);
		curl_close($ch);
		$result=json_decode($content,true);
		return $result;
		
			}			
 
 
 	function getOrderItems($DataProduct){
		
					$chItems = curl_init();
					curl_setopt($chItems, CURLOPT_URL, 'http://localhost/api/orders.php?request=get_order_items');
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
	
	 	function cekStok($sku){
		
					$chItems = curl_init();
					curl_setopt($chItems, CURLOPT_URL, 'http://localhost/api/products.php?request=cek_stok');
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
					
				
					
					return $stock;
 
	}
	

function getHistory($order_id){
		
					$chItems = curl_init();
					curl_setopt($chItems, CURLOPT_URL, 'http://localhost/api/orders.php?request=cek_history');
					$payloadItem = json_encode( array( "order_id"=> $order_id ) );

					curl_setopt( $chItems, CURLOPT_POSTFIELDS, $payloadItem );
					curl_setopt( $chItems, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
					curl_setopt($chItems, CURLOPT_RETURNTRANSFER, 1);
					$contentItem = curl_exec($chItems);
					//$resultItem=json_encode($contentItem,true);
					curl_close($chItems);

					//mengubah data json menjadi data array asosiatif
					$resultItem=json_decode($contentItem,true);
					
					
					$data =$resultItem['data'];
					
				
					
					return $data;
 
	}
	
	


		
  
  

  $result = getOrders();

 echo'<table id="TableOrders" class="table table-striped table-hover">
					<thead>
						<tr>
							<th>
								<span class="custom-checkbox">
									<input type="checkbox" id="selectAll">
									<label for="selectAll"></label>
								</span>
							</th>
							<th>Produk</th>
							<th>Status</th>
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
				$merchant_name = $DataProduct['shop_name'] ;
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
							<h6 class="css-11v3zrg">
						'.$merchant_name.	'	
						</h6> '.$price.	'
						
						'.$order_number.	'
						'.$customer_first_name.	'	
						'.$customer_last_name.	'								
						</div>';
					
					
							$resultItem = getOrderItems($order_id);
							$cekHistoryOrder = getHistory($order_id);
					  	
									  foreach($resultItem['data'] as $DataOrderItems)

									  {
										  
										  
							
							$resultStok = cekStok($DataOrderItems['sku']);
							
						
									  
							
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
								
							echo $DataOrderItems['sku'] ;
	
							echo'</div>';
							
						
							echo '<div class="css-11v3zrg">';
							echo $DataOrderItems['paid_price'];
							echo'</div>';
							
	
							
							echo '</div></div>';
							
}
							echo'</td>';
							
							
					
							
							
							echo'<td>';
	
	
							if ($cekHistoryOrder == null ) {
							if ($resultStok == "") {
								
								echo '<span style="color:blue;">Produk belum ada di sistem</span>';
							
								
							}else if ($resultStok == 0){
								
							echo '<span style="color:red;">Stok Kosong</span>';
							
								
							}else if ($resultStok > 0){
								
							
							echo '<span style="color:green;">Stok Tersedia</span>';
								
							}
							
							}else{

							echo '<span style="color:green;">Pesanan sedang diproses</span>';

							}								
							
							echo '</td>';

									  

	

							echo '<td>';

							
					if ($cekHistoryOrder == null ) {
						
					if ($resultStok == 0) {			
				
					echo'<a data-toggle="modal" data-id="'.$order_id.'"  data-name="'.$customer_first_name.'" data-marketplace="'.$marketplace.'"  title="Ubah Pesanan"  class="EditOrder btn btn-primary" href="#EditOrder">Ubah Pesanan</a>';			
                                        
					}else{
						
					echo'<a data-toggle="modal" data-id="'.$order_id.'" data-merchant_name="'.$merchant_name.'" title="Atur Pengiriman"  class="AcceptOrder btn btn-primary" href="#AcceptOrder">Atur Pengiriman</a>';			
					
					}
					
					
					}
								
					
					
							 
?>

	 
<script>

$(document).on("click", ".EditOrder", function () {
     var order_id = $(this).data('id');
	  var name = $(this).data('name');
	  var marketplace = $(this).data('marketplace');

	  
    $(".modal-body #order_id").val(order_id );
	$(".modal-body #name").val(name);
	$(".modal-body #marketplace").val(marketplace);

     // As pointed out in comments, 
     // it is unnecessary to have to manually call the modal.
	   $('.modal-body #marketplace').attr("disabled","disabled");
	   $('.modal-body #order_id').attr("disabled","disabled");
		$('.modal-body #name' ).attr("disabled","disabled");
      $('#EditOrder').modal('show');
	  
});



$(document).on("click", ".AcceptOrder", function () {
     var order_id = $(this).data('id');
	 var merchant_name = $(this).data('merchant_name');
     $("#AcceptOrder .modal-body #order_id").val( order_id );
	$("#AcceptOrder .modal-body #merchant_name").val( merchant_name );

     // As pointed out in comments, 
     // it is unnecessary to have to manually call the modal.
      $('#AcceptOrder').modal('show');
});




function SendAcceptOrders(){
     var order_id = $('#order_id').val();
	 var merchant_name = $('#merchant_name').val();
   // var shipping_provider = $('#shipping_provider').val();
    //var delivery_type = $('#delivery_type').val();
	
		var e = document.getElementById("shipping_providers");
		var shipping_provider = e.options[e.selectedIndex].text;
		
		var f = document.getElementById("delivery_type");
		var delivery_type = f.options[f.selectedIndex].text;
		
		//alert(shipping_provider);
        $.ajax({
        type: 'POST',
		dataType: 'json',
		contentType: 'application/json',
		processData: false,
		data: '{"order_id": "'+ order_id +'", "merchant_name": "'+ merchant_name +'","shipping_provider": "'+shipping_provider+'", "delivery_type": "'+delivery_type+'"}',
        url:'/api/orders.php?request=accept_order',
           
            beforeSend: function () {
                $('.btn').attr("disabled","disabled");
                $('#AcceptOrder .modal-body').css('opacity', '.5');
            },
            success:function(data){
				
				console.log(data.message);
				console.log(data.status);
				
                if(data.status == '200'){
					 $('#AcceptOrder #order_id').val('');
                    $('#AcceptOrder #shipping_provider').val('');
                    $('#AcceptOrder #delivery_type').val('');
					
                    $('.statusMsg').html('<span style="color:green;"></p>' +data.message );
					alert(data.message);
					 window.location.href = '/orders'; 
                }else{
					
					$('.statusMsg').html('<span style="color:red;"></p>'+data.message);
					alert(data.message);
					 window.location.href = '/orders'; 
                }
                $('.btn').removeAttr("disabled");
                $('#AcceptOrder .modal-body').css('opacity', '');
				
				
                
            },
			error: function(){
			alert("Cannot get data");
			}
			
        });
    
}
</script>
					
					
					
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


