
<?php
error_reporting(error_reporting() & ~E_NOTICE);
include "../config/db_connection.php";
include "../config/config_type.php";
include "../config/lazada/LazopSdk.php";

$rows = array();
$rows2 = array();
$rowsLazada = array();

$current_app_version_code = "1"; //App Version Code
$current_app_version_name = "0.1.0"; //App Version Name

$token_header = ""; //Header Token

$version_code_header = ""; //Header Version Code
$version_name_header = ""; //Header Version Name
$version_name_header = ""; //Header Version Name
$userid_header = "";
$modeHeader = 1;


$url='https://api.lazada.co.id/rest';


//-------------------------------------- API Function Start Here ----------------------------------------//


//Check request content
$content = "";

if (isset($_GET['request'])) {
  $content = $_GET['request'];
}




if (isset($content) && $content != "") {

  //Load Models
  include "../config/model.php";

  $db = new Model_user();



  if ($content == "add_cart") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);
    $user_id = $post['UserID'];
    $sku_id = $post['SkuID'];
    $token_session = $post['TokenSession'];
    //$sku_id = "WK-HTM-AB-S-HTM-39";
    //Get Value For History Order details from post

    if (isset($sku_id)) {
      $getData = $db->checkProductBySKU($sku_id);

      if ($getData != null) {

        while ($row = $getData->fetch_assoc()) {
          $rows[] = $row;
        }


        foreach ($rows as $obj) {

          $product_id =$obj['ProductID'];
          $price =$obj['PriceRetail'];
          $stock =$obj['Stock'];
          $quantity = "1";
          $product_variant_id =$obj['ProductVariantID'];
          $product_variant_detail_id =$obj['ProductVariantDetailID'];



        }


        if ($stock > 0) {

          $createdCart = $db->createCart($token_session,$customer_id, $user_id ,$firstname , $lastname , $grand_total , $item_count);

          //jika produk berhasil
          if ($createdCart != null) {

            ////while ($id = $createdCart->fetch_assoc()) {
            //		$ids[] = $id;

            //}


            $cart_id = $createdCart;
//echo $createdCart;die;
            $created = $db->createCartDetail($cart_id , $user_id, $sku_id ,$product_id , $price , $quantity , $product_variant_id, $product_variant_detail_id);

            if ($created != null) {

              $return = array(
                "status" => 200,
                "total_rows" => 1,
                "message" => "Produk berhasil ditambahkan kekeranjang",
                "data" => $price
              );


            }



            //jika produk gagal
          } else {
            $return = array(
              "status" => 404,
              "message" => "Gagal saat menambahkan ke keranjang"
            );
          }




        }else{

          $return = array(
            "status" => 404,
            "message" => "Stok Kosong"
          );

        }




      } else {
        $return = array(
          "status" => 404,
          "message" => "Produk tidak ada di database"
        );
      }


      //Jika user id tidak ada//
    } else {
      $return = array(
        "status" => 404,
        "message" => "Sku tidak boleh kosong"
      );
    }
    echo json_encode($return);
  }


  if ($content == "add_cart_detail") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);
    $user_id = $post['UserID'];
    $sku_id = $post['SkuID'];
    //$sku_id = "WK-HTM-AB-S-HTM-39";
    //Get Value For History Order details from post

    if (isset($sku_id)) {
      $getData = $db->checkProductBySKU($sku_id);
      if ($getData != null) {
        while ($row = $getData->fetch_assoc()) {
          $rows[] = $row;
        }


        foreach ($rows as $obj) {

          $product_id =$obj['ProductID'];
          $price =$obj['PriceRetail'];
          $stock =$obj['Stock'];
          $quantity = "1";
          $product_variant_id =$obj['ProductVariantID'];
          $product_variant_detail_id =$obj['ProductVariantDetailID'];

          if ($stock > 0) {

            $created = $db->createCartDetail($user_id, $sku_id ,$product_id , $price , $quantity , $product_variant_id, $product_variant_detail_id);

          }
        }



        //jika produk berhasil
        if ($created != null) {

          $return = array(
            "status" => 200,
            "total_rows" => 1,
            "message" => "Berhasil",
            "data" => $price
          );


          //jika produk gagal
        } else {
          $return = array(
            "status" => 404,
            "message" => "Stok Kosong"
          );
        }

      } else {
        $return = array(
          "status" => 404,
          "message" => "Produk tidak ada di database"
        );
      }


      //Jika user id tidak ada//
    } else {
      $return = array(
        "status" => 404,
        "message" => "Sku tidak boleh kosong"
      );
    }
    echo json_encode($return);
  }

  if ($content == "get_cart_details") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
    $user_id = 5;

    $page = 1;

    $limit = 0;

    if (isset($post['UserID'])) {
      $user_id = $post['UserID'];
    }


    if (isset($post['Page'])) {
      $page = $post['Page'];
    }
    $user_id = 5;

    $page = 1;




    $getData = $db->getDataCartDetail($user_id, $page, $limit);
    if ($getData != null) {

      while ($row = $getData->fetch_assoc()) {

        $rows[] = $row;
        $sub_total[] = $row['SubTotal'];

      }



      $total = mysqli_num_rows($getData);

      $sub_total = array_sum($sub_total);

      $return = array(
        "status" => 200,
        "message" => "ok",
        "total_rows" => $total,
        "sub_total" => $sub_total,
        "data" => $rows
      );
    } else {
      $return = array(
        "status" => 200,
        "total_rows" => 0,
        "message" => "Belum ada Produk",
        "data" => []
      );
    }


    echo json_encode($return);
  }

  if ($content == "delete_cart_details") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
    $user_id = 5;
    $CartDetailID = $post['CartDetailID'];



    $getData = $db->deleteCartDetailByUserID($user_id, $CartDetailID);
    if ($getData) {




      $return = array(
        "status" => 200,
        "message" => "Berhasil menghapus item",
        "total_rows" => $total,
        "data" => $rows
      );
    } else {
      $return = array(
        "status" => 200,
        "total_rows" => 0,
        "message" => "ERROR",
        "data" => []
      );
    }


    echo json_encode($return);
  }



  if ($content == "get_orders") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);

    $user_id = 5;
    $status = $post['status'];
    $marketplace = $post['marketplace'];
    $merchant_name = null;


    if (isset($post['merchant_name'])) {
      $merchant_name = $post['merchant_name'];
    }

    $orderArr = array();
    $page = null;
    $limit = 0;

    if (isset($post['UserID'])) {
      $user_id = $post['UserID'];
    }

    if (isset($post['Page'])) {
      $page = $post['Page'];
    }

    if (isset($post['status_id'])) {
      $status_id = $post['status_id'];
    }
    //   $status_id = 4;

    
    if ($status == 1) {

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $base_url . '/public/api/lazada/get_orders');
      $payloadLazada = json_encode(array("user_id" => $user_id,
        "merchant_name" => $merchant_name,
        "status" => $status));
      curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadLazada);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $lazadacontent = curl_exec($ch);
      curl_close($ch);
      $resultLazada = json_decode($lazadacontent);
      $dataLazada = $resultLazada->data;

     //echo json_encode($dataLazada);die;

      $chShopee = curl_init();
      curl_setopt($chShopee, CURLOPT_URL, $base_url . '/public/api/shopee/get_orders');
      $payloadShopee = json_encode(array("user_id" => $user_id,
        "merchant_name" => $merchant_name,
        "status" => $status));
      curl_setopt($chShopee, CURLOPT_POSTFIELDS, $payloadShopee);
      curl_setopt($chShopee, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
      curl_setopt($chShopee, CURLOPT_RETURNTRANSFER, 1);
      $shopeeContent = curl_exec($chShopee);
      curl_close($chShopee);

      $resultShopee = json_decode($shopeeContent);
      $dataShopee = $resultShopee->data;

      // echo json_encode($dataShopee);die;



      if ($resultLazada->status == 200 && $resultShopee->status == 200) {




        $r = [];
        $r = array_merge($dataLazada, $dataShopee);


        $return = array(
          "status" => 200,
          "message" => "ok bos",
          "data" => $r
        );

      } else if ($getData != null) {


        while ($row = $getData->fetch_assoc()) {

          $rows[] = $row;


        }


        $result = json_decode($content, true);
        //get new orders lazada


        $return = array(
          "status" => 200,
          "message" => "ok",
          "data" => $rows
        );


      } else if (($resultLazada != null)) {


        $return = array(
          "status" => 200,
          "message" => "ok lazada",
          "data" => $dataLazada
        );


      } else if (($resultShopee != null)) {


        $return = array(
          "status" => 200,
          "message" => "ok Shopee",
          "data" => $dataShopee
        );

      } else {

        $return = array(
          "status" => 404,
          "message" => "Belum ada Data",
          "data" => []
        );
      }

    }else{
      //call database
      $getData = $db->getDataOrders($user_id, $page, $limit, $status);

      if ($getData != null) {

        while ($row = $getData->fetch_assoc()) {

          $rows[] = $row;

          $order_id = $row['order_id'];
          $order_number = $row['order_number'];
          $branch_number = $row['branch_number'];
          $warehouse_code = $row['warehouse_code'];
          $marketplace = $row['marketplace'];
          $merchant_name = $row['merchant_name'];
          $customer_first_name = $row['customer_first_name'];
          $customer_last_name = $row['customer_last_name'];
          $price = $row['price'];
          $items_count = $row['items_count'];
          $payment_method = $row['payment_method'];
          $voucher = $row['voucher'];
          $voucher_code = $row['voucher_code'];
          $voucher_platform = $row['voucher_platform'];
          $voucher_seller = $row['voucher_seller'];
          $gift_option = $row['gift_option'];
          $gift_message = $row['gift_message'];
          $shipping_fee = $row['shipping_fee'];
          $shipping_fee_original = $row['shipping_fee_original'];
          $shipping_fee_discount_seller = $row['shipping_fee_discount_seller'];
          $shipping_fee_discount_platform = $row['shipping_fee_discount_platform'];
          $promised_shipping_times = $row['promised_shipping_times'];
          $national_registration_number = $row['national_registration_number'];
          $tax_code = $row['tax_code'];
          $extra_attributes = $row['extra_attributes'];
          $remarks = $row['remarks'];
          $delivery_info = $row['delivery_info'];
          $statuses = $row['statuses'];
          $created_at = $row['created_at'];
          $updated_at = $row['updated_at'];
          $address_billing = $row['address_billing'];


          $getDataAddressShipping = $db->getDataAddressShipping($order_id);

          if ($getDataAddressShipping != null) {


            while ($rowAddressShipping = $getDataAddressShipping->fetch_assoc()) {

              $first_name = $rowAddressShipping['first_name'];
              $last_name = $rowAddressShipping['last_name'];
              $country = $rowAddressShipping['country'];
              $phone = $rowAddressShipping['phone'];
              $phone2 = $rowAddressShipping['phone2'];
              $address1 = $rowAddressShipping['address1'];
              $address2 = $rowAddressShipping['address2'];
              $address3 = $rowAddressShipping['address3'];
              $address4 = $rowAddressShipping['address4'];
              $address5 = $rowAddressShipping['address5'];
              $city = $rowAddressShipping['city'];
              $post_code = $rowAddressShipping['post_code'];

            }

          } else {

            $first_name = "";
            $last_name = "";
            $country = "";
            $phone = "";
            $phone2 = "";
            $address1 = "";
            $address2 = "";
            $address3 = "";
            $address4 = "";
            $address5 = "";
            $city = "";
            $post_code = "";
          }

        

          $getDataOrderItems = $db->getDataOrderItems($user_id, $page, $limit, $order_id);

         
          if ($getDataOrderItems != null) {


            while ($rowOrderItems = $getDataOrderItems->fetch_assoc()) {
             // echo json_encode($rowOrderItems);die;

              $order_item_id = $rowOrderItems['order_item_id'];
              $order_id = $rowOrderItems['order_id'];
              $purchase_order_id = $rowOrderItems['purchase_order_id'];
              $purchase_order_number = $rowOrderItems['purchase_order_number'];
              $invoice_number = $rowOrderItems['purchase_order_number'];
              $sla_time_stamp = $rowOrderItems['sla_time_stamp'];
              $package_id = $rowOrderItems['package_id'];
              $shop_id = $rowOrderItems['shop_id'];
              $order_type = $rowOrderItems['order_type'];
              $shop_sku = $rowOrderItems['shop_sku'];
              $sku = $rowOrderItems['sku'];
              $name = $rowOrderItems['name'];
              $variation = $rowOrderItems['variation'];
              $item_price = $rowOrderItems['item_price'];
              $paid_price = $rowOrderItems['paid_price'];
              $qty = $rowOrderItems['qty'];
              $currency = $rowOrderItems['currency'];
              $tax_amount = $rowOrderItems['tax_amount'];
              $product_detail_url = $rowOrderItems['product_detail_url'];
              $shipment_provider = $rowOrderItems['shipment_provider'];
              $tracking_code_pre = $rowOrderItems['tracking_code_pre'];
              $tracking_code = $rowOrderItems['tracking_code'];
              $shipping_type = $rowOrderItems['shipping_type'];
              $shipping_provider_type = $rowOrderItems['shipping_provider_type'];
              $shipping_fee_original = $rowOrderItems['shipping_fee_original'];
              $shipping_service_cost = $rowOrderItems['shipping_service_cost'];
              // $shipping_fee_discount_seller= $rowOrderItems['shipping_fee_discount_seller'];
              $shipping_amount = $rowOrderItems['shipping_amount'];
              $is_digital = $rowOrderItems['is_digital'];
              $voucher_amount = $rowOrderItems['voucher_amount'];
              $voucher_seller = $rowOrderItems['voucher_seller'];
              $voucher_code_seller = $rowOrderItems['voucher_code_seller'];
              $voucher_code = $rowOrderItems['voucher_code'];
              $voucher_code_platform = $rowOrderItems['voucher_code_platform'];
              $voucher_platform = $rowOrderItems['voucher_platform'];
              $order_flag = $rowOrderItems['order_flag'];
              $promised_shipping_time = $rowOrderItems['promised_shipping_time'];
              $digital_delivery_info = $rowOrderItems['digital_delivery_info'];
              $extra_attributes = $rowOrderItems['extra_attributes'];
              $cancel_return_initiator = $rowOrderItems['cancel_return_initiator'];
              $reason = $rowOrderItems['reason'];
              $reason_detail = $rowOrderItems['reason_detail'];
              $stage_pay_status = $rowOrderItems['stage_pay_status'];
              $warehouse_code = $rowOrderItems['warehouse_code'];
              $return_status = $rowOrderItems['return_status'];
              $imageImageVariant = $rowOrderItems['product_main_image'];
             

           // }

            

            $orderArr[$order_id]['order_id'] = $order_id;
            $orderArr[$order_id]['order_number'] = $order_number;
            $orderArr[$order_id]['user_id'] = $user_id;
            $orderArr[$order_id]['marketplace'] = $marketplace;
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
            $orderArr[$order_id]['order_items'][] = array("order_item_id" => $order_item_id,
              "order_id" => $order_id,
              "purchase_order_id" => $purchase_order_id,
              "purchase_order_number" => $purchase_order_number,
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


            $orderArr[$order_id]['address_shipping'] = array("order_id" => $order_id,
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

            $orderArr[$order_id]['address_billing'] = array("order_id" => $order_id,
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

          }

        $result = array_values($orderArr);
          

          $return = array(
            "status" => 200,
            "message" => "ok database",
            "total_rows" => COUNT($result),
            "data" => $result

          );

        }

      }else{

        $return = array(
          "status" => 404,
          "message" => "Belum ada Data",
          "data" => []
        );

      }

    }

    echo json_encode($return);
  }





  if ($content == "get_order") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);

    $user_id = 5;
    $status = $post['status'];
    $marketplace = $post['marketplace'];
    $merchant_name = null;
    $order_id = $post['order_id'];
    if (isset($post['merchant_name'])) {
      $merchant_name = $post['merchant_name'];
    }

    $page = null;
    $limit = 0;

    if (isset($post['UserID'])) {
      $user_id = $post['UserID'];
    }

    if (isset($post['Page'])) {
      $page = $post['Page'];
    }


    //   $status_id = 4;
    if (isset($post['order_id'])) {


    if ($status == 1) {

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $base_url . '/public/api/lazada/get_order');
      $payloadLazada = json_encode(array("user_id" => $user_id,
        "order_id" => $order_id,
        "merchant_name" => $merchant_name,
        "status" => $status));
      curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadLazada);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $lazadacontent = curl_exec($ch);
      curl_close($ch);
      $resultLazada = json_decode($lazadacontent);
      $dataLazada = $resultLazada->data;

//echo json_encode($resultLazada);die;

      $chShopee = curl_init();
      curl_setopt($chShopee, CURLOPT_URL, $base_url . '/public/api/shopee/get_order');
      $payloadShopee = json_encode(array("user_id" => $user_id,
        "merchant_name" => $merchant_name,
        "status" => $status));
      curl_setopt($chShopee, CURLOPT_POSTFIELDS, $payloadShopee);
      curl_setopt($chShopee, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
      curl_setopt($chShopee, CURLOPT_RETURNTRANSFER, 1);
      $shopeeContent = curl_exec($chShopee);
      curl_close($chShopee);

      $resultShopee = json_decode($shopeeContent);
      $dataShopee = $resultShopee->data;

      //  echo json_encode($dataShopee);die;



      if ($resultLazada->status == 200 && $resultShopee->status == 200) {


        $r = [];
        $r = array_merge($dataLazada, $dataShopee);


        $return = array(
          "status" => 200,
          "message" => "ok bos",
          "data" => $r
        );

      } else if ($getData != null) {


        while ($row = $getData->fetch_assoc()) {

          $rows[] = $row;


        }


        $result = json_decode($content, true);
        //get new orders lazada


        $return = array(
          "status" => 200,
          "message" => "ok",
          "data" => $rows
        );


      } else if ($resultLazada->status == 200) {


        $return = array(
          "status" => 200,
          "message" => "ok lazadas",
          "data" => $dataLazada
        );


      } else if ($resultShopee->status == 200) {


        $return = array(
          "status" => 200,
          "message" => "ok Shopee",
          "data" => $dataShopee
        );

      } else {

        $return = array(
          "status" => 404,
          "message" => "Belum ada Dataa",
          "data" => []
        );
      }

    }else{
      //call database
      $getData = $db->getDataOrder($user_id, $page, $limit, $status);

      if ($getData != null) {

        while ($row = $getData->fetch_assoc()) {

          //$rows[] = $row;

          $order_id = $row['order_id'];
          $order_number = $row['order_number'];
          $branch_number = $row['branch_number'];
          $warehouse_code = $row['warehouse_code'];
          $marketplace = $row['marketplace'];
          $merchant_names = $row['merchant_name'];
          $customer_first_name = $row['customer_first_name'];
          $customer_last_name = $row['customer_last_name'];
          $price = $row['price'];
          $items_count = $row['items_count'];
          $payment_method = $row['payment_method'];
          $voucher = $row['voucher'];
          $voucher_code = $row['voucher_code'];
          $voucher_platform = $row['voucher_platform'];
          $voucher_seller = $row['voucher_seller'];
          $gift_option = $row['gift_option'];
          $gift_message = $row['gift_message'];
          $shipping_fee = $row['shipping_fee'];
          $shipping_fee_original = $row['shipping_fee_original'];
          $shipping_fee_discount_seller = $row['shipping_fee_discount_seller'];
          $shipping_fee_discount_platform = $row['shipping_fee_discount_platform'];
          $promised_shipping_times = $row['promised_shipping_times'];
          $national_registration_number = $row['national_registration_number'];
          $tax_code = $row['tax_code'];
          $extra_attributes = $row['extra_attributes'];
          $remarks = $row['remarks'];
          $delivery_info = $row['delivery_info'];
          $statuses = $row['statuses'];
          $created_at = $row['created_at'];
          $updated_at = $row['updated_at'];
          $address_billing = $row['address_billing'];

          //echo json_encode($merchant_names);die;
          $getDataAddressShipping = $db->getDataAddressShipping($order_id);

          if ($getDataAddressShipping != null) {


            while ($rowAddressShipping = $getDataAddressShipping->fetch_assoc()) {

              $first_name = $rowAddressShipping['first_name'];
              $last_name = $rowAddressShipping['last_name'];
              $country = $rowAddressShipping['country'];
              $phone = $rowAddressShipping['phone'];
              $phone2 = $rowAddressShipping['phone2'];
              $address1 = $rowAddressShipping['address1'];
              $address2 = $rowAddressShipping['address2'];
              $address3 = $rowAddressShipping['address3'];
              $address4 = $rowAddressShipping['address4'];
              $address5 = $rowAddressShipping['address5'];
              $city = $rowAddressShipping['city'];
              $post_code = $rowAddressShipping['post_code'];

            }

          } else {

            $first_name = "";
            $last_name = "";
            $country = "";
            $phone = "";
            $phone2 = "";
            $address1 = "";
            $address2 = "";
            $address3 = "";
            $address4 = "";
            $address5 = "";
            $city = "";
            $post_code = "";
          }

          $getDataOrderItems = $db->getDataOrderItem($user_id, $page, $limit, $order_id);


          if ($getDataOrderItems != null) {


            while ($rowOrderItems = $getDataOrderItems->fetch_assoc()) {

              $order_item_id = $rowOrderItems['order_item_id'];
              $order_id = $rowOrderItems['order_id'];
              $purchase_order_id = $rowOrderItems['purchase_order_id'];
              $purchase_order_number = $rowOrderItems['purchase_order_number'];
              $invoice_number = $rowOrderItems['purchase_order_number'];
              $sla_time_stamp = $rowOrderItems['sla_time_stamp'];
              $package_id = $rowOrderItems['package_id'];
              $shop_id = $rowOrderItems['shop_id'];
              $order_type = $rowOrderItems['order_type'];
              $shop_sku = $rowOrderItems['shop_sku'];
              $sku = $rowOrderItems['sku'];
              $name = $rowOrderItems['name'];
              $variation = $rowOrderItems['variation'];
              $item_price = $rowOrderItems['item_price'];
              $paid_price = $rowOrderItems['paid_price'];
              $qty = 1;
              $currency = $rowOrderItems['currency'];
              $tax_amount = $rowOrderItems['tax_amount'];
              $product_detail_url = $rowOrderItems['product_detail_url'];
              $shipment_provider = $rowOrderItems['shipment_provider'];
              $tracking_code_pre = $rowOrderItems['tracking_code_pre'];
              $tracking_code = $rowOrderItems['tracking_code'];
              $shipping_type = $rowOrderItems['shipping_type'];
              $shipping_provider_type = $rowOrderItems['shipping_provider_type'];
              $shipping_fee_original = $rowOrderItems['shipping_fee_original'];
              $shipping_service_cost = $rowOrderItems['shipping_service_cost'];
              // $shipping_fee_discount_seller= $rowOrderItems['shipping_fee_discount_seller'];
              $shipping_amount = $rowOrderItems['shipping_amount'];
              $is_digital = $rowOrderItems['is_digital'];
              $voucher_amount = $rowOrderItems['voucher_amount'];
              $voucher_seller = $rowOrderItems['voucher_seller'];
              $voucher_code_seller = $rowOrderItems['voucher_code_seller'];
              $voucher_code = $rowOrderItems['voucher_code'];
              $voucher_code_platform = $rowOrderItems['voucher_code_platform'];
              $voucher_platform = $rowOrderItems['voucher_platform'];
              $order_flag = $rowOrderItems['order_flag'];
              $promised_shipping_time = $rowOrderItems['promised_shipping_time'];
              $digital_delivery_info = $rowOrderItems['digital_delivery_info'];
              $extra_attributes = $rowOrderItems['extra_attributes'];
              $cancel_return_initiator = $rowOrderItems['cancel_return_initiator'];
              $reason = $rowOrderItems['reason'];
              $reason_detail = $rowOrderItems['reason_detail'];
              $stage_pay_status = $rowOrderItems['stage_pay_status'];
              $warehouse_code = $rowOrderItems['warehouse_code'];
              $return_status = $rowOrderItems['return_status'];
              $imageImageVariant = $rowOrderItems['product_main_image'];

            }


            $orderArr[$order_id]['order_id'] = $order_id;
            $orderArr[$order_id]['order_number'] = $order_number;
            $orderArr[$order_id]['user_id'] = $user_id;
            $orderArr[$order_id]['marketplace'] = $marketplace;
            $orderArr[$order_id]['merchant_name'] = $merchant_names;
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
            $orderArr[$order_id]['order_items'][] = array("order_item_id" => $order_item_id,
              "order_id" => $order_id,
              "purchase_order_id" => $purchase_order_id,
              "purchase_order_number" => $purchase_order_number,
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


            $orderArr[$order_id]['address_shipping'] = array("order_id" => $order_id,
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

            $orderArr[$order_id]['address_billing'] = array("order_id" => $order_id,
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
          // echo json_encode($rows);die;

          $return = array(
            "status" => 200,
            "message" => "ok database",
            "total_rows" => COUNT($result),
            "data" => $result

          );

        }

      }else{

        $return = array(
          "status" => 404,
          "message" => "Belum ada Data",
          "data" => []
        );

      }

    }

    }else{

      $return = array(
        "status" => 404,
        "message" => "ERROR",
        "data" => []
      );

    }

    echo json_encode($return);
  }

if ($content == "set_rts") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);

    
    $user_id = 5;
	$shipping_provider = $post['shipping_provider'] ;
	$delivery_type = $post['delivery_type'] ;
	$tracking_number = $post['tracking_number'] ;
  $order_id = $post['order_id'] ;

    if (isset($tracking_number) || isset($order_id)) {

	
		
		 


	
	     //jika produk berhasil
//if ($setRts) {
		
if ($marketplace == "LAZADA"){
	
	//cek order id and user id
      $getData = $db->getDetailHistoryOrderByTracking($tracking_number);

      if ($getData != false) {

        while ($row = $getData->fetch_assoc()) {

          $rows = $row;
          $order_item_ids[]  = 	(int)$row['order_item_id'];
		  $order_id  = 	$row['order_id'];

        }
		
	  }
	   $setRts = $db->setRts($user_id, $order_id);
	   
		 $chrts = curl_init();
           curl_setopt($chrts, CURLOPT_URL, $base_url . '/public/api/lazada.php?request=set_rts');
            $payloadRts = json_encode( array( "order_item_ids"=> $order_item_ids,
              "shipping_provider"=> $shipping_provider,
              "delivery_type"=> $delivery_type,
              "tracking_number"=> $tracking_number,
              "user_id"=> $user_id) );
			  
			//echo $payloadRts;die;
			
            curl_setopt( $chrts, CURLOPT_POSTFIELDS, $payloadRts );
            curl_setopt( $chrts, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($chrts, CURLOPT_RETURNTRANSFER, 1);
            $contentRts = curl_exec($chrts);
            curl_close($chrts);


            //mengubah data json menjadi data array asosiatif
           $resultRts=json_decode($contentRts);
		   
		   if ($resultRts->status == "200") {
			   
			   
            $return = array(
          "status" => 200,
          "message" => "Pesanan berhasil dirubah, silahkan kirim pesanan anda ke jasa pengiriman yang ditentukan"
			);
			
		   }else{
			   
			 
			 
			$return = array(
          "status" => 404,
          "message" => $resultRts->message
        );   
		   }
	
		   
		   
	
}else{
	
	$setRts = $db->setRts($user_id, $order_id);
	
     $return = array(
          "status" => 200,
          "message" => "Pesanan berhasil dirubah, silahkan kirim pesanan anda ke jasa pengiriman yang ditentukan"
			);
	
}
     
			
     
	 
			
			
           // }else {

	//	$return = array(
      //    "status" => 404,
       //   "message" => "Gagal"
      //  );
		
        //   }
		

	  

        

            //jika produk gagal
         

        





      


      //Jika user id tidak ada//
    } else {
      $return = array(
        "status" => 504,
        "message" => "ERROR"
      );
    }
    echo json_encode($return);
  }
  
  if ($content == "set_ship") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);

    $order_id = $post['order_id'] ;
    $user_id = 5;
    //$order_id = "2008046PJSNM2B" ;
    $created_at = $post['created_at'] ;
    $name = $post['name'] ;
    $sku = $post['sku'] ;
    $paid_price = $post['paid_price'] ;
    $shipment_provider = $post['shipment_provider'] ;
    $shipping_amount = $post['shipping_amount'] ;


    if (isset($order_id) && isset($user_id)) {



      //cek order id and user id
      $getData = $db->checkHistoryOrderByOrder($order_id , $user_id);


      if ($getData != false) {

        while ($row = $getData->fetch_assoc()) {

          $rows = $row;
          $status = 	$row['statuses'];

        }

        if ($status == 3) {

          $return = array(
            "status" => 200,
            "message" => "Pesanan masih diperjalanan"
          );

        }else{

          $create = $db->setShip($user_id, $order_id);

          //jika produk berhasil
          if ($create) {

            $to = "twinzahrashop@gmail.com";
            $subject = "Pesanan Order ID ".$order_id." dalam perjalanan";

            $message = "<b>Hai Admin</b>";
            $message = "<p>Pesanan kamu dalam perjalanan</p>
				<b>Rincian Pesanan</b>
				 <p>No Pesanan : ".$order_id." </p>
				  <p>Tanggal Pesanan : ".$created_at." </p>
				  <p>Nama Produk : ".$name." </p>
				   <p>Sku : ".$sku." </p>
				   <p>Paid Price : ".$paid_price." </p>
				  <p>Jasa Pengiriman : ".$shipment_provider." </p>
				   <p>Ongkos Kirim: ".$shipping_amount." </p>";
            $header = "From:no_replay@twinzahra.com \r\n";
            $header .= "Cc:no_replay@twinzahra.com \r\n";
            $header .= "MIME-Version: 1.0\r\n";
            $header .= "Content-type: text/html\r\n";

            $retval = mail ($to,$subject,$message,$header);

            if( $retval == true ) {

              $return = array(
                "status" => 200,
                "message" => "Pesanan telah dirubah menjadi Dalam Perjalan"
              );
            }else {



              $return = array(
                "status" => 404,
                "message" => "Gagal mengirim Email"
              );
            }

            //jika produk gagal
          } else {

            $return = array(
              "status" => 404,
              "message" => "error"
            );

          }

        }





      } else {
        $return = array(
          "status" => 404,
          "message" => "No Order tidak ditemukan"
        );
      }


      //Jika user id tidak ada//
    } else {
      $return = array(
        "status" => 504,
        "message" => "ERROR"
      );
    }
    echo json_encode($return);
  }

  if ($content == "set_delivery") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);


    $order_id = $post['order_id'] ;
  

      //cek order id and user id
      $getData = $db->checkHistoryOrderByOrder($order_id);


      if ($getData != false) {

        while ($row = $getData->fetch_assoc()) {

          $rows = $row;
          $status = 	$row['statuses'];

        }



          $create = $db->setDelivery($order_id);

          //jika produk berhasil
          if ($create) {
           
            $return = array(
              "status" => 200,
              "order_id" => $order_id,
              "message" => "Berhasil"
            );


            //jika produk gagal
          } else {
            $return = array(
              "status" => 404,
              "order_id" => $order_id,
              "message" => "ERROR"
            );
          }

        


      } else {
        $return = array(
          "status" => 404,
          "message" => "No Order tidak ditemukan"
        );
      }

    echo json_encode($return);
  }

  if ($content == "set_cancel") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);


    $order_id = $post['order_id'] ;
  

      //cek order id and user id
      $getData = $db->checkHistoryOrderByOrder($order_id);


      if ($getData != false) {

        while ($row = $getData->fetch_assoc()) {

          $rows = $row;
          $status = 	$row['statuses'];

        }



          $create = $db->setCancel($order_id);

          //jika produk berhasil
          if ($create) {
           
            $return = array(
              "status" => 200,
              "order_id" => $order_id,
              "message" => "Berhasil"
            );


            //jika produk gagal
          } else {
            $return = array(
              "status" => 404,
              "order_id" => $order_id,
              "message" => "ERROR"
            );
          }

        


      } else {
        $return = array(
          "status" => 404,
          "message" => "No Order tidak ditemukan"
        );
      }

    echo json_encode($return);
  }

  if ($content == "set_filed") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);


    $order_id = $post['order_id'] ;
  

      //cek order id and user id
      $getData = $db->checkHistoryOrderByOrder($order_id);


      if ($getData != false) {

        while ($row = $getData->fetch_assoc()) {

          $rows = $row;
          $status = 	$row['statuses'];

        }



          $create = $db->setFiled($order_id);

          //jika produk berhasil
          if ($create) {
           
            $return = array(
              "status" => 200,
              "order_id" => $order_id,
              "message" => "Berhasil"
            );


            //jika produk gagal
          } else {
            $return = array(
              "status" => 404,
              "order_id" => $order_id,
              "message" => "ERROR"
            );
          }

        


      } else {
        $return = array(
          "status" => 404,
          "message" => "No Order tidak ditemukan"
        );
      }

    echo json_encode($return);
  }


  if ($content == "set_unpaid") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);


    $order_id = $post['order_id'] ;
  

      //cek order id and user id
      $getData = $db->checkHistoryOrderByOrder($order_id);


      if ($getData != false) {

        while ($row = $getData->fetch_assoc()) {

          $rows = $row;
          $status = 	$row['statuses'];

        }



          $create = $db->setUnpaid($order_id);

          //jika produk berhasil
          if ($create) {
           
            $return = array(
              "status" => 200,
              "order_id" => $order_id,
              "message" => "Berhasil"
            );


            //jika produk gagal
          } else {
            $return = array(
              "status" => 404,
              "order_id" => $order_id,
              "message" => "ERROR"
            );
          }

        


      } else {
        $return = array(
          "status" => 404,
          "message" => "No Order tidak ditemukan"
        );
      }

    echo json_encode($return);
  }

  if ($content == "set_proses") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);


    $order_id = $post['order_id'] ;
  

      //cek order id and user id
      $getData = $db->checkHistoryOrderByOrder($order_id);


      if ($getData != false) {

        while ($row = $getData->fetch_assoc()) {

          $rows = $row;
          $status = 	$row['statuses'];

        }



          $create = $db->setProses($order_id);

          //jika produk berhasil
          if ($create) {
           
            $return = array(
              "status" => 200,
              "order_id" => $order_id,
              "message" => "Berhasil"
            );


            //jika produk gagal
          } else {
            $return = array(
              "status" => 404,
              "order_id" => $order_id,
              "message" => "ERROR"
            );
          }

        


      } else {
        $return = array(
          "status" => 404,
          "message" => "No Order tidak ditemukan"
        );
      }

    echo json_encode($return);
  }



  if ($content == "set_return") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);


    $order_id = $post['order_id'] ;
  

      //cek order id and user id
      $getData = $db->checkHistoryOrderByOrder($order_id);


      if ($getData != false) {

        while ($row = $getData->fetch_assoc()) {

          $rows = $row;
          $status = 	$row['statuses'];

        }



          $create = $db->setReturn($order_id);

          //jika produk berhasil
          if ($create) {
           
            $return = array(
              "status" => 200,
              "order_id" => $order_id,
              "message" => "Berhasil"
            );


            //jika produk gagal
          } else {
            $return = array(
              "status" => 404,
              "order_id" => $order_id,
              "message" => "ERROR"
            );
          }

        


      } else {
        $return = array(
          "status" => 404,
          "message" => "No Order tidak ditemukan"
        );
      }

    echo json_encode($return);
  }

  if ($content == "get_order_items") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
    $user_id = 5;
    $order_id =$post['order_id'];
    //$order_id =481702361920270;
    $page = null;

    $limit = 0;
    $marketplace = null;

    if (isset($post['marketplace'])) {
      $marketplace = $post['marketplace'];
    }

    $merchant_name = null;

    if (isset($post['merchant_name'])) {
      $merchant_name = $post['merchant_name'];
    }

    if (isset($post['UserID'])) {
      $user_id = $post['UserID'];
    }


    if (isset($post['Page'])) {
      $page = $post['Page'];
    }

    if (isset($order_id) && isset($user_id) && isset($merchant_name)) {
      //Get data from database


      if ($marketplace == "LAZADA") {

        //Get data from lazada
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $base_url . '/public/api/lazada.php?request=get_order_items');
        $payload = json_encode(array("order_id" => $order_id,
          "UserID" => "5",
          "merchant_name" => $merchant_name));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $lazadacontent = curl_exec($ch);
        curl_close($ch);
        $rowLazada = json_decode($lazadacontent, true);

        //echo json_encode( $rowLazada);die;

        $total = count($rowLazada);


        $return = array(
          "status" => 200,
          "total_rows" => $total,
          "message" => "ok lazada",
          "data" => $rowLazada
        );

      } else if ($marketplace == "SHOPEE") {

        //Get data from shopee

        $chShopee = curl_init($base_url . "/public/api/shopee.php?request=get_order_items");
        $payloadShopee = json_encode(array("ordersn_list" => array($order_id),
          "UserID" => "5",
          "merchant_name" => $merchant_name));
        curl_setopt($chShopee, CURLOPT_POSTFIELDS, $payloadShopee);
        curl_setopt($chShopee, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($chShopee, CURLOPT_RETURNTRANSFER, true);
        $resultShopee = curl_exec($chShopee);
        curl_close($chShopee);
        $rowShopee = json_decode($resultShopee, true);

        $total = count($rowShopee);

        $return = array(
          "status" => 200,
          "total_rows" => $total,
          "message" => "ok shopee",
          "data" => $rowShopee
        );


      } else {
        $getData = $db->getDataOrderItems($user_id, $page, $limit, $order_id);

        if ($getData != null) {

          while ($row = $getData->fetch_assoc()) {
            $rows[] = $row;
          }

          $total = mysqli_num_rows($getData);


          $return = array(
            "status" => 200,
            "total_rows" => $total,
            "message" => "ok database",
            "data" => $rows
          );


        } else {
          $return = array(
            "status" => 404,
            "total_rows" => 0,
            "message" => "Belum ada Data",
            "data" => []
          );
        }


      }
    }else{

      $return = array(
        "status" => 404,
        "total_rows" => 0,
        "message" => "Data tidak lengkap",
        "data" => []
      );
    }

    echo json_encode($return);
  }

  if ($content == "cek_history") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);

    $order_id = $post['order_id'];

    //$order_id = $post['order_id'];



    $getData = $db->getDataHistory($order_id);
    if ($getData != null) {

      while ($row = $getData->fetch_assoc()) {

        $rows = $row;


      }



      $total = mysqli_num_rows($getData);


      $return = array(
        "status" => 200,
        "message" => "Berhasil Ditemukan",
        "total_rows" => $total,
        "data" => $rows
      );
    } else {
      $return = array(
        "status" => 404,
        "total_rows" => 0,
        "message" => "Data produk tidak ditemukan",
        "data" => null
      );
    }


    echo json_encode($return);
  }

  if ($content == "get_rts") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
    $user_id = 5;
    $status_id = 2;

    $page = null;

    $limit = 0;

    if (isset($post['UserID'])) {
      $user_id = $post['UserID'];
    }


    if (isset($post['Page'])) {
      $page = $post['Page'];
    }

    if (isset($post['status_id'])) {
      $status_id = $post['status_id'];
    }






    $getData = $db->getDataRts($user_id, $page, $limit , $status_id);

    if ($getData != null) {


      while ($row = $getData->fetch_assoc()) {

        $rows[] = $row;


      }


      $result=json_decode($content,true);
      //get new orders lazada


      $return = array(
        "status" => 200,
        "message" => "ok",
        "data" => $rows
      );



    } else {
      $return = array(
        "status" => 200,
        "message" => "Belum ada Data",
        "data" => []
      );
    }


    echo json_encode($return);
  }


  if ($content == "get_rts_items") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
    //$user_id = 5;
    $order_id =$post['order_id'];
    //$order_id =10;
    $page = null;

    $limit = 0;

    if (isset($post['UserID'])) {
      $user_id = $post['UserID'];
    }


    if (isset($post['Page'])) {
      $page = $post['Page'];
    }


    //Get data from database
    $getData = $db->getDataRtsItems($user_id, $page, $limit , $order_id);



    if ($getData != null) {

      while ($row = $getData->fetch_assoc()) {

        $rows[] = $row;


      }

      $total = mysqli_num_rows($getData);


      $return = array(
        "status" => 200,
        "total_rows" => $total,
        "message" => "Berhasil",
        "data" => $rows
      );





    } else {
      $return = array(
        "status" => 200,
        "total_rows" => 0,
        "message" => "Belum ada Data",
        "data" => []
      );
    }


    echo json_encode($return);
  }



  if ($content == "created_order") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);

    //Get Value For History Order from post



    $marketplace = $post['marketplace']  ;
    $order_id = $post['order_id']  ;
    $order_number = $post['order_id']  ;
    $merchant_name = $post['merchant_name']  ;
    $customer_first_name = $post['name']  ;
    $shipping_provider = $post['shipping_provider']  ;
    $delivery_type = $post['delivery_type'];
  //  $tracking_code = $post['tracking_code']  ;
    $shipping_amount = $post['shipping_amount']  ;
    $payment_method = $post['payment_method']  ;
    $tracking_code_pre = $post['tracking_code_pre']  ;
    $remarks = $post['remark']  ;
    $action = $post['action']  ;
    $user_id = 5;



    if (isset($order_id) && isset($user_id) && isset($merchant_name)&& isset($marketplace)) {


    $chItems = curl_init();
    curl_setopt($chItems, CURLOPT_URL, $base_url . '/public/api/orders/get_order');
    $payloadItem = json_encode( array( "order_id"=> $order_id,
      "merchant_name"=> $merchant_name,
      "marketplace"=> $marketplace,
      "status"=> 1,
    ) );
    curl_setopt( $chItems, CURLOPT_POSTFIELDS, $payloadItem );
    curl_setopt( $chItems, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($chItems, CURLOPT_RETURNTRANSFER, 1);
    $contentItem = curl_exec($chItems);
    curl_close($chItems);

    $resultItem=json_decode($contentItem,true);
    $dataItems = $resultItem['data'];

  // echo json_encode($resultItem);die;
   
 foreach ($resultItem ['data'] as $datas) {


    $order_number = $datas['order_number'] ;
    $branch_number = $datas['branch_number'] ;
    $warehouse_code = $datas['warehouse_code'] ;
    $customer_first_name = $datas['customer_first_name'] ;
    $customer_last_name = $datas['customer_last_name'] ;
    $price = $datas['price'] ;
    $items_count = $datas['items_count'] ;
    $payment_method = $datas['payment_method'] ;
    $voucher = $datas['voucher'] ;
    $voucher_code = $datas['voucher_code'] ;
    $voucher_platform = $datas['voucher_platform'] ;
    $voucher_seller = $datas['voucher_seller'] ;
    $gift_option = $datas['gift_option'] ;
    $gift_message = $datas['gift_message'] ;
    $shipping_fee = $datas['shipping_fee'] ;
    $shipping_fee_discount_seller = $datas['shipping_fee_discount_seller'] ;
    $shipping_fee_discount_platform = $datas['shipping_fee_discount_platform'] ;
    $promised_shipping_times = $datas['promised_shipping_times'] ;
    $national_registration_number = $datas['national_registration_number'] ;
    $tax_code = $datas['tax_code'] ;
    $extra_attributes = $datas['extra_attributes'] ;
    $remarks = $datas['remarks'] ;
    $delivery_info = $datas['delivery_info'] ;
    $statuses = 10;
    $created_at = $datas['created_at'] ;
    $updated_at = $datas['updated_at'] ;


    foreach($datas['order_items'] as $DataOrderItems)

    {

      //cek stok
      $tracking_number = $DataOrderItems['tracking_code'];
      $order_item_id = $DataOrderItems['order_item_id'];




		
	//	echo json_encode($tracking_number);die;
          //Set array untuk variable history orders
          $variant_details[] = array(
    
            "order_item_id" =>$DataOrderItems['order_item_id'] ,
            "order_id" =>$DataOrderItems['order_id'] ,
            "purchase_order_id" =>$DataOrderItems['purchase_order_id'] ,
            "purchase_order_number" =>$DataOrderItems['purchase_order_number'],
            "invoice_number" => $DataOrderItems['invoice_number'],
            "sla_time_stamp" => $DataOrderItems['sla_time_stamp'],
            "package_id" =>$DataOrderItems['package_id'],
            "shop_id" =>$DataOrderItems['shop_id'],
            "order_type" => $DataOrderItems['order_type'],
            "shop_sku" =>$DataOrderItems['shop_sku'] ,
            "sku" =>$DataOrderItems['sku'],
            "name" =>$DataOrderItems['name'],
            "qty" =>1,
            "variation" =>$DataOrderItems['variation'],
            "item_price" =>$DataOrderItems['item_price'],
            "paid_price" =>$DataOrderItems['paid_price'],
            "currency" =>$DataOrderItems['currency'],
            "tax_amount" => $DataOrderItems['tax_amount'],
            "product_main_image" =>$DataOrderItems['image_variant'],
            "product_detail_url" =>$DataOrderItems['product_detail_url'],
            "shipment_provider" =>$DataOrderItems['shipment_provider'],
            "tracking_code_pre" =>$DataOrderItems['tracking_code_pre'],
            "tracking_code" =>$DataOrderItems['tracking_code'],
            "shipping_type" =>$DataOrderItems['shipping_type'],
            "shipping_provider_type" =>$DataOrderItems['shipping_provider_type'],
            "shipping_fee_original" =>$DataOrderItems['shipping_fee_original'],
            "shipping_service_cost " =>$DataOrderItems['shipping_service_cost'],
            "shipping_fee_discount_seller" =>$DataOrderItems['shipping_fee_discount_seller'],
            "shipping_amount" =>$DataOrderItems['shipping_amount'],
            "is_digital" =>$DataOrderItems['is_digital'],
            "voucher_amount" =>$DataOrderItems['voucher_amount'],
            "voucher_seller" =>$DataOrderItems['voucher_seller'],
            "voucher_code_seller" =>$DataOrderItems['voucher_code_seller'],
            "voucher_code" =>$DataOrderItems['voucher_code'],
            "voucher_code_platform" =>$DataOrderItems['voucher_code_platform'],
            "voucher_platform" =>$DataOrderItems['voucher_platform'],
            "order_flag" =>$DataOrderItems['order_flag'],
            "promised_shipping_time" =>$DataOrderItems['promised_shipping_time'],
            "digital_delivery_info" =>$DataOrderItems['digital_delivery_info'],
            "extra_attributes" =>$DataOrderItems['extra_attributes'],
            "cancel_return_initiator" =>$DataOrderItems['cancel_return_initiator'],
            "reason" =>$DataOrderItems['reason'],
            "reason_detail" =>$DataOrderItems['reason_detail'],
            "stage_pay_status" =>$DataOrderItems['stage_pay_status'],
            "warehouse_code" =>$DataOrderItems['warehouse_code'],
            "return_status" =>$DataOrderItems['return_status'],
            "status" =>$DataOrderItems['status'],
            "created_at" =>$DataOrderItems['created_at'],
            "updated_at" =>$DataOrderItems['updated_at']
    
          );
		  
		  	  //set rts
		 //
		 //  $chrts = curl_init();
           //curl_setopt($chrts, CURLOPT_URL, $base_url . '/public/api/lazada.php?request=set_rts');
            //$payloadRts = json_encode( array( "order_item_ids"=> array($order_item_id),
              //"shipping_provider"=> $shipping_provider,
              //"delivery_type"=> $delivery_type,
              //"tracking_number"=> $tracking_number,
              //"user_id"=> $user_id,
              //"merchant_name"=> $merchant_name) );
			  
			//echo $payloadRts;die;
			
            //curl_setopt( $chrts, CURLOPT_POSTFIELDS, $payloadRts );
            //curl_setopt( $chrts, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            //curl_setopt($chrts, CURLOPT_RETURNTRANSFER, 1);
           // $contentRts = curl_exec($chrts);
           // curl_close($chrts);


            //mengubah data json menjadi data array asosiatif
         //   $resultRts=json_decode($contentRts);
			//echo json_encode($resultRts);die;
			
        }

 }

 


      //cek user id
      $getData = $db->checkHistoryOrderByOrder($order_id , $user_id);

      if ($getData == null) {

        //print_r ($order_number);die;

        //Isi History Orders

        $createHistoryOrders = $db->createHistoryOrders(
          $order_id,
          $order_number,
          $user_id,
          $marketplace,
          $merchant_name,
          $branch_number,
          $warehouse_code,
          $customer_first_name,
          $customer_last_name,
          $price,
          $items_count,
          $payment_method,
          $voucher,
          $voucher_code,
          $voucher_platform,
          $voucher_seller,
          $gift_option,
          $gift_message,
          $shipping_fee,
          $shipping_fee_discount_seller,
          $shipping_fee_discount_platform,
          $promised_shipping_times,
          $national_registration_number,
          $tax_code,
          $extra_attributes,
          $remarks,
          $delivery_info,
          $statuses,
          $created_at,
          $updated_at);




        //jika produk berhasil
        if ($createHistoryOrders == true) {


          $variant_details = json_encode($variant_details, true);

          $createHistoryOrderDetails = $db->createHistoryOrderDetails(
            $order_id , $variant_details);



          if ($createHistoryOrderDetails == true) {
			  
			  
		


            $updateStokBySKU = $db->updateStokBySKU($variant_details);



            $return = array(
              "status" => 200,
              "action" => "createHistoryOrderDetails",
              "message" => "Pesanan berhasil di konfirmasi",
             // "data" => $createHistoryOrderDetails
			 "data" => $resultRts
            );


          }else{

            $return = array(
              "status" => 404,
              "action" => "createHistoryOrderDetails",
              "message" => "Gagal Menambahkan Variant Produk",
              "data" => []
            );

          }

          //jika produk gagal
        } else {
          $return = array(
            "status" => 404,
            "action" => " createHistoryOrders",
            "message" => "Gagal"
          );
        }




      } else {
        $return = array(
          "status" => 404,
          "message" => "Pesanan sudah pernah di konfirmasi"
        );
      }


      //Jika user id tidak ada//
    } else {
      $return = array(
        "status" => 404,
        "message" => "Order Number belum terisi"
      );
    }
    echo json_encode($return);
  }

  if ($content == "created_order2") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);

    $marketplace = $post['marketplace']  ;
    $merchant_name = $post['merchant_name']  ;
    $data_order = $post['data_order']  ;
    $user_id = 5;



    if (isset($data_order) && isset($user_id) && isset($merchant_name)&& isset($marketplace)) {

      if (base64_encode(base64_decode($post['data_order'], true)) === $post['data_order']){
        $data_order = base64_decode($post['data_order']);
        $resultOrder = Json_decode($data_order,true);
      } else {
        $data_order = $post['data_order'];
      }

        $order_id = $resultOrder['order_id'] ;
        $order_number = $resultOrder['order_number'] ;
        $branch_number = $resultOrder['branch_number'] ;
        $warehouse_code = $resultOrder['warehouse_code'] ;
        $customer_first_name = $resultOrder['customer_first_name'] ;
        $customer_last_name = $resultOrder['customer_last_name'] ;
        $price = $resultOrder['price'] ;
        $items_count = $resultOrder['items_count'] ;
        $payment_method = $resultOrder['payment_method'] ;
        $voucher = $resultOrder['voucher'] ;
        $voucher_code = $resultOrder['voucher_code'] ;
        $voucher_platform = $resultOrder['voucher_platform'] ;
        $voucher_seller = $resultOrder['voucher_seller'] ;
        $gift_option = $resultOrder['gift_option'] ;
        $gift_message = $resultOrder['gift_message'] ;
        $shipping_fee = $resultOrder['shipping_fee'] ;
        $shipping_fee_discount_seller = $resultOrder['shipping_fee_discount_seller'] ;
        $shipping_fee_discount_platform = $resultOrder['shipping_fee_discount_platform'] ;
        $promised_shipping_times = $resultOrder['promised_shipping_times'] ;
        $national_registration_number = $resultOrder['national_registration_number'] ;
        $tax_code = $resultOrder['tax_code'] ;
        $extra_attributes = $resultOrder['extra_attributes'] ;
        $remarks = $resultOrder['remarks'] ;
        $delivery_info = $resultOrder['delivery_info'] ;
        $statuses = 2;
        $created_at = $resultOrder['created_at'] ;
        $updated_at = $resultOrder['updated_at'] ;

      $address_shipping = $resultOrder['address_shipping'];
      $address_billing = $resultOrder['address_billing'];


        foreach($resultOrder['order_items'] as $DataOrderItems)

        {


          $tracking_number = $DataOrderItems['tracking_code'];
          $order_item_id = $DataOrderItems['order_item_id'];
          $sku = $DataOrderItems['sku'];

          //cek stok
          $chItems = curl_init();
          curl_setopt($chItems, CURLOPT_URL, $base_url . '/public/api/products/cek_stok');
          $payloadItem = json_encode( array( "sku"=> $sku ) );
          curl_setopt( $chItems, CURLOPT_POSTFIELDS, $payloadItem );
          curl_setopt( $chItems, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
          curl_setopt($chItems, CURLOPT_RETURNTRANSFER, 1);
          $contentItem = curl_exec($chItems);
          curl_close($chItems);

          $resultItem=json_decode($contentItem,true);

          $stock =(int)$resultItem['data'];


          if ($stock == 0) {

            $return = array(
              "status" => 404,
              "message" => "Stok kosong"
            );

          echo json_encode($return);die;

         }

          $variant_details[] = array(

            "order_item_id" =>$DataOrderItems['order_item_id'] ,
            "order_id" =>$DataOrderItems['order_id'] ,
            "purchase_order_id" =>$DataOrderItems['purchase_order_id'] ,
            "purchase_order_number" =>$DataOrderItems['purchase_order_number'],
            "invoice_number" => $DataOrderItems['invoice_number'],
            "sla_time_stamp" => $DataOrderItems['sla_time_stamp'],
            "package_id" =>$DataOrderItems['package_id'],
            "shop_id" =>$DataOrderItems['shop_id'],
            "order_type" => $DataOrderItems['order_type'],
            "shop_sku" =>$DataOrderItems['shop_sku'] ,
            "sku" =>$DataOrderItems['sku'],
            "name" =>$DataOrderItems['name'],
            "qty" =>1,
            "variation" =>$DataOrderItems['variation'],
            "item_price" =>$DataOrderItems['item_price'],
            "paid_price" =>$DataOrderItems['paid_price'],
            "currency" =>$DataOrderItems['currency'],
            "tax_amount" => $DataOrderItems['tax_amount'],
            "product_main_image" =>$DataOrderItems['image_variant'],
            "product_detail_url" =>$DataOrderItems['product_detail_url'],
            "shipment_provider" =>$DataOrderItems['shipment_provider'],
            "tracking_code_pre" =>$DataOrderItems['tracking_code_pre'],
            "tracking_code" =>$DataOrderItems['tracking_code'],
            "shipping_type" =>$DataOrderItems['shipping_type'],
            "shipping_provider_type" =>$DataOrderItems['shipping_provider_type'],
            "shipping_fee_original" =>$DataOrderItems['shipping_fee_original'],
            "shipping_service_cost " =>$DataOrderItems['shipping_service_cost'],
            "shipping_fee_discount_seller" =>$DataOrderItems['shipping_fee_discount_seller'],
            "shipping_amount" =>$DataOrderItems['shipping_amount'],
            "is_digital" =>$DataOrderItems['is_digital'],
            "voucher_amount" =>$DataOrderItems['voucher_amount'],
            "voucher_seller" =>$DataOrderItems['voucher_seller'],
            "voucher_code_seller" =>$DataOrderItems['voucher_code_seller'],
            "voucher_code" =>$DataOrderItems['voucher_code'],
            "voucher_code_platform" =>$DataOrderItems['voucher_code_platform'],
            "voucher_platform" =>$DataOrderItems['voucher_platform'],
            "order_flag" =>$DataOrderItems['order_flag'],
            "promised_shipping_time" =>$DataOrderItems['promised_shipping_time'],
            "digital_delivery_info" =>$DataOrderItems['digital_delivery_info'],
            "extra_attributes" =>$DataOrderItems['extra_attributes'],
            "cancel_return_initiator" =>$DataOrderItems['cancel_return_initiator'],
            "reason" =>$DataOrderItems['reason'],
            "reason_detail" =>$DataOrderItems['reason_detail'],
            "stage_pay_status" =>$DataOrderItems['stage_pay_status'],
            "warehouse_code" =>$DataOrderItems['warehouse_code'],
            "return_status" =>$DataOrderItems['return_status'],
            "status" =>2,
            "created_at" =>$DataOrderItems['created_at'],
            "updated_at" =>$DataOrderItems['updated_at']

          );


        }

       // echo json_encode($variant_details);die;


      //cek user id
      $getData = $db->checkHistoryOrderByOrder($order_id , $user_id);

    //  echo json_encode($getData);die;
      if ($getData == false) {


        $createHistoryOrders = $db->createHistoryOrders(
          $order_id,
          $order_number,
          $user_id,
          $marketplace,
          $merchant_name,
          $branch_number,
          $warehouse_code,
          $customer_first_name,
          $customer_last_name,
          $price,
          $items_count,
          $payment_method,
          $voucher,
          $voucher_code,
          $voucher_platform,
          $voucher_seller,
          $gift_option,
          $gift_message,
          $shipping_fee,
          $shipping_fee_discount_seller,
          $shipping_fee_discount_platform,
          $promised_shipping_times,
          $national_registration_number,
          $tax_code,
          $extra_attributes,
          $remarks,
          $delivery_info,
          $statuses,
          $created_at,
          $updated_at);

        //jika produk berhasil
        if ($createHistoryOrders == true) {


          $variant_details = json_encode($variant_details, true);

          $createHistoryOrderDetails = $db->createHistoryOrderDetails(
            $order_id , $variant_details);



          if ($createHistoryOrderDetails == true) {

            $createHistoryAddressShipping = $db->createHistoryAddressShipping(
              $order_id , $address_shipping);

            $createHistoryAddressBilling = $db->createHistoryAddressBilling(
              $order_id , $address_billing);


            $updateStokBySKU = $db->updateStokBySKU($variant_details);

            $return = array(
              "status" => 200,
              "action" => "createHistoryOrderDetails",
              "message" => "Pesanan berhasil di konfirmasi",
              // "data" => $createHistoryOrderDetails
              "data" => $resultRts
            );


          }else{

            $return = array(
              "status" => 404,
              "action" => "createHistoryOrderDetails",
              "message" => "Gagal Menambahkan Variant Produk",
              "data" => []
            );

          }

          //jika produk gagal
        } else {
          $return = array(
            "status" => 404,
            "action" => " createHistoryOrders",
            "message" => "Gagal"
          );
        }




      } else {
        $return = array(
          "status" => 404,
          "message" => "Pesanan sudah pernah di konfirmasi"
        );
      }


      //Jika user id tidak ada//
    } else {
      $return = array(
        "status" => 404,
        "message" => "Data order belum terisi"
      );
    }
    echo json_encode($return);
  }


  if ($content == "created_kasir") {
    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);

    //Get Value For History Order from post


    $token_session = $post['TokenSession']  ;
    $marketplace = $post['marketplace']  ;
    $order_id = $post['order_id']  ;
    $order_number = $post['order_id']  ;
    $merchant_name = $post['merchant_name']  ;
    $customer_first_name = $post['name']  ;
    $shipping_provider = $post['shipping_provider']  ;
    $tracking_code = $post['tracking_code']  ;
    $shipping_amount = $post['shipping_amount']  ;
    $payment_method = $post['payment_method']  ;
    $tracking_code_pre = $post['tracking_code_pre']  ;
    $remarks = $post['remark']  ;
    $action = $post['action']  ;
	$merchant_name = $post['merchant_name']  ;


    $user_id = 5;


	
