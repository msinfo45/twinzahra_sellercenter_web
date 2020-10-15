
                     
						<div class="main-card mb-3 card">
                            <div class="card-body">
                        
                         

									  <div class="col-md-5">
										    <label >Market Place</label>
											</div>
										 	<div class="col-md-5 mb-3">
                                          	<select class="form-control" name="marketplace" id="marketplace">
											<option value="">Market Place</option>
											<option value="LAZADA">LAZADA</option>
											<option value="TOKOPEDIA">TOKOPEDIA</option>
											<option value="SHOPEE">SHOPEE</option>
											<option value="BUKALAPAK">BUKALAPAK</option>
											<option value="OFFLINE">OFFLINE</option>
											</select>
   
                                            <div class="invalid-feedback">
                                              
                                            </div>
                                        </div>
										 
										
										
										
										  <div class="col-md-5">
										  <label >Order Number</label>
										   </div>
										  
										    <div class="col-md-5 mb-3">
                                            
                                            <input type="text" class="form-control" name="order_number" id="order_number" placeholder="Order Number" name="order_number" required>
                                            <div class="invalid-feedback">
                                              
                                            </div>
                                        </div>
										
										
										
										
                                        <div class="col-md-5">
                                            <label >Nama Pelanggan</label>
											 </div>
											  <div class="col-md-5 mb-3">
                                            <input type="text" class="form-control" id="name" placeholder="Masukan nama pelanggan" value="" name="name" required>
                                            <div class="valid-feedback">
                                                Looks good!
                                            </div>
                                        </div>
                                      
										
                                     
										
										
                             
								
								
										<?php
									
				
				echo '<div class="col-md-10">
                 <label >SKU</label>
                  <input type="text" class="form-control" name=SkuID id="SkuID" onkeydown="search(this)" placeholder="Masukan SKU Produk " required>
				</div>';
				
				echo '<div class="table-responsive">';
			
				
				
				echo '<div id="ResultItemOrders"></div></div>  ';
			?>	
				
						
			
		
			
									<div class="col-md-9 mb-3">
                                            <label for="validationCustom05">Jasa Expedisi</label>
											<select class="form-control" name="shipping_provider" id="shipping_provider">
											<option value="">Jasa Expedisi</option>
											<option value="LELEXPRESS">LELEXPRESS</option>
											<option value="NINJAXPRESS">NINJAXPRESS</option>
											<option value="JNE REG">JNE REG</option>
											<option value="JNT EXPRESS">JNT EXPRESS</option>
											<option value="ANTER AJA">ANTER AJA</option>
											<option value="ID EXPRESS">ID EXPRESS</option>
											<option value="SICEPAT REG">SICEPAT REG</option>
											<option value="SICEPAT EXPRESS">SICEPAT EXPRESS</option>
											<option value="SICEPAT HALU">SICEPAT HALU</option>
											</select>
   
                                            <div class="invalid-feedback">
                                              
                                            </div>
                                        </div>
										
										
									
									    <div class="col-md-6 mb-3">
                                            <label for="validationCustom06">No Resi</label>
                                            <input type="text" class="form-control" id="tracking_code" placeholder="No Resi" name="tracking_code" required>
                                            <div class="invalid-feedback">
                                              
                                            </div>
                                        </div>
                                    
									    <div class="col-md-6 mb-3">
                                            <label for="validationCustom07">Biaya Pengiriman</label>
                                            <input type="text" class="form-control" id="shipping_amount" placeholder="Rp" name="shipping_amount" required>
                                            <div class="invalid-feedback">
                                            
                                            </div>
                                        </div>
									
									    <div class="col-md-6 mb-3">
                                            <label for="validationCustom08">Kode Booking</label>
                                            <input type="text" class="form-control" name="tracking_code_pre" id="tracking_code_pre" placeholder="0" name="tracking_code_pre" required>
                                            <div class="invalid-feedback">
                                           
                                            </div>
                                        </div>
									
									    <div class="col-md-6 mb-3">
                                            <label for="validationCustom09">Catatan</label>
                                            <input type="text" class="form-control" name="remark" id="remark" placeholder="Catatan" name="remark" required>
                                            <div class="invalid-feedback">
                                              
                                            </div>
                                        </div>
										
											<div class="col-md-6 mb-3">
                                            <label for="validationCustom10">Payment Method</label>
											<select class="form-control" name="payment_method" id="payment_method">
											<option value="">Metode Pembayaran</option>
											<option value="NON COD">NON COD</option>
											<option value="COD">COD</option>
											<option value="CASH">CASH</option>
											</select>
   
                                            <div class="invalid-feedback">
                                              
                                            </div>
                                        </div>
										
										
										
									
									     <button class="btn btn-primary create_orders" type="submit" name="ok" >Simpan</button>
										 
										  </div>
											</div>
										 
										 
				



