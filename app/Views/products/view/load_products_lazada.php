    
<?php


$user_id = null;
$page = null;
$search = null;
$search_size = null;
$search_color = null;

if (isset($_POST['UserID'])) {
    $user_id = $_POST['UserID'];
}


if (isset($_POST['Page'])) {
    $page = $_POST['Page'];
}

if (isset($_POST['Search'])) {
    $search = $_POST['Search'];

}

if (isset($_POST['SearchSize'])) {
    $search_size = $_POST['SearchSize'];

}

if (isset($_POST['SearchColor'])) {
    $search_color = $_POST['SearchColor'];

}


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, base_url('public/api/lazada.php?request=get_products'));
$payload = json_encode( array( "Page"=> $page ,
    "UserID"=> 5 ,
    "Status"=> 1 ,
    "Search"=> $search,
    "SearchSize"=> $search_size,
    "SearchColor"=> $search_color) );
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
        foreach($DataProduct['skus']as $skus)
        {
            foreach($skus['Images']as $images)
            {
                $image = $images;
            }
            
        

        }


        echo' <div class="card">';

        echo'<div class="card-header">
        <div class="row">   
       <div class="col-auto">'.$DataProduct['merchant_name'].	' </div> 
       
       <div class="ml-auto">Produk belum Sync</div>                          
        </div></div>';

        echo '<div class="card-body">';
        echo '<div class="row">';
        echo '<div class="col-auto">'; 
		echo ' <a href="'.$image.'" data-toggle="lightbox" data-title="'.$DataProduct['name'].'">
              <img class="img-product" width="100px" height="100px" src='.$image.'>      
              </a>';
        echo' </div>';

        echo '<div class="col">';
        echo '<h6>';
        echo $DataProduct['name'];
        echo '</h6></div>';

        echo '</div></div>';


        echo'<div class="card-header">';
        echo'<div class="row">';
        echo' <div class="col justify-content-center align-self-center">';
  
        echo ' <button class="btn btn-primary col-auto" data-toggle="collapse" data-target="#variant'.$DataProduct['item_id'].'" >Lihat Variant</button>';

        echo '<div id="variant'.$DataProduct['item_id'].'" class="collapse">';





     echo' <div class="col">
          <table class="table table-striped table-hover">
             <tbody>';
       echo' <tr>
                     <th style="width:20%">Variant</th>
                      <th style="width:25%">Sku</th>
                     <th style="width:20%">Harga Satuan</th>
                      <th style="width:5%">Stok</th>
                   
                       </tr>';

  echo '<tr >';
//         if ($resultItem->total_rows > 0) {


    foreach($DataProduct['skus'] as $skus)
    {



                        echo '<td> ';
                         echo '<div class="css-11v3zrg">';
                     echo $skus['color_family'] . " " . $skus['size']  ;
                    echo '</div>';
                    echo '</td>';

                        echo '<td> ';
                        echo '<div class="css-11v3zrg">';
                        echo $skus['SellerSku'] ;
                        echo '</div>';
                        echo '</td>';


                        echo '<td> ';
                        echo '<div class="css-11v3zrg">';
                        echo $skus['price'] ;
                        echo '</div>';
                        echo '</td>';



                        echo '<td> ';
                        echo '<div class="css-11v3zrg">';
                        echo $skus['quantity'] ;
                        echo '</div>';
                        echo '</td>';




                    echo '</tr>';

        }
//                 }
//             }
//        }else{

//  echo '<div class="card-body text-center" >'.$result['message'] .'</div>';

//         }
       echo'</tbody>
                 </table>
                 </div> ';

                 
echo' </div></div> ';

echo'<div class="ml-auto">
<a  target="_blank" title="Sync Marketplace"  class="btn btn-primary" 
href="http://localhost/twinzahra/public/api/products.php?request=sync_marketplace">
<img class="img-product" width="15px" height="15px" src="public/images/update.png"> 
</a>   
 </div>';
        echo' </div></div> ';

        echo'</div>';
   }

}else{

   echo '<div class="card-body text-center" >'.$result['message'] .'</div>';

}

echo'</div></div> ';



?>
