<?php


		 $user_id = null;
				
		$page = null;
					
		$search = null;
					
					
		if (isset($_POST['UserID'])) {
         $user_id = $_POST['UserID'];
         }
					
					
       if (isset($_POST['Page'])) {
        $page = $_POST['Page'];
        }
					
		if (isset($_POST['Search'])) {
       $search = $_POST['Search'];
	   

        }

		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://sellercenter.twinzahra.com/api/lazada.php?request=get_products');
		$payload = json_encode( array( "Page"=> $page ,
		"Search"=> $search) );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$content = curl_exec($ch);
		curl_close($ch);

  //mengubah data json menjadi data array asosiatif
  $result=json_decode($content,true);
  

		
		
		
		
		 echo' 
 
			<div class="card">
			<table class="table table-striped table-hover">
			<tbody>';
					if ($result['total_rows'] > 0) {
					
					  foreach($result['data'] as $DataProduct)

					  {
		
		
		
			echo '<tr>';
							
		
							echo '<td>';
							//echo json_encode($images[0]);							
							echo'<div class="css-1cagh9d">';
							// foreach($DataProduct['images'] as $image)
							//{
			
							$images = $image;
							echo'<img class="img-product" src='.$DataProduct['images'][0].'
							alt='.$DataProduct['name'].' width="80" height="80">';
							//}
					
							
							echo '<div class="css-gjyepm">';
							
							echo '<div class="styPLCProductNameInfo"><h6>';
							echo $DataProduct['merchant_name'];	
							echo '</h6></div>';
							
							echo '<div class="styPLCProductNameInfo"><h6>';
							echo $DataProduct['name'];	
							echo '</h6></div>';
							
							echo '<div class="css-11v3zrg">';
							echo $DataProduct['SellerSku'];
							echo '</div>';
							
							echo '<div class="css-11v3zrg">';
							echo $DataProduct['price'];
							echo '</div>';
							
							echo '<div class="css-11v3zrg">';
							echo "Stok: " . $DataProduct['quantity'];
							echo '</div>';
							
							
							echo '</div></div>';
							

						
							echo'</td>';
							
					
						echo'</tr>';
						

    }     
	
	
	}else{
		
	echo json_encode($result['message']);
	
	}
	
					
					 
					echo'</tbody>
				</table>
				
		</div> 
		';   
			echo'</div><a id="inifiniteLoader">Loading... <img src="images/loading.gif" /></a>';

	
	

	?>
	
	<script>
	
	
	jQuery(document).ready(function($) {
          var count = 2;
          $(window).scroll(function(){
                  if  ($(window).scrollTop() == $(document).height() - $(window).height()){
                    loadProducts(count);
                     count++;
                  }
          }); 

      });
	  
	  
	  </script>
		
	