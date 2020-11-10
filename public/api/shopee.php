<?php

include "../config/db_connection.php";
include "../config/config_type.php";


$rowsShopee= array();
$content = $_GET['request'];
if (isset($content) && $content != "") {

  //Load Models
  include "../models/Model_Shopee.php";

  $db = new Model_Shopee();
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

    $tgl="Y-m-d";
    $waktu="H:i:s";
    $waktu_sekarang=date("$tgl $waktu");
    $ditambah_5_menit = date("$tgl $waktu", strtotime('+5 minutes'));

    $key = "ca1e9d30c90d5982f2e9a0d1e2a42e8f18650b008c02d289592a153a53a32fc2";
    $url="https://partner.shopeemobile.com/api/v1/items/get";
    $pagination_offset=0;
    $pagination_entries_per_page=100;
    $partner_id = 844193;
    $shopid = 121739607;
    $timestamp=strtotime($ditambah_5_menit);
    $return = array(
      "status" => 404,
      "total_rows" => 9,
      //"message" => $resultInvoice['message'],
      //"data" => resultInvoice
    );

    $convertJson = array ("pagination_offset"=> $pagination_offset,
      "pagination_entries_per_page"=>$pagination_entries_per_page,
      "partner_id"=>$partner_id,
      "shopid"=>$shopid,
      "timestamp"=>$timestamp);

    $base_string = $url."|" . json_encode($convertJson);


    $hmac = hash_hmac('sha256', $base_string , $key);
    //	echo $hmac . " " .  json_encode($convertJson) ;die;

    $ch = curl_init( $url );
# Setup request to send json via POST.
    $payload = json_encode( $convertJson );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json',
      'Authorization: '.$hmac.''));
# Return response instead of printing.
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
# Send request.
    $result = curl_exec($ch);
    curl_close($ch);
    $jsonDecode = json_decode($result);
