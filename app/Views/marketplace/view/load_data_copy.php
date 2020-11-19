
<?php

    $post = json_decode(file_get_contents("php://input"), true);

    $seller_id = $post['seller_id'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, base_url('public/api/lazada/create_product'));
    $payload = json_encode( array( "user_id"=> "5",
        "seller_id"=> $seller_id
    ) );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $content = curl_exec($ch);
    curl_close($ch);
    $result=json_decode($content,true);




if (count($result['data']) > 0) {



    echo ' <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
  <thead>
    <tr>
      <th scope="col">Marketplace</th>
      <th scope="col">Nama Toko</th>
      <th scope="col">Nama Produk</th>
      <th scope="col">Status</th>
       <th scope="col">Kode Error</th>
    </tr>
  </thead>
  <tbody>';


    foreach($result['data'] as $DataProduct)
    {

        //Set Variable History Orders
        $marketplace = $DataProduct['marketplace'] ;
        $merchant_name = $DataProduct['merchant_name'] ;
        $product_name = $DataProduct['product_name'] ;
        $status = $DataProduct['status'] ;
        $msg = $DataProduct['msg'] ;
        $code = $DataProduct['code'] ;

       echo ' <tr>
      <td>'.$marketplace.	'</td>
        <td>'.$merchant_name.	'</td>
      <td>'.$product_name.	'</td>
      <td>'.$status.	'</td>
       <td>'.$msg.	'</td>
    </tr>';



    }

   echo' </tbody></table></div>';


}else{

    echo '<div class="card-body text-center" >'.$result['message'] .'</div>';


}



?>

