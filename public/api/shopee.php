<?php

include "../config/db_connection.php";
include "../config/config_type.php";


$rowsShopee= array();
$content = $_GET['request'];
if (isset($content) && $content != "") {

  //Load Models
  include "../models/Model_Shopee.php";
  include "../config/model.php";
  $db = new Model_Shopee();
  $dbModel = new Model_user();
  //Timestamp
  $tgl="Y-m-d";
  $waktu="H:i:s";
  $waktu_sekarang=date("$tgl $waktu");
  $ditambah_5_menit = date("$tgl $waktu", strtotime('+5 minutes'));
  $timestamp=strtotime($ditambah_5_menit);

  if ($content == "auth_partner") {

    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);

    $tgl="Y-m-d";
    $waktu="H:i:s";
    $waktu_sekarang=date("$tgl $waktu");
    $ditambah_5_menit = date("$tgl $waktu", strtotime('+5 minutes'));

    $path = "/api/v1/shop/auth_partner";
    $partner_id = "844193";
    $partner_key = "ca1e9d30c90d5982f2e9a0d1e2a42e8f18650b008c02d289592a153a53a32fc2";
    $redirect = "google.com";
    $timestamp=strtotime($ditambah_5_menit);
    $base_string = $partner_key . $redirect;
    $token = hash('sha256', $base_string);
    $hmac = hash_hmac('sha256', "https://partner.shopeemobile.com/api/v1/orders/detail|");
    echo $timestamp;die;
    echo  "https://partner.shopeemobile.com/api/v1/shop/auth_partner?id=".$partner_id."&token=".$token."&redirect=".$redirect."";die;
    // persiapkan curl
    $ch = curl_init();

    // set url
    curl_setopt($ch, CURLOPT_URL, "https://partner.uat.shopeemobile.com/api/v2/shop/auth_partner?partner_id='".$partner_id."'&timestamp='".$timestamp."'&sign='".$sign."'&redirect='".$redirect."'");

    // return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // $output contains the output string
    $output = curl_exec($ch);

    // tutup curl
    curl_close($ch);

    // menampilkan hasil curl
    echo $output;




    $return = array(
      "status" => 200,
      "message" => "Berhasil",
      "data" => $contentItem

    );






    //
    echo json_encode($return);

  }

  if ($content == "get_products") {


    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);


    $user_id = 5;
    $marketplace = "SHOPEE";
    $seller_id = null;

    if (isset($post['seller_id'])) {
    $seller_id = $post['seller_id'];
    }

    $resultArr = [];

    if (isset($user_id)) {

      $getDataMarketplace = $dbModel->getDataMarketplace($marketplace);

      if ($getDataMarketplace != null) {

        while ($rowMarketplace = $getDataMarketplace->fetch_assoc()) {

          $appkey = $rowMarketplace['app_key'];
          $appSecret = $rowMarketplace['app_secret'];

        }

        //mencari data toko
        $getDataToko= $dbModel->getDataToko($user_id , $seller_id , $marketplace);

        if ($getDataToko != null) {

          while ($rowToko= $getDataToko->fetch_assoc()) {
            $rows[] = $rowToko;

          }

          foreach ($rows as $rowsToko) {

       $accessToken = $rowsToko['access_token'];
       $merchant_name = $rowsToko['merchant_name'];
       $marketplace_name = $rowsToko['marketplace_name'];
       $shop_id = $rowsToko['seller_id'];

        $tgl = "Y-m-d";
        $waktu = "H:i:s";
        $waktu_sekarang = date("$tgl $waktu");
        $ditambah_5_menit = date("$tgl $waktu", strtotime('+5 minutes'));


        $url = "https://partner.shopeemobile.com/api/v1/items/get";
        $pagination_offset = 0;
        $pagination_entries_per_page = 100;
        $timestamp = strtotime($ditambah_5_menit);


        $convertJson = array("pagination_offset" => $pagination_offset,
          "pagination_entries_per_page" => $pagination_entries_per_page,
          "partner_id" => (int)$appkey,
          "shopid" => (int)$shop_id,
          "timestamp" => $timestamp);


        $base_string = $url . "|" . json_encode($convertJson);
        $hmac = hash_hmac('sha256', $base_string, $appSecret);

        $ch = curl_init($url);
        $payload = json_encode($convertJson);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json',
          'Authorization: ' . $hmac . ''));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $jsonDecode = json_decode($result);
        $items = $jsonDecode->items;

        foreach ($items as $products) {

          $product_id = $products->item_id;

          $tgl = "Y-m-d";
          $waktu = "H:i:s";
          $waktu_sekarang = date("$tgl $waktu");
          $ditambah_5_menit = date("$tgl $waktu", strtotime('+5 minutes'));


          $url = "https://partner.shopeemobile.com/api/v1/item/get";
          $pagination_offset = 0;
          $pagination_entries_per_page = 100;
          $timestamp = strtotime($ditambah_5_menit);


          $convertJson = array("pagination_offset" => $pagination_offset,
            "item_id" => (int)$product_id,
            "partner_id" => (int)$appkey,
            "shopid" => (int)$shop_id,
            "timestamp" => $timestamp);

          $base_string = $url . "|" . json_encode($convertJson);


          $hmac = hash_hmac('sha256', $base_string, $appSecret);


          $ch = curl_init($url);
          $payload = json_encode($convertJson);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
          curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json',
            'Authorization: ' . $hmac . ''));
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $result = curl_exec($ch);
          curl_close($ch);
          $jsonDecode = json_decode($result);

         //echo json_encode($jsonDecode);die;
          $items = $jsonDecode->item;

          $product_name = $items -> name;
          $description = $items ->description;
          foreach ($items -> images as $Images) {
            $image = $Images;

            //$imagesArr[$product_id]['Images'][] = $image;

          }

         // $resultImages = array_values($imagesArr);

          foreach ($items -> variations as $skus) {


            $skusArr = array (
              "ProductVariantID" => $skus->variation_id,
              "ProductVariantName" => "",
              "ProductVariantDetailName" => $skus->name,
              "price" => $skus->price,
              "PriceRetail" => $skus->original_price,
              "PriceReseller" => "",
              "Stock" => $skus->stock,
              "SkuID" => $skus->variation_sku,
              "Barcode" => "",
              "Images" => array($image)

            );


          }

          $productArr[$product_id]['marketplace'] = $marketplace;
          $productArr[$product_id]['merchant_name'] = $merchant_name;
          $productArr[$product_id]['ProductID'] = $product_id;
          $productArr[$product_id]['UserID'] = $user_id;
          $productArr[$product_id]['SupplierID'] = "";
          $productArr[$product_id]['ProductName'] = $product_name;
          $productArr[$product_id]['CategoryID'] = "";
          $productArr[$product_id]['BrandID'] = "";
          $productArr[$product_id]['Description'] = $description;
          $productArr[$product_id]['Images'][] = $image;
          $productArr[$product_id]['skus'][]= $skusArr;



        }

            $result = array_values($productArr);



        $return = array(
          "status" => 200,
          "message" => "ok",
          "total_rows" => COUNT($result),
          "data" => $result

        );






}
}
      }

    } else {
      $return = array(
        "status" => 404,
        "message" => "Oops sepertinya ada yang salah!"
      );
    }

    //
    echo json_encode($return);

  }

  if ($content == "get_product_items") {

    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);

    $productArr = [];
    $skusArr = [];
    $imageskusArr = [];
$item_id = $post['item_id'];
//$item_id =   (int)2743844751;
$user_id = 5;
$merchant_name = null;

if (isset($post['merchant_name'])) {
  $merchant_name = $post['merchant_name'];
}
 //  $merchant_name = "Twinzahra Shop";


$getDataShopee = $db->getDataShopee($user_id, $merchant_name);