if ($marketplace == "OFFLINE" && $payment_method == "CASH") {
	
	
	$status = 4;
	
} else if ($marketplace == "OFFLINE" && $payment_method == "DEBIT") {
	
	$status = 4;
	
}else{
	
	$status = 2;
	
}

    
	
    if (isset($user_id) && $order_id != "" && $marketplace != "" && $payment_method != "" && isset($merchant_name) ) {

      $getDataCartDetail = $db->checkCart($user_id , $token_session);

      if ($getDataCartDetail != null) {

        while ($row = $getDataCartDetail->fetch_assoc()) {

          $rows = $row;
          $cart_id = $row['CartID'];
		  
          $variant_details[] = array(
			"order_item_id" =>$order_id ,
            "order_id" =>$order_id ,
            "name" =>$rows['ProductName'],
            "sku" =>$rows['SKU'] ,
            "paid_price" =>$rows['Price'] ,
            "item_price" =>$rows['Price'] ,
            "qty" =>$rows['Quantity'] ,
			"currency" =>"IDR" ,
            "variation" =>$rows['ProductVariantName'] . " " . $rows['ProductVariantDetailName'] ,
            "shipment_provider" =>$shipping_provider,
            "tracking_code_pre" =>$tracking_code_pre,
            "tracking_code" =>$tracking_code,
            "shipping_amount" =>$shipping_amount,
            "product_main_image" =>$rows['ImageProductVariantName'] ,
            "status" =>$status
          );

        }
		
      }


      $getData = $db->checkHistoryOrderByOrder($order_id , $user_id);

      if ($getData == null) {

        //Isi History Orders

        $createHistoryOrders = $db->createHistoryOrders(
          $order_id,
          $order_number,
          $user_id,
          $marketplace,
		  $merchant_name,
          $branch_number,
          $warehouse_code,
          $customer_first_name,
          $customer_last_name,
          $price,
          $items_count,
          $payment_method,
          $voucher,
          $voucher_code,
          $voucher_platform,
          $voucher_seller,
          $gift_option,
          $gift_message,
          $shipping_fee,
          $shipping_fee_discount_seller,
          $shipping_fee_discount_platform,
          $promised_shipping_times,
          $national_registration_number,
          $tax_code,
          $extra_attributes,
          $remarks,
          $delivery_info,
          $statuses,
          $created_at,
          $updated_at);




        //jika produk berhasil
        if ($createHistoryOrders == true) {


          $variant_details = json_encode($variant_details, true);

          $createHistoryOrderDetails = $db->createHistoryOrderDetails(
            $order_id , $variant_details);



          if ($createHistoryOrderDetails == true) {

            $updateStokBySKU = $db->updateStokBySKU($variant_details);

            $deleteCartDetailByUser = $db->deleteCartDetailByUser($user_id , $cart_id);


            $return = array(
              "status" => 200,
              "message" => "Data berhasil disimpan",
              "data" => $createHistoryOrderDetails
            );


          }else{

            $return = array(
              "status" => 404,
              "message" => "Gagal menyimpan data produk",
              "data" => []
            );

          }

          //jika produk gagal
        } else {
          $return = array(
            "status" => 404,
            "message" => "Gagal meyimpan data produk"
          );
        }




      } else {
        $return = array(
          "status" => 404,
          "message" => "Order Double"
        );
      }



    }else{


      $return = array(
        "status" => 404,
        "message" => "Data yang kamu masukan tidak lengkap"
      );


    }



    echo json_encode($return);
  }



  if ($content == "accept_order") {

    $modeHeader = 0;
    $post = json_decode(file_get_contents("php://input"), true);
    $order_id = $post['order_id'] ;
    $user_id = 5;
    $shipping_provider =$post['shipping_provider'];
    $delivery_type = $post['delivery_type'];
    $merchant_name = $post['merchant_name'];
    $marketplace = $post['marketplace'];



    if (isset($order_id) && isset($user_id) && isset($shipping_provider)&& isset($delivery_type)&& isset($merchant_name)&& isset($marketplace)) {

      ///Variable variant details
      $variant_details = array();

      $chItems = curl_init();
      curl_setopt($chItems, CURLOPT_URL, $base_url . '/public/api/orders.php?request=get_order_items');
      $payloadItem = json_encode( array( "order_id"=> $order_id ,
        "marketplace"=> $marketplace,
        "merchant_name"=> $merchant_name) );
      curl_setopt( $chItems, CURLOPT_POSTFIELDS, $payloadItem );
      curl_setopt( $chItems, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
      curl_setopt($chItems, CURLOPT_RETURNTRANSFER, 1);
      $contentItem = curl_exec($chItems);
      curl_close($chItems);

    
      //mengubah data json menjadi data array asosiatif
      $resultItem=json_decode($contentItem,true);


      foreach($resultItem['data'] as $DataOrderItems)
      {

        $order_item_ids[] = $DataOrderItems['order_item_id'];
        $order_item_id = $DataOrderItems['order_item_id'];

      }

      $order_item_ids = json_encode($order_item_ids, true);

     // echo json_encode($order_item_ids);die;
      if($shipping_provider != null && $delivery_type != null ) {
 
		$chpick = curl_init();
        curl_setopt($chpick, CURLOPT_URL, $base_url . '/public/api/lazada.php?request=set_pick');
        $payloadItem = json_encode( array( "order_item_ids"=> $order_item_ids,
          "shipping_provider"=> $shipping_provider,
          "delivery_type"=> $delivery_type,
          "user_id"=> $user_id,
          "merchant_name"=> $merchant_name	) );
        curl_setopt( $chpick, CURLOPT_POSTFIELDS, $payloadItem );
        curl_setopt( $chpick, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($chpick, CURLOPT_RETURNTRANSFER, 1);
        $contentpick = curl_exec($chpick);
        curl_close($chpick);

        $resultpick=json_decode($contentpick);
		$dataPick = $resultpick -> data;
		
          
    // echo json_encode ($resultpick);die;
		



        //print_r ($order_item_id);die;set_invoice

        //jika set pick berhasil
        if ($resultpick->status == 200) {

       //Set Invoice
          $chInvoice = curl_init();
          curl_setopt($chInvoice, CURLOPT_URL, $base_url . '/public/api/lazada.php?request=set_invoice');
          $payloadInvoice = json_encode( array( "order_item_id"=> $order_item_id,
            "user_id"=> $user_id,
            "merchant_name"=> $merchant_name
          ) );
          curl_setopt( $chInvoice, CURLOPT_POSTFIELDS, $payloadInvoice );
          curl_setopt( $chInvoice, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
          curl_setopt($chInvoice, CURLOPT_RETURNTRANSFER, 1);
          $contentInvoice = curl_exec($chInvoice);
          curl_close($chInvoice);
          $resultInvoice=json_decode($contentInvoice);
		  $dataInvoice = $resultInvoice->data;


		// echo json_encode ($resultInvoice);die;

          //jika set invoice berhasil
          if ($resultInvoice->status == 200) {

		
              // Simpan data ke database
              $data1 = array(
                'order_id'=> $order_id,
                'merchant_name'=> $merchant_name,
                'user_id'=> $user_id,
                'marketplace'=> $marketplace
              );

             // echo json_encode($data1);die;

              $options = array(
                'http' => array(
                  'method'  => 'POST',
                  'content' => json_encode( $data1 ),
                  'header'=>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
                )
              );

              $context  = stream_context_create( $options );
            $result = file_get_contents( $base_url . "/public/api/orders.php?request=created_order", false, $context );
              $response = json_decode($result, true );

              //print_r ($response);die;

              //jika created order berhasil
              if ($response['status'] == 200) {

                $return = array(
                  "status" => 200,
                  "total_rows" => 1,
                  "message" => $response['message'],
                  "data" => $response
                );


                //jika tidak
              } else {
                $return = array(
                  "status" => 404,
				  "action" => "created order",
                  "message" => $response['message']
                );
              }

            //jika respond pack error
          }else{


            $return = array(
              "status" => 404,
              "total_rows" => 0,
			  "action" => "set invoice",
			   "message" => $resultInvoice['message'],
              "data" => $resultInvoice['data']
              
            );

          }

          //jika respond invoice error
        }else{


          $return = array(
            "status" => 404,
            "total_rows" => 0,
			"action" => "set pick",
			"message" => $resultpick['message'],
              "data" => $resultpick
          );

        }

      }else{

        $return = array(
          "status" => 404,
          "total_rows" => 1,
          "message" => "Kurir belum di pilih",
          "data" => []
        );

      }











      //Jika user id tidak ada//
    } else {
      $return = array(
        "status" => 504,
        "message" => "ERROR"
      );
    }
    echo json_encode($return);
  }





  // ---------------------------------------- API that need token below ------------------------------------------- //
  if ($modeHeader == 1) {
    //Check header token
    $token_header = $_SERVER['HTTP_TOKEN'];
    $userid_header = $_SERVER['HTTP_USER_ID'];
    $version_code_header = $_SERVER['HTTP_VERSION_CODE'];
    $version_name_header = $_SERVER['HTTP_VERSION_NAME'];
    $version_check = 1;






    // if($current_app_version_code['Value'] == $version_code_header){
    // 	$version_check = 1;
    // }

    // $data = [
    // 	"token_header" 				=> $token_header,
    // 	"userid_header" 		    => $userid_header,
    // 	"version_code_header" 		=> $version_code_header
    // ];

    // echo json_encode($data);
    $nurse_type_header = isset($_SERVER['HTTP_NURSE_TYPE']) ? $_SERVER['HTTP_NURSE_TYPE'] : 1;

    if (isset($token_header) && isset($userid_header) && $token_header != "" && $userid_header != "" && $version_check == 1) {

      $checkLoginGoogle = $db->getUserByID($userid_header);

      $loginGoogle = 0;
      if ($checkLoginGoogle) {
        $checkLoginGoogle = $checkLoginGoogle->fetch_assoc();
        if (isset($checkLoginGoogle['GoogleUserID'])) {
          $loginGoogle = 1;
        }
      }

      if ($loginGoogle == 0) {
        $checkToken = $db->checkToken($token_header, $userid_header);
      } elseif ($loginGoogle == 1) {
        $checkToken = $db->checkToken2($token_header, $userid_header);
      }
      if ($checkToken) {













      } else {
        //Token not match !!!
        $return = array(
          "status" => 406,
          "message" => "Anda sudah login di device lain!"
        );

        echo json_encode($return);
      }
    } elseif ($version_check == 0) {
      $return = array(
        "status" => 407,
        "force_update" => $force_update,
        "message" => "Versi app terbaru sudah ada di playstore, harap update app terbaru !"
      );

      echo json_encode($return);
    } else {
      $return = array(
        "status" => 406,
        "message" => "Oops sesi anda sudah habiss!"
      );

      echo json_encode($return);
    }
  }

} else {
  //Aha, what you're looking for !!!
  $return = array(
    "status" => 404,
    "message" => "Method Not Founde!"
  );

  echo json_encode($return);
}

?>
