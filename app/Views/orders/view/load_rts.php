 
 <?php

 	function getOrders(){
		

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, base_url('public/api/orders.php?request=get_rts'));
					$payload = json_encode( array( "UserID"=> "5",
										"status_id"=> "2"	
										) );
					curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
					curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$content = curl_exec($ch);
					curl_close($ch);

					//mengubah data json menjadi data array asosiatif
					$result=json_decode($content,true);
					
					return $result;
 

	
		
			}			
 
 
 	function getOrderItems($DataProduct){


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
	
	 	function cekStok($sku){
		

					$chItems = curl_init();
					curl_setopt($chItems, CURLOPT_URL, base_url('public/api/products.php?request=cek_stok'));
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
					curl_setopt($chItems, CURLOPT_URL, base_url('public/api/orders.php?request=cek_history'));
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

//echo json_encode($result);die;

					
		if (count($result['data']) > 0) {

			foreach($result['data'] as $DataProduct)
			{
				//Set Variable History Orders
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
				
				echo '<div class="card" >';

				echo'<div class="card-header">
					<div class="row">
					<div class="col-auto">'.$DataProduct['marketplace'].	' </div>
				
					<div class="col-auto">'.$price.	'	</div>
					<div class="col-auto">'.$order_number.	'	</div>
					<div class="col-auto">'.$customer_first_name.	'	</div>
											
					</div></div>';

		$resultItem = getOrderItems($order_id);
		$cekHistoryOrder = getHistory($order_id);

			foreach($resultItem['data'] as $DataOrderItems)
			{
				$resultStok = cekStok($DataOrderItems['sku']);

					echo'<div class="card-body">';
					echo'<div class="row">';
					echo'<div  class="col-auto">';
					echo'<img class="img-product" width="100px" height="100px" src='.$DataOrderItems['product_main_image'].'>';
					echo ' </div>';

					echo '<div class="col">';

					echo '<div class="card-title"><a href='.$DataOrderItems['product_detail_url'].'  target="_blank"> 
						';
					echo mb_strimwidth($DataOrderItems['name'], 0, 40, "...");	
					echo '</a></div>';
					
					echo ' <div class="card-text">';
					echo '<div class="mt-1">';
					echo $DataOrderItems['order_item_id'];
					echo'</div>';
							
					echo '<div class="mt-1">';
					echo $DataOrderItems['sku'] ;
					echo'</div>';

					echo '<div class="mt-1">';
					echo $DataOrderItems['paid_price'];
					echo'</div>';

					echo '</div></div></div></div>';
							
			}



					echo'<div class="card-header">';
					echo'<div class="row">';
					echo' <div class="col justify-content-center align-self-center">';

				if ($cekHistoryOrder == null ) {
				if ($resultStok == "") {
								
					echo '<span style="color:blue;" class="col-auto">Produk belum ada di sistem</span>';
										
				}else if ($resultStok == 0){
								
					echo '<span style="color:red;" class="col-auto">Stok Kosong</span>';
							
								
				}else if ($resultStok > 0){
								
					echo '<span style="color:green;" class="col-auto">Stok Tersedia</span>';
								
				}
							
				}else{

					echo '<span style="color:green;" class="col-auto">Pesanan sedang diproses</span>';

				}	
echo ' </div>';

echo ' <div class="col-auto">';
				if ($cekHistoryOrder != null ) {
                    echo'<a data-id="'.$order_id.'" title="Kirim"  class="SetToRTS btn btn-primary" href="#SetToRTS">Cetak Label</a>';
					echo'<a data-id="'.$order_id.'" title="Kirim"  class="SetToRTS btn btn-primary" href="#SetToRTS">Kirim</a>';			
					
				}
				echo '</div></div>';
				
				
				
				echo'</div></div>';				
												
						

						

				
					echo'</div>';

				}
				
		}else{
		
			echo '<div class="card-body text-center" >'.$result['message'] .'</div>';

	
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





$(document).on("click", ".SetToRTS", function () {
  var OrderID = $(this).data('id');
  var text;
    var r = confirm("Anda yakin ?");
    if (r == true) {

        $.ajax({
        type: 'POST',
    dataType: 'json',
    contentType: 'application/json',
    processData: false,
    data: '{"order_item_ids": "'+ OrderID +'"}',
      url:'<?= base_url('public/api/lazada.php?request=set_rts') ?>',
      
           
            beforeSend: function () {
                $('.deleteItemCartDetail').attr("disabled","disabled");
                $('.table-wrapper').css('opacity', '.5');
            },
            success:function(data){
        
        console.log(data.message);
        console.log(data.status);
        
                if(data.status == '200'){
          

          //alert(data.message);
          loadDataItem();
                }else{
          sound_error();
          alert(data.message);
        
                }
                $('.deleteItemCartDetail').removeAttr("disabled");
                $('.table-wrapper').css('opacity', '');
        
        
                
            },
      error: function(){
      alert("Cannot get data");
      }
      
        });
     
    } else {
     
    }
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

    	 url:'<?= base_url('public/api/orders.php?request=accept_order') ?>',
           
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
					 window.location.href = '<?= base_url('orders') ?>'; 
                }else{
					
					$('.statusMsg').html('<span style="color:red;"></p>'+data.message);
					alert(data.message);
					 window.location.href = '<?= base_url('orders') ?>'; 
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
<?= $this->include('orders/modal/accept_order') ?>
<?= $this->include('orders/modal/edit_order') ?>