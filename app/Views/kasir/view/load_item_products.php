
				
			<div class="col-auto">
	
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
							<th>Harga Satuan</th>
                            <th>Jumlah</th>
                            <th>Harga Akhir</th>
                            <th>Opsi</th>
						</tr>
					</thead>
					<tbody>
				
			<tr>
			
			<?php
  
   // End script ambil data
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, base_url('public/api/orders.php?request=get_cart_details'));
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
							echo $DataProduct['SKU'];
							echo '</td>';
					
					
					
							echo'<td>';
		
							echo '<div class="rounded-circle">';
							
							echo $DataProduct['ProductName'];
						
							echo'</td>';
							
							echo'<td>';
							echo $DataProduct['Price'];
							echo '</td>';

                            echo'<td>';
                            echo $DataProduct['Quantity'];
                            echo '</td>';

                            echo'<td>';
                            echo $DataProduct['Price'] * $DataProduct['Quantity'] ;
                            echo '</td>';
					

						
							
							
							
							
							echo '<td>
								<a href="#" data-id="'.$DataProduct['CartDetailID'].'" class="deleteItemCartDetail" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete">Hapus</i></a>
							</td>';
							
							  } 
							  
		}else{

		//echo json_encode($result['message']);

		}
							  
						echo '</tr></tr>
			</tbody>
			</table>';
									

	
	
	
	

  ?>

</div>







  