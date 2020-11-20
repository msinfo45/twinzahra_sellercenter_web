<?php

include "../config/lazada/LazopSdk.php";
include "../config/db_connection.php";
include "../config/config_type.php";
include "../config/model.php";

$url='https://api.lazada.co.id/rest';
$db = new Model_user();
$order_id = array();
$order_id_lazada = array();
$user_id = 5;
$merchant_name = null;

if (isset($post['merchant_name'])) {
  $merchant_name = $post['merchant_name'];
}


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/public/api/lazada.php?request=get_orders');
$payload = json_encode( array(
  "status"=> 1,
  "merchant_name"=> $merchant_name) );
curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$contentOrders = curl_exec($ch);
curl_close($ch);
$resultOrders=json_decode($contentOrders,true);


foreach($resultOrders['data'] as $dataOrders)
{

  $order_id = $dataOrders['order_id'] ;
  $merchant_name = $dataOrders['merchant_name'] ;
  $marketplace = $dataOrders['marketplace'] ;
  $created_at =  $dataOrders['created_at'];
foreach($dataOrders['order_items'] as $orderItems) {

  $sku =  $orderItems['sku'];

}
    //echo json_encode($sku);die;
    $chItems = curl_init();
    curl_setopt($chItems, CURLOPT_URL,$base_url . '/public/api/products.php?request=cek_stok');
    $payloadItem = json_encode( array( "sku"=> $sku ) );
    curl_setopt( $chItems, CURLOPT_POSTFIELDS, $payloadItem );
    curl_setopt( $chItems, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($chItems, CURLOPT_RETURNTRANSFER, 1);
    $contentItem = curl_exec($chItems);
    curl_close($chItems);

    $resultItem=json_decode($contentItem,true);

    $stock =$resultItem['data'];

   // echo json_encode($stock);die;

    if ($stock > 0) {


      $chItems = curl_init();
      curl_setopt($chItems, CURLOPT_URL,$base_url . '/public/api/orders.php?request=accept_order');
      $payloadItem = json_encode( array(
        "order_id"=> $order_id ,
        "merchant_name"=> $merchant_name ,
        "marketplace"=> $marketplace,
        "shipping_provider"=> "dropship" ,
        "delivery_type"=> "dropship"
      ));

      //echo $payloadItem;die;

      curl_setopt( $chItems, CURLOPT_POSTFIELDS, $payloadItem );
      curl_setopt( $chItems, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
      curl_setopt($chItems, CURLOPT_RETURNTRANSFER, 1);
      $contentItem = curl_exec($chItems);
     curl_close($chItems);
      $resultItem=json_decode($contentItem,true);

      //echo json_encode($resultItem);die;

if ($resultItem['status'] == "200"){


  $status = "berhasil";
  $subject  = "Pesanan " . $order_id . " berhasil dikonfirmasi otomatis" ;
  $message = '<div>
<table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr></tbody></table></td>
</tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
Hai Seller,
</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr><tr><td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
Pesanan ' . $order_id . ' berhasil dikonfirmasi otomatis, Mohon untuk segera kirimkan produk berikut.
</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="1" bgcolor="#ffffff">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%">
<table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="0">&nbsp;</td></tr><tr><td><table style="table-layout:fixed" width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td colspan="2" style="text-align:left;font-family:Helvetica,arial,sans-serif;color:#1f1f1f;font-size:16px;font-weight:bold;height:10px"> </td></tr><tr><td colspan="2" style="text-align:left;font-family:Helveticƒa,arial,sans-serif;color:#1f1f1f;font-size:13px;font-weight:bold">
DETAIL ORDER </td></tr><tr><td style="font-size:1px;line-height:1px" width="" height="10">&nbsp;</td> </tr>

<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
Marketplace: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top">
'. $marketplace .'</td>
 </tr>
 
 <tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
Nama Toko: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top">
'. $merchant_name .'</td>
 </tr>
 
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

<tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table>';

  foreach($dataOrders['order_items'] as $orderItems) {
    $name = $orderItems['name'];
    $sku =  $orderItems['sku'];
    $paid_price = $orderItems['paid_price'];
    $shipment_provider =  $orderItems['shipment_provider'];
    $shipping_amount =  $orderItems['shipping_amount'];
    $variation =  $orderItems['variation'];
    $qty = $orderItems['qty'];
    $product_main_image = $orderItems['image_variant'];
    $product_detail_url= $orderItems['product_detail_url'];

    $message .= '<table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" align="left"><tbody><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody>

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

<tr><td colspan="2" style="font-size:1px;line-height:1px" width="" height="10">&nbsp;</td> </tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table>';

  }

  $message .= '<div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div>

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
}else{

$status = "gagal";
$subject  = "Pesanan " . $order_id . " gagal dikonfirmasi otomatis" ;
$message = '<div>
<table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr></tbody></table></td>
</tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
Hai Seller,
</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr><tr><td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
Pesanan ' . $order_id . ' gagal dikonfirmasi karna ada masalah disistem, silahkan untuk menghubungi admin.
</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="1" bgcolor="#ffffff">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%">
<table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="0">&nbsp;</td></tr><tr><td><table style="table-layout:fixed" width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td colspan="2" style="text-align:left;font-family:Helvetica,arial,sans-serif;color:#1f1f1f;font-size:16px;font-weight:bold;height:10px"> </td></tr><tr><td colspan="2" style="text-align:left;font-family:Helveticƒa,arial,sans-serif;color:#1f1f1f;font-size:13px;font-weight:bold">
DETAIL ORDER </td></tr><tr><td style="font-size:1px;line-height:1px" width="" height="10">&nbsp;</td> </tr>

<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
Marketplace: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top">
'. $marketplace .'</td>
 </tr>
 
 <tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
Nama Toko: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top">
'. $merchant_name .'</td>
 </tr>
 
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

<tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table>';

  foreach($dataOrders['order_items'] as $orderItems) {
    $name = $orderItems['name'];
    $sku =  $orderItems['sku'];
    $paid_price = $orderItems['paid_price'];
    $shipment_provider =  $orderItems['shipment_provider'];
    $shipping_amount =  $orderItems['shipping_amount'];
    $variation =  $orderItems['variation'];
    $qty = $orderItems['qty'];
    $product_main_image = $orderItems['image_variant'];
    $product_detail_url= $orderItems['product_detail_url'];

    $message .= '<table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" align="left"><tbody><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody>

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

<tr><td colspan="2" style="font-size:1px;line-height:1px" width="" height="10">&nbsp;</td> </tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table>';

  }

  $message .= '<div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div>

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

}

}else if ($stock == null) {

$status = "gagal";
$subject  = "Pesanan " . $order_id . " gagal dikonfirmasi otomatis" ;
$message = '<div>
<table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr></tbody></table></td>
</tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
Hai Seller,
</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr><tr><td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
Pesanan ' . $order_id . ' gagal dikonfirmasi karna produk belum terdaftar didatabase, silahkan terlebih dahulu untuk menambahkan produk berikut.
</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="1" bgcolor="#ffffff">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%">
<table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="0">&nbsp;</td></tr><tr><td><table style="table-layout:fixed" width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td colspan="2" style="text-align:left;font-family:Helvetica,arial,sans-serif;color:#1f1f1f;font-size:16px;font-weight:bold;height:10px"> </td></tr><tr><td colspan="2" style="text-align:left;font-family:Helveticƒa,arial,sans-serif;color:#1f1f1f;font-size:13px;font-weight:bold">
DETAIL ORDER </td></tr><tr><td style="font-size:1px;line-height:1px" width="" height="10">&nbsp;</td> </tr>

<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
Marketplace: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top">
'. $marketplace .'</td>
 </tr>
 
 <tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
Nama Toko: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top">
'. $merchant_name .'</td>
 </tr>
 
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

<tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table>';

foreach($dataOrders['order_items'] as $orderItems) {
  $name = $orderItems['name'];
  $sku =  $orderItems['sku'];
  $paid_price = $orderItems['paid_price'];
  $shipment_provider =  $orderItems['shipment_provider'];
  $shipping_amount =  $orderItems['shipping_amount'];
  $variation =  $orderItems['variation'];
  $qty = $orderItems['qty'];
  $product_main_image = $orderItems['image_variant'];
  $product_detail_url= $orderItems['product_detail_url'];

$message .= '<table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" align="left"><tbody><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody>

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

<tr><td colspan="2" style="font-size:1px;line-height:1px" width="" height="10">&nbsp;</td> </tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table>';

}

$message .= '<div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div>

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

}else{

$status = "gagal";
$subject  = "Pesanan " . $order_id . " gagal dikonfirmasi otomatis" ;
$message = '<div>
<table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr></tbody></table></td>
</tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
Hai Seller,
</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr><tr><td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
Pesanan ' . $order_id . ' gagal dikonfirmasi karna stok produk kosong, silahkan terlebih dahulu untuk menambahkan stok produk berikut.
</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr><tr><td style="font-size:1px;line-height:1px" width="100%" height="1" bgcolor="#ffffff">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div><table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%">
<table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center"><tbody><tr><td style="font-size:1px;line-height:1px" height="0">&nbsp;</td></tr><tr><td><table style="table-layout:fixed" width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td colspan="2" style="text-align:left;font-family:Helvetica,arial,sans-serif;color:#1f1f1f;font-size:16px;font-weight:bold;height:10px"> </td></tr><tr><td colspan="2" style="text-align:left;font-family:Helveticƒa,arial,sans-serif;color:#1f1f1f;font-size:13px;font-weight:bold">
DETAIL ORDER </td></tr><tr><td style="font-size:1px;line-height:1px" width="" height="10">&nbsp;</td> </tr>

<tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
Marketplace: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top">
'. $marketplace .'</td>
 </tr>
 
 <tr>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">
Nama Toko: </td>
<td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top">
'. $merchant_name .'</td>
 </tr>
 
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

<tr><td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="font-size:1px;line-height:1px" height="10">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table>';

      foreach($dataOrders['order_items'] as $orderItems) {
        $name = $orderItems['name'];
        $sku =  $orderItems['sku'];
        $paid_price = $orderItems['paid_price'];
        $shipment_provider =  $orderItems['shipment_provider'];
        $shipping_amount =  $orderItems['shipping_amount'];
        $variation =  $orderItems['variation'];
        $qty = $orderItems['qty'];
        $product_main_image = $orderItems['image_variant'];
        $product_detail_url= $orderItems['product_detail_url'];

        $message .= '<table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"><tbody><tr><td><table width="600" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td width="100%"><table width="600" cellspacing="0" cellpadding="0" border="0" align="left"><tbody><tr><td><table width="560" cellspacing="0" cellpadding="0" border="0" align="center"><tbody>

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

<tr><td colspan="2" style="font-size:1px;line-height:1px" width="" height="10">&nbsp;</td> </tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table>';

      }

      $message .= '<div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div><div style="width:100%;height:1px;display:block" align="center"><div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">&nbsp;</div></div>

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
    }





    $dataOrder[] = array(
      "marketplace" => $marketplace,
      "merchant_name" => $merchant_name,
      "order_id" => $order_id,
      "sku" => $sku,
      "status" => $status
    );



  //}
}


$return = array(
  "status" => 200,
  "data" => $dataOrder
);





echo json_encode($return);

?>
