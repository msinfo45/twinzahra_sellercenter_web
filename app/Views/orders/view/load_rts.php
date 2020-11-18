
<?php

function getOrders(){


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, base_url('public/api/orders.php?request=get_orders'));
    $payload = json_encode( array( "user_id"=> "5",
        "status"=> 2
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
        $order_id = $DataProduct['order_id'] ;
        $order_number = $DataProduct['order_number'] ;
        $user_id = $DataProduct['user_id'] ;
        $marketplace = $DataProduct['marketplace'] ;
        $merchant_name = $DataProduct['merchant_name'] ;
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
        //$extra_attributes = $DataProduct['extra_attributes'] ;
        $remarks = $DataProduct['remarks'] ;
        $delivery_info = $DataProduct['delivery_info'] ;
        $statuses = $DataProduct['statuses'] ;
        $created_at = $DataProduct['created_at'] ;
        $updated_at = $DataProduct['updated_at'] ;

        echo '<div class="card" >';

        echo'<div class="card-header">
					<div class="row">';
        if ($marketplace == "SHOPEE"){
            echo '<div class="col-auto justify-content-center align-self-center"><img class="img-product" width="40px" height="40px" src="https://twinzahra.masuk.id/public/images/shopee.png"></div>';
        }else if ($marketplace == "LAZADA"){
            echo '<div class="col-auto justify-content-center align-self-center"><img class="img-product" width="40px" height="40px" src="https://twinzahra.masuk.id/public/images/lazada.png"></div>';
        }else if ($marketplace == "OFFLINE"){
            echo '<div class="col-auto justify-content-center align-self-center">OFFLINE</div>';
        }
        echo'<div class="col font-weight-bold justify-content-center align-self-center"> '.$merchant_name.	'	</div>';

        echo'<div class="col-auto justify-content-center align-self-center"> No Pesanan '.$order_number.	'	</div>
											
					</div></div>';

        $cekHistoryOrder = getHistory($order_id);



        echo '<div class="card-body">';
        echo '<div class="row">';
        echo '<div  class="col ">';

        foreach($DataProduct['order_items'] as $DataOrderItems) {
            $resultStok = cekStok($DataOrderItems['sku']);

			$order_item_ids[] = $DataOrderItems['order_item_id'] ;
			$tracking_code = $DataOrderItems['tracking_code'] ;
			
            echo '<div class="row">';

            echo '<div  class="col-auto ">';
            echo '<img class="img-product" width="100px" height="100px" src=' . $DataOrderItems['image_variant'] . '>';
            echo ' </div>';

            echo '<div class="col">';
            echo '<div class="card-title "><a href=' . $DataOrderItems['product_detail_url'] . '  target="_blank"> ';
            echo mb_strimwidth($DataOrderItems['name'], 0, 40, "...");
            echo '</a></div>';

            echo ' <div class="card-text">';

            echo '<div class="mt-1">';
            echo $DataOrderItems['order_item_id'];
            echo '</div>';

            echo '<div class="mt-1">';
            echo $DataOrderItems['sku'];
            echo '</div>';

            echo '<div class="mt-1">';
            echo $DataOrderItems['paid_price'];
            echo '</div>';

            echo '</div>'; //end div card-text

            echo '</div>';//end div col


            echo '</div>';//end div row

            if ($remarks != "") {

                echo ' <div class="card-text mt-3 font-italic">Catatan : ' . $remarks . '</div>';


            }

        }
	
	//	$order_item_ids = json_encode($order_item_id);

        echo '</div>';//end div col


if ($DataProduct['address_shipping']['address1'] != "") {
        echo'<div  class="col-2">';
        echo '<div class="card-title font-weight-bold">Alamat Pengiriman</div> ';
        echo ' <div class="card-text">'.$DataProduct['address_shipping']['first_name'].'</div>';
        echo ' <div class="card-text">'.$DataProduct['address_shipping']['address1'].'</div>';
        echo ' <div class="card-text">'.$DataProduct['address_shipping']['phone'].'</div>';
        echo '</div>';//end div col-auto
}
        echo'<div  class="col-2">';
        echo '<div class="card-title font-weight-bold">Jasa Pengiriman</div> ';
        echo ' <div class="card-text">'.$DataOrderItems['shipment_provider'].'</div>';

        if ($DataOrderItems['tracking_code'] != "") {
            echo '<div class="card-title font-weight-bold">No Resi</div> ';
            echo ' <div class="card-text">' . $DataOrderItems['tracking_code'] . '</div>';
        }

        echo '</div>';//end div col-auto



        echo'<div  class="col-1">';
        echo '<div class="card-title font-weight-bold">Total Harga</div> ';
        echo ' <div class="card-text">'.$price.'</div>';
        echo '</div>';//end div col-auto

        echo '</div>';//end div row
        echo '</div>';//end div card-body

        //}




        echo'<div class="card-header">
					<div class="row">';

        echo'<div  class="col justify-content-center align-self-center">';

      

            echo '<div class="card-text font-weight-bold"><span style="color:green;">Pesanan sedang diproses</span></div>';

        

        echo ' </div>';
		
 
		

        echo'<div  class="col text-right" >';
		
 echo'<div  class="row " >';		
 echo'<div  class="col" >';


     echo'<a data-toggle="modal" data-shipping_provider="dropship" data-delivery_type="dropship" 
	 data-tracking_number="'.$tracking_code.'" data-order_item_id="'.base64_encode(json_encode($order_item_ids)).'"  data-merchant_name="'.$merchant_name.'" data-marketplace="'.$marketplace.'" 
	 title="Cetak Label Pesanan"  class="PrintLabel btn btn-primary" href="#PrintLabel">Cetak Label</a>';


        echo '</div>';//end div col-auto

 echo'<div  class="col-auto text-right" >';
     echo'<a data-toggle="modal" data-shipping_provider="dropship" data-delivery_type="dropship" 
	 data-tracking_number="'.$tracking_code.'" data-order_id="'.$order_id.'"  data-merchant_name="'.$merchant_name.'" data-marketplace="'.$marketplace.'" 
	 title="Kirim Pesanan"  class="SendOrder btn btn-primary" href="#SendOrder">Kirim</a>';
  echo '</div>';//end div col-auto

  echo '</div>';//end div col-auto
  
        echo '</div>';//end div col-auto
        echo '</div></div>';





        echo'</div>';

    }

}else{

    echo '<div class="card-body text-center" >'.$result['message'] .'</div>';


}



