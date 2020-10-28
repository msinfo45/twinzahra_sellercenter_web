
<!-- Modal -->
<div class="modal fade" id="SearchProducts" tabindex="-1" role="dialog" aria-labelledby="SearchProducts" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="SearchProducts">Daftar Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
              


                    <span id="success_message"></span>
                    <div class="form-group" id="process">

<?php

$user_id = null;
$page = null;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, base_url('public/api/products.php?request=get_products'));
$payload = json_encode( array( "Page"=> $page ,
    "UserID"=> 5 ,
    "Status"=> 1 ) );
curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$content = curl_exec($ch);
curl_close($ch);

//mengubah data json menjadi data array asosiatif
$result=json_decode($content,true);


echo' <table id="example2" class="table table-bordered table-hover ">
                            <tbody>';
                            if ($result['total_rows'] > 0) {

                            foreach($result['data'] as $DataProduct)
                            {
                            echo '<tr>';
                                echo '<td ><div class="input-group">

                                        <div class="filtr-item col-sm-1" data-category="1" data-sort="'.$DataProduct['ProductName'].'">
                                            <a href="'.$DataProduct['ImageProductName'].'" data-toggle="lightbox" data-title="'.$DataProduct['ProductName'].'">
                                                <img src="'.$DataProduct['ImageProductName'].'" class="img-thumbnail mb-2" alt="image"/>
                                            </a>
                                        </div>';

                                        echo '<div class="css-gjyepm">';
                                            echo '<div class="styPLCProductNameInfo"><h6>';
                                                    echo $DataProduct['ProductName'];
                                                    echo '</h6></div>
                                        </div>';

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


                    echo ' <button class="btn btn-primary col-auto" data-toggle="collapse" data-target="#variant'.$DataProduct['ProductID'].'" >Lihat Variant</button>';

                    echo '<div id="variant'.$DataProduct['ProductID'].'" class="collapse">';


                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, base_url('public/api/products.php?request=get_product_item'));
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
                                
                                    <th style="width:5%">Stok</th>
                                    <th style="width:10%">Barcode</th>
                                </tr>';
                                echo '<tr >';
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

                                echo '<div class="card-body text-center" >'.$result['message'] .'</div>';

                                }
                                echo'</tbody>
                            </table>
                        </div> ';

                                echo' </div> ';

                                echo'</td>';
                                echo'</tr>';
                            }

                            }else{

                                echo '<div class="card-body text-center" >'.$result['message'] .'</div>';

                            }

echo'</tbody>
                 </table>
                 
                 </div> ';
?>
                    </div>


                    <span id="result_message"></span>
                    <div class="modal-footer">

                        <button align="absolute" type="button"  class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                 
                </div>
            </div>
        </div>
