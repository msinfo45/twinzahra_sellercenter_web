    
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
curl_setopt($ch, CURLOPT_URL,'/api/products.php?request=get_products');
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
                <img class="img-product" src='.$DataProduct['ImageProductName'].'
                alt='.$DataProduct['ProductName'].' width="50" height="50">';
        echo '<div class="css-gjyepm">';
        echo '<div class="styPLCProductNameInfo"><h6>';
        echo $DataProduct['ProductName'];
        echo '</h6></div>';

        // echo '<div class="css-11v3zrg">';
        // echo $DataProduct['SkuID'];
        //  echo '</div>';

        // echo '<div class="css-11v3zrg">';
        // echo $DataProduct['ProductVariantName'] . " " . $DataProduct['ProductVariantDetailName'];
        //echo '</div>';

        //echo '<div class="css-11v3zrg">';
        //echo $DataProduct['PriceRetail'];
        //  echo '</div>';


        echo '</div></div>';


        echo ' <button class="btn btn-primary" data-toggle="collapse" data-target="#variant'.$DataProduct['ProductID'].'" >Lihat Variant</button>';



        echo '<div id="variant'.$DataProduct['ProductID'].'" class="collapse">';


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, '/api/products.php?request=get_product_item');
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



        echo' <div class="card">
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
        echo '<tr>';
        if ($resultItem->total_rows > 0) {

            foreach ($resultItem -> data as $ProductItem) {

                foreach ($ProductItem-> product_variants as $ProductVariants) {

                    foreach ($ProductVariants-> product_variant_details as $ProductVariantDetails) {



                        echo '<td> ';
                        echo '<div class="css-11v3zrg">';
                        echo $ProductVariants->ProductVariantName . " " . $ProductVariantDetails->ProductVariantDetailName;
                        echo '</div>';
                        echo '</td>';

                        echo '<td> ';
                        echo '<div class="css-11v3zrg">';
                        echo $ProductVariantDetails->SkuID;
                        echo '</div>';
                        echo '</td>';


                        echo '<td> ';
                        echo '<div class="css-11v3zrg">';
                        echo $ProductVariantDetails->PriceRetail;
                        echo '</div>';
                        echo '</td>';

                        echo '<td> ';
                        echo '<div class="css-11v3zrg">';
                        echo $ProductVariantDetails->PriceReseller;
                        echo '</div>';
                        echo '</td>';


                        echo '<td> ';
                        echo '<div class="css-11v3zrg">';
                        echo $ProductVariantDetails->Stock;
                        echo '</div>';
                        echo '</td>';

                        echo '<td> ';
                        echo '<div class="css-11v3zrg">';
                        echo $ProductVariantDetails->Barcode;
                        echo '</div>';
                        echo '</td>';


                        echo '</tr>';

                    }
                }
            }
        }else{

            echo json_encode($result['message']);

        }
        echo'</tbody>
                 </table>
                 </div> ';

        echo' </div> ';

        echo'</td>';
        echo'</tr>';
    }

}else{

    echo json_encode($result['message']);

}

echo'</tbody>
                 </table>
                 </div> ';



?>