?>



<script>


     
		
 $(document).on("click", ".SendOrder", function () {

	var shipping_provider = $(this).data('shipping_provider');
	var delivery_type = $(this).data('delivery_type');
	var tracking_number = $(this).data('tracking_number');
	var merchant_name = $(this).data('merchant_name');
	var marketplace = $(this).data('marketplace');
	var order_id = $(this).data('order_id');
		
            $.ajax({
            type: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            processData: false,
            data: '{"order_id": "'+ order_id +'" ,"shipping_provider": "'+ shipping_provider +'","delivery_type": "'+delivery_type+'", "tracking_number": "'+tracking_number+'", "merchant_name": "'+merchant_name+'", "marketpalce": "'+marketplace+'"}',

            url:'<?= base_url('public/api/orders.php?request=set_rts') ?>',

            beforeSend: function () {
                $('.btn').attr("disabled","disabled");
                $('.content').css('opacity', '.5');
            },
            success:function(data){

                //console.log(data.message);
                //console.log(data.status);

                if(data.status == '200'){
                    $('#AcceptOrder #order_id').val('');
                    $('#AcceptOrder #shipping_provider').val('');
                    $('#AcceptOrder #delivery_type').val('');

                    $('.statusMsg').html('<span style="color:green;"></p>' +data.message );
					$('.nav-tabs a[href="#custom-tabs-orders-readytoship-tab"').tab('show');
					
					
                
	
                    alert(data.message);
					
                    window.location.href = '<?= base_url('orders') ?>';
	
	
					//$(document).on("click", "#custom-tabs-orders-readytoship-tab");
					
                    //window.location.href = '<?= base_url('orders') ?>';
		
					
                }else{

                    $('.statusMsg').html('<span style="color:red;"></p>'+data.message);
					$('.nav-tabs a[href="orders#custom-tabs-orders-readytoship-tab"').tab('click');
					//loadRTS();
                    alert(data.message);
                    window.location.href = '<?= base_url('orders') ?>';
				  	
                }
                $('.btn').removeAttr("disabled");
                $('.content').css('opacity', '');



            },
            error: function(){
                alert("Cannot get data");
            }

        });
  });

   		
 $(document).on("click", ".PrintLabel", function () {

	var shipping_provider = $(this).data('shipping_provider');
	var delivery_type = $(this).data('delivery_type');
	var tracking_number = $(this).data('tracking_number');
	var merchant_name = $(this).data('merchant_name');
	var marketplace = $(this).data('marketplace');
	var order_item_id = $(this).data('order_item_id');
		
            $.ajax({
            type: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            processData: false,
            data: '{"order_item_ids": "'+ order_item_id +'" ,"shipping_provider": "'+ shipping_provider +'","delivery_type": "'+delivery_type+'", "tracking_number": "'+tracking_number+'", "merchant_name": "'+merchant_name+'", "marketpalce": "'+marketplace+'"}',

            url:'<?= base_url('public/api/lazada/get_document') ?>',

            beforeSend: function () {
                $('.btn').attr("disabled","disabled");
                $('.content').css('opacity', '.5');
            },
            success:function(data){

                //console.log(data.message);
                //console.log(data.status);

                if(data.status == '200'){
                    $('#AcceptOrder #order_id').val('');
                    $('#AcceptOrder #shipping_provider').val('');
                    $('#AcceptOrder #delivery_type').val('');

                    $('.statusMsg').html('<span style="color:green;"></p>' +data.message );
					$('.nav-tabs a[href="#custom-tabs-orders-readytoship-tab"').tab('show');
					
					

	
                    alert(data.message);
					

	
					//$(document).on("click", "#custom-tabs-orders-readytoship-tab");
					
                    //window.location.href = '<?= base_url('orders') ?>';
				//	window.location.href = '<?= base_url('orders#custom-tabs-orders-readytoship-content') ?>';
					
                }else{

                    $('.statusMsg').html('<span style="color:red;"></p>'+data.message);
					$('.nav-tabs a[href="#custom-tabs-orders-readytoship-tab"').tab('click');
					//loadRTS();
                    alert(data.message);
                  //  wnidow.location.href = '<?= base_url('orders') ?>';
				  	
                }
                $('.btn').removeAttr("disabled");
                $('.content').css('opacity', '');



            },
            error: function(){
                alert("Cannot get data");
            }

        });
  });

   

</script>
