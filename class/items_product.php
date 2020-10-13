<?php
//Daftar Produk

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
		curl_setopt($ch, CURLOPT_URL, 'https://sellercenter.twinzahra.com/api/products.php?request=get_products');
		$payload = json_encode( array( "Page"=> $page ,
		"UserID"=> 5 ,
		"Status"=> 1 ,
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
							
		
							echo '<td> <div class="css-1cagh9d">
							
							<img class="img-product" src='.$DataProduct['ImageProductVariantName'].'
							alt='.$DataProduct['ProductName'].' width="80" height="80">';
							
							echo '<div class="css-gjyepm">';
							
							echo '<div class="styPLCProductNameInfo"><h6>';
							echo $DataProduct['ProductName'];	
							echo '</h6></div>';
							
							echo '<div class="css-11v3zrg">';
							echo $DataProduct['SkuID'];
							echo '</div>';
							
							echo '<div class="css-11v3zrg">';
							echo $DataProduct['ProductVariantName'] . " " . $DataProduct['ProductVariantDetailName'];
							echo '</div>';
							
							echo '<div class="css-11v3zrg">';
							echo $DataProduct['PriceRetail'];
							echo '</div>';
							
							echo '<div class="css-11v3zrg">';
							echo "Stok: " . $DataProduct['Stock'];
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
		
	