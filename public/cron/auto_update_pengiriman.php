<?php

include "../config/lazada/LazopSdk.php";

include "../config/db_connection.php";
include "../config/config_type.php";

$url='https://api.lazada.co.id/rest';
//Load Models
include "../config/model.php";

$db = new Model_user();

$order_id = array();
$order_id_lazada = array();

$user_id = 5;
$merchant_name = null;
$marketplace = "LAZADA";

if (isset($post['merchant_name'])) {
  $merchant_name = $post['merchant_name'];
}

$getDataLazada = $db->getDataLazada($user_id, $merchant_name);



if ($getDataLazada != null) {



//echo json_encode($rowsHistory);die;
    while ($rowLazada = $getDataLazada->fetch_assoc()) {
      $rows[] = $rowLazada;

    }


    foreach ($rows as $obj) {

      $appkey =  $obj['AppKey'];
      $appSecret =  $obj['AppSecret'];
      $accessToken =  $obj['AccessToken'];
      $merchant_name =  $obj['merchant_name'];

  $getDataHistoryOrders = $db->getHistoryOrder($user_id , $marketplace);

  if ($getDataHistoryOrders != null) {

    while ($rowHistory = $getDataHistoryOrders->fetch_assoc()) {
      $order_id = $rowHistory['order_id'];
      $statuses = (int)$rowHistory['statuses'];
	  
     
      $c = new LazopClient($url,$appkey,$appSecret);
      $request = new LazopRequest('/orders/items/get','GET');
      $request->addApiParam('order_ids', json_encode(array($order_id)));
      $jdecode=json_decode($c->execute($request, $accessToken));
      //$data=$jdecode->data;





      if (isset($jdecode->data)) {

        foreach ($jdecode->data as $datas) {
          $rowsLazada[] = $datas;
          $order_id= $datas -> order_id;
          $order_number = $datas->order_number;


          foreach ($datas -> order_items as $order_items) {
        

            if ($order_items -> status == "pending") {

               $status = 1;
         
             }else if ($order_items -> status == "ready_to_ship") {
         
               $status = 10;
         
             }else if ($order_items -> status == "shipped") {
         
               $status = 3;
         
             }else if ($order_items -> status == "delivered") {
         
               $status = 4;
         
         
             }else if ($order_items -> status == "canceled") {
         
               $status = 6;
         
             }else if ($order_items -> status == "failed") {
         
               $status = 7;
         
             }else if ($order_items -> status == "returned") {
         
               $status = 5;
         
             }else if ($order_items -> status == "unpaid") {
         
               $status = 8;
         
         
             }else{
         
             //  $status = "ALL";
         
             }

             
            $created_at =  $order_items->created_at;
            $name = $order_items->name;
            $sku =  $order_items->sku;
            $paid_price = $order_items->paid_price;
            $shipment_provider =  $order_items->shipment_provider;
            $shipping_amount =  $order_items->shipping_amount;
            $variation =  $order_items->variation;
            $product_main_image = $order_items->product_main_image;
            $product_detail_url= $order_items->product_detail_url;

            ///update status data
           
if ($statuses != $status ) {
				
				
if ($status == 3){
					
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/public/api/orders.php?request=set_ship');
$payload = json_encode( array( "order_id"=> $order_id ,
"created_at"=> $created_at,
"name"=> $name,
"sku"=> $sku,
"paid_price"=> $paid_price,
"shipment_provider"=> $shipment_provider,
"shipping_amount"=> $shipping_amount) );
curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$lazadacontent = curl_exec($ch);
curl_close($ch);
$resultLazada=json_decode($lazadacontent,true);

$status = "dalam pengiriman";

$subject  = "Pesanan " . $order_id . " dalam pengiriman " . $shipment_provider ;
$message = '<div>
<table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr></tbody></table></td>
</tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
Hai Seller,
</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr><tr><td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
Pesanan ' . $order_id . ' dalam pengiriman ' . $shipment_provider .' 
</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="1" bgcolor="#ffffff">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%">
<table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="0">&nbsp;</td></tr><tr><td><table style="table-layout:fixed" width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td colspan="2" style="text-align:left;font-family:Helvetica,arial,sans-serif;color:#1f1f1f;font-size:16px;font-weight:bold;height:10px"> </td></tr><tr><td colspan="2" style="text-align:left;font-family:Helveticƒa,arial,sans-serif;color:#1f1f1f;font-size:13px;font-weight:bold">
DETAIL ORDER </td></tr><tr><td style="font-size:1px;line-height:1px" width="" height="10">&nbsp;</td> </tr>

<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
No. Order: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top">
'. $order_id .'</td>
 </tr>
 
<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
Tanggal Order: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
'. $created_at  .'</td>
</tr>

<tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" align="left"><tbody><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody>

<tr>
<td width="560" height="140" align="left">
<a href="'.$product_detail_url .'" target="_blank">
<img src="'. $product_main_image. '" alt="" style="display:block;border:none;outline:none;text-decoration:none" class="CToWUd" width="140" height="140" border="0">
</a>
</td>
</tr>

</tbody></table><table cellspacing="0" cellpadding="0" border="0" align="left"> <tbody><tr><td style="font-size:1px;line-height:1px" width="100%" height="15">&nbsp;</td></tr></tbody></table><table style="table-layout:fixed" width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td colspan="2" style="word-break:break-word;font-size:1px;line-height:1px" width="" height="20">&nbsp;</td></tr>

<tr>
<td colspan="2" style="word-break:break-word;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left">
'. $name.'
</td>
</tr>

<tr><td colspan="2" style="word-break:break-word;font-size:1px;line-height:1px" width="" height="15">&nbsp;</td></tr>

<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
Sku: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
'. $sku .'</td>
</tr>
											 
<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
Variasi: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
'. $variation .'</td>
</tr>

<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
Jumlah: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
1</td>
</tr>

<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
Harga: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
Rp. '. $paid_price .'</td>
</tr>

<tr><td colspan="2" style="font-size:1px;line-height:1px" width="" height="10">&nbsp;</td> </tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div>
<table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="20">&nbsp;</td></tr><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr>

<tr>
<td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
Hormat Kami,
<br>
Twinzahra Shop
</td>
</tr>

</tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody>

<tr>
<td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#747474;text-align:center;line-height:18px">
Download Aplikasi Twinzahra Shop
</td>
</tr>
<tr><td style="font-size:1px;line-height:1px" width="100%" height="5">&nbsp;</td></tr><tr><td><table width="100%" cellspacing="0" cellpadding="0" border="0"><tbody>

<tr>
<center><a href="https://play.google.com/store/apps/details?id=com.project.msinfo.twinzahra" 
target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://play.google.com/store/apps/details?id%3Dcom.shopee.id&amp;source=gmail&amp;ust=1605079754589000&amp;usg=AFQjCNF3Xjc9cfaH08Hol1C6CfYeVZUMTA"><img src="https://ci3.googleusercontent.com/proxy/wMyUGP_9zlO1kmTJ1wI6w5tG3QYq6dXydCJg0ePOV7p6DUBeZlw99BuZZlU0LOW8jD20PqkxMfCK8ZAGJ7m0OnXAWokK0I08RWyEqio=s0-d-e1-ft#https://cf.shopee.sg/file/cacc3e27277d02501b0989fdcbaf18e9" style="width:130px" class="CToWUd" width="130"></a></center>
                                                         
</tr></tbody></table></td> </tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="5">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><div style="width:100%;height:5px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0"></div></div><div class="yj6qo"></div><div class="adL"></div>
</div>';


$sendEmail = $db->send_email($subject , $message);

}else if ($status == 4) {
					
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/public/api/orders.php?request=set_delivery');
$payload = json_encode( array( "order_id"=> $order_id) );
curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$lazadacontent = curl_exec($ch);
curl_close($ch);
$resultLazada=json_decode($lazadacontent,true);

$status = "delivered";
	
$subject  = "Pesanan " . $order_id . " sudah diterima pembeli";
$message = '<div>
<table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr></tbody></table></td>
</tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
Hai Seller,
</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr><tr><td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
Pesanan ' . $order_id . ' sudah diterima pembeli 
</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="1" bgcolor="#ffffff">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%">
<table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="0">&nbsp;</td></tr><tr><td><table style="table-layout:fixed" width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td colspan="2" style="text-align:left;font-family:Helvetica,arial,sans-serif;color:#1f1f1f;font-size:16px;font-weight:bold;height:10px"> </td></tr><tr><td colspan="2" style="text-align:left;font-family:Helveticƒa,arial,sans-serif;color:#1f1f1f;font-size:13px;font-weight:bold">
DETAIL ORDER </td></tr><tr><td style="font-size:1px;line-height:1px" width="" height="10">&nbsp;</td> </tr>

<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
No. Order: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top">
'. $order_id .'</td>
 </tr>
 
<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
Tanggal Order: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
'. $created_at  .'</td>
</tr>

<tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" align="left"><tbody><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody>

<tr>
<td width="560" height="140" align="left">
<a href="'.$product_detail_url .'" target="_blank">
<img src="'. $product_main_image. '" alt="" style="display:block;border:none;outline:none;text-decoration:none" class="CToWUd" width="140" height="140" border="0">
</a>
</td>
</tr>

</tbody></table><table cellspacing="0" cellpadding="0" border="0" align="left"> <tbody><tr><td style="font-size:1px;line-height:1px" width="100%" height="15">&nbsp;</td></tr></tbody></table><table style="table-layout:fixed" width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td colspan="2" style="word-break:break-word;font-size:1px;line-height:1px" width="" height="20">&nbsp;</td></tr>

<tr>
<td colspan="2" style="word-break:break-word;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left">
'. $name.'
</td>
</tr>

<tr><td colspan="2" style="word-break:break-word;font-size:1px;line-height:1px" width="" height="15">&nbsp;</td></tr>

<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
Sku: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
'. $sku .'</td>
</tr>
											 
<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
Variasi: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
'. $variation .'</td>
</tr>

<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
Jumlah: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
1</td>
</tr>

<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
Harga: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
Rp. '. $paid_price .'</td>
</tr>

<tr><td colspan="2" style="font-size:1px;line-height:1px" width="" height="10">&nbsp;</td> </tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div>
<table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="20">&nbsp;</td></tr><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr>

<tr>
<td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
Hormat Kami,
<br>
Twinzahra Shop
</td>
</tr>

</tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody>

<tr>
<td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#747474;text-align:center;line-height:18px">
Download Aplikasi Twinzahra Shop
</td>
</tr>
<tr><td style="font-size:1px;line-height:1px" width="100%" height="5">&nbsp;</td></tr><tr><td><table width="100%" cellspacing="0" cellpadding="0" border="0"><tbody>

<tr>
<center><a href="https://play.google.com/store/apps/details?id=com.project.msinfo.twinzahra" 
target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://play.google.com/store/apps/details?id%3Dcom.shopee.id&amp;source=gmail&amp;ust=1605079754589000&amp;usg=AFQjCNF3Xjc9cfaH08Hol1C6CfYeVZUMTA"><img src="https://ci3.googleusercontent.com/proxy/wMyUGP_9zlO1kmTJ1wI6w5tG3QYq6dXydCJg0ePOV7p6DUBeZlw99BuZZlU0LOW8jD20PqkxMfCK8ZAGJ7m0OnXAWokK0I08RWyEqio=s0-d-e1-ft#https://cf.shopee.sg/file/cacc3e27277d02501b0989fdcbaf18e9" style="width:130px" class="CToWUd" width="130"></a></center>
                                                         
</tr></tbody></table></td> </tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="5">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><div style="width:100%;height:5px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0"></div></div><div class="yj6qo"></div><div class="adL"></div>
</div>';


$sendEmail = $db->send_email($subject , $message);					
					
}else if ($status == 5) {
					
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/public/api/orders.php?request=set_return');
$payload = json_encode( array( "order_id"=> $order_id) );
curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$lazadacontent = curl_exec($ch);
curl_close($ch);
        
$resultLazada=json_decode($lazadacontent,true);
        
$status = "retur";
          
$subject  = "Pesanan " . $order_id . " dalam perjalan retur";
$message = '<div>
<table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr></tbody></table></td>
</tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
Hai Seller,
</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr><tr><td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
Pesanan ' . $order_id . ' dalam perjalan retur oleh jasa pengiriman ' . $shipment_provider . '
</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="1" bgcolor="#ffffff">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%">
<table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="0">&nbsp;</td></tr><tr><td><table style="table-layout:fixed" width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td colspan="2" style="text-align:left;font-family:Helvetica,arial,sans-serif;color:#1f1f1f;font-size:16px;font-weight:bold;height:10px"> </td></tr><tr><td colspan="2" style="text-align:left;font-family:Helveticƒa,arial,sans-serif;color:#1f1f1f;font-size:13px;font-weight:bold">
DETAIL ORDER </td></tr><tr><td style="font-size:1px;line-height:1px" width="" height="10">&nbsp;</td> </tr>

<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
No. Order: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top">
'. $order_id .'</td>
 </tr>
 
<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
Tanggal Order: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
'. $created_at  .'</td>
</tr>

<tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" align="left"><tbody><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody>

<tr>
<td width="560" height="140" align="left">
<a href="'.$product_detail_url .'" target="_blank">
<img src="'. $product_main_image. '" alt="" style="display:block;border:none;outline:none;text-decoration:none" class="CToWUd" width="140" height="140" border="0">
</a>
</td>
</tr>

</tbody></table><table cellspacing="0" cellpadding="0" border="0" align="left"> <tbody><tr><td style="font-size:1px;line-height:1px" width="100%" height="15">&nbsp;</td></tr></tbody></table><table style="table-layout:fixed" width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td colspan="2" style="word-break:break-word;font-size:1px;line-height:1px" width="" height="20">&nbsp;</td></tr>

<tr>
<td colspan="2" style="word-break:break-word;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left">
'. $name.'
</td>
</tr>

<tr><td colspan="2" style="word-break:break-word;font-size:1px;line-height:1px" width="" height="15">&nbsp;</td></tr>

<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
Sku: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
'. $sku .'</td>
</tr>
											 
<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
Variasi: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
'. $variation .'</td>
</tr>

<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
Jumlah: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
1</td>
</tr>

<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
Harga: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
Rp. '. $paid_price .'</td>
</tr>

<tr><td colspan="2" style="font-size:1px;line-height:1px" width="" height="10">&nbsp;</td> </tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div>
<table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="20">&nbsp;</td></tr><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr>

<tr>
<td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
Hormat Kami,
<br>
Twinzahra Shop
</td>
</tr>

</tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody>

<tr>
<td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#747474;text-align:center;line-height:18px">
Download Aplikasi Twinzahra Shop
</td>
</tr>
<tr><td style="font-size:1px;line-height:1px" width="100%" height="5">&nbsp;</td></tr><tr><td><table width="100%" cellspacing="0" cellpadding="0" border="0"><tbody>

<tr>
<center><a href="https://play.google.com/store/apps/details?id=com.project.msinfo.twinzahra" 
target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://play.google.com/store/apps/details?id%3Dcom.shopee.id&amp;source=gmail&amp;ust=1605079754589000&amp;usg=AFQjCNF3Xjc9cfaH08Hol1C6CfYeVZUMTA"><img src="https://ci3.googleusercontent.com/proxy/wMyUGP_9zlO1kmTJ1wI6w5tG3QYq6dXydCJg0ePOV7p6DUBeZlw99BuZZlU0LOW8jD20PqkxMfCK8ZAGJ7m0OnXAWokK0I08RWyEqio=s0-d-e1-ft#https://cf.shopee.sg/file/cacc3e27277d02501b0989fdcbaf18e9" style="width:130px" class="CToWUd" width="130"></a></center>
                                                         
</tr></tbody></table></td> </tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="5">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><div style="width:100%;height:5px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0"></div></div><div class="yj6qo"></div><div class="adL"></div>
</div>';


$sendEmail = $db->send_email($subject , $message);     

 }else if ($status == 6) {
					
					
                      $ch = curl_init();
                              curl_setopt($ch, CURLOPT_URL, $base_url . '/public/api/orders.php?request=set_cancel');
                              $payload = json_encode( array( "order_id"=> $order_id) );
                              curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
                              curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                              $lazadacontent = curl_exec($ch);
                              curl_close($ch);
                
                              $resultLazada=json_decode($lazadacontent,true);
                
                //echo json_encode($resultLazada);die;
                              $status = "dibatalkan";
 $subject  = "Pesanan " . $order_id . " telah dibatalkan";
$message = '<div>
<table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr></tbody></table></td>
</tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
Hai Seller,
</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr><tr><td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
Pesanan ' . $order_id . ' telah dibatalkan
</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="1" bgcolor="#ffffff">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%">
<table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="0">&nbsp;</td></tr><tr><td><table style="table-layout:fixed" width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td colspan="2" style="text-align:left;font-family:Helvetica,arial,sans-serif;color:#1f1f1f;font-size:16px;font-weight:bold;height:10px"> </td></tr><tr><td colspan="2" style="text-align:left;font-family:Helveticƒa,arial,sans-serif;color:#1f1f1f;font-size:13px;font-weight:bold">
DETAIL ORDER </td></tr><tr><td style="font-size:1px;line-height:1px" width="" height="10">&nbsp;</td> </tr>

<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
No. Order: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top">
'. $order_id .'</td>
 </tr>
 
<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
Tanggal Order: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
'. $created_at  .'</td>
</tr>

<tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" align="left"><tbody><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody>

<tr>
<td width="560" height="140" align="left">
<a href="'.$product_detail_url .'" target="_blank">
<img src="'. $product_main_image. '" alt="" style="display:block;border:none;outline:none;text-decoration:none" class="CToWUd" width="140" height="140" border="0">
</a>
</td>
</tr>

</tbody></table><table cellspacing="0" cellpadding="0" border="0" align="left"> <tbody><tr><td style="font-size:1px;line-height:1px" width="100%" height="15">&nbsp;</td></tr></tbody></table><table style="table-layout:fixed" width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td colspan="2" style="word-break:break-word;font-size:1px;line-height:1px" width="" height="20">&nbsp;</td></tr>

<tr>
<td colspan="2" style="word-break:break-word;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left">
'. $name.'
</td>
</tr>

<tr><td colspan="2" style="word-break:break-word;font-size:1px;line-height:1px" width="" height="15">&nbsp;</td></tr>

<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
Sku: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
'. $sku .'</td>
</tr>
											 
<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
Variasi: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
'. $variation .'</td>
</tr>

<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
Jumlah: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
1</td>
</tr>

<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
Harga: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
Rp. '. $paid_price .'</td>
</tr>

<tr><td colspan="2" style="font-size:1px;line-height:1px" width="" height="10">&nbsp;</td> </tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div>
<table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="20">&nbsp;</td></tr><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr>

<tr>
<td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
Hormat Kami,
<br>
Twinzahra Shop
</td>
</tr>

</tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody>

<tr>
<td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#747474;text-align:center;line-height:18px">
Download Aplikasi Twinzahra Shop
</td>
</tr>
<tr><td style="font-size:1px;line-height:1px" width="100%" height="5">&nbsp;</td></tr><tr><td><table width="100%" cellspacing="0" cellpadding="0" border="0"><tbody>

<tr>
<center><a href="https://play.google.com/store/apps/details?id=com.project.msinfo.twinzahra" 
target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://play.google.com/store/apps/details?id%3Dcom.shopee.id&amp;source=gmail&amp;ust=1605079754589000&amp;usg=AFQjCNF3Xjc9cfaH08Hol1C6CfYeVZUMTA"><img src="https://ci3.googleusercontent.com/proxy/wMyUGP_9zlO1kmTJ1wI6w5tG3QYq6dXydCJg0ePOV7p6DUBeZlw99BuZZlU0LOW8jD20PqkxMfCK8ZAGJ7m0OnXAWokK0I08RWyEqio=s0-d-e1-ft#https://cf.shopee.sg/file/cacc3e27277d02501b0989fdcbaf18e9" style="width:130px" class="CToWUd" width="130"></a></center>
                                                         
</tr></tbody></table></td> </tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="5">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><div style="width:100%;height:5px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0"></div></div><div class="yj6qo"></div><div class="adL"></div>
</div>';


$sendEmail = $db->send_email($subject , $message);                 
                          
                          
                            }else if ($status == 7) {
					
					
                              $ch = curl_init();
                                      curl_setopt($ch, CURLOPT_URL, $base_url . '/public/api/orders.php?request=set_filed');
                                      $payload = json_encode( array( "order_id"=> $order_id) );
                                      curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
                                      curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                                      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                      $lazadacontent = curl_exec($ch);
                                      curl_close($ch);
                        
                                      $resultLazada=json_decode($lazadacontent,true);
                        
                        //echo json_encode($resultLazada);die;
                                      $status = "gagal";
                          
                                      $subject  = "Pesanan " . $order_id . " gagal dikirim";
                                      $message = '<div>
                                      <table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr></tbody></table></td>
                                      </tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
                                      Hai Seller,
                                      </td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr><tr><td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
                                      Pesanan ' . $order_id . ' gagal dikirim oleh jasa pengiriman ' . $shipment_provider . '
                                      </td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="1" bgcolor="#ffffff">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%">
                                      <table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="0">&nbsp;</td></tr><tr><td><table style="table-layout:fixed" width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td colspan="2" style="text-align:left;font-family:Helvetica,arial,sans-serif;color:#1f1f1f;font-size:16px;font-weight:bold;height:10px"> </td></tr><tr><td colspan="2" style="text-align:left;font-family:Helveticƒa,arial,sans-serif;color:#1f1f1f;font-size:13px;font-weight:bold">
                                      DETAIL ORDER </td></tr><tr><td style="font-size:1px;line-height:1px" width="" height="10">&nbsp;</td> </tr>
                                      
                                      <tr>
                                      <td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
                                      No. Order: </td>
                                      <td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top">
                                      '. $order_id .'</td>
                                       </tr>
                                       
                                      <tr>
                                      <td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
                                      Tanggal Order: </td>
                                      <td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
                                      '. $created_at  .'</td>
                                      </tr>
                                      
                                      <tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" align="left"><tbody><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody>
                                      
                                      <tr>
                                      <td width="560" height="140" align="left">
                                      <a href="'.$product_detail_url .'" target="_blank">
                                      <img src="'. $product_main_image. '" alt="" style="display:block;border:none;outline:none;text-decoration:none" class="CToWUd" width="140" height="140" border="0">
                                      </a>
                                      </td>
                                      </tr>
                                      
                                      </tbody></table><table cellspacing="0" cellpadding="0" border="0" align="left"> <tbody><tr><td style="font-size:1px;line-height:1px" width="100%" height="15">&nbsp;</td></tr></tbody></table><table style="table-layout:fixed" width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td colspan="2" style="word-break:break-word;font-size:1px;line-height:1px" width="" height="20">&nbsp;</td></tr>
                                      
                                      <tr>
                                      <td colspan="2" style="word-break:break-word;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left">
                                      '. $name.'
                                      </td>
                                      </tr>
                                      
                                      <tr><td colspan="2" style="word-break:break-word;font-size:1px;line-height:1px" width="" height="15">&nbsp;</td></tr>
                                      
                                      <tr>
                                      <td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
                                      Sku: </td>
                                      <td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
                                      '. $sku .'</td>
                                      </tr>
                                                             
                                      <tr>
                                      <td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
                                      Variasi: </td>
                                      <td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
                                      '. $variation .'</td>
                                      </tr>
                                      
                                      <tr>
                                      <td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
                                      Jumlah: </td>
                                      <td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
                                      1</td>
                                      </tr>
                                      
                                      <tr>
                                      <td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
                                      Harga: </td>
                                      <td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top" width="280">
                                      Rp. '. $paid_price .'</td>
                                      </tr>
                                      
                                      <tr><td colspan="2" style="font-size:1px;line-height:1px" width="" height="10">&nbsp;</td> </tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div>
                                      <table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="20">&nbsp;</td></tr><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr>
                                      
                                      <tr>
                                      <td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
                                      Hormat Kami,
                                      <br>
                                      Twinzahra Shop
                                      </td>
                                      </tr>
                                      
                                      </tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody>
                                      
                                      <tr>
                                      <td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#747474;text-align:center;line-height:18px">
                                      Download Aplikasi Twinzahra Shop
                                      </td>
                                      </tr>
                                      <tr><td style="font-size:1px;line-height:1px" width="100%" height="5">&nbsp;</td></tr><tr><td><table width="100%" cellspacing="0" cellpadding="0" border="0"><tbody>
                                      
                                      <tr>
                                      <center><a href="https://play.google.com/store/apps/details?id=com.project.msinfo.twinzahra" 
                                      target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://play.google.com/store/apps/details?id%3Dcom.shopee.id&amp;source=gmail&amp;ust=1605079754589000&amp;usg=AFQjCNF3Xjc9cfaH08Hol1C6CfYeVZUMTA"><img src="https://ci3.googleusercontent.com/proxy/wMyUGP_9zlO1kmTJ1wI6w5tG3QYq6dXydCJg0ePOV7p6DUBeZlw99BuZZlU0LOW8jD20PqkxMfCK8ZAGJ7m0OnXAWokK0I08RWyEqio=s0-d-e1-ft#https://cf.shopee.sg/file/cacc3e27277d02501b0989fdcbaf18e9" style="width:130px" class="CToWUd" width="130"></a></center>
                                                                                               
                                      </tr></tbody></table></td> </tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="5">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><div style="width:100%;height:5px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0"></div></div><div class="yj6qo"></div><div class="adL"></div>
                                      </div>';
                                      
                                      
                                      $sendEmail = $db->send_email($subject , $message);                                  
                                  
                                    }else if ($status == 8) {
					
					
                                      $ch = curl_init();
                                              curl_setopt($ch, CURLOPT_URL, $base_url . '/public/api/orders.php?request=set_unpaid');
                                              $payload = json_encode( array( "order_id"=> $order_id) );
                                              curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
                                              curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                                              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                              $lazadacontent = curl_exec($ch);
                                              curl_close($ch);
                                
                                              $resultLazada=json_decode($lazadacontent,true);
                                
                                //echo json_encode($resultLazada);die;
                                              $status = "belum dibayar";
                                  
                                          
                                          
                                            }else if ($status == 10) {
					
					
                                              $ch = curl_init();
                                                      curl_setopt($ch, CURLOPT_URL, $base_url . '/public/api/orders.php?request=set_proses');
                                                      $payload = json_encode( array( "order_id"=> $order_id) );
                                                      curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
                                                      curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                                                      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                                      $lazadacontent = curl_exec($ch);
                                                      curl_close($ch);
                                        
                                                      $resultLazada=json_decode($lazadacontent,true);
                                        
                                        //echo json_encode($resultLazada);die;
                                                      $status = "menunggu update";
                                          
                                                  
                                                  
                                                }

            }else{
				
			if ($status == 3){

			$status = "Masih dalam pengiriman";

			}else if ($status == 10) {


			$status = "Masih dalam menunggu update";

			}				
				
				
			}



            $dataOrders[]= array(

              "order_id" => $order_id,
              "order_number" => $order_number,
              //"msg" => $resultLazada,
              "status" => $status

            );



          }


        }

      }
    }






  

      $return= array(
        "status" => 200,
        "message" => "",
        "total_rows" => count($dataOrders),
        "data" => $dataOrders


      );


    }
	}

} else {
  $return = array(
    "status" => 404,
    "message" => "Akun lazada belum diatur"
  );
}

//
echo json_encode($return);




?>
