<?php
include "../../../config/config_type.php";

?>

<div class="col-auto">

    <table class="table table-striped table-hover">
        <thead>
        <tr>

            <th>Nama Promosi</th>
            <th>Produk</th>
            <th>Status</th>
            <th>Priode</th>
            <th>Opsi</th>
        </tr>
        </thead>
        <tbody>

        <tr>

            <?php

            // End script ambil data
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $BASE_URL.'/api/promosi/discount.php?request=get_discount');
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

                foreach($result['data'] as $DataProduct) {
                    echo '<tr>';


                    echo '<td>';
                    echo $DataProduct['DiscountName'];
                    echo '</td>';


                    echo '<td>';

                    echo '<div class="rounded-circle">';

                    echo $DataProduct['CountSku'] . ' Sku';

                    echo '</td>';

                    echo '<td>';
                    echo $DataProduct['DiscountStatusName'];
                    echo '</td>';


                    echo '<td>';
                    echo $DataProduct['StartDate'] . " - " . $DataProduct['EndDate'];
                    echo '</td>';


                    echo '<td>
					<button class="btn btn-primary" data-toggle="collapse" data-target="#discount'.$DataProduct['DiscountID'].'" >Lihat Produk</button>
                    <div id="discount' . $DataProduct['DiscountID'] . '" class="collapse">';

						echo'	</td>';

                }

            }else{

                //echo json_encode($result['message']);

            }

            echo '</tr></tr>
			</tbody>
			</table>';







            ?>

</div>







