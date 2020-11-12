    
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
curl_setopt($ch, CURLOPT_URL, base_url('public/api/products.php?request=get_products'));
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
        echo' <div class="container table-bordered p-1">';
        echo '<div class="row">';
        echo '<div class="col-auto">';
		echo ' <a href="'.$DataProduct['Images'].'" data-toggle="lightbox" data-title="'.$DataProduct['ProductName'].'">
               <img class="img-product" width="100px" height="100px" src='.$DataProduct['Images'].'>
              </a>';
      echo' </div>';

        echo '<div class="col">';
        echo '<h6>';
        echo $DataProduct['ProductName'];
        echo '</h6></div>';


        echo '</div>';


        echo ' <button class="btn btn-primary col-auto" data-toggle="collapse" data-target="#variant'.$DataProduct['ProductID'].'" >Lihat Variant</button>';

        echo '<div id="variant'.$DataProduct['ProductID'].'" class="collapse">';


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, base_url('public/api/products.php?request=get_product_items'));
        $payload = json_encode( array(
            "UserID"=> 5 ,
            "ProductID"=> $DataProduct['ProductID'] ) );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($ch);
        curl_close($ch);

        //mengubah data json menjadi data array asosiatif
        $resultItem=json_decode($content);



        echo' <div class="col">
                <table class="table table-striped table-hover">
                <tbody>';
        echo' <tr>
                       <th style="width:20%">Variant</th>
                       <th style="width:25%">Sku</th>
                       <th style="width:20%">Harga Satuan</th>
                       <th style="width:20%">Harga Reseller</th>
                       <th style="width:5%">Stok</th>
                       <th style="width:10%">Barcode</th>
                       </tr>';
        echo '<tr >';
        if ($resultItem->total_rows > 0) {

          foreach ($resultItem -> data as $ProductItem) {

            foreach ($ProductItem-> skus as $skus) {

                   // foreach ($ProductVariants-> product_variant_details as $ProductVariantDetails) {



                        echo '<td> ';
                        echo '<div class="css-11v3zrg">';
                       echo $skus->ProductVariantName . " " . $skus->ProductVariantDetailName;
                        echo '</div>';
                        echo '</td>';

                        echo '<td> ';
                        echo '<div class="css-11v3zrg">';
                        echo $skus->SkuID;
                        echo '</div>';
                        echo '</td>';


                        echo '<td> ';
                        echo '<div class="css-11v3zrg">';
                       echo $skus->PriceRetail;
                        echo '</div>';
                        echo '</td>';

                        echo '<td> ';
                        echo '<div class="css-11v3zrg">';
                        echo $skus->PriceReseller;
                        echo '</div>';
                        echo '</td>';


                        echo '<td> ';
                        echo '<div class="css-11v3zrg">';
                        echo $skus->Stock;
                        echo '</div>';
                        echo '</td>';

                        echo '<td> ';
                        echo '<div class="css-11v3zrg">';
                        echo $skus->Barcode;
                        echo '</div>';
                        echo '</td>';


                        echo '</tr>';

                  //}
                }
            }
       }else{

 echo '<div class="card-body text-center" >'.$result['message'] .'</div>';

        }
        echo'</tbody>
                 </table>
                 </div> ';

        echo' </div> ';

        echo'</div>';
    }

}else{

   echo '<div class="card-body text-center" >'.$result['message'] .'</div>';

}

echo'</div></div> ';



?>