if ($getDataShopee != null) {

  while ($rowShopee = $getDataShopee->fetch_assoc()) {
    $rows[] = $rowShopee;

  }

  foreach ($rows as $obj) {

    $partner_id = $obj['partner_id'];
    $partner_key = $obj['partner_key'];
    $shop_id = $obj['shop_id'];
    $code = $obj['code'];
    $merchant_name = $obj['merchant_name'];

    $tgl = "Y-m-d";
    $waktu = "H:i:s";
    $waktu_sekarang = date("$tgl $waktu");
    $ditambah_5_menit = date("$tgl $waktu", strtotime('+5 minutes'));


    $url = "https://partner.shopeemobile.com/api/v1/item/get";
    $pagination_offset = 0;
    $pagination_entries_per_page = 100;
    $timestamp = strtotime($ditambah_5_menit);


    $convertJson = array("pagination_offset" => $pagination_offset,
      "item_id" => (int)$item_id,
      "partner_id" => (int)$partner_id,
      "shopid" => (int)$shop_id,
      "timestamp" => $timestamp);

    $base_string = $url . "|" . json_encode($convertJson);


    $hmac = hash_hmac('sha256', $base_string, $partner_key);


    $ch = curl_init($url);
    $payload = json_encode($convertJson);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json',
      'Authorization: ' . $hmac . ''));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    $jsonDecode = json_decode($result);

    $items = $jsonDecode->item;
 //echo json_encode($items);die;


    $product_name = $items -> name;
    $description = $items ->description;
   // $images = $items ->description;
  foreach ($items -> images as $Images) {
    $image[] = $Images;
  }

    foreach ($items -> variations as $skus) {


      $skusArr = array ("ProductVariantID" => $skus->variation_id,
        "ProductVariantDetailName" => $skus->variation_sku,
        "original_price" => $skus->original_price,
        "price" => $skus->price,
        "Stock" => $skus->stock,

      );

      $productArr[$item_id]['ProductID'] = $item_id;
      $productArr[$item_id]['ProductName'] = $product_name;
      $productArr[$item_id]['Description'] = $description;
      $productArr[$item_id]['Images'] = $image;
      $productArr[$item_id]['skus'][]= $skusArr;
    }



    $result = array_values($productArr);

    $return = array(
      "status" => 200,
      "message" => "ok",
     "total_rows" => COUNT($skusArr),
      "data" => $result

    );

  }

  }else{
    $return= array(
      "status" => 404,
      "message" => "Toko Shopee tidak ada yang aktif",
      "total_rows" => 0,
      "data" => []


    );


  }



    //
    echo json_encode($return);

  }


  if ($content == "get_orders") {

    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);

    $user_id = 5;
    $merchant_name = null;
    $status = $post['status'];

    $orderArr = array();
    $orderItemsArr = array();
    $rowsOrdersn = array();

	$result = array();
	$resultOrdersItems = array();
    if (isset($post['merchant_name'])) {
      $merchant_name = $post['merchant_name'];
    }

if ($status == 1) {

  $status = "READY_TO_SHIP";

}else if ($status == 4) {

  $status = "COMPLETED";

}else if ($status == 9) {

  $status = "IN_CANCEL";

}else if ($status == 6) {

  $status = "CANCELLED";

}else if ($status == 5) {

  $status = "TO_RETURN";

}else{

  $status = "ALL";

}

 $status = "READY_TO_SHIP";
    $getDataShopee = $db->getDataShopee($user_id, $merchant_name);

    if ($getDataShopee != null) {

      while ($rowShopee = $getDataShopee->fetch_assoc()) {
        $rows[] = $rowShopee;

      }

      foreach ($rows as $obj) {

        $partner_id = $obj['partner_id'];
        $partner_key = $obj['partner_key'];
        $shop_id = $obj['shop_id'];
        $code = $obj['code'];
        $merchant_name = $obj['merchant_name'];

        $url = "https://partner.shopeemobile.com/api/v1/orders/get";

        $create_time_from = 0;

        $tgl="Y-m-d";
        $waktu="H:i:s";
        $waktu_sekarang=date("$tgl $waktu");
        $ditambah_5_menit = date("$tgl $waktu", strtotime('-14 day'));
        $create_time_from=strtotime($ditambah_5_menit);

        $convertJson = array(
          "order_status" => $status,
          "create_time_from" =>$create_time_from,
          "create_time_to" =>$timestamp,
          "partner_id" => (int)$partner_id,
          "shopid" => (int)$shop_id,
          "timestamp" => $timestamp);

        $base_string = $url . "|" . json_encode($convertJson);

        $hmac = hash_hmac('sha256', $base_string, $partner_key);

        $ch = curl_init($url);
        $payload = json_encode($convertJson);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json',
          'Authorization: ' . $hmac . ''));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $jsonDecode = json_decode($result);

     // echo json_encode($jsonDecode);die;

        $orders = $jsonDecode->orders;
       // echo json_encode($orders);die;
        foreach($orders as $order) {
			
        
          $rowsOrdersn[] = $order->ordersn;

//echo json_encode($rowsOrdersn);die;
       }

          $urlItems = "https://partner.shopeemobile.com/api/v1/orders/detail";
          $convertJsonItems = array(
            "ordersn_list" =>$rowsOrdersn,
            "partner_id" => (int)$partner_id,
            "shopid" => (int)$shop_id,
            "timestamp" => $timestamp);
          $base_string_items = $urlItems . "|" . json_encode($convertJsonItems);
          $hmacItems = hash_hmac('sha256', $base_string_items, $partner_key);

          $chItems = curl_init($urlItems);
          $payloadItems = json_encode($convertJsonItems);
          curl_setopt($chItems, CURLOPT_POSTFIELDS, $payloadItems);
          curl_setopt($chItems, CURLOPT_HTTPHEADER, array('Content-Type:application/json',
            'Authorization: ' . $hmacItems . ''));
          curl_setopt($chItems, CURLOPT_RETURNTRANSFER, true);
          $resultItems = curl_exec($chItems);
          curl_close($chItems);
          $jdecodeItems2 = json_decode($resultItems);

         // echo json_encode($jdecodeItems2);die;


         if ( isset($jdecodeItems2->orders)){
           $orderItem2 = $jdecodeItems2->orders;
         }