# Print response.
//echo $result;die;
    $items = $jsonDecode->items;





    $return = array(
      "status" => 200,
      "message" => "ok",
      "total_rows"=>COUNT($items),
      "data" => $items

    );






    //
    echo json_encode($return);

  }

  if ($content == "get_orders") {

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

        $url = "https://partner.shopeemobile.com/api/v1/orders/get";
        $order_status = "READY_TO_SHIP";
        $create_time_from = 0;

        $tgl="Y-m-d";
        $waktu="H:i:s";
        $waktu_sekarang=date("$tgl $waktu");
        $ditambah_5_menit = date("$tgl $waktu", strtotime('-14 day'));
        $create_time_from=strtotime($ditambah_5_menit);

        $convertJson = array(
          "order_status" => $order_status,
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


        $orders = $jsonDecode->orders;

        foreach($orders as $order)

        {
          $order_id = $order->ordersn;
          $order_number = $order->ordersn;
          $branch_number= "";
          $voucher= "";
          $voucher_platform= "";
          $gift_option= "";
          $gift_message= "";
          $shipping_fee= "";
          $shipping_fee_discount_seller="";
          $shipping_fee_discount_platform= "";
          $national_registration_number= "";
          $tax_code= "";
          $delivery_info= "";

          $chItems = curl_init("http://localhost/twinzahra/public/api/shopee.php?request=get_order_items");
          $payloadItems = json_encode( array( "ordersn_list"=> array($order_id),
            "UserID"=> "5",
            "merchant_name"=> $merchant_name) );
          curl_setopt($chItems, CURLOPT_POSTFIELDS, $payloadItems);
          curl_setopt($chItems, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
          curl_setopt($chItems, CURLOPT_RETURNTRANSFER, true);
          $resultItems = curl_exec($chItems);
          curl_close($chItems);
        $jsonDecodeItems = json_decode($resultItems);

         // echo $resultItems;die;

          foreach($jsonDecodeItems as $items)

          {

          $warehouse_code= $items->warehouse_code;
          $customer_first_name= $items->customer_first_name;
          $customer_last_name= $items->customer_last_name;
          $price= $items->price;
          $items_count= COUNT($jsonDecodeItems);
          $payment_method= $items->payment_method;
          $voucher_code= $items->voucher_code;
          $voucher_seller= $items->voucher_seller;
          $shipping_fee_original= $items->shipping_fee_original;
          $promised_shipping_times= $items->promised_shipping_time;
          $extra_attributes= $items->extra_attributes;
          $remarks= $items->remark;
          $statuses= $items->status;
          $created_at= $items->created_at;
          $updated_at= $items->updated_at;


            $return[] = array(
              "order_id" => $order_id,
              "order_number" => $order_number,
              "marketplace" => "SHOPEE",
              "merchant_name" => $merchant_name,
              "branch_number"=>$branch_number,
              "warehouse_code"=>$warehouse_code,
              "customer_first_name" => $customer_first_name,
              "customer_last_name" => $customer_last_name,
              "price"=>$price,
              "items_count"=>$items_count,
              "payment_method"=>$payment_method,
              "voucher"=>$voucher,
              "voucher_code"=>$voucher_code,
              "voucher_platform"=>$voucher_platform,
              "voucher_seller"=>$voucher_seller,
              "gift_option"=>$gift_option,
              "gift_message"=>$gift_message,
              "shipping_fee"=>$shipping_fee,
              "shipping_fee_discount_seller"=>$shipping_fee_discount_seller,
              "shipping_fee_discount_platform"=>$shipping_fee_discount_platform,
              "promised_shipping_times"=>$promised_shipping_times,
              "national_registration_number"=>$national_registration_number,
              "tax_code"=>$tax_code,
              "extra_attributes"=>$extra_attributes,
              "remarks"=>$remarks,
              "delivery_info"=>$delivery_info,
              "statuses"=>$statuses,
              "created_at"=>$created_at,
              "updated_at"=>$updated_at,
            );

          }



				   }

        }




    //$return = array(
     // "status" => 200,
      //"message" => "ok",
    // "total_rows"=>COUNT($orders),
      //"data" => $orders

    //);




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
   // $ordersn_list = ["201109J08RDDET"];
    if (isset($post['merchant_name'])) {
      $merchant_name = $post['merchant_name'];
    }


if ($ordersn_list) {


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
          $product_main_image= "";
          $product_detail_url= "";
          $shipment_provider= $order->shipping_carrier;
          $tracking_code_pre= "";
          $tracking_code= $order->tracking_no;
          $shipping_type= "";
          $shipping_provider_type= "";
          $shipping_fee_original= $order->estimated_shipping_fee;
          $shipping_service_cost= 0;
          $shipping_amount= $order->estimated_shipping_fee;
          $is_digital= 0;
          $voucher_amount= "";
          $voucher_seller= "";
          $voucher_code_seller= "";
          $voucher_code= "";
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
          $status= $order->order_status;
          $payment_method= $order->payment_method;
          $created_at= date('yy-m-d H:i:s', $order->create_time);
          $updated_at= date('yy-m-d H:i:s', $order->update_time);


          foreach($order->items as $item)
        {

//array items
          $order_item_id = $item->item_id;
          $shop_sku= $item->item_sku;
          $sku= $item->variation_sku;
          $name= $item->item_name;
          $variation= $item->variation_name;
          $item_price= $item->variation_original_price;
          $paid_price= $item->variation_discounted_price;

          }


          $return[] = array(
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
            "customer_first_name" =>$customer_first_name,
            "customer_last_name" =>$customer_last_name,
            "price" =>$price,
            "name" =>$name,
            "variation" =>$variation,
            "item_price" =>$item_price,
            "paid_price" =>$paid_price,
            "currency" =>$currency,
            "tax_amount" => $tax_amount,
            "product_main_image" =>$product_main_image,
            "product_detail_url" =>$product_detail_url,
            "shipment_provider" =>$shipment_provider,
            "tracking_code_pre" =>$tracking_code_pre,
            "tracking_code" =>$tracking_code,
            "shipping_type" =>$shipping_type,
            "shipping_provider_type" =>$shipping_provider_type,
            "shipping_fee_original" =>$shipping_fee_original,
            "shipping_service_cost " =>$shipping_service_cost,
            "shipping_amount" =>$shipping_amount,
            "is_digital" =>$is_digital,
            "voucher_amount" =>$voucher_amount,
            "voucher_seller" =>$voucher_seller,
            "voucher_code_seller" =>$voucher_code_seller,
            "voucher_code" =>$voucher_code,
            "order_flag" =>$order_flag,
            "promised_shipping_time" =>$promised_shipping_time,
            "digital_delivery_info" =>$digital_delivery_info,
            "extra_attributes" =>$extra_attributes,
            "cancel_return_initiator" =>$cancel_return_initiator,
            "remark" =>$remark,
            "reason" =>$reason,
            "reason_detail" =>$reason_detail,
            "stage_pay_status" =>$stage_pay_status,
            "warehouse_code" =>$warehouse_code,
            "return_status" =>$return_status,
            "payment_method" =>$payment_method,
            "status" =>$status,
            "created_at" =>$created_at,
            "updated_at" =>$updated_at
          );

        }
        	}else{

						 $return = array(
                          "status" => 404,
                          "message" => "Error",
                           "data" => $jdecode->errors
                        );

					}


      }




      //$return = array(
      // "status" => 200,
      //"message" => "ok",
      // "total_rows"=>COUNT($orders),
      //"data" => $orders

      //);




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
    "message" => "Error",
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