<script>

  $(document).on("click", ".create_orders", function () {
	  
	  var ea = document.getElementById("marketplace");
		var marketplace = ea.options[ea.selectedIndex].value;
		 
	   var order_number = $('#order_number').val();
	   var name = $('#name').val();  
	   
	   var ba = document.getElementById("shipping_provider");
	   var shipping_provider = ba.options[ba.selectedIndex].value;

	   var tracking_code = $('#tracking_code').val();
	   var shipping_amount = $('#shipping_amount').val();
	   var tracking_code_pre = $('#tracking_code_pre').val();
	   var remark = $('#remark').val();
	   
	   	var ca = document.getElementById("payment_method");
	   var payment_method = ca.options[ca.selectedIndex].value;
	   
	   
	$.ajax({
        type: 'POST',
		dataType: 'json',
		contentType: 'application/json',
		processData: false,
		data: '{"UserID": "5", "marketplace": "'+marketplace+'" , "order_id": "'+order_number+'", "name": "'+name+'", "shipping_provider": "'+shipping_provider+'", "tracking_code": "'+tracking_code+'", "shipping_amount": "'+shipping_amount+'", "tracking_code_pre": "'+tracking_code_pre+'", "remark": "'+remark+'", "payment_method": "'+payment_method+'"}',
        url:'https://sellercenter.twinzahra.com/api/orders.php?request=created_order',
           
            beforeSend: function () {
              $('.create_orders').attr("disabled","disabled");
               $('.main-card').css('opacity', '.5');
            },
            success:function(data){
				
				$('.create_orders').removeAttr("disabled");
               $('.main-card').css('opacity', '');
				console.log(data.message);
				console.log(data.status);
				
                if(data.status == '200'){
					 $('#marketplace').val('');
					 $('#order_number').val('');
					 $('#name').val('');
					 $('#shipping_provider').val('');
					 $('#tracking_code').val('');
					 $('#shipping_amount').val('');
					 $('#tracking_code_pre').val('');
					 $('#remark').val('');
					 $('#payment_method').val('');
					 
                    //$('#shipping_provider').val('');
                    //$('#delivery_type').val('');
					
                    //$('.statusMsg').html('<span style="color:green;"></p>' +data.message );
					alert(data.message);
					window.location.href = 'https://sellercenter.twinzahra.com/orders/?request=create_orders'; 
					sound_add();
					//loadDataItem(); 
					
                }else{
					sound_error();
					//$('.statusMsg').html('<span style="color:red;"></p>'+data.message);
					alert(data.message);
					// window.location.href = '/orders'; 
                }
                //$('.submitBtn').removeAttr("disabled");
                //$('.modal-body').css('opacity', '');
				
				
                
            },
			error: function(){
				$('.create_orders').removeAttr("disabled");
               $('.main-card').css('opacity', '');
			alert("Cannot get data");
			}
			
        });

});


</script>

