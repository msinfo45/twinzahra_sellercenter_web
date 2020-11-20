<?php

include "../config/lazada/LazopSdk.php";

include "../config/db_connection.php";
include "../config/config_type.php";

$url='https://api.lazada.co.id/rest';


$rowsLazada = array();
$rows = [];
$content = $_GET['request'];
if (isset($content) && $content != "") {

  //Load Models
  include "../config/model.php";

  $db = new Model_user();


  if ($content == "get_seller") {
    $post = json_decode(file_get_contents("php://input"), true);
    //$user_id = $post['UserID'];
    $user_id = 5;

    $merchant_name = null;

    if (isset($post['merchant_name'])) {
      $merchant_name = $post['merchant_name'];
    }

    if (isset($user_id)) {

      $getDataLazada = $db->getDataLazada($user_id , $merchant_name);

      if ($getDataLazada != null) {


        while ($rowLazada = $getDataLazada->fetch_assoc()) {
          $rows[] = $rowLazada;


        }

        foreach ($rows as $obj) {

          $appkey =  $obj['AppKey'];
          $appSecret =  $obj['AppSecret'];
          $accessToken =  $obj['AccessToken'];
          $merchant_name =  $obj['merchant_name'];



          $c = new LazopClient($url,$appkey,$appSecret );
          $request = new LazopRequest('/seller/get','GET');
          $jdecode=json_decode($c->execute($request, $accessToken));
          $data=$jdecode->data;

          $dataReturn[] = $data;

        }







        $return= array(
          "status" => 200,
          "data" => $dataReturn
        );



      }


    } else {
      $return = array(
        "status" => 404,
        "message" => "Oops sepertinya ada yang salah!"
      );
    }


    echo json_encode($return);



  }

  if ($content == "get_orders") {
    $post = json_decode(file_get_contents("php://input"), true);
    $user_id = 5;


    $status = $post['status'];
    $orderArr = array();
    $orderItemsArr = array();
    $rowsOrdersn = array();
    $result = array();
    $resultOrdersItems = array();

    $seller_id = null;
    $marketplace = "lAZADA";

    if (isset($post['seller_id'])) {
      $seller_id = $post['seller_id'];
    }

    if ($status == 1) {

      $status = "pending";

    }else if ($status == 2) {

      $status = "ready_to_ship";

    }else if ($status == 3) {

      $status = "shipped";

    }else if ($status == 4) {

      $status = "delivered";


    }else if ($status == 6) {

      $status = "canceled";

    }else if ($status == 7) {

      $status = "failed";

    }else if ($status == 5) {

      $status = "returned";

    }else if ($status == 8) {

      $status = "unpaid";


    }else{

    //  $status = "ALL";

    }


    if (isset($user_id)) {

      $getDataMarketplace = $db->getDataMarketplace($marketplace);

      if ($getDataMarketplace != null) {

      while ($rowMarketplace = $getDataMarketplace->fetch_assoc()) {

          $appkey = $rowMarketplace['app_key'];
          $appSecret = $rowMarketplace['app_secret'];

        }

       //mencari data toko
        $getDataToko= $db->getDataToko($user_id , $seller_id ,$marketplace);

        if ($getDataToko != null) {

          while ($rowToko= $getDataToko->fetch_assoc()) {
            $rows[] = $rowToko;

          }


          foreach ($rows as $rowsToko) {
            $accessToken = $rowsToko['access_token'];
            $merchant_name = $rowsToko['merchant_name'];
            $marketplace_name = $rowsToko['marketplace_name'];



          $c = new LazopClient($url,$appkey,$appSecret );
          $request = new LazopRequest('/orders/get','GET');
          $request->addApiParam('status',$status);
          $request->addApiParam('created_after','2020-01-01T09:00:00+08:00');
          $request->addApiParam('sort_direction','DESC');
          $request->addApiParam('offset','0');
          $request->addApiParam('update_after','2020-01-01T09:00:00+08:00');
          $request->addApiParam('sort_by','updated_at');
          //var_dump($c->execute($request, $access_token));
          $jdecode=json_decode($c->execute($request, $accessToken));
          //$jencode=json_encode($jdecode, true);

          //	echo json_encode($jdecode);die;






          //cek token expire
          if ($jdecode->code == "0") {

            $order=$jdecode->data->orders;
            $count=$jdecode->data->count;



            foreach($order as $datas)

            {


              $order_id = $datas->order_id;
              $order_number = $datas->order_number;
              $branch_number= $datas->branch_number;
              $warehouse_code= $datas->warehouse_code;
              $marketplace = "LAZADA";
              $customer_first_name= $datas->customer_first_name;
              $customer_last_name= $datas->customer_last_name;
              $price= $datas->price;
              $items_count= $datas->items_count;
              $payment_method= $datas->payment_method;
              $voucher= $datas->voucher;
              $voucher_code= $datas->voucher_code;
              $voucher_platform= $datas->voucher_platform;
              $voucher_seller= $datas->voucher_seller;
              $gift_option= $datas->gift_option;
              $gift_message= $datas->gift_message;
              $shipping_fee= $datas->shipping_fee;
              $shipping_fee_original= $datas->shipping_fee_original;
              $shipping_fee_discount_seller= $datas->shipping_fee_discount_seller;
              $shipping_fee_discount_platform= $datas->shipping_fee_discount_platform;
              $promised_shipping_times= $datas->promised_shipping_times;
              $national_registration_number= $datas->national_registration_number;
              $tax_code= $datas->tax_code;
              $extra_attributes= $datas->extra_attributes;
              $remarks= $datas->remarks;
              $delivery_info= $datas->delivery_info;
              $statuses= $datas->statuses;
              $created_at= $datas->created_at;
              $updated_at= $datas->updated_at;
              $address_billing=$datas->address_billing;


              $first_name = $datas ->address_shipping->first_name;
              $last_name = $datas ->address_shipping->last_name;
              $country = $datas ->address_shipping->country;
              $phone = $datas ->address_shipping->phone;
              $phone2 = $datas ->address_shipping->phone2;
              $address1 = $datas ->address_shipping->address1;
              $address2 =$datas ->address_shipping->address2;
              $address3 = $datas ->address_shipping->address3;
              $address4 = $datas ->address_shipping->address4;
              $address5 = $datas ->address_shipping->address5;
              $city = $datas ->address_shipping->city;
              $post_code = $datas ->address_shipping->post_code;

              $cOrderItems = new LazopClient($url,$appkey,$appSecret );
              $requestOrderItems  = new LazopRequest('/order/items/get','GET');
              $requestOrderItems ->addApiParam('order_id',$order_id);
              $jdecodeOrderItems =json_decode($cOrderItems ->execute($requestOrderItems , $accessToken));

              //echo json_encode($jdecodeOrderItems);die;

              foreach ($jdecodeOrderItems->data as $orderItems) {

                $order_item_id = $orderItems->order_item_id;
                $order_id = $orderItems->order_id;
                $purchase_order_id = $orderItems->purchase_order_id;
                $purchase_order_number = $orderItems->purchase_order_number;
                $invoice_number = $orderItems->purchase_order_number;
                $sla_time_stamp = $orderItems->sla_time_stamp;
                $package_id= $orderItems->package_id;
                $shop_id= $orderItems->shop_id;
                $order_type= $orderItems->order_type;
                $shop_sku= $orderItems->shop_sku;
                $sku= $orderItems->sku;
                $name= $orderItems->name;
                $variation= $orderItems->variation;
                $item_price= $orderItems->item_price;
                $paid_price= $orderItems->paid_price;
                $qty= 1;
                $currency= $orderItems->currency;
                $tax_amount= $orderItems->tax_amount;
                $product_detail_url= $orderItems->product_detail_url;
                $shipment_provider= $orderItems->shipment_provider;
                $tracking_code_pre= $orderItems->tracking_code_pre;
                $tracking_code= $orderItems->tracking_code;
                $shipping_type= $orderItems->shipping_type;
                $shipping_provider_type= $orderItems->shipping_provider_type;
                $shipping_fee_original= $orderItems->shipping_fee_original;
                $shipping_service_cost= $orderItems->shipping_service_cost;
               // $shipping_fee_discount_seller= $orderItems->shipping_fee_discount_seller;
                $shipping_amount= $orderItems->shipping_amount;
                $is_digital= $orderItems->is_digital;
                $voucher_amount= $orderItems->voucher_amount;
                $voucher_seller= $orderItems->voucher_seller;
                $voucher_code_seller= $orderItems->voucher_code_seller;
                $voucher_code= $orderItems->voucher_code;
                $voucher_code_platform= $orderItems->voucher_code_platform;
                $voucher_platform= $orderItems->voucher_platform;
                $order_flag= $orderItems->order_flag;
                $promised_shipping_time= $orderItems->promised_shipping_time;
                $digital_delivery_info= $orderItems->digital_delivery_info;
                $extra_attributes= $orderItems->extra_attributes;
                $cancel_return_initiator= $orderItems->cancel_return_initiator;
                $reason= $orderItems->reason;
                $reason_detail= $orderItems->reason_detail;
                $stage_pay_status= $orderItems->stage_pay_status;
                 $warehouse_code= $orderItems->warehouse_code;
                 $return_status= $orderItems->return_status;
                $imageImageVariant=$orderItems->product_main_image;

              }

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
              $orderArr[$order_id]['items_count'] = $items_count;
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



            $return = array(
              "status" => 200,
              "message" => "ok lazada",
              "total_rows"=>COUNT($result),
              "data" => $result

            );

            //	}


          }else{

            $return = array(
              "status" => 404,
              "message" => $jdecode->message
            );

          }


        }


      }



}





    } else {
      $return = array(
        "status" => 404,
        "message" => "Oops sepertinya ada yang salah!"
      );
    }

    echo json_encode($return);
  }

  if ($content == "get_order") {
    $post = json_decode(file_get_contents("php://input"), true);
    $user_id = 5;
    $merchant_name = null;

    $status = $post['status'];
    $order_id = $post['order_id'];
    $orderArr = array();
    $orderItemsArr = array();
    $rowsOrdersn = array();
    $result = array();
    $resultOrdersItems = array();
    if (isset($post['merchant_name'])) {
      $merchant_name = $post['merchant_name'];
    }





    if (isset($user_id) && isset($order_id)) {

      $getDataLazada = $db->getDataLazada($user_id, $merchant_name);

      if ($getDataLazada != null) {

        while ($rowLazada = $getDataLazada->fetch_assoc()) {
          $rows[] = $rowLazada;

        }


        foreach ($rows as $obj) {


          $appkey =  $obj['AppKey'];
          $appSecret =  $obj['AppSecret'];
          $accessToken =  $obj['AccessToken'];
          $merchant_name =  $obj['merchant_name'];



          $c = new LazopClient($url,$appkey,$appSecret );
          $request = new LazopRequest('/order/get','GET');
          $request->addApiParam('order_id',$order_id);
          //var_dump($c->execute($request, $access_token));
          $jdecode=json_decode($c->execute($request, $accessToken));
          //$jencode=json_encode($jdecode, true);

          //$dataReturn[] = $data;

          	//echo json_encode($jdecode);die;






          //cek token expire
          if ($jdecode->code == "0") {

            $datas=$jdecode->data;
            //$count=$jdecode->data->count;





              $order_id = $datas->order_id;
              $order_number = $datas->order_number;
              $branch_number= $datas->branch_number;
              $warehouse_code= $datas->warehouse_code;
              $marketplace = "LAZADA";
              $customer_first_name= $datas->customer_first_name;
              $customer_last_name= $datas->customer_last_name;
              $price= $datas->price;
              $items_count= $datas->items_count;
              $payment_method= $datas->payment_method;
              $voucher= $datas->voucher;
              $voucher_code= $datas->voucher_code;
             // $voucher_platform= $datas->voucher_platform;
             // $voucher_seller= $datas->voucher_seller;
              $gift_option= $datas->gift_option;
              $gift_message= $datas->gift_message;
              $shipping_fee= $datas->shipping_fee;
              $shipping_fee_original= $datas->shipping_fee_original;
              $shipping_fee_discount_seller= $datas->shipping_fee_discount_seller;
              $shipping_fee_discount_platform= $datas->shipping_fee_discount_platform;
              $promised_shipping_times= $datas->promised_shipping_times;
              $national_registration_number= $datas->national_registration_number;
              $tax_code= $datas->tax_code;
              $extra_attributes= $datas->extra_attributes;
              $remarks= $datas->remarks;
              $delivery_info= $datas->delivery_info;
              $statuses= $datas->statuses;
              $created_at= $datas->created_at;
              $updated_at= $datas->updated_at;
              $address_billing=$datas->address_billing;


              $first_name = $datas ->address_shipping->first_name;
              $last_name = $datas ->address_shipping->last_name;
              $country = $datas ->address_shipping->country;
              $phone = $datas ->address_shipping->phone;
              $phone2 = $datas ->address_shipping->phone2;
              $address1 = $datas ->address_shipping->address1;
              $address2 =$datas ->address_shipping->address2;
              $address3 = $datas ->address_shipping->address3;
              $address4 = $datas ->address_shipping->address4;
              $address5 = $datas ->address_shipping->address5;
              $city = $datas ->address_shipping->city;
              $post_code = $datas ->address_shipping->post_code;

              $cOrderItems = new LazopClient($url,$appkey,$appSecret );
              $requestOrderItems  = new LazopRequest('/order/items/get','GET');
              $requestOrderItems ->addApiParam('order_id',$order_id);
              $jdecodeOrderItems =json_decode($cOrderItems ->execute($requestOrderItems , $accessToken));

              //echo json_encode($jdecodeOrderItems);die;

              foreach ($jdecodeOrderItems->data as $orderItems) {

                $order_item_id = $orderItems->order_item_id;
                $order_id = $orderItems->order_id;
                $purchase_order_id = $orderItems->purchase_order_id;
                $purchase_order_number = $orderItems->purchase_order_number;
                $invoice_number = $orderItems->purchase_order_number;
                $sla_time_stamp = $orderItems->sla_time_stamp;
                $package_id= $orderItems->package_id;
                $shop_id= $orderItems->shop_id;
                $order_type= $orderItems->order_type;
                $shop_sku= $orderItems->shop_sku;
                $sku= $orderItems->sku;
                $name= $orderItems->name;
                $variation= $orderItems->variation;
                $item_price= $orderItems->item_price;
                $paid_price= $orderItems->paid_price;
                $qty= 1;
                $currency= $orderItems->currency;
                $tax_amount= $orderItems->tax_amount;
                $product_detail_url= $orderItems->product_detail_url;
                $shipment_provider= $orderItems->shipment_provider;
                $tracking_code_pre= $orderItems->tracking_code_pre;
                $tracking_code= $orderItems->tracking_code;
                $shipping_type= $orderItems->shipping_type;
                $shipping_provider_type= $orderItems->shipping_provider_type;
                $shipping_fee_original= $orderItems->shipping_fee_original;
                $shipping_service_cost= $orderItems->shipping_service_cost;
                // $shipping_fee_discount_seller= $orderItems->shipping_fee_discount_seller;
                $shipping_amount= $orderItems->shipping_amount;
                $is_digital= $orderItems->is_digital;
                $voucher_amount= $orderItems->voucher_amount;
                $voucher_seller= $orderItems->voucher_seller;
                $voucher_code_seller= $orderItems->voucher_code_seller;
                $voucher_code= $orderItems->voucher_code;
                $voucher_code_platform= $orderItems->voucher_code_platform;
                $voucher_platform= $orderItems->voucher_platform;
                $order_flag= $orderItems->order_flag;
                $promised_shipping_time= $orderItems->promised_shipping_time;
                $digital_delivery_info= $orderItems->digital_delivery_info;
                $extra_attributes= $orderItems->extra_attributes;
                $cancel_return_initiator= $orderItems->cancel_return_initiator;
                $reason= $orderItems->reason;
                $reason_detail= $orderItems->reason_detail;
                $stage_pay_status= $orderItems->stage_pay_status;
                $warehouse_code= $orderItems->warehouse_code;
                $return_status= $orderItems->return_status;
                $imageImageVariant=$orderItems->product_main_image;

              }

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
              $orderArr[$order_id]['items_count'] = $items_count;
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



          //  }

            $result = array_values($orderArr);



            $return = array(
              "status" => 200,
              "message" => "ok lazada",
              "total_rows"=>COUNT($result),
              "data" => $result

            );

            //	}


          }else{

            $return = array(
              "status" => 404,
              "message" => $jdecode->message
            );

          }

          //}else {
          //  $return = array(
          //     "status" => 404,
          //    "message" => "Akun lazada belum ada yang aktif"
          //);
          //}

        }


      } else {
        $return = array(
          "status" => 404,
          "message" => "Akun lazada belum diatur"
        );
      }








    } else {
      $return = array(
        "status" => 404,
        "message" => "Oops sepertinya ada yang salah!"
      );
    }

    echo json_encode($return);
  }


  if ($content == "get_order_items") {
    $post = json_decode(file_get_contents("php://input"), true);
    //$user_id = $post['UserID'];
    $user_id = 5;
    $order_id = $post['order_id'];;


    $merchant_name = null;

    if (isset($post['merchant_name'])) {
      $merchant_name = $post['merchant_name'];
    }


    //	$order_id = 483725973795096;
    //$merchant_name="Twinzahra Shop";

    if (isset($user_id)) {

      $getDataLazada = $db->getDataLazada($user_id , $merchant_name);


      if ($getDataLazada != null) {

        while ($rowLazada = $getDataLazada->fetch_assoc()) {
          $rows[] = $rowLazada;

        }



        foreach ($rows as $obj) {

          $appkey =  $obj['AppKey'];
          $appSecret =  $obj['AppSecret'];
          $accessToken =  $obj['AccessToken'];
          $merchant_name =  $obj['merchant_name'];


          $c = new LazopClient($url,$appkey,$appSecret );

          $request = new LazopRequest('/order/items/get','GET');
          $request->addApiParam('order_id',$order_id);

          $jdecode=json_decode($c->execute($request, $accessToken));




//echo json_encode($jdecode);die;

//cek token expire
          if ($jdecode->code == "0") {

            $data=$jdecode->data;



            foreach ($data as $datas) {

              $order_item_id = $datas->order_item_id;
              $order_id = $datas->order_id;
              $purchase_order_id= $datas->purchase_order_id;
              $purchase_order_number= $datas->purchase_order_number;
              $invoice_number= $datas->invoice_number;
              $sla_time_stamp= $datas->sla_time_stamp;
              $package_id= $datas->package_id;
              $shop_id= $datas->shop_id;
              $order_type= $datas->order_type;
              $shop_sku= $datas->shop_sku;
              $sku= $datas->sku;
              $name= $datas->name;
              $variation= $datas->variation;
              $item_price= $datas->item_price;
              $paid_price= $datas->paid_price;
              $currency= $datas->currency;
              $tax_amount= $datas->tax_amount;
              $product_main_image= $datas->product_main_image;
              $product_detail_url= $datas->product_detail_url;
              $shipment_provider= $datas->shipment_provider;
              $tracking_code_pre= $datas->tracking_code_pre;
              $tracking_code= $datas->tracking_code;
              $shipping_type= $datas->shipping_type;
              $shipping_provider_type= $datas->shipping_provider_type;
              $shipping_fee_original= $datas->shipping_fee_original;
              $shipping_service_cost= $datas->shipping_service_cost;
              //$shipping_fee_discount_seller= $datas->shipping_fee_discount_seller;
              $shipping_amount= $datas->shipping_amount;
              $is_digital= $datas->is_digital;
              $voucher_amount= $datas->voucher_amount;
              $voucher_seller= $datas->voucher_seller;
              $voucher_code_seller= $datas->voucher_code_seller;
              $voucher_code= $datas->voucher_code;
              //$voucher_code_platform->voucher_code_platform;
              //$voucher_platform= $voucher_platform;
              $order_flag= $datas->order_flag;
              $promised_shipping_time= $datas->promised_shipping_time;
              $digital_delivery_info= $datas->digital_delivery_info;
              $extra_attributes= $datas->extra_attributes;
              $cancel_return_initiator= $datas->cancel_return_initiator;
              $reason= $datas->reason;
              $reason_detail= $datas->reason_detail;
              $stage_pay_status= $datas->stage_pay_status;
              $warehouse_code= $datas->warehouse_code;
              $return_status= $datas->return_status;
              $status= $datas->status;
              $created_at= $datas->created_at;
              $updated_at= $datas->updated_at;


              $return[] = array(

                "order_item_id" => $order_item_id,
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
                //"shipping_fee_discount_seller" =>$shipping_fee_discount_seller,
                "shipping_amount" =>$shipping_amount,
                "is_digital" =>$is_digital,
                "voucher_amount" =>$voucher_amount,
                "voucher_seller" =>$voucher_seller,
                "voucher_code_seller" =>$voucher_code_seller,
                "voucher_code" =>$voucher_code,
                //"voucher_code_platform" =>$voucher_code_platform,
                //"voucher_platform" =>$voucher_platform,
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



          }else{

            $return = array(
              "status" => 404,
              "message" => $jdecode->message
            );

          }

        }


      } else {
        $return = array(
          "status" => 404,
          "message" => "Token Belum di setting"
        );
      }



    } else {
      $return = array(
        "status" => 404,
        "message" => "Oops sepertinya ada yang salah!"
      );
    }


    echo json_encode($return);



  }


  if ($content == "get_shipment_providers") {
    $post = json_decode(file_get_contents("php://input"), true);
    //$user_id = $post['UserID'];
    $user_id = 5;
    $merchant_name = null;

    if (isset($post['merchant_name'])) {
      $merchant_name = $post['merchant_name'];
    }

    if (isset($user_id)) {

      $getDataLazada = $db->getDataLazada($user_id , $merchant_name);

      if ($getDataLazada != null) {


        while ($rowLazada = $getDataLazada->fetch_assoc()) {
          $appkey =  $rowLazada['AppKey'];
          $appSecret =  $rowLazada['AppSecret'];
          $accessToken =  $rowLazada['AccessToken'];
        }


        $c = new LazopClient($url,$appkey,$appSecret );
        $request = new LazopRequest('/shipment/providers/get','GET');
        $jdecode=json_decode($c->execute($request, $accessToken));
        $shipment_providers=$jdecode->data->shipment_providers;



        $return = $shipment_providers;

      }





    } else {
      $return = array(
        "status" => 404,
        "message" => "Oops sepertinya ada yang salah!"
      );
    }


    echo json_encode($return);



  }



  if ($content == "create_product") {
    $post = json_decode(file_get_contents("php://input"), true);

    $user_id = 5;
    $marketplace = "lAZADA";
    $seller_id = $post['seller_id'];

    if (isset($seller_id)) {
    //Mencari data marketplace
    $getDataMarketplace = $db->getDataMarketplace($marketplace);

    if ($getDataMarketplace != null) {

      while ($rowMarketplace = $getDataMarketplace->fetch_assoc()) {

        $appkey = $rowMarketplace['app_key'];
        $appSecret = $rowMarketplace['app_secret'];

      }

      //mencari data toko
      $getDataToko= $db->getDataToko($user_id , $seller_id , $marketplace);

      if ($getDataToko != null) {

        while ($rowToko= $getDataToko->fetch_assoc()) {

          $accessToken = $rowToko['access_token'];
          $merchant_name = $rowToko['merchant_name'];
          $marketplace_name = $rowToko['marketplace_name'];

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

        // echo json_encode($resultProducts);die;
        if ($resultProducts->status == 200) {

          foreach($resultProducts->data as $dataProducts) {

            $xml_output = '<?xml version="1.0" encoding="UTF-8" ?>';
            $xml_output .= '<Request>';
            $xml_output .= '<Product>';
            $xml_output .= '<PrimaryCategory>' . $dataProducts->CategoryID . '</PrimaryCategory>';
            $xml_output .= '<Attributes>';
            $xml_output .= '<name>' . $dataProducts->ProductName . '</name>';
            $xml_output .= '<short_description>' . $dataProducts->Description . '</short_description>';
            $xml_output .= '<brand>Wakai</brand>';
//	$xml_output .= '<model>' . $dataProducts->BrandID . '</model>';
            $xml_output .= '</Attributes>';
            $xml_output .= '<Skus>';




            foreach ($dataProducts->skus as $dataSkus) {

              $SkuID = $dataSkus->SkuID;
              $Stock = $dataSkus->Stock;
              $PriceRetail = $dataSkus->PriceRetail;


              $xml_output .= '<Sku>';

              $xml_output .= '<SellerSku>' . $dataSkus->SkuID . '</SellerSku>';
              $xml_output .= '<color_family>' . $dataSkus->ProductVariantName . '</color_family>';
              $xml_output .= '<size>' . $dataSkus->ProductVariantDetailName . '</size>';
              $xml_output .= '<quantity>' . $dataSkus->Stock . '</quantity>';
              $xml_output .= '<price>' . $dataSkus->PriceRetail . '</price>';
              $xml_output .= '<package_length>5</package_length>';
              $xml_output .= '<package_height>5</package_height>';
              $xml_output .= '<package_weight>0.5</package_weight>';
              $xml_output .= '<package_width>5</package_width>';
              // $xml_output .= '<package_content>44</package_content>';
              $xml_output .= '<Images>';

              foreach ($dataSkus->Images as $images) {

                $xml_output .= '<Image>' . $images . '</Image>';
              }



              $xml_output .= '</Images>';

              $xml_output .= '</Sku>';

            }


            $xml_output .= '</Skus>';
            $xml_output .= '</Product>';
            $xml_output .= '</Request>';

            //  echo $xml_output;die;
            $c = new LazopClient($url,$appkey,$appSecret);
            $request = new LazopRequest('/product/create');
            // $request = new LazopRequest('/product/update');
            $request->addApiParam('payload', $xml_output);
            $jdecode=json_decode($c->execute($request, $accessToken));
            //  echo json_encode($jdecode);die;
            $code = $jdecode->code;
            //$message = $jdecode->message;

            if ($code == 0) {

              $status = "Sukses";
              $message = "";

            }else{
              $status = "Gagal";
              //  $message = $jdecode->message;
              $message = "Produk sudah ada";

            }

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

  if ($content == "update_product") {
    $post = json_decode(file_get_contents("php://input"), true);

    $user_id = 5;
    $marketplace = "lAZADA";

    $seller_id = null;

    if (isset($post['seller_id'])) {
      $seller_id = $post['seller_id'];
    }
    

   // if (isset($seller_id)) {
      //Mencari data marketplace
      $getDataMarketplace = $db->getDataMarketplace($marketplace);

      if ($getDataMarketplace != null) {

        while ($rowMarketplace = $getDataMarketplace->fetch_assoc()) {

          $appkey = $rowMarketplace['app_key'];
          $appSecret = $rowMarketplace['app_secret'];

        }

        //mencari data toko
        $getDataToko= $db->getDataToko($user_id , $seller_id , $marketplace);

        if ($getDataToko != null) {

          while ($rowToko= $getDataToko->fetch_assoc()) {
            $rows[] = $rowToko;   

          }

        foreach ($rows as $rowsToko2) {
          $accessToken = $rowsToko2['access_token'];
          $merchant_name = $rowsToko2['merchant_name'];
          $marketplace_name = $rowsToko2['marketplace_name'];

        
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $base_url . '/public/api/products.php?request=get_products');
          $payload = json_encode( array( "UserID"=> $user_id) );
          curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
          curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          $productContent = curl_exec($ch);
          curl_close($ch);
          $resultProducts=json_decode($productContent);

          // echo json_encode($resultProducts);die;
          if ($resultProducts->status == 200) {

            foreach($resultProducts->data as $dataProducts) {

              $xml_output = '<?xml version="1.0" encoding="UTF-8" ?>';
              $xml_output .= '<Request>';
              $xml_output .= '<Product>';
              //$xml_output .= '<ItemId>' . $dataProducts->CategoryID . '</ItemId>';
              $xml_output .= '<Attributes>';
              $xml_output .= '<name>' . $dataProducts->ProductName . '</name>';
              $xml_output .= '<short_description>' . $dataProducts->Description . '</short_description>';
              $xml_output .= '<brand>Wakai</brand>';
//	$xml_output .= '<model>' . $dataProducts->BrandID . '</model>';
              $xml_output .= '</Attributes>';
              $xml_output .= '<Skus>';




              foreach ($dataProducts->skus as $dataSkus) {

                $SkuID = $dataSkus->SkuID;
                $Stock = $dataSkus->Stock;
                $PriceRetail = $dataSkus->PriceRetail;


                $xml_output .= '<Sku>';

                $xml_output .= '<SellerSku>' . $dataSkus->SkuID . '</SellerSku>';
                $xml_output .= '<color_family>' . $dataSkus->ProductVariantName . '</color_family>';
                $xml_output .= '<size>' . $dataSkus->ProductVariantDetailName . '</size>';
                $xml_output .= '<quantity>' . $dataSkus->Stock . '</quantity>';
                $xml_output .= '<price>' . $dataSkus->PriceRetail . '</price>';
                $xml_output .= '<package_length>5</package_length>';
                $xml_output .= '<package_height>5</package_height>';
                $xml_output .= '<package_weight>0.5</package_weight>';
                $xml_output .= '<package_width>5</package_width>';
                // $xml_output .= '<package_content>44</package_content>';
                $xml_output .= '<Images>';

                foreach ($dataSkus->Images as $images) {

                  $xml_output .= '<Image>' . $images . '</Image>';
                }



                $xml_output .= '</Images>';

                $xml_output .= '</Sku>';

              }


              $xml_output .= '</Skus>';
              $xml_output .= '</Product>';
              $xml_output .= '</Request>';

              //  echo $xml_output;die;
              $c = new LazopClient($url,$appkey,$appSecret);
             $request = new LazopRequest('/product/update');
              $request->addApiParam('payload', $xml_output);
              $jdecode=json_decode($c->execute($request, $accessToken));
              //  echo json_encode($jdecode);die;
              $code = $jdecode->code;
              //$message = $jdecode->message;

              if ($code == 0) {

                $status = "Sukses";
                $message = "";

              }else{
                $status = "Gagal";
                 $message = $jdecode->message;
                //$message = "Produk sudah ada";

              }

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


		}else{
			
	

      $return = array(
        "status" => 404,
        "message" => "Belum ada toko yang aktif",
        "data" => []
     );
    	
			
		}

		}






    echo json_encode($return);
  }


  if ($content == "get_document") {
    $post = json_decode(file_get_contents("php://input"), true);
    //$user_id = $post['UserID'];
    $user_id = 5;
	$order_item_ids = base64_decode($post['order_item_ids']);
	$doc_type = $post['doc_type'];
	

	 $tgl = "d mm Yr";
        $waktu = "H:i:s";
        $waktu_sekarang = date("d F Y h:i:s");
        //$ditambah_5_menit = date("$tgl $waktu", strtotime('+5 minutes'));

  $timestamp = strtotime($waktu_sekarang);
    $merchant_name = null;
//echo $timestamp;die;

    if (isset($post['merchant_name'])) {
      $merchant_name = $post['merchant_name'];
    }

    if (isset($user_id)) {

      $getDataLazada = $db->getDataLazada($user_id, $merchant_name);

      if ($getDataLazada != null) {


        while ($rowLazada = $getDataLazada->fetch_assoc()) {
          $appkey =  $rowLazada['AppKey'];
          $appSecret =  $rowLazada['AppSecret'];
          $accessToken =  $rowLazada['AccessToken'];
        }

	$dataArr = array("doc_type" => $doc_type,
					"order_item_ids" =>$order_item_ids,
					"app_key" =>$appkey,
					"sign_method" =>"sha256",
					"timestamp" =>$timestamp,
					"access_token" =>$accessToken);
		
asort($dataArr);

foreach($dataArr as $x => $x_value) {
	
  $base_string = "/order/document/get" . $x . $x_value;


  $sign = hash('sha256', $base_string);
	
	
	}
	
	//echo $sign;die;
	
echo 'https://api.lazada.co.id/rest/order/document/get?doc_type='. $doc_type.'&order_item_ids='. $order_item_ids.'&app_key='. $appkey.'&sign_method=sha256&timestamp='. $timestamp.'&access_token='. $accessToken.'&sign='. $sign.'';die;

        $c = new LazopClient($url,$appkey,$appSecret );

        $request = new LazopRequest('/order/document/get','GET');
        $request->addApiParam('doc_type','shippingLabel');
        $request->addApiParam('order_item_ids',$order_item_ids);

        $jdecode=json_decode($c->execute($request, $accessToken));





        $data=$jdecode->data;

        $return = array(
          "status" => 200,
          "message" => $jdecode
        );

      }


    } else {
      $return = array(
        "status" => 404,
        "message" => "Oops sepertinya ada yang salah!"
      );
    }


    echo json_encode($return);



  }


  if ($content == "set_pick") {
    $post = json_decode(file_get_contents("php://input"), true);
    //$user_id = $post['UserID'];
    $user_id = 5;
    $order_item_ids = $post['order_item_ids'];
    $shipping_provider =$post['shipping_provider'];
    $delivery_type = $post['delivery_type'];
    $merchant_name = $post['merchant_name'];



 // echo json_encode($order_item_ids);die;

    if (isset($user_id) && isset($order_item_ids) && isset($shipping_provider) && isset($delivery_type) ) {

      $getDataLazada = $db->getDataLazada($user_id ,$merchant_name);

      if ($getDataLazada != null) {

        while ($rowLazada = $getDataLazada->fetch_assoc()) {
          $rows[] = $rowLazada;

        }

        foreach ($rows as $obj) {

          $appkey =  $obj['AppKey'];
          $appSecret =  $obj['AppSecret'];
          $accessToken =  $obj['AccessToken'];
          $merchant_name =  $obj['merchant_name'];



          $c = new LazopClient($url,$appkey,$appSecret );

          $request = new LazopRequest('/order/pack');
          $request->addApiParam('shipping_provider',$shipping_provider);
          $request->addApiParam('delivery_type', $delivery_type);
          $request->addApiParam('order_item_ids', $order_item_ids);
          $data=json_decode($c->execute($request, $accessToken));


        // echo json_encode($data);die;
        // if (isset($datas=$data->data->order_items)) {
        //  $datas=$data->data->order_items;
        // }
         


          if ( $data->code  == "0") {
			  
            $datas=$data->data->order_items;
			
            $return = array(
              "status" => 200,
              "message" => "Pesanan berhasil dikonfirmasi",
              "data" => $datas
            );
          }else{

            $return = array(
              "status" => 404,
              "message" => $data->message,
              "data" =>$data
            );

          }



        }

      } else {
        $return = array(
          "status" => 404,
          "message" => "Toko anda belum diatur",
          "data" => []
        );
      }



    } else {
      $return = array(
        "status" => 404,
        "message" => "Oops sepertinya ada yang salah!",
        "data" => []
      );
    }


    echo json_encode($return);



  }

   if ($content == "set_rts") {
    $post = json_decode(file_get_contents("php://input"), true);
    //$user_id = $post['UserID'];
    $user_id = 5;
    $order_item_ids = $post['order_item_ids'];
    $shipping_provider =$post['shipping_provider'];
    $delivery_type = $post['delivery_type'];
    $tracking_number = $post['tracking_number'];
	
    $merchant_name = $post['merchant_name'];

  //echo "". json_encode($order_item_ids) . "" ;die;

    if (isset($user_id) && isset($order_item_ids) && isset($shipping_provider) && isset($delivery_type) && isset($tracking_number) ) {

      $getDataLazada = $db->getDataLazada($user_id , $merchant_name);

      if ($getDataLazada != null) {

        while ($rowLazada = $getDataLazada->fetch_assoc()) {
          $rows[] = $rowLazada;

        }

        foreach ($rows as $obj) {

          $appkey =  $obj['AppKey'];
          $appSecret =  $obj['AppSecret'];
          $accessToken =  $obj['AccessToken'];
          $merchant_name =  $obj['merchant_name'];


          $c = new LazopClient($url,$appkey,$appSecret );


         $request = new LazopRequest('/order/rts');
          $request->addApiParam('delivery_type', $delivery_type);
          $request->addApiParam('order_item_ids', json_encode($order_item_ids) );
          $request->addApiParam('shipment_provide', $shipping_provider);
          $request->addApiParam('tracking_number', $tracking_number);
          $jdecode=json_decode($c->execute($request, $accessToken));
  
          //$response = json_encode($data);
		  $data=$jdecode->data;

         

          if ( $jdecode->code  == "0") {
 //echo  json_encode($data);die;
            $return = array(
              "status" => 200,
             "message" => "Pesanan berhasil dikirim , tunggu update dari jasa pengiriman",
              "data" => $data
            );
          }else{

            $return = array(
              "status" => 404,
            "message" => $jdecode->message,
              "data" =>$data
            );

          }

        }



      } else {
        $return = array(
          "status" => 404,
          "message" => "Toko anda belum diatur",
          "data" => []
        );
      }


    } else {
      $return = array(
        "status" => 404,
        "message" => "Oops sepertinya ada yang salah!",
        "data" => []
      );
    }


    echo json_encode($return);



  }


  if ($content == "set_invoice") {
    $post = json_decode(file_get_contents("php://input"), true);
    //$user_id = $post['UserID'];
    $user_id = 5;
    $order_item_id = $post['order_item_id'];
    $merchant_name = $post['merchant_name'];


    //$order_item_id = "439088831036995";
    //$shipping_provider = "LEX ID" ;
    //$delivery_type = "dropship" ;
    //$tracking_number = "LXAD-2026285224";



    if (isset($user_id) && isset($order_item_id) ) {

      $invoice_number = $order_item_id;

      $getDataLazada = $db->getDataLazada($user_id , $merchant_name);

      if ($getDataLazada != null) {

        while ($rowLazada = $getDataLazada->fetch_assoc()) {
          $rows[] = $rowLazada;

        }

        foreach ($rows as $obj) {

          $appkey =  $obj['AppKey'];
          $appSecret =  $obj['AppSecret'];
          $accessToken =  $obj['AccessToken'];
          $merchant_name =  $obj['merchant_name'];


          $c = new LazopClient($url,$appkey,$appSecret );

          $request = new LazopRequest('/order/invoice_number/set');
          $request->addApiParam('order_item_id', $order_item_id);
          $request->addApiParam('invoice_number', $invoice_number);
          $jsonEncode=json_decode($c->execute($request, $accessToken));
		  $data=$jsonEncode->data;
          $response = json_encode($data);

          //print_r ($response);die;

          if ( $jsonEncode->code  == "0") {

            $return = array(
              "status" => 200,
              "message" => "ok",
              "data" => $jsonEncode
            );
          }else{

            $return = array(
              "status" => 404,
              "message" => $jsonEncode->message,
              "data" =>$jsonEncode
            );

          }
        }


      } else {
        $return = array(
          "status" => 404,
          "message" => "Toko anda belum diatur",
          "data" => []
        );
      }


    } else {
      $return = array(
        "status" => 404,
        "message" => "Oops sepertinya ada yang salah?",
        "data" => []
      );
    }


    echo json_encode($return);



  }






  if ($content == "get_products") {

    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);


    $merchant_name = null;

    if (isset($post['merchant_name'])) {
      $merchant_name = $post['merchant_name'];
    }
    //$user_id = $post['UserID'];
    $user_id = 5;

    $productArr = [];
    $skusArr = [];

    $getDataLazada = $db->getDataLazada($user_id, $merchant_name);

    if ($getDataLazada != null) {

      while ($rowLazada = $getDataLazada->fetch_assoc()) {
        $rows[] = $rowLazada;

      }

      foreach ($rows as $obj) {

        $appkey =  $obj['AppKey'];
        $appSecret =  $obj['AppSecret'];
        $accessToken =  $obj['AccessToken'];
        $merchant_name =  $obj['merchant_name'];


        $c = new LazopClient($url,$appkey,$appSecret);
        $request = new LazopRequest('/products/get','GET');
        $request->addApiParam('filter','all');
        $jdecode=json_decode($c->execute($request, $accessToken));
        $data=$jdecode->data;
        $product=$jdecode->data->products;

        //echo json_encode($jdecode);die;



        foreach($product as $products)
        {



          foreach($products -> skus as $skus)
          {
            //$rowSkus [] = $skus	;
            //$skusArr[$products->item_id]['SkuId'] = $skus['SkuId'];
            //$skusArr[$products->item_id]['Status'] =$skus['Status'];
            //$skusArr[$products->item_id]['SellerSku'] = $skus['SellerSku'];
            //$skusArr[$products->item_id]['ShopSku'] = $skus['ShopSku'];
            //$skusArr[$products->item_id]['color_family'] = $skus['color_family'];
            //$skusArr[$products->item_id]['quantity'] = $skus['quantity'];
            //$skusArr[$products->item_id]['price'] = $skus['price'];
            //$skusArr[$products->item_id]['special_price'] = $skus['special_price'];
            //$skusArr[$products->item_id]['size'] = $skus['size'];
            //$skusArr[$products->item_id]['Url'] = $skus['Url'];

            //}

//$resultSkus = array_values($skusArr);

            $productArr[$products->item_id]['item_id'] = $products->item_id;
            $productArr[$products->item_id]['merchant_name'] = $merchant_name;
            $productArr[$products->item_id]['name'] = $products->attributes->name;
            $productArr[$products->item_id]['skus'][]= $skus;
          }
        }


        $result = array_values($productArr);

      }
      //echo json_encode($dataProducts);die;

      $return= array(
        "status" => 200,
        "message" => "",
        "total_rows" => count($result),
        "data" => $result


      );



    }else{
      $return= array(
        "status" => 404,
        "message" => "Toko lazada tidak ada yang aktif",
        "total_rows" => 0,
        "data" => []


      );


    }




    //
    echo json_encode($return);

  }

  if ($content == "get_product_items") {

    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);


    $merchant_name = null;

    if (isset($post['merchant_name'])) {
      $merchant_name = $post['merchant_name'];
    }



    $item_id = $post['item_id'];


    $seller_sku = null;

    if (isset($post['seller_sku'])) {
      $seller_sku = $post['seller_sku'];
    }

    //$user_id = $post['UserID'];
    $user_id = 5;

    if ($item_id != null) {

      $getDataLazada = $db->getDataLazada($user_id, $merchant_name);

      if ($getDataLazada != null) {

        while ($rowLazada = $getDataLazada->fetch_assoc()) {
          $rows[] = $rowLazada;

        }

        foreach ($rows as $obj) {

          $appkey =  $obj['AppKey'];
          $appSecret =  $obj['AppSecret'];
          $accessToken =  $obj['AccessToken'];
          $merchant_name =  $obj['merchant_name'];


          $c = new LazopClient($url,$appkey,$appSecret);
          $request = new LazopRequest('/product/item/get','GET');
          $request->addApiParam('item_id', $item_id);
          $jdecode=json_decode($c->execute($request, $accessToken));


          //echo json_encode($jdecode);die;


          if ($jdecode->code == "0") {

            $data=$jdecode->data;
            $primary_category=$data->primary_category;
            $attributes=$data->attributes;

            foreach($jdecode->data->skus as $skus)
            {

              foreach($skus->Images as $Images)
              {
                $dataImage = $Images;
              }

              $dataSkus[] = array("Status" => $skus->Status,
                "SellerSku" =>$skus->SellerSku,
                "ShopSku" => $skus->ShopSku,
                "SkuId" => $skus->SkuId,
                "color_family" =>$skus->color_family,
                "Url" => $skus->Url,
                "price" => $skus->price,
                "special_price" => $skus->special_price,
                "quantity" => $skus->quantity,
                "package_width" => $skus->package_width,
                "package_height" => $skus->package_height,
                "package_length" => $skus->package_length,
                "package_weight" => $skus->package_weight,
                "image_skus" => array($dataImage)
              );


              $dataItems = array(

                "merchant_name" => $merchant_name,
                "item_id" => $item_id,
                "name" => $attributes->name,
                "brand" => $attributes->brand,
                //"short_description" => $attributes->short_description,
                //"description" => $attributes->description,
                "primary_category" => $primary_category,
                "skus" => $dataSkus

              );

            }

          }else{


            $return= array(
              "status" => 404,
              "message" => $jdecode->message,
              "total_rows" => 0,
              "data" => []


            );

          }
        }
        //echo json_encode($dataProducts);die;

        $return= array(
          "status" => 200,
          "message" => "Sukses",
          "total_rows" => count($dataSkus),
          "data" => $dataItems


        );



      }else{
        $return= array(
          "status" => 404,
          "message" => "Toko lazada tidak ada yang aktif",
          "total_rows" => 0,
          "data" => []


        );


      }


    }else{
      $return= array(
        "status" => 404,
        "message" => "Item ID tidak boleh kosong",
        "total_rows" =>0,
        "data" => []


      );


    }

    //
    echo json_encode($return);

  }

  if ($content == "get_skus") {

    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);
    $merchant_name = null;

    if (isset($post['merchant_name'])) {
      $merchant_name = $post['merchant_name'];
    }

    $user_id = 5;

    $getDataLazada = $db->getDataLazada($user_id, $merchant_name);

    $addProduct = 0;

    if ($getDataLazada != null) {

      while ($rowLazada = $getDataLazada->fetch_assoc()) {
        $rows[] = $rowLazada;

      }


      foreach ($rows as $obj) {



        $appkey =  $obj['AppKey'];
        $appSecret =  $obj['AppSecret'];
        $accessToken =  $obj['AccessToken'];
        $merchant_name =  $obj['merchant_name'];




        $c = new LazopClient($url,$appkey,$appSecret);
        $request = new LazopRequest('/products/get','GET');
        $jdecode=json_decode($c->execute($request, $accessToken));
        $data=$jdecode->data;
        $product=$jdecode->data->products;




        foreach($data->products  as $products)

        {



          foreach($products->skus  as $skus)

          {
            $sku[]= $skus->SellerSku;




          }
        }


        $dataSkus[] = array(
          $merchant_name =>
            $sku

        );




        $return= array(
          "status" => 200,
          "message" => "",
          "total_rows" => count($sku),
          "data" => $dataSkus


        );


      }


    } else {
      $return = array(
        "status" => 404,
        "message" => "Akun lazada belum diatur"
      );
    }



    //
    echo json_encode($return);

  }

  if ($content == "cek_status") {

    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);


    $user_id = 5;
    $merchant_name = null;

    if (isset($post['merchant_name'])) {
      $merchant_name = $post['merchant_name'];
    }

    $getDataLazada = $db->getDataLazada($user_id, $merchant_name);



    if ($getDataLazada != null) {

      $getDataHistoryOrders = $db->getHistoryOrder($user_id);

      if ($getDataHistoryOrders != null) {

        while ($rowHistory = $getDataHistoryOrders->fetch_assoc()) {
          $rowsHistory[] = $rowHistory['order_id'];

        }


        while ($rowLazada = $getDataLazada->fetch_assoc()) {
          $rows[] = $rowLazada;

        }


        foreach ($rows as $obj) {



          $appkey =  $obj['AppKey'];
          $appSecret =  $obj['AppSecret'];
          $accessToken =  $obj['AccessToken'];
          $merchant_name =  $obj['merchant_name'];



          //echo json_encode($rowsHistory);die;
          $c = new LazopClient($url,$appkey,$appSecret);
          $request = new LazopRequest('/orders/items/get','GET');
          $request->addApiParam('order_ids', json_encode($rowsHistory));
          $jdecode=json_decode($c->execute($request, $accessToken));
          $data=$jdecode->data;
          $order_items=$jdecode->data->order_items;

          //echo json_encode($jdecode);die;

          foreach ($data as $datas) {
            $rowsLazada[] = $datas;
            $order_id= $datas -> order_id;
            $order_number = $datas->order_number;


            foreach ($datas -> order_items as $order_items) {

              $status= $order_items -> status;
              $created_at =  $order_items->created_at;
              $name = $order_items->name;
              $sku =  $order_items->sku;
              $paid_price = $order_items->paid_price;
              $shipment_provider =  $order_items->shipment_provider;
              $shipping_amount =  $order_items->shipping_amount;

              if ($status == "shipped") {

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

                $status = "shipped";
              }else if ($status == "delivered") {

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $base_url . '/api/orders.php?request=set_delivery');
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

                $status = "delivered";

              }



              $dataOrders[]= array(

                "order_id" => $order_id,
                "order_number" => $order_number,
                "msg" => $resultLazada,
                "status" => $status


              );



            }


          }








          $return= array(
            "status" => 200,
            "message" => "",
            "total_rows" => count($dataOrders),
            "data" => $dataOrders


          );


        }
      } else {
        $return = array(
          "status" => 404,
          "message" => "Belum ada History"
        );
      }

    } else {
      $return = array(
        "status" => 404,
        "message" => "Akun lazada belum diatur"
      );
    }

    //
    echo json_encode($return);

  }

  if ($content == "sync_products") {

    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);
    $user_id = 5;



    $getDataProducts = $db->getDataProductsdanVariant($user_id);

    if ($getDataProducts != null) {

      while ($rowProducts = $getDataProducts->fetch_assoc()){

        //$rows[] = $rowProducts;

        $ProductID = $rowProducts['ProductID'];
        $PrimaryCategory = $rowProducts['CategoryID'];
        $name = $rowProducts['ProductName'];
        $short_description =  $rowProducts['Description'];
        $brand =  $rowProducts['BrandName'];

        $color_family = $rowProducts['ProductVariantName'];

        $SellerSku = $rowProducts['SkuID'];
        $size = $rowProducts['ProductVariantDetailName'];
        $quantity = $rowProducts['Stock'];
        $price = $rowProducts['PriceRetail'];

        $package_length = "5";
        $package_height = "5";
        $package_weight = "0.5";
        $package_width = "5";
        $package_content = "Paperbag";

        $getImagesProducts = $db->getImagesProducts($ProductID);


        if ($getImagesProducts != null) {

          while ($rowImages = $getImagesProducts->fetch_assoc()){

            //$images[] = $rowImages['ImageProductName'];

            $images[] = array (
              $rowImages['ImageProductName']

            );

          }}
        //echo json_encode($images);die;



        $dataVariant = array (
          "color_family" => $color_family,
          "SellerSku" => $SellerSku,
          "size" => $size,
          "quantity" => $quantity,
          "price" => $price,
          "package_length" => $package_length,
          "package_height" => $package_height,
          "package_weight" => $package_weight,
          "package_width" => $package_width,
          "package_content" => $package_content
        );



        $rows[$rowProducts['ProductID']]['ProductID'] = $rowProducts['ProductID'];
        $rows[$rowProducts['ProductID']]['PrimaryCategory'] = $rowProducts['CategoryID'];
        $rows[$rowProducts['ProductID']]['name'] = $rowProducts['ProductName'];
        $rows[$rowProducts['ProductID']]['short_description'] = $rowProducts['Description'];
        $rows[$rowProducts['ProductID']]['brand'] = $rowProducts['brand'];
        //$rows[$rowProducts['ProductID']]['images']= $images;
        $rows[$rowProducts['ProductID']]['variant'][] = $dataVariant;


        $result = array_values($rows);



      }
    }



    //$data[] = array (
    //"PrimaryCategory" => $PrimaryCategory,
    //"name" => $name,
    //"short_description" => $short_description,
    //"brand" => $brand,
    //"variant" =>$dataVariant
    //);

    //echo json_encode($result);die;

    $chLazada = curl_init();
    curl_setopt($chLazada, CURLOPT_URL, $base_url . '/api/lazada.php?request=create_product');
    $payloadLazada = json_encode( array(
      "UserID"=> 5 ,
      "Data"=> $result
    ) );
    curl_setopt( $chLazada, CURLOPT_POSTFIELDS, $payloadLazada );
    curl_setopt( $chLazada, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($chLazada, CURLOPT_RETURNTRANSFER, 1);
    $contentLazada = curl_exec($chLazada);
    curl_close($chLazada);

    //mengubah data json menjadi data array asosiatif
    $resultLazada=json_decode($contentLazada,true);





    $return= array(
      "status" => 200,
      "message" => "",
      "data" => $resultLazada


    );








    //
    echo json_encode($return);

  }


  if ($content == "sync_marketplace") {
    $post = json_decode(file_get_contents("php://input"), true);

    $user_id = 5;
    $marketplace = "lAZADA";
    $seller_id ="1000308198";


    //Mencari data marketplace
    $getDataMarketplace = $db->getDataMarketplace($marketplace);

    if ($getDataMarketplace != null) {

      while ($rowMarketplace = $getDataMarketplace->fetch_assoc()) {

        $appkey = $rowMarketplace['app_key'];
        $appSecret = $rowMarketplace['app_secret'];

      }

      //mencari data toko
      $getDataToko= $db->getDataToko($user_id , $seller_id);

      if ($getDataToko != null) {

        while ($rowToko= $getDataToko->fetch_assoc()) {

          $accessToken = $rowToko['access_token'];
          $merchant_name = $rowToko['merchant_name'];
          $marketplace_name = $rowToko['marketplace_name'];

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

        // echo json_encode($resultProducts);die;
        if ($resultProducts->status == 200) {

          foreach($resultProducts->data as $dataProducts) {

            $xml_output = '<?xml version="1.0" encoding="UTF-8" ?>';
            $xml_output .= '<Request>';
            $xml_output .= '<Product>';
            $xml_output .= '<PrimaryCategory>' . $dataProducts->CategoryID . '</PrimaryCategory>';
            $xml_output .= '<Attributes>';
            $xml_output .= '<name>' . $dataProducts->ProductName . '</name>';
            $xml_output .= '<short_description>' . $dataProducts->Description . '</short_description>';
            $xml_output .= '<brand>Wakai</brand>';
//	$xml_output .= '<model>' . $dataProducts->BrandID . '</model>';
            $xml_output .= '</Attributes>';
            $xml_output .= '<Skus>';




            foreach ($dataProducts->skus as $dataSkus) {

              $SkuID = $dataSkus->SkuID;
              $Stock = $dataSkus->Stock;
              $PriceRetail = $dataSkus->PriceRetail;


              $xml_output .= '<Sku>';

              $xml_output .= '<SellerSku>' . $dataSkus->SkuID . '</SellerSku>';
              $xml_output .= '<color_family>' . $dataSkus->ProductVariantName . '</color_family>';
              $xml_output .= '<size>' . $dataSkus->ProductVariantDetailName . '</size>';
              $xml_output .= '<quantity>' . $dataSkus->Stock . '</quantity>';
              $xml_output .= '<price>' . $dataSkus->PriceRetail . '</price>';
              $xml_output .= '<package_length>5</package_length>';
              $xml_output .= '<package_height>5</package_height>';
              $xml_output .= '<package_weight>0.5</package_weight>';
              $xml_output .= '<package_width>5</package_width>';
              // $xml_output .= '<package_content>44</package_content>';
              $xml_output .= '<Images>';

              foreach ($dataSkus->Images as $images) {

                $xml_output .= '<Image>' . $images . '</Image>';
              }



              $xml_output .= '</Images>';

              $xml_output .= '</Sku>';

            }


            $xml_output .= '</Skus>';
            $xml_output .= '</Product>';
            $xml_output .= '</Request>';

            //  echo $xml_output;die;
            $c = new LazopClient($url,$appkey,$appSecret);
            $request = new LazopRequest('/product/create');
           // $request = new LazopRequest('/product/update');
            $request->addApiParam('payload', $xml_output);
            $jdecode=json_decode($c->execute($request, $accessToken));
            //  echo json_encode($jdecode);die;
            $code = $jdecode->code;
            //$message = $jdecode->message;

            if ($code == 0) {

              $status = "Sukses";
              $message = "";

            }else{
              $status = "Gagal";
              //  $message = $jdecode->message;
              $message = "Produk sudah ada";

            }

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
          "message" => "Toko tidak aktif",
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


     // }














    echo json_encode($return);
  }








  if ($content == "create_token") {

    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);

    $code = $_GET['code'];
    $marketplace = "LAZADA";
	

	if (isset($code)){
		
    $getDataLazada = $db->getDataMarketplace($marketplace);

    if ($getDataLazada !=null ) {

      while ($rowLazada = $getDataLazada->fetch_assoc()) {

        $app_key =  $rowLazada['app_key'];
        $appSecret =  $rowLazada['app_secret'];
 

      }

      $c = new LazopClient($url,$app_key,$appSecret);
      $request = new LazopRequest('/auth/token/create','GET');
      $request->addApiParam('code', $code);
      $jdecode=json_decode($c->execute($request));
     // $data=$jdecode->data;
     //echo json_encode($jdecode);die;

      //var_dump($jdecode);die;
      //print_r($jdecode);die;

      $return = array(
        "status" => 200,
        "message" => "ok",
        "data" => $jdecode

      );
	  
	}

    }else{


      $return = array(
        "status" => 404,
        "message" => "ERROR",
        "data" => []

      );

    }



    //
    echo json_encode($return);

  }




  if ($content == "call_back") {

    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);

    $code = $_GET['code'];
    $user_id = $_GET['user_id'];
    if ($code != null && $user_id != null ) {

      //Cek UserID
      $cekUserID = $db->cekUserIDLazada($user_id);
      if ($cekUserID != false) {

        //Jika userid ada maka data diupdate
        $UpdateData = $db->updateCodeLazada($user_id ,$code);

        if($UpdateData != false) {


          $return = array(
            "status" => 200,
            "message" => "Code Berhasil di update",
            "data" => $UpdateData

          );

        }else{

          $return = array(
            "status" => 404,
            "message" => "Gagal Update Code Lazada",
            "data" => []

          );

        }


      }else{


        //Jika tidak maka insert
        //$InsertData = $db->saveCodeLazada($user_id ,$code);

        //if($InsertData != false) {


        //$return = array(
        // "status" => 200,
        //"message" => "Code Berhasil di tambahkan",
        //"data" => $InsertData

        //);

        //}else{

        //$return = array(
        // "status" => 404,
        // "message" => "Gagal Tambah Code Lazada",
        // "data" => []

        // );

        //}


        $return = array(
          "status" => 404,
          "message" => "Anda belum mengatur lazada",
          "data" => []

        );


      }




    }else{


      $return = array(
        "status" => 404,
        "message" => "User ID dan Kode harus diisi",
        "data" => []

      );

    }





    //
    echo json_encode($return);

  }


  if ($content == "create_global_product") {

    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);

    $code = $Get['code'];


    $c = new LazopClient($url,$app_key,$appSecret );
    $request = new LazopRequest('/product/global/create');
    $request->addApiParam('payload',' <?xml version="1.0" encoding="UTF-8"?>

<Request>    
 <Product>      
  <PrimaryCategory>11069</PrimaryCategory>       
  <SPUId/>       
  <AssociatedSku/>       
  <AutoAllocateStock>false</AutoAllocateStock>       
  <Ventures>       
   <Venture>MY</Venture>         
   <Venture>SG</Venture>         
   <Venture>TH</Venture>      
  </Ventures>     
  <Attributes>        
   <name>api create product test sample</name>         
   <short_description>This is a nice product</short_description>         
   <description>This is a nice product description</description>       
   <brand>Remark</brand>         
   <model>asdf</model>         
   <kid_years>Kids (6-10yrs)</kid_years>       
   <package_length>11</package_length>         
   <package_height>22</package_height>         
   <package_weight>1</package_weight>         
   <package_width>44</package_width>         
   <package_content>this is whats in the box</package_content>       
  </Attributes>       
  <Skus>        
   <Sku>          
    <SellerSku>api-create-test1-14</SellerSku>           
    <color_family>Green</color_family>           
    <size>40</size>           
    <quantity>120</quantity>           
    <sg_retail_price>388.50</sg_retail_price>           
    <sg_sales_price>308.50</sg_sales_price>          
    <retail_price>388.50</retail_price>           
    <sales_price>308.50</sales_price>           
    <tax_class>default</tax_class>         
    <Images>            
     <Image>http://imgsrc.baidu.com/imgad/pic/item/37d12f2eb9389b508e646c9b8f35e5dde6116e64.jpg</Image>             
     <Image>http://imgsrc.baidu.com/imgad/pic/item/37d12f2eb9389b508e646c9b8f35e5dde6116e64.jpg</Image>          
    </Images>        
   </Sku>      
  </Skus>    
 </Product>  
</Request>');

    var_dump($c->execute($request, $access_token));



    $return = array(
      "status" => 200,
      "message" => "ok",
      "data" => $request

    );

    //
    echo json_encode($return);

  }


  if ($content == "update_stock") {

    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);

    $user_id = 5;
    $merchant_name = null;

    if (isset($post['merchant_name'])) {
      $merchant_name = $post['merchant_name'];
    }



    $getDataLazada = $db->getDataLazada($user_id, $merchant_name);

    if ($getDataLazada != null) {

      while ($rowLazada = $getDataLazada->fetch_assoc()) {
        $rows[] = $rowLazada;

      }

      foreach ($rows as $obj) {

        $appkey =  $obj['AppKey'];
        $appSecret =  $obj['AppSecret'];
        $accessToken =  $obj['AccessToken'];
        $merchant_name =  $obj['merchant_name'];


//get product lazada

        $chProduct = curl_init($base_url . "/public/api/lazada.php?request=get_products");
       // $payloadProduct = json_encode($convertJson);
       // curl_setopt($chProduct, CURLOPT_POSTFIELDS, $payloadProduct);
        curl_setopt($chProduct, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($chProduct, CURLOPT_RETURNTRANSFER, true);
        $resultProduct = curl_exec($chProduct);
        curl_close($chProduct);
        $jsonDecodeProduct = json_decode($resultProduct);


//echo json_encode($jsonDecodeProduct);die;
        foreach ($jsonDecodeProduct -> data as $objProduct)
        {
          $rowProduct[] = $objProduct;
          $item_id = $objProduct -> item_id;
          $merchant_name = $objProduct -> merchant_name;
          $name = $objProduct -> name;



          //looping skus
          foreach ($objProduct -> skus as $objskus)
          {
            $rowSkus [] = $objskus;
            $SellerSku = $objskus -> SellerSku;
            $SkuId = $objskus -> SkuId;
            $quantity = $objskus -> quantity;


            $chSkus = curl_init($base_url . "/public/api/products.php?request=get_skus");
            $payloadSkus = json_encode( array( "skus"=> $SellerSku) );
            curl_setopt($chSkus, CURLOPT_POSTFIELDS, $payloadSkus);
            curl_setopt($chSkus, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($chSkus, CURLOPT_RETURNTRANSFER, true);
            $resultSkus = curl_exec($chSkus);
            curl_close($chSkus);
            $jsonDecodeSkus = json_decode($resultSkus);

          //  echo json_encode($jsonDecodeSkus);die;
            if ($jsonDecodeSkus-> status == "200") {

              foreach ($jsonDecodeSkus -> data as $objSkus2)
              {

             //   $SkuID = $objSkus2->SkuID;
                $stock = $objSkus2->Stock;
                $PriceRetail = $objSkus2->PriceRetail;
                $PriceReseller = $objSkus2->PriceReseller;

              //  if ($quantity != $stock ) {
                  $xml_output = '<?xml version="1.0" encoding="UTF-8" ?>';
                  $xml_output .= "<Request>\n";
                  $xml_output .= "\t<Product>\n";
                  $xml_output .= "\t<Skus>\n";
                  $xml_output .= "\t<Sku>\n";
                  $xml_output .= "\t\t<ItemId>" . $item_id . "</ItemId>\n";
                  $xml_output .= "\t\t<SkuId>" . $SkuId. "</SkuId>\n";
                  $xml_output .= "\t\t<SellerSku>" . $SellerSku . "</SellerSku>\n";
                  $xml_output .= "\t\t<Price>" . $PriceRetail . "</Price>\n";
                 $xml_output .= "\t\t<SalePrice>" . $PriceReseller . "</SalePrice>\n";
               $xml_output .= "\t\t<SaleStartDate>2020-11-10</SaleStartDate>\n";
                $xml_output .= "\t\t<SaleEndDate>2020-11-13</SaleEndDate>\n";
                  $xml_output .= "\t\t<Quantity>" . $stock . "</Quantity>\n";
                  $xml_output .= "\t</Sku>\n";
                  $xml_output .= "\t</Skus>\n";
                  $xml_output .= "\t</Product>\n";
                  $xml_output .= "</Request>";

                  $c = new LazopClient($url,$appkey,$appSecret);
                  $request = new LazopRequest('/product/price_quantity/update');
                  $request->addApiParam('payload', $xml_output);
                  $jdecode=json_decode($c->execute($request, $accessToken));
                  $code = $jdecode->code;
                  $resultData = json_encode($jdecode , true);

        //   echo json_encode($resultData);die;
                  if ($code == 0) {

                    $status = "Sukses";
                    $message = 'Skus sudah berhasil sync';

                  }else{

                    $status = "Gagal";
                    $message = $jdecode->message;


                  }
               // }else{

                //  $status = "Sukses";
                //  $message = 'Skus tidak perlu di sync';

               // }




              }



            }else {


              $status = "Gagal";
              $message = 'skus tidak ditemukan';

            }




            $dataResult[]= array (
              "item_id" => $item_id,
              "merchant_name" => $merchant_name,
              "name"=>$name,
              "SellerSku"=>$SellerSku,
              "Status"=>$status,
              "Msg"=>$message
            );


          } //end skus







        } //end product



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