//echo json_encode($jdecodeItems2);die;
          foreach($orderItem2  as $orderItems2) {
		
			      $order_id = $orderItems2->ordersn;
            $order_number = $orderItems2->ordersn;
            $user_id = $user_id;
            $marketplace = "SHOPEE";
            $merchant_name = $merchant_name;
            $branch_number = "";
            $voucher = "";
            $voucher_platform = "";
            $gift_option = "";
            $gift_message = "";
            $shipping_fee = "";
            $shipping_fee_discount_seller = "";
            $shipping_fee_discount_platform = "";
            $national_registration_number = "";
            $tax_code = "";
            $delivery_info = "";

          //  $customer_first_name = $items->recipient_address->name;
            $customer_first_name= $orderItems2 ->recipient_address->name;
            $customer_last_name = "";
            $price = $orderItems2->total_amount;
            $payment_method = $orderItems2->payment_method;
            $shipping_fee_original = $orderItems2->estimated_shipping_fee;
            $remarks = $orderItems2->message_to_seller;
            $promised_shipping_times = "";

            if ($orderItems2->order_status == "UNPAID") {
              $statuses = 8;
            } else if ($orderItems2->order_status == "READY_TO_SHIP") {
              $statuses = 1;
            } else if ($orderItems2->order_status == "COMPLETED") {
              $statuses = 4;
            } else if ($orderItems2->order_status == "IN_CANCEL") {
              $statuses = 9;
            } else if ($orderItems2->order_status == "CANCELLED") {
              $statuses = 6;
            } else if ($orderItems2->order_status == "TO_RETURN") {
              $statuses = 5;
            } else {
              $statuses = 0;
            }


            $created_at = date('yy-m-d H:i:s', $orderItems2->create_time);
            $updated_at = date('yy-m-d H:i:s', $orderItems2->update_time);


	
			$first_name = $orderItems2 ->recipient_address->name;
			$last_name = "";
			$country = $orderItems2 ->recipient_address->country;
			$phone = $orderItems2 ->recipient_address->phone;
			$phone2 = "";
			$address1 = $orderItems2 ->recipient_address->full_address;
			$address2 = "";
			$address3 = "";
			$address4 = "";
			$address5 = "";
			$city = $orderItems2 ->recipient_address->city;
			$post_code = $orderItems2 ->recipient_address->zipcode;	
			
			
			
            foreach ($orderItems2->items as $items) {

			        $order_item_id = $items->item_id;
              $purchase_order_id= "";
              $purchase_order_number= "";
              $invoice_number= "";
              $sla_time_stamp= date('yy-m-d H:i:s', $orderItems2->ship_by_date);
              $package_id= "";
           //   $shop_id= "";
              $order_type= "";
              $shop_sku= $items->item_sku;
              $sku= $items->variation_sku;
              $name= $items->item_name;
              $variation= $items->variation_name;
              $item_price= $items->variation_original_price;
              $paid_price= $items->variation_discounted_price;
              $qty =  $items->variation_quantity_purchased;
              $currency= $orderItems2->currency;
              $tax_amount= $orderItems2->escrow_tax;
              $product_detail_url= "";
              $shipment_provider= $orderItems2->shipping_carrier;
              $tracking_code_pre= "";
              $tracking_code= $orderItems2->tracking_no;
              $shipping_type= "";
              $shipping_provider_type= "";
              $shipping_fee_original= $orderItems2->estimated_shipping_fee;
              $shipping_service_cost= 0;
              $shipping_fee_discount_seller = 0;
              $shipping_amount= $orderItems2->estimated_shipping_fee;
              $is_digital= 0;
              $voucher_amount= "";
              $voucher_code_seller= "";
              $voucher_code= "";
              $voucher_code_platform= "";
              $order_flag= $orderItems2->order_flag;
              $promised_shipping_time= "";
              $digital_delivery_info= "";
              $extra_attributes="";
              $cancel_return_initiator= $orderItems2->buyer_cancel_reason;
              $reason= "";
              $reason_detail= "";
              $stage_pay_status="";
              $warehouse_code= "";
              $return_status= "";
              $voucher_seller = "";

            }

              $tglImageVariant = "Y-m-d";
              $waktuImageVariant = "H:i:s";
              $waktu_sekarangImageVariant= date("$tglImageVariant $waktuImageVariant");
              $ditambah_5_menitImageVariant = date("$tgl $waktuImageVariant", strtotime('+5 minutes'));

              $urlImageVariant = "https://partner.shopeemobile.com/api/v1/item/get";
              $pagination_offset = 0;
              $pagination_entries_per_page = 100;
              $timestampImageVariant = strtotime($ditambah_5_menitImageVariant);


              $convertJsonImageVariant = array("pagination_offset" => $pagination_offset,
                "item_id" => (int)$order_item_id,
                "partner_id" => (int)$partner_id,
                "shopid" => (int)$shop_id,
                "timestamp" => $timestampImageVariant);

          //   echo json_encode($convertJsonImageVariant);die;
              $base_stringImageVariant = $urlImageVariant . "|" . json_encode($convertJsonImageVariant);


              $hmacImageVariant = hash_hmac('sha256', $base_stringImageVariant, $partner_key);


              $chImageVariant = curl_init($urlImageVariant);
              $payloadImageVariant = json_encode($convertJsonImageVariant);
              curl_setopt($chImageVariant, CURLOPT_POSTFIELDS, $payloadImageVariant);
              curl_setopt($chImageVariant, CURLOPT_HTTPHEADER, array('Content-Type:application/json',
                'Authorization: ' . $hmacImageVariant . ''));
              curl_setopt($chImageVariant, CURLOPT_RETURNTRANSFER, true);
              $resultImageVariant = curl_exec($chImageVariant);
              curl_close($chImageVariant);
              $jsonDecodeImageVariant = json_decode($resultImageVariant);
          //  echo json_encode($jsonDecodeImageVariant);die;
              $itemsImageVariant = $jsonDecodeImageVariant->item;
              // echo json_encode($items);die;


              $product_name = $itemsImageVariant -> name;
              $description = $itemsImageVariant ->description;
              // $images = $items ->description;
              foreach ($itemsImageVariant -> images as $ImagesImageVariant) {
                $imageImageVariant = $ImagesImageVariant;
              }

        // echo json_encode($imageImageVariant);die;
      

			
		      $orderArr[$order_id]['order_id'] = $order_id;
          $orderArr[$order_id]['order_number'] = $order_number;
          $orderArr[$order_id]['user_id'] = $user_id;
          $orderArr[$order_id]['marketplace'] =$marketplace;
          $orderArr[$order_id]['merchant_name'] = $merchant_name;
          $orderArr[$order_id]['branch_number'] = $branch_number;
          $orderArr[$order_id]['warehouse_code'] = $warehouse_code;
          $orderArr[$order_id]['customer_first_name'] = $customer_first_name;
          $orderArr[$order_id]['customer_last_name'] = $customer_last_name;
          $orderArr[$order_id]['price'] = $price;
          $orderArr[$order_id]['items_count'] = COUNT($orderItems2->items);
          $orderArr[$order_id]['payment_method'] = $payment_method;
          $orderArr[$order_id]['voucher'] = $voucher;
          $orderArr[$order_id]['voucher_code'] = $voucher_code;
          $orderArr[$order_id]['voucher_platform'] = $voucher_platform;
          $orderArr[$order_id]['voucher_seller'] = $voucher_seller;
          $orderArr[$order_id]['gift_option'] = $gift_option;
          $orderArr[$order_id]['gift_message'] = $gift_message;
          $orderArr[$order_id]['shipping_fee'] = $shipping_fee;
          $orderArr[$order_id]['shipping_fee_discount_seller'] = $shipping_fee_discount_seller;
          $orderArr[$order_id]['shipping_fee_discount_platform'] = $shipping_fee_discount_platform;
          $orderArr[$order_id]['promised_shipping_times'] = $promised_shipping_times;
          $orderArr[$order_id]['national_registration_number'] = $national_registration_number;
          $orderArr[$order_id]['tax_code'] = $tax_code;
          $orderArr[$order_id]['remarks'] = $remarks;
          $orderArr[$order_id]['delivery_info'] = $delivery_info;
          $orderArr[$order_id]['statuses'] = $statuses;
          $orderArr[$order_id]['created_at'] = $created_at;
          $orderArr[$order_id]['updated_at'] = $updated_at;
          $orderArr[$order_id]['image'] = $imageImageVariant;
		      $orderArr[$order_id]['order_items'][]= array("order_item_id" => $order_item_id,
                                            "order_id" => $order_id,
                                            "purchase_order_id" => $purchase_order_id,
                                            "purchase_order_number" =>$purchase_order_number,
                                            "invoice_number" => $invoice_number,
                                            "sla_time_stamp" => $sla_time_stamp,
                                            "package_id" => $package_id,
                                            "shop_id" => $shop_id,
                                            "order_type" => $order_type,
                                            "shop_sku" => $shop_sku,
                                            "sku" => $sku,
                                            "name" => $name,
                                            "variation" => $variation,
                                            "item_price" => $item_price,
                                            "paid_price" => $paid_price,
                                            "qty" => $qty,
                                            "currency" => $currency,
                                            "tax_amount" => $tax_amount,                                            
                                            "product_detail_url" => $product_detail_url,
                                            "shipment_provider" => $shipment_provider,
                                            "tracking_code_pre" => $tracking_code_pre,
                                            "tracking_code" => $tracking_code,
                                            "shipping_type" => $shipping_type,
                                            "shipping_provider_type" => $shipping_provider_type,
                                            "shipping_fee_original" => $shipping_fee_original,
                                            "shipping_service_cost" => $shipping_service_cost,
                                            "shipping_fee_discount_seller" => $shipping_fee_discount_seller,
                                            "shipping_amount" => $shipping_amount,
                                            "is_digital" => $is_digital,
                                            "voucher_amount" => $voucher_amount,
                                            "voucher_seller" => $voucher_seller,
                                            "voucher_code_seller" => $voucher_code_seller,
                                            "voucher_code" => $voucher_code,
                                            "voucher_code_platform" => $voucher_code_platform,
                                            "voucher_platform" => $voucher_platform,
                                            "order_flag" => $order_flag,
                                            "promised_shipping_time" => $promised_shipping_time,
                                            "digital_delivery_info" => $digital_delivery_info,
                                            "extra_attributes" => $extra_attributes,
                                            "cancel_return_initiator" => $cancel_return_initiator,
                                            "reason" => $reason,
                                            "reason_detail" => $reason_detail,
                                            "stage_pay_status" => $stage_pay_status,
                                            "warehouse_code" => $warehouse_code,
                                            "return_status" => $return_status,
                                            "status" => $statuses,
                                            "created_at" => $created_at,
                                            "updated_at" => $updated_at,
                                            "image_variant" => $imageImageVariant
														);
														
														
		 $orderArr[$order_id]['address_shipping']= array("order_id" => $order_id,
		 "first_name" => $first_name,
		 "last_name" => $last_name,
		 "country" => $country,
		 "phone" => $phone,
		 "phone2" => $phone2,
		 "address1" => $address1,
		 "address2" => $address2,
		 "address3" => $address3,
		 "address4" => $address4,
		 "address5" => $address5,
		 "city" => $city,
		 "post_code" => $post_code,
		 
			);	
			
		$orderArr[$order_id]['address_billing']= array("order_id" => $order_id,
		"first_name" => $first_name,
		 "last_name" => $last_name,
		 "country" => $country,
		 "phone" => $phone,
		 "phone2" => $phone2,
		 "address1" => $address1,
		 "address2" => $address2,
		 "address3" => $address3,
		 "address4" => $address4,
		 "address5" => $address5,
		 "city" => $city,
		 "post_code" => $post_code,
		 
			);								
			

			  
            

            }
			
		
			
			$result = array_values($orderArr);
			
   //echo json_encode($result);die;




	
   


      }




      $return = array(
       "status" => 200,
      "message" => "ok shopee",
       "total_rows"=>COUNT($result),
      "data" => $result

      );




    }else{
      $return= array(
        "status" => 404,
        "message" => "Toko Shopee tidak ada yang aktif",
        "total_rows" => 0,
        "data" => []


      );


    }

    //
    echo json_encode($return);

  }

  if ($content == "create_product") {
    $post = json_decode(file_get_contents("php://input"), true);

    $user_id = 5;
    $marketplace = "SHOPEE";
    $seller_id = $post['seller_id'];

    if (isset($seller_id)) {
      //Mencari data marketplace
      $getDataMarketplace = $dbModel->getDataMarketplace($marketplace);

      if ($getDataMarketplace != null) {

        while ($rowMarketplace = $getDataMarketplace->fetch_assoc()) {

          $appkey = $rowMarketplace['app_key'];
          $appSecret = $rowMarketplace['app_secret'];

        }

        //mencari data toko
        $getDataToko= $dbModel->getDataToko($user_id , $seller_id , $marketplace);

        if ($getDataToko != null) {

          while ($rowToko= $getDataToko->fetch_assoc()) {

            $accessToken = $rowToko['access_token'];
            $merchant_name = $rowToko['merchant_name'];
            $marketplace_name = $rowToko['marketplace_name'];
            $seller_id = $rowToko['seller_id'];
          }

          //get logistic


          $urlLogistic = "https://partner.shopeemobile.com/api/v1/logistics/channel/get";
          $tglLogistic="Y-m-d";
          $waktuLogistic="H:i:s";
          $waktu_sekarangLogistic=date("$tglLogistic $waktuLogistic");
          $ditambah_5_menitLogistic = date("$tgl $waktuLogistic", strtotime('+5 minutes'));
          $timestampLogistic=strtotime($ditambah_5_menitLogistic);

//echo json_encode($timestampLogistic);die;

          $convertJsonLogistic = array(
            "partner_id" => (int)$appkey,
            "shopid" => (int)$seller_id,
            "timestamp" => $timestampLogistic);


          $base_stringLogistic = $urlLogistic . "|" . json_encode($convertJsonLogistic);

          $hmacLogistic = hash_hmac('sha256', $base_stringLogistic, $appSecret);

          $chLogistic = curl_init($urlLogistic);
          $payloadLogistic = json_encode($convertJsonLogistic);
          curl_setopt($chLogistic, CURLOPT_POSTFIELDS, $payloadLogistic);
          curl_setopt($chLogistic, CURLOPT_HTTPHEADER, array('Content-Type:application/json',
            'Authorization: ' . $hmacLogistic . ''));
          curl_setopt($chLogistic, CURLOPT_RETURNTRANSFER, true);
          $resultLogistic = curl_exec($chLogistic);
          curl_close($chLogistic);
          $jsonDecodeLogistic = json_decode($resultLogistic);

          foreach ($jsonDecodeLogistic->logistics as $logistics) {

            $dataLogistic[] = array("logistic_id" => $logistics->logistic_id,
              "enabled" => true ) ;
          }



          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $base_url . '/public/api/products.php?request=get_products');
          $payload = json_encode( array( "UserID"=> $user_id) );
          curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
          curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          $productContent = curl_exec($ch);
          curl_close($ch);
          $resultProducts=json_decode($productContent);

       //   echo json_encode($resultProducts);die;
          if ($resultProducts->status == 200) {

            foreach($resultProducts->data as $dataProducts) {


              $url = "https://partner.shopeemobile.com/api/v1/item/add";

              $category_id = 7276;
              $ProductName =  $dataProducts->ProductName;
              $description =  $dataProducts->Description;

              foreach($dataProducts->skus as $dataSkus) {

                $ProductVariantDetailName =  $dataSkus->ProductVariantDetailName;
                $price =  $dataSkus->PriceRetail;
                $stock =  $dataSkus->Stock;
                $sku =  $dataSkus->SkuID;



                $skusArr[] = array(
                  "name" => $ProductVariantDetailName,
                  "stock" =>(int)$stock,
                  "price" =>(int)$price,
                  "variation_sku" => $sku);

                foreach($dataSkus->Images as $dataImages) {

                  $imagesArr= array(
                    "url" => $dataImages
                  );

                }

              }


             $weight = 0.5;

              $tgl="Y-m-d";
              $waktu="H:i:s";
              $waktu_sekarang=date("$tgl $waktu");
              $ditambah_5_menit = date("$tgl $waktu", strtotime('+5 minutes'));
              $timestamp=strtotime($ditambah_5_menit);



              $convertJson = array(
                "category_id" => $category_id,
                "name" =>$ProductName,
                "description" =>$description,
                "price" => (float)$price,
                "stock" => (int)$stock,
                "variations" => $skusArr,
                "images" => array($imagesArr),
                "logistics" => $dataLogistic,
                "weight" =>$weight,
                "partner_id" => (int)$appkey,
                "shopid" => (int)$seller_id,
                "timestamp" => $timestamp);

        //echo json_encode($convertJson);die;

              $base_string = $url . "|" . json_encode($convertJson);

              $hmac = hash_hmac('sha256', $base_string, $appSecret);

              $ch = curl_init($url);
              $payload = json_encode($convertJson);
              curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
              curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json',
                'Authorization: ' . $hmac . ''));
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              $result = curl_exec($ch);
              curl_close($ch);
              $jsonDecode = json_decode($result);

              echo json_encode($jsonDecode);die;


              $dataResult[]= array (
                "marketplace"=>$marketplace_name,
                "merchant_name"=>$merchant_name,
                "product_name"=>$dataProducts->ProductName,
                "status"=>$status,
                "msg"=>$message,
                "code"=>$code,
              );


            }

            $return = array(
              "status" => 200,
              "message" => "Berhasil",
              "total_rows" => COUNT($dataResult),
              "data" => $dataResult

            );




          }else{

            $return = array(
              "status" => 404,
              "message" => "Belum ada produk",
              "total_rows" => 0,
              "data" => []

            );

          }
        }





      } else {
        $return = array(
          "status" => 404,
          "message" => "Belum ada product yang aktif",
          "data" => []
        );
      }


    }else{

      $return = array(
        "status" => 404,
        "message" => "Error",
        "data" => []
      );
    }














    echo json_encode($return);
  }

  if ($content == "get_order") {

    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);

    $user_id = 5;
    $merchant_name = null;
    $status = $post['status'];

    $orderArr = array();
    $orderItemsArr = array();
    $rowsOrdersn = array();

    $result = array();
    $resultOrdersItems = array();
    if (isset($post['merchant_name'])) {
      $merchant_name = $post['merchant_name'];
    }

    if ($status == 1) {

      $status = "READY_TO_SHIP";

    }else if ($status == 4) {

      $status = "COMPLETED";

    }else if ($status == 9) {

      $status = "IN_CANCEL";

    }else if ($status == 6) {

      $status = "CANCELLED";

    }else if ($status == 5) {

      $status = "TO_RETURN";

    }else{

      $status = "ALL";

    }

    $status = "READY_TO_SHIP";
    $getDataShopee = $db->getDataShopee($user_id, $merchant_name);

    if ($getDataShopee != null) {

      while ($rowShopee = $getDataShopee->fetch_assoc()) {
        $rows[] = $rowShopee;

      }

      foreach ($rows as $obj) {

        $partner_id = $obj['partner_id'];
        $partner_key = $obj['partner_key'];
        $shop_id = $obj['shop_id'];
        $code = $obj['code'];
        $merchant_name = $obj['merchant_name'];

        $url = "https://partner.shopeemobile.com/api/v1/orders/get";

        $create_time_from = 0;

        $tgl="Y-m-d";
        $waktu="H:i:s";
        $waktu_sekarang=date("$tgl $waktu");
        $ditambah_5_menit = date("$tgl $waktu", strtotime('-14 day'));
        $create_time_from=strtotime($ditambah_5_menit);

        $convertJson = array(
          "order_status" => $status,
          "create_time_from" =>$create_time_from,
          "create_time_to" =>$timestamp,
          "partner_id" => (int)$partner_id,
          "shopid" => (int)$shop_id,
          "timestamp" => $timestamp);

        $base_string = $url . "|" . json_encode($convertJson);

        $hmac = hash_hmac('sha256', $base_string, $partner_key);

        $ch = curl_init($url);
        $payload = json_encode($convertJson);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json',
          'Authorization: ' . $hmac . ''));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $jsonDecode = json_decode($result);

        // echo json_encode($jsonDecode);die;

        $orders = $jsonDecode->orders;
        // echo json_encode($orders);die;
        foreach($orders as $order) {


          $rowsOrdersn[] = $order->ordersn;

//echo json_encode($rowsOrdersn);die;
        }

        $urlItems = "https://partner.shopeemobile.com/api/v1/orders/detail";
        $convertJsonItems = array(
          "ordersn_list" =>$rowsOrdersn,
          "partner_id" => (int)$partner_id,
          "shopid" => (int)$shop_id,
          "timestamp" => $timestamp);
        $base_string_items = $urlItems . "|" . json_encode($convertJsonItems);
        $hmacItems = hash_hmac('sha256', $base_string_items, $partner_key);

        $chItems = curl_init($urlItems);
        $payloadItems = json_encode($convertJsonItems);
        curl_setopt($chItems, CURLOPT_POSTFIELDS, $payloadItems);
        curl_setopt($chItems, CURLOPT_HTTPHEADER, array('Content-Type:application/json',
          'Authorization: ' . $hmacItems . ''));
        curl_setopt($chItems, CURLOPT_RETURNTRANSFER, true);
        $resultItems = curl_exec($chItems);
        curl_close($chItems);
        $jdecodeItems2 = json_decode($resultItems);

        // echo json_encode($jdecodeItems2);die;


        if ( isset($jdecodeItems2->orders)){
          $orderItem2 = $jdecodeItems2->orders;
        }