<script>

    function sound_error() {
        var audioElement = document.createElement('audio');
        audioElement.setAttribute('src', '/assets/music/beep_error.mp3');
        audioElement.setAttribute('autoplay', 'autoplay');
        audioElement.load();
        audioElement.play();
    }
	
	   function sound_add() {
        var audioElement = document.createElement('audio');
        audioElement.setAttribute('src', '/assets/music/beep_add.mp3');
        audioElement.setAttribute('autoplay', 'autoplay');
        audioElement.load();
        audioElement.play();
    }
	
	
 function loadDataItem(){
var displayProduct = 5;
	$('#ResultItemOrders').html(createSkeleton(displayProduct));
	
    setTimeout(function(){
      loadItems(displayProduct);
    }, 0);

    function createSkeleton(limit){
      var skeletonHTML = '';
      for(var i = 0; i < limit; i++){
        skeletonHTML += '<div class="ph-item">';
        skeletonHTML += '<div class="ph-col-4">';
        skeletonHTML += '<div class="ph-picture"></div>';
        skeletonHTML += '</div>';
        skeletonHTML += '<div>';
        skeletonHTML += '<div class="ph-row">';
        skeletonHTML += '<div class="ph-col-12 big"></div>';
        skeletonHTML += '<div class="ph-col-12"></div>';
        skeletonHTML += '<div class="ph-col-12"></div>';
        skeletonHTML += '<div class="ph-col-12"></div>';
        skeletonHTML += '<div class="ph-col-12"></div>';
        skeletonHTML += '</div>';
        skeletonHTML += '</div>';
        skeletonHTML += '</div>';
      }
      return skeletonHTML;
    }
	
    function loadItems(limit){
      $.ajax({
         url:"https://sellercenter.twinzahra.com/class/items_create_orders.php",
        method:"POST",
        data:{action: 'load_products', limit:limit},
        success:function(data) {
          $('#ResultItemOrders').html(data);
        }
      });
    }
 }

//Load Data Items
loadDataItem();	

 $(document).on("keypress", "#SkuID", function(e){

        if(e.which == 13){
  
 
 var SkuID = $('#SkuID').val();
 $.ajax({
        type: 'POST',
		dataType: 'json',
		contentType: 'application/json',
		processData: false,
		data: '{"UserID": "5", "SkuID": "'+SkuID+'"}',
        url:'https://sellercenter.twinzahra.com/api/orders.php?request=add_cart_detail',
           
            beforeSend: function () {
               // $('.submitBtn').attr("disabled","disabled");
               // $('.modal-body').css('opacity', '.5');
            },
            success:function(data){
				
				console.log(data.message);
				console.log(data.status);
				
                if(data.status == '200'){
					 $('#SkuID').val('');
					 
                    //$('#shipping_provider').val('');
                    //$('#delivery_type').val('');
					
                    //$('.statusMsg').html('<span style="color:green;"></p>' +data.message );
					//alert(data.message);
					// window.location.href = '/orders'; 
					sound_add();
					loadDataItem();
					
                }else{
					sound_error();
					//$('.statusMsg').html('<span style="color:red;"></p>'+data.message);
					alert(data.message);
					// window.location.href = '/orders'; 
                }
                //$('.submitBtn').removeAttr("disabled");
                //$('.modal-body').css('opacity', '');
				
				
                
            },
			error: function(){
			alert("Cannot get data");
			}
			
        });

   }

    });
	
	
$(document).on("click", ".deleteItemCartDetail", function () {
	var CartDetailID = $(this).data('id');
	var text;
    var r = confirm("Kamu yakin mau menghapus item?");
    if (r == true) {

        $.ajax({
        type: 'POST',
		dataType: 'json',
		contentType: 'application/json',
		processData: false,
		data: '{"CartDetailID": "'+ CartDetailID +'"}',
        url:'https://sellercenter.twinzahra.com/api/orders.php?request=delete_cart_details',
           
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



	
	</script>
						

    

