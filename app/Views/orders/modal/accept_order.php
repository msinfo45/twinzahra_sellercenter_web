
<!-- Modal -->
<div class="modal fade" id="AcceptOrder" tabindex="-1" role="dialog" aria-labelledby="AcceptOrder" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="AcceptOrder">Atur Pengiriman</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               	<?php
					$chAC = curl_init();
					curl_setopt($chAC, CURLOPT_URL, 'http://localhost/api/lazada.php?request=get_shipment_providers');
					curl_setopt( $chAC, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
					curl_setopt($chAC, CURLOPT_RETURNTRANSFER, 1);
					$contentAC = curl_exec($chAC);
					curl_close($chAC);

					//mengubah data json menjadi data array asosiatif
					$resultShipment = json_decode($contentAC,true);
					
					 
										  
							echo'<p class="statusMsg"></p><input type="hidden"  name="order_id" id="order_id">
							<input type="hidden"  name="merchant_name" id="merchant_name">
							<input type="hidden"  name="marketplace" id="marketplace">';
							
							echo' <div class="col-md-9 mb-3">';
						
						
											echo '<select class="form-control" name="shipping_providers" id="shipping_providers">
											
											<option value="">Pilih Jasa Pengiriman</option>';
											
												//foreach($resultShipment as $dataShipment)
												//{
										
													
													
											//echo '<option value='.$dataShipment['name'].'>'.$dataShipment['name'].'</option>';
											echo '<option value="JNE MP">JNE MP</option>	';
											echo '<option value="LEX ID">LEX ID</option>	';
											echo '<option value="Ninja Van MP">Ninja Van MP</option>	';
												//}											
											echo'</select>';
									
                                   
                                        echo'</div>';
										
										
										echo'<div class="col-md-9 mb-3">';
 
											echo '<select class="form-control" name="delivery_type" id="delivery_type">
											
											<option value="">Metode Pengiriman</option>';

											echo '<option value="dropship">dropship</option>	';
																					
											echo'</select>';
									
                                   
                                        echo'</div>';
										
										   ?>	
										
								


								
            </div>
            <div class="modal-footer">
             <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary "  onclick="SendAcceptOrders()">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>



          
