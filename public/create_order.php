<?php

require './include/head.php';
require './include/slidebar.php';

?>
                     
						<div class="main-card mb-3 card">
                            <div class="card-body">
                        
                                <form class="needs-validation" novalidate  method="post"> 
								
                                   <div class="form-row">
								   
                                    
										
										
											<div class="col-md-9 mb-3">
                                            <label for="validationCustom01">Market Place</label>
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
										
										
										
										    <div class="col-md-9 mb-3">
                                            <label for="validationCustom02">Order Number</label>
                                            <input type="text" class="form-control" name="order_number" id="validationCustom02" placeholder="Order Number" name="order_number" required>
                                            <div class="invalid-feedback">
                                              
                                            </div>
                                        </div>
										
										
										
                                        <div class="col-md-6 mb-3">
                                            <label for="validationCustom03">Nama Depan</label>
                                            <input type="text" class="form-control" id="validationCustom03" placeholder="Nama Depan" value="" name="firstname" required>
                                            <div class="valid-feedback">
                                                Looks good!
                                            </div>
                                        </div>
                                       
                                        <div class="col-md-6 mb-3">
                                            <label for="validationCustom04">Nama Belakang</label>
                                            <input type="text" class="form-control" id="validationCustom04" placeholder="Nama Belakang"  name="lastname" required>
                                            <div class="invalid-feedback">
                                                Please provide a valid state.
                                            </div>
                                        </div>
										
                                     
										
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
                                            <input type="text" class="form-control" id="validationCustom06" placeholder="No Resi" name="tracking_code" required>
                                            <div class="invalid-feedback">
                                              
                                            </div>
                                        </div>
                                    
									    <div class="col-md-6 mb-3">
                                            <label for="validationCustom07">Biaya Pengiriman</label>
                                            <input type="text" class="form-control" id="validationCustom07" placeholder="Rp" name="shipping_amount" required>
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
                                            <input type="text" class="form-control" name="remark" id="validationCustom09" placeholder="Catatan" name="remark" required>
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
										
										
										
									
									     <button class="btn btn-primary" type="submit" name="ok" >Buat Pesanan</button>
										 
										  </div>
										 
                                </form>
								
								
										<?php
									
									
						if(isset($_POST['ok'])){
										
						$data = array(
							'userID'      => '5',
							'marketplace'    => $_POST['marketplace'],
							'order_number'       => $_POST['order_number'],
							'firstname'       => $_POST['firstname'],
							'lastname' => $_POST['lastname'] , 
							'shipping_provider' => $_POST['shipping_provider'] , 
							'tracking_code' => $_POST['tracking_code'] , 
							'shipping_amount' => $_POST['shipping_amount'] , 
							'tracking_code_pre' => $_POST['tracking_code_pre'] , 
							'remark' => $_POST['remark'] , 
							'payment_method' => $_POST['payment_method'] , 
							);

						$options = array(
						'http' => array(
						'method'  => 'POST',
						'content' => json_encode( $data ),
						'header'=>  "Content-Type: application/json\r\n" .
						"Accept: application/json\r\n"
							)
						);

						$context  = stream_context_create( $options );
						$result = file_get_contents( "http://sellercenter.twinzahra.com/api/orders.php?request=created_order", false, $context );
						$response = json_decode( $result );



			}	
				
			?>	
				
						
				<div class="col-md-9 mb-3">
                 <label >SKU</label>
                  <input type="text" class="form-control" name=SkuID id="SkuID" onkeydown="search(this)" placeholder="0" required>
				</div><br>
				
	
				





		
		
		
		
				<div class="table-responsive">
			<div class="table-wrapper">
	
				<table class="table table-striped table-hover">
					<thead>
						<tr>
							<th>
								<span class="custom-checkbox">
									<input type="checkbox" id="selectAll">
									<label for="selectAll"></label>
								</span>
							</th>
							<th>SKU</th>
							<th>Nama Produk</th>
							<th>Harga</th>
							<th>Variant</th>
						</tr>
					</thead>
					<tbody>
				
			<tr>
			
			<?php
  
   // End script ambil data
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'http://sellercenter.twinzahra.com/api/orders.php?request=get_cart_details');
		$payload = json_encode( array( "Page"=> "1" ) );
		$payload = json_encode( array( "UserID"=> "5" ) );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$content = curl_exec($ch);
		curl_close($ch);

  //mengubah data json menjadi data array asosiatif
  $result=json_decode($content,true);
  				if ($result['total_rows'] > 0) {
					
					  foreach($result['data'] as $DataProduct)

						{
						echo '<tr>
							<td>
								<span class="custom-checkbox">
									<input type="checkbox" id="checkbox1" name="options[]" value="1">
									<label for="checkbox1"></label>
								</span>
							</td>';
							
							echo'<td>';
							echo $DataProduct['SkuID'];
							echo '</td>';
					
					
					
							echo'<td>';
		
							echo '<div class="rounded-circle">
							<img src='.$DataProduct['ImageProductVariantName'].'
							alt='.$DataProduct['ProductName'].' width="100" height="100"></div>';
							
							echo $DataProduct['ProductName'];
						
							echo'</td>';
							
							echo'<td>';
							echo $DataProduct['Price'];
							echo '</td>';
					
							echo'<td>';
							echo $DataProduct['ProductVariantName'] . " " . $DataProduct['ProductVariantDetailName'];
							echo '</td>';
							
							
							
							
							
							
							echo '<td>
								<a href="#editEmployeeModal" class="edit" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>
								<a href="#deleteEmployeeModal" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
							</td>
						</tr>';
						
      //echo "Product Name : ".$productname['ProductName']."<br>";
    }     
	
	
	}else{
		
	echo json_encode($result['message']);
	
	}
	

  ?>
  
  
							
							
							
						
	
					
						</tr>
						

   </tbody>
				</table>
				
	</div>
  </div>
				
                               <script>
                                    // Example starter JavaScript for disabling form submissions if there are invalid fields
                                    (function() {
                                        'use strict';
                                        window.addEventListener('load', function() {
                                            // Fetch all the forms we want to apply custom Bootstrap validation styles to
                                            var forms = document.getElementsByClassName('needs-validation');
                                            // Loop over them and prevent submission
                                            var validation = Array.prototype.filter.call(forms, function(form) {
                                                form.addEventListener('submit', function(event) {
                                                    if (form.checkValidity() === false) {
                                                        event.preventDefault();
                                                        event.stopPropagation();
                                                    }
                                                    form.classList.add('was-validated');
                                                }, false);
                                            });
                                        }, false);
                                    })();
                               </script>
							   
							   <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>

<script>

    $(document).on("keypress", "input", function(e){

        if(e.which == 13){

            var inputVal = $(this).val();


			
			var xhr = new XMLHttpRequest();
var url = "http://sellercenter.twinzahra.com/api/orders.php?request=add_cart_detail";
xhr.open("POST", url, true);
xhr.setRequestHeader("Content-Type", "application/json");
xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
		      
        var json = JSON.parse(xhr.responseText);
        console.log(json.UserID + ", " + json.SkuID);
		window.location.href = 'http://sellercenter.twinzahra.com/create_order.php'; 
    }
};
var data = JSON.stringify({"UserID": "5", "SkuID": inputVal});
xhr.send(data);

        }

    });

</script>
						
               
  </div>
  </div>
    
<?php


require './include/footer.php';

?>
     