//echo json_encode($jdecodeItems2);die;
        foreach($orderItem2  as $orderItems2) {

          $order_id = $orderItems2->ordersn;
          $order_number = $orderItems2->ordersn;
          $user_id = $user_id;
          $marketplace = "SHOPEE";
          $merchant_name = $merchant_name;
          $branch_number = "";
          $voucher = "";
          $voucher_platform = "";
          $gift_option = "";
          $gift_message = "";
          $shipping_fee = "";
          $shipping_fee_discount_seller = "";
          $shipping_fee_discount_platform = "";
          $national_registration_number = "";
          $tax_code = "";
          $delivery_info = "";

          //  $customer_first_name = $items->recipient_address->name;
          $customer_first_name= $orderItems2 ->recipient_address->name;
          $customer_last_name = "";
          $price = $orderItems2->total_amount;
          $payment_method = $orderItems2->payment_method;
          $shipping_fee_original = $orderItems2->estimated_shipping_fee;
          $remarks = $orderItems2->message_to_seller;
          $promised_shipping_times = "";

          if ($orderItems2->order_status == "UNPAID") {
            $statuses = 8;
          } else if ($orderItems2->order_status == "READY_TO_SHIP") {
            $statuses = 1;
          } else if ($orderItems2->order_status == "COMPLETED") {
            $statuses = 4;
          } else if ($orderItems2->order_status == "IN_CANCEL") {
            $statuses = 9;
          } else if ($orderItems2->order_status == "CANCELLED") {
            $statuses = 6;
          } else if ($orderItems2->order_status == "TO_RETURN") {
            $statuses = 5;
          } else {
            $statuses = 0;
          }


          $created_at = date('yy-m-d H:i:s', $orderItems2->create_time);
          $updated_at = date('yy-m-d H:i:s', $orderItems2->update_time);



          $first_name = $orderItems2 ->recipient_address->name;
          $last_name = "";
          $country = $orderItems2 ->recipient_address->country;
          $phone = $orderItems2 ->recipient_address->phone;
          $phone2 = "";
          $address1 = $orderItems2 ->recipient_address->full_address;
          $address2 = "";
          $address3 = "";
          $address4 = "";
          $address5 = "";
          $city = $orderItems2 ->recipient_address->city;
          $post_code = $orderItems2 ->recipient_address->zipcode;



          foreach ($orderItems2->items as $items) {

            $order_item_id = $items->item_id;
            $purchase_order_id= "";
            $purchase_order_number= "";
            $invoice_number= "";
            $sla_time_stamp= date('yy-m-d H:i:s', $orderItems2->ship_by_date);
            $package_id= "";
            //   $shop_id= "";
            $order_type= "";
            $shop_sku= $items->item_sku;
            $sku= $items->variation_sku;
            $name= $items->item_name;
            $variation= $items->variation_name;
            $item_price= $items->variation_original_price;
            $paid_price= $items->variation_discounted_price;
            $qty =  $items->variation_quantity_purchased;
            $currency= $orderItems2->currency;
            $tax_amount= $orderItems2->escrow_tax;
            $product_detail_url= "";
            $shipment_provider= $orderItems2->shipping_carrier;
            $tracking_code_pre= "";
            $tracking_code= $orderItems2->tracking_no;
            $shipping_type= "";
            $shipping_provider_type= "";
            $shipping_fee_original= $orderItems2->estimated_shipping_fee;
            $shipping_service_cost= 0;
            $shipping_fee_discount_seller = 0;
            $shipping_amount= $orderItems2->estimated_shipping_fee;
            $is_digital= 0;
            $voucher_amount= "";
            $voucher_code_seller= "";
            $voucher_code= "";
            $voucher_code_platform= "";
            $order_flag= $orderItems2->order_flag;
            $promised_shipping_time= "";
            $digital_delivery_info= "";
            $extra_attributes="";
            $cancel_return_initiator= $orderItems2->buyer_cancel_reason;
            $reason= "";
            $reason_detail= "";
            $stage_pay_status="";
            $warehouse_code= "";
            $return_status= "";
            $voucher_seller = "";

          }

          $tglImageVariant = "Y-m-d";
          $waktuImageVariant = "H:i:s";
          $waktu_sekarangImageVariant= date("$tglImageVariant $waktuImageVariant");
          $ditambah_5_menitImageVariant = date("$tgl $waktuImageVariant", strtotime('+5 minutes'));

          $urlImageVariant = "https://partner.shopeemobile.com/api/v1/item/get";
          $pagination_offset = 0;
          $pagination_entries_per_page = 100;
          $timestampImageVariant = strtotime($ditambah_5_menitImageVariant);


          $convertJsonImageVariant = array("pagination_offset" => $pagination_offset,
            "item_id" => (int)$order_item_id,
            "partner_id" => (int)$partner_id,
            "shopid" => (int)$shop_id,
            "timestamp" => $timestampImageVariant);

          //   echo json_encode($convertJsonImageVariant);die;
          $base_stringImageVariant = $urlImageVariant . "|" . json_encode($convertJsonImageVariant);


          $hmacImageVariant = hash_hmac('sha256', $base_stringImageVariant, $partner_key);


          $chImageVariant = curl_init($urlImageVariant);
          $payloadImageVariant = json_encode($convertJsonImageVariant);
          curl_setopt($chImageVariant, CURLOPT_POSTFIELDS, $payloadImageVariant);
          curl_setopt($chImageVariant, CURLOPT_HTTPHEADER, array('Content-Type:application/json',
            'Authorization: ' . $hmacImageVariant . ''));
          curl_setopt($chImageVariant, CURLOPT_RETURNTRANSFER, true);
          $resultImageVariant = curl_exec($chImageVariant);
          curl_close($chImageVariant);
          $jsonDecodeImageVariant = json_decode($resultImageVariant);
          //  echo json_encode($jsonDecodeImageVariant);die;
          $itemsImageVariant = $jsonDecodeImageVariant->item;
          // echo json_encode($items);die;


          $product_name = $itemsImageVariant -> name;
          $description = $itemsImageVariant ->description;
          // $images = $items ->description;
          foreach ($itemsImageVariant -> images as $ImagesImageVariant) {
            $imageImageVariant = $ImagesImageVariant;
          }

          // echo json_encode($imageImageVariant);die;



          $orderArr[$order_id]['order_id'] = $order_id;
          $orderArr[$order_id]['order_number'] = $order_number;
          $orderArr[$order_id]['user_id'] = $user_id;
          $orderArr[$order_id]['marketplace'] =$marketplace;
          $orderArr[$order_id]['merchant_name'] = $merchant_name;
          $orderArr[$order_id]['branch_number'] = $branch_number;
          $orderArr[$order_id]['warehouse_code'] = $warehouse_code;
          $orderArr[$order_id]['customer_first_name'] = $customer_first_name;
          $orderArr[$order_id]['customer_last_name'] = $customer_last_name;
          $orderArr[$order_id]['price'] = $price;
          $orderArr[$order_id]['items_count'] = COUNT($orderItems2->items);
          $orderArr[$order_id]['payment_method'] = $payment_method;
          $orderArr[$order_id]['voucher'] = $voucher;
          $orderArr[$order_id]['voucher_code'] = $voucher_code;
          $orderArr[$order_id]['voucher_platform'] = $voucher_platform;
          $orderArr[$order_id]['voucher_seller'] = $voucher_seller;
          $orderArr[$order_id]['gift_option'] = $gift_option;
          $orderArr[$order_id]['gift_message'] = $gift_message;
          $orderArr[$order_id]['shipping_fee'] = $shipping_fee;
          $orderArr[$order_id]['shipping_fee_discount_seller'] = $shipping_fee_discount_seller;
          $orderArr[$order_id]['shipping_fee_discount_platform'] = $shipping_fee_discount_platform;
          $orderArr[$order_id]['promised_shipping_times'] = $promised_shipping_times;
          $orderArr[$order_id]['national_registration_number'] = $national_registration_number;
          $orderArr[$order_id]['tax_code'] = $tax_code;
          $orderArr[$order_id]['remarks'] = $remarks;
          $orderArr[$order_id]['delivery_info'] = $delivery_info;
          $orderArr[$order_id]['statuses'] = $statuses;
          $orderArr[$order_id]['created_at'] = $created_at;
          $orderArr[$order_id]['updated_at'] = $updated_at;
          $orderArr[$order_id]['image'] = $imageImageVariant;
          $orderArr[$order_id]['order_items'][]= array("order_item_id" => $order_item_id,
            "order_id" => $order_id,
            "purchase_order_id" => $purchase_order_id,
            "purchase_order_number" =>$purchase_order_number,
            "invoice_number" => $invoice_number,
            "sla_time_stamp" => $sla_time_stamp,
            "package_id" => $package_id,
            "shop_id" => $shop_id,
            "order_type" => $order_type,
            "shop_sku" => $shop_sku,
            "sku" => $sku,
            "name" => $name,
            "variation" => $variation,
            "item_price" => $item_price,
            "paid_price" => $paid_price,
            "qty" => $qty,
            "currency" => $currency,
            "tax_amount" => $tax_amount,
            "product_detail_url" => $product_detail_url,
            "shipment_provider" => $shipment_provider,
            "tracking_code_pre" => $tracking_code_pre,
            "tracking_code" => $tracking_code,
            "shipping_type" => $shipping_type,
            "shipping_provider_type" => $shipping_provider_type,
            "shipping_fee_original" => $shipping_fee_original,
            "shipping_service_cost" => $shipping_service_cost,
            "shipping_fee_discount_seller" => $shipping_fee_discount_seller,
            "shipping_amount" => $shipping_amount,
            "is_digital" => $is_digital,
            "voucher_amount" => $voucher_amount,
            "voucher_seller" => $voucher_seller,
            "voucher_code_seller" => $voucher_code_seller,
            "voucher_code" => $voucher_code,
            "voucher_code_platform" => $voucher_code_platform,
            "voucher_platform" => $voucher_platform,
            "order_flag" => $order_flag,
            "promised_shipping_time" => $promised_shipping_time,
            "digital_delivery_info" => $digital_delivery_info,
            "extra_attributes" => $extra_attributes,
            "cancel_return_initiator" => $cancel_return_initiator,
            "reason" => $reason,
            "reason_detail" => $reason_detail,
            "stage_pay_status" => $stage_pay_status,
            "warehouse_code" => $warehouse_code,
            "return_status" => $return_status,
            "status" => $statuses,
            "created_at" => $created_at,
            "updated_at" => $updated_at,
            "image_variant" => $imageImageVariant
          );


          $orderArr[$order_id]['address_shipping']= array("order_id" => $order_id,
            "first_name" => $first_name,
            "last_name" => $last_name,
            "country" => $country,
            "phone" => $phone,
            "phone2" => $phone2,
            "address1" => $address1,
            "address2" => $address2,
            "address3" => $address3,
            "address4" => $address4,
            "address5" => $address5,
            "city" => $city,
            "post_code" => $post_code,

          );

          $orderArr[$order_id]['address_billing']= array("order_id" => $order_id,
            "first_name" => $first_name,
            "last_name" => $last_name,
            "country" => $country,
            "phone" => $phone,
            "phone2" => $phone2,
            "address1" => $address1,
            "address2" => $address2,
            "address3" => $address3,
            "address4" => $address4,
            "address5" => $address5,
            "city" => $city,
            "post_code" => $post_code,

          );





        }



        $result = array_values($orderArr);

        //echo json_encode($result);die;








      }




      $return = array(
        "status" => 200,
        "message" => "ok shopee",
        "total_rows"=>COUNT($result),
        "data" => $result

      );




    }else{
      $return= array(
        "status" => 404,
        "message" => "Toko Shopee tidak ada yang aktif",
        "total_rows" => 0,
        "data" => []


      );


    }

    //
    echo json_encode($return);

  }
  if ($content == "get_order_items") {

    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);

    $user_id = 5;
    $merchant_name = null;
    $ordersn_list = $post['ordersn_list'];
  // $ordersn_list = ["201112QJCT9KYK"];
    if (isset($post['merchant_name'])) {
      $merchant_name = $post['merchant_name'];
    }


    if (isset($user_id) && isset($ordersn_list) && isset($merchant_name) ) {


      $getDataShopee = $db->getDataShopee($user_id, $merchant_name);

      if ($getDataShopee != null) {

        while ($rowShopee = $getDataShopee->fetch_assoc()) {
          $rows[] = $rowShopee;

        }

        foreach ($rows as $obj) {

          $partner_id = $obj['partner_id'];
          $partner_key = $obj['partner_key'];
          $shop_id = $obj['shop_id'];
          $code = $obj['code'];
          $merchant_name = $obj['merchant_name'];

          $url = "https://partner.shopeemobile.com/api/v1/orders/detail";



          $convertJson = array(
            "ordersn_list" =>$ordersn_list,
            "partner_id" => (int)$partner_id,
            "shopid" => (int)$shop_id,
            "timestamp" => $timestamp);

          $base_string = $url . "|" . json_encode($convertJson);

          $hmac = hash_hmac('sha256', $base_string, $partner_key);

          $ch = curl_init($url);
          $payload = json_encode($convertJson);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
          curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json',
            'Authorization: ' . $hmac . ''));
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $result = curl_exec($ch);
          curl_close($ch);
          $jdecode = json_decode($result);

//echo json_encode($jdecode);die;

          if ($jdecode->errors == []) {


            $orders = $jdecode->orders;
//echo json_encode($orders);die;
            foreach($orders as $order)

            {


              $order_id = $order->ordersn;
              $purchase_order_id= "";
              $purchase_order_number= "";
              $invoice_number= "";
              $sla_time_stamp= date('yy-m-d H:i:s', $order->ship_by_date);
              $package_id= "";
              $shop_id= "";
              $order_type= "";
              $customer_first_name= $order->recipient_address->name;
              $customer_last_name="";
              $currency= $order->currency;
              $tax_amount= $order->escrow_tax;
              $price= $order->total_amount;

              foreach($order->items as $item) {

               $order_item_id = $item->item_id;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $base_url . '/public/api/shopee.php?request=get_product_items');
                $payload = json_encode(array("item_id" => $order_item_id,
                  "merchant_name" => $merchant_name));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $skuscontent = curl_exec($ch);
                curl_close($ch);

                $resultSkus = json_decode($skuscontent);
                $datas = $resultSkus->data;
//echo json_encode($resultSkus);die;

                foreach ($datas as $data) {

                  foreach ($data->Images as $image) {

                    $image_variants []= $image;

                  }


                }





              }


              $product_detail_url= "";
              $shipment_provider= $order->shipping_carrier;
              $tracking_code_pre= "";
              $tracking_code= $order->tracking_no;
              $shipping_type= "";
              $shipping_provider_type= "";
              $shipping_fee_original= $order->estimated_shipping_fee;
              $shipping_service_cost= 0;
              $shipping_fee_discount_seller = 0;
              $shipping_amount= $order->estimated_shipping_fee;
              $is_digital= 0;
              $voucher_amount= "";
              $voucher_seller= "";
              $voucher_code_seller= "";
              $voucher_code= "";
              $voucher_platform = "";
              $voucher_code_platform= "";
              $order_flag= $order->order_flag;
              $promised_shipping_time= "";
              $digital_delivery_info= "";
              $extra_attributes="";
              $cancel_return_initiator= $order->buyer_cancel_reason;
              $remark= $order->message_to_seller;
              $reason= "";
              $reason_detail= "";
              $stage_pay_status="";
              $warehouse_code= "";
              $return_status= "";

              if ($order->order_status == "UNPAID") {
                $status= 8;
              }else  if ($order->order_status == "READY_TO_SHIP") {
                $status= 1;
              }else  if ($order->order_status == "COMPLETED") {
                $status= 4;
              }else  if ($order->order_status == "IN_CANCEL") {
                $status= 6;
              }else  if ($order->order_status == "CANCELLED") {
                $status= 6;
              }else  if ($order->order_status == "TO_RETURN") {
                $status= 5;
              }else{
                $status= 0;
              }


              $payment_method= $order->payment_method;
              $created_at= date('yy-m-d H:i:s', $order->create_time);
              $updated_at= date('yy-m-d H:i:s', $order->update_time);


              foreach($order->items as $item)
              {

                $order_item_id = $item->item_id;
                $shop_sku= $item->item_sku;
                $sku= $item->variation_sku;
                $name= $item->item_name;
                $variation= $item->variation_name;
                $item_price= $item->variation_original_price;
                $paid_price= $item->variation_discounted_price;
                $qty =  $item->variation_quantity_purchased;

              }


              $arrVariants[] = array(
                "order_item_id" => $order_id,
                "order_id" => $order_id,
                "purchase_order_id" =>$purchase_order_id ,
                "purchase_order_number" =>$purchase_order_number,
                "invoice_number" => $invoice_number,
                "sla_time_stamp" => $sla_time_stamp,
                "package_id" =>$package_id,
                "shop_id" =>$shop_id,
                "order_type" => $order_type,
                "shop_sku" =>$shop_sku ,
                "sku" =>$sku,
                "name" =>$name,
                "variation" =>$variation,
                "item_price" =>$item_price,
                "paid_price" =>$paid_price,
                "qty" =>$qty,
                "currency" =>$currency,
                "tax_amount" => $tax_amount,
                "image_variants" => $image_variants,
                "product_detail_url" =>$product_detail_url,
                "shipment_provider" =>$shipment_provider,
                "tracking_code_pre" =>$tracking_code_pre,
                "tracking_code" =>$tracking_code,
                "shipping_type" =>$shipping_type,
                "shipping_provider_type" =>$shipping_provider_type,
                "shipping_fee_original" =>$shipping_fee_original,
                "shipping_service_cost " =>$shipping_service_cost,
                "shipping_fee_discount_seller " =>$shipping_fee_discount_seller,
                "shipping_amount" =>$shipping_amount,
                "is_digital" =>$is_digital,
                "voucher_amount" =>$voucher_amount,
                "voucher_seller" =>$voucher_seller,
                "voucher_code_seller" =>$voucher_code_seller,
                "voucher_code" =>$voucher_code,
                "voucher_code_platform" =>$voucher_code_platform,
                "voucher_platform" =>$voucher_platform,
                "order_flag" =>$order_flag,
                "promised_shipping_time" =>$promised_shipping_time,
                "digital_delivery_info" =>$digital_delivery_info,
                "extra_attributes" =>$extra_attributes,
                "cancel_return_initiator" =>$cancel_return_initiator,
                "reason" =>$reason,
                "reason_detail" =>$reason_detail,
                "stage_pay_status" =>$stage_pay_status,
                "warehouse_code" =>$warehouse_code,
                "return_status" =>$return_status,
                "status" =>$status,
                "created_at" =>$created_at,
                "updated_at" =>$updated_at
              );

            }

            $return = array(
              "status" => 200,
              "message" => "ok",
              "total_rows" => COUNT($arrVariants),
              "data" => $arrVariants
            );

          }else{

            $return = array(
              "status" => 404,
              "message" => "Error",
              "data" => $jdecode->errors
            );

          }


        }





      }else{
        $return= array(
          "status" => 404,
          "message" => "Toko Shopee tidak ada yang aktif",
          "total_rows" => 0,
          "data" => []


        );


      }







    }else{
      $return= array(
        "status" => 404,
        "message" => "error",
        "total_rows" => 0,
        "data" => []


      );


    }
    //
    echo json_encode($return);

  }


  if ($content == "update_variant_stock") {

    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);

    $user_id = 5;
    $merchant_name = null;

    if (isset($post['merchant_name'])) {
      $merchant_name = $post['merchant_name'];
    }



    $getDataShopee = $db->getDataShopee($user_id, $merchant_name);

    if ($getDataShopee != null) {

      while ($rowShopee = $getDataShopee->fetch_assoc()) {
        $rows[] = $rowShopee;

      }

      foreach ($rows as $obj) {

        $partner_id = $obj['partner_id'];
        $partner_key = $obj['partner_key'];
        $shop_id = $obj['shop_id'];
        $code = $obj['code'];
        $merchant_name = $obj['merchant_name'];


//get product shopee

        $chProduct = curl_init($base_url . "/public/api/shopee.php?request=get_products");

        //$payloadProduct = json_encode($convertJson);
        //curl_setopt($chProduct, CURLOPT_POSTFIELDS, $payloadProduct);
        curl_setopt($chProduct, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($chProduct, CURLOPT_RETURNTRANSFER, true);
        $resultProduct = curl_exec($chProduct);
        curl_close($chProduct);
        $jsonDecodeProduct = json_decode($resultProduct);

     //echo json_encode($jsonDecodeProduct->data);die;

        //looping Array data product
        //if (isset($resultProduct -> data)) {

        foreach ($jsonDecodeProduct->data as $objProduct)
        {
          $rowProduct[] = $objProduct;
          $item_id = $objProduct -> ProductID;


          //looping variants
          foreach ($objProduct -> skus as $objVariant)
          {
            $rowSkus [] = $objVariant;
            $variation_sku = $objVariant -> SkuID;
            $variation_id = $objVariant -> ProductVariantID;


            $chSkus = curl_init($base_url . "/public/api/products.php?request=get_skus");
            $payloadSkus = json_encode( array( "skus"=> $variation_sku) );
            curl_setopt($chSkus, CURLOPT_POSTFIELDS, $payloadSkus);
            curl_setopt($chSkus, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($chSkus, CURLOPT_RETURNTRANSFER, true);
            $resultSkus = curl_exec($chSkus);
            curl_close($chSkus);
            $jsonDecodeSkus = json_decode($resultSkus);

            if ($jsonDecodeSkus-> status == "200") {

              foreach ($jsonDecodeSkus -> data as $objSkus)
              {

                $stock = $objSkus->Stock;

                //echo json_encode($stock);die;
                $url = "https://partner.shopeemobile.com/api/v1/items/update_variation_stock";
                $tgl="Y-m-d";
                $waktu="H:i:s";
                $waktu_sekarang=date("$tgl $waktu");
                $ditambah_5_menit = date("$tgl $waktu", strtotime('-14 day'));
                $create_time_from=strtotime($ditambah_5_menit);

                $convertJson = array(
                  "item_id" => $item_id,
                  "variation_id" =>$variation_id,
                  "stock" =>(int)$stock,
                  "partner_id" => (int)$partner_id,
                  "shopid" => (int)$shop_id,
                  "timestamp" => $timestamp);

                $base_string = $url . "|" . json_encode($convertJson);

                $hmac = hash_hmac('sha256', $base_string, $partner_key);

                $ch = curl_init($url);
                $payload = json_encode($convertJson);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json',
                  'Authorization: ' . $hmac . ''));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);
                $jsonDecode = json_decode($result);

                $status = "Sukses";
                $message = 'Skus berhasil sync';

              }


            }else {


              $status = "Gagal";
              $message = 'skus tidak ditemukan';

            }




            $dataResult[]= array (
              "item_id" => $item_id,
              "variation_id" => $variation_id,
              "variation_sku"=>$variation_sku,
              "Status"=>$status,
              "Msg"=>$message
              //"Code"=>$code,
            );


          }
          //echo json_encode($rowProduct);die;



          //  $orders = $jsonDecode->orders;


//echo json_encode($jsonDecode);die;




        }



        //}





      }




      $return = array(
        "status" => 200,
        "message" => "Berhasil",
        "total_rows" => COUNT($dataResult),
        "data" => $dataResult
      );




    }else{
      $return= array(
        "status" => 404,
        "message" => "Toko Shopee tidak ada yang aktif",
        "total_rows" => 0,
        "data" => []


      );


    }

    //
    echo json_encode($return);

  }


  if ($content == "get_logistic_info") {

    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);

    $user_id = 5;
    $merchant_name = null;

    if (isset($post['merchant_name'])) {
      $merchant_name = $post['merchant_name'];
    }



    $getDataShopee = $db->getDataShopee($user_id, $merchant_name);

    if ($getDataShopee != null) {

      while ($rowShopee = $getDataShopee->fetch_assoc()) {
        $rows[] = $rowShopee;

      }

      foreach ($rows as $obj) {

        $partner_id = $obj['partner_id'];
        $partner_key = $obj['partner_key'];
        $shop_id = $obj['shop_id'];
        $code = $obj['code'];
        $merchant_name = $obj['merchant_name'];


//get product shopee

        $chProduct = curl_init($base_url . "/public/api/shopee.php?request=get_products");
        //$payloadProduct = json_encode($convertJson);
        //curl_setopt($chProduct, CURLOPT_POSTFIELDS, $payloadProduct);
        curl_setopt($chProduct, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($chProduct, CURLOPT_RETURNTRANSFER, true);
        $resultProduct = curl_exec($chProduct);
        curl_close($chProduct);
        $jsonDecodeProduct = json_decode($resultProduct);

        //echo json_encode($jsonDecodeProduct);die;

        //looping Array data product
        //if (isset($resultProduct -> data)) {

        foreach ($jsonDecodeProduct -> data as $objProduct)
        {
          $rowProduct[] = $objProduct;
          $item_id = $objProduct -> item_id;
          $shopid = $objProduct -> shopid;
          $update_time = $objProduct -> update_time;
          $status = $objProduct -> status;
          $item_sku = $objProduct -> item_sku;


          //looping variants
          foreach ($objProduct -> variations as $objVariant)
          {
            $rowSkus [] = $objVariant;
            $variation_sku = $objVariant -> variation_sku;
            $variation_id = $objVariant -> variation_id;


            $chSkus = curl_init($base_url . "/public/api/products.php?request=get_skus");
            $payloadSkus = json_encode( array( "skus"=> $variation_sku) );
            curl_setopt($chSkus, CURLOPT_POSTFIELDS, $payloadSkus);
            curl_setopt($chSkus, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($chSkus, CURLOPT_RETURNTRANSFER, true);
            $resultSkus = curl_exec($chSkus);
            curl_close($chSkus);
            $jsonDecodeSkus = json_decode($resultSkus);

            if ($jsonDecodeSkus-> status == "200") {

              foreach ($jsonDecodeSkus -> data as $objSkus)
              {

                $stock = $objSkus->Stock;

                //echo json_encode($stock);die;
                $url = "https://partner.shopeemobile.com/api/v1/logistics/init_info/get";
                $tgl="Y-m-d";
                $waktu="H:i:s";
                $waktu_sekarang=date("$tgl $waktu");
                $ditambah_5_menit = date("$tgl $waktu", strtotime('-14 day'));
                $create_time_from=strtotime($ditambah_5_menit);

                $convertJson = array(
                  "ordersn" => $item_id,
                  "partner_id" => (int)$partner_id,
                  "shopid" => (int)$shop_id,
                  "timestamp" => $timestamp);

                $base_string = $url . "|" . json_encode($convertJson);

                $hmac = hash_hmac('sha256', $base_string, $partner_key);

                $ch = curl_init($url);
                $payload = json_encode($convertJson);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json',
                  'Authorization: ' . $hmac . ''));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);
                $jsonDecode = json_decode($result);

                $status = "Sukses";
                $message = 'Skus berhasil sync';

              }


            }else {


              $status = "Gagal";
              $message = 'skus tidak ditemukan';

            }




            $dataResult[]= array (
              "item_id" => $item_id,
              "variation_id" => $variation_id,
              "variation_sku"=>$variation_sku,
              "Status"=>$status,
              "Msg"=>$message
              //"Code"=>$code,
            );


          }
          //echo json_encode($rowProduct);die;



          //  $orders = $jsonDecode->orders;


//echo json_encode($jsonDecode);die;




        }



        //}





      }




      $return = array(
        "status" => 200,
        "message" => "Berhasil",
        "total_rows" => COUNT($dataResult),
        "data" => $dataResult
      );




    }else{
      $return= array(
        "status" => 404,
        "message" => "Toko Shopee tidak ada yang aktif",
        "total_rows" => 0,
        "data" => []


      );


    }

    //
    echo json_encode($return);

  }


} else {
  //Aha, what you're looking for !!!
  $return = array(
    "status" => 404,
    "message" => "Method Not Found!"
  );

  echo json_encode($return);
}


?>
