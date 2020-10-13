<?php

include "../config/db_connection.php";
include "../config/config_type.php";
include "../config/Util.php";
include "../config/bca_api.php";
include "../config/BCA_api2.php";


$rows = array();
$rows2 = array();

$current_app_version_code = "1"; //App Version Code
$current_app_version_name = "0.1.0"; //App Version Name

$token_header = ""; //Header Token
$version_code_header = ""; //Header Version Code
$version_name_header = ""; //Header Version Name
$version_name_header = ""; //Header Version Name
$userid_header = "";
$modeHeader = 1;
$uploaddir = $UPLOAD_DIR;
// $uploaddir2 = '/home/vtalid01/api-dev.v-tal.id/v1/images/chats/nurse_orders/'.$order_id.'/';

//-------------------------------------- API Function Start Here ----------------------------------------//

//Check request content
$content = $_GET['request'];
if (isset($content) && $content != "") {

    //Load Models
    include "../config/model.php";

    $db = new Model_user();
    $dbutil = new Util();
    $dbbca = new BCAAPI();
    $dbbca2 = new bca();

    //-------------------------------------- API Without header token below ----------------------------------------//

    /**
     * Register Login
     * @param : phone, password (JSON)
     * returns data
     */
    
    if ($content == "perfectmoney") {
        $modeHeader = 0;
        $post = json_decode(file_get_contents("php://input"), true);
        
    $string=
    $_POST['PAYMENT_ID'].':'.$_POST['PAYEE_ACCOUNT'].':'.
    $_POST['PAYMENT_AMOUNT'].':'.$_POST['PAYMENT_UNITS'].':'.
    $_POST['PAYMENT_BATCH_NUM'].':'.
    $_POST['PAYER_ACCOUNT'].':'. $_POST['ALTERNATE_PHRASE_HASH'].':'.
    $_POST['TIMESTAMPGMT'];
    
    $hash=strtoupper(md5($string));
    
    
    /*
     Please use this tool to see how valid hash is generated:
     https://perfectmoney.is/acct/md5check.html
     */
    if($hash==$_POST['V2_HASH']){ // processing payment if only hash is valid
        
        /* In section below you must implement comparing of data you received
         with data you sent. This means to check if $_POST['PAYMENT_AMOUNT'] is
         particular amount you billed to client and so on. */
        
        $return = array(
                        "status" => 200,
                        "message" => "ok"
                        );
        
        if($_POST['PAYMENT_AMOUNT']=='15.95' && $_POST['PAYEE_ACCOUNT']=='U1234567' && $_POST['PAYMENT_UNITS']=='USD'){
            
            /* ...insert some code to process valid payments here... */
            
            // uncomment code below if you want to log successfull payments
            /* $f=fopen(PATH_TO_LOG."good.log", "ab+");
             fwrite($f, date("d.m.Y H:i")."; POST: ".serialize($_POST)."; STRING: $string; HASH: $hash\n");
             fclose($f); */
            $return = array(
                            "status" => 200,
                            "message" => "Payment on process"
                            );
            
        }else{ // you can also save invalid payments for debug purposes
            
            // uncomment code below if you want to log requests with fake data
            /* $f=fopen(PATH_TO_LOG."bad.log", "ab+");
             fwrite($f, date("d.m.Y H:i")."; REASON: fake data; POST: ".serialize($_POST)."; STRING: $string; HASH: $hash\n");
             fclose($f); */
            
        }
        
        
    }else{ // you can also save invalid payments for debug purposes
        
        // uncomment code below if you want to log requests with bad hash
        /* $f=fopen(PATH_TO_LOG."bad.log", "ab+");
         fwrite($f, date("d.m.Y H:i")."; REASON: bad hash; POST: ".serialize($_POST)."; STRING: $string; HASH: $hash\n");
         fclose($f); */
        
    }

        echo json_encode($return);
    }
    
    if ($content == "payments_fasapay") {
          $modeHeader = 0;
        $post = json_decode(file_get_contents("php://input"), true);
        
        $fp_acc_request = $_POST['fp_acc'];
        $fp_store_request = $_POST['fp_store'];
        $fp_item_request = $_POST['fp_item'];
        $fp_amnt_request = $_POST['fp_amnt'];
        $fp_fee_mode = $_POST['fp_fee_mode'];
        $fp_currency_request = $_POST['fp_currency'];
        $fp_comments_request = $_POST['fp_comments'];
        $fp_merchant_ref_request= $_POST['fp_merchant_ref'];
        $fp_status_url_request = $_POST['fp_status_url'];
        $fp_success_url_request = $_POST['fp_success_url'];
        $fp_success_method_request= $_POST['fp_success_method'];
        $fp_fail_url_request = $_POST['fp_fail_url'];
        $fp_fail_method_request = $_POST['fp_fail_method'];
        $fp_custom = $_POST['fp_custom'];
        $fp_host = $_POST['fp_host'];
        $security_word = $_POST['security_word'];
 
        
//        if (isset($fp_acc_request) && isset($fp_store_request) && isset($fp_amnt_request) && isset($fp_fee_mode) && isset($fp_currency_request) && isset($fp_comments_request) && isset($fp_merchant_ref_request) && isset($fp_status_url_request)){
            $now = new DateTime();
            $fp_paidto = $fp_acc_request ;
            $fp_fee_mode = $fp_fee_mode;
            $fp_amnt = $fp_amnt_request;
            $fp_currency= $fp_currency_request;
            $fp_store = $fp_store_request;
            $fp_merchant_ref = $fp_merchant_ref_request;
            
            $fp_paidby = "FP151570";
            
            if ($_POST['fp_fee_mode'] == "FiS") {
                $fp_fee_amnt = 1 /100 * $fp_amnt_request;
                $fp_total = $fp_amnt_request - $fp_fee_amnt;
            }else{
                $fp_fee_amnt = 0;
                $fp_total = $fp_amnt;
            }

            
            $fp_batchnumber = 'TR' . date('Ymd') . $now->getTimestamp() ;
    
            $fp_timestamp = date('Y-m-d H:i:s');
            
            
            
            $fp_hash= hash('sha256',   $fp_paidto . ':' . $fp_paidby . ':' . $fp_store . ':' . $fp_currency . ':' . $security_word );
            
            $fp_hash_2 = hash('sha256', $fp_paidto . ':' . $fp_paidby . ':' . $fp_store .  ':' . $fp_amnt . ':' . $fp_fee_amnt . ':' . $fp_fee_mode . ':' . $fp_total . ':' . $fp_batchnumber . ':' . $fp_currency . ':'. $security_word );

            $data = array(
                          "fp_paidto" => $fp_acc_request ,
                          "fp_paidby" => $fp_paidby ,
                          "fp_amnt" => $fp_amnt ,
                          "fp_fee_amnt" => $fp_fee_amnt ,
                          "fp_fee_mode" => $fp_fee_mode ,
                          "fp_total" => $fp_total ,
                          "fp_currency" => $fp_currency ,
                          "fp_batchnumber" => $fp_batchnumber ,
                          "fp_store" => $fp_store ,
                          "fp_timestamp" => $fp_timestamp ,
                          "fp_merchant_ref" => $fp_merchant_ref ,
                          "fp_hash" => $fp_hash ,
                          "fp_hash_2" => $fp_hash_2 ,
                           "enc" => $fp_custom,
                          "csrf_token" => "a2f67ed1898d5725f8e678fe4a4013651a0a36ac"
                          );
            # Create a connection
            $url = $fp_status_url_request;
            $ch = curl_init($url);
            # Form data string
            $postString = http_build_query($data, '', '&');
            # Setting our options

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//                                                   'Host:'  . $fp_host,
//                                                   'Accept: text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5',
                                                   'Content-Type: application/x-www-form-urlencoded',
//                                                   'Content-Type: application/json',
                                                   'Connection: Keep-Alive',
                                                   'Content-Length: 759',
                                                   'Referer: https://www.fasapay.com/sci',
                                                   'Accept: */*',
                                                   'User-Agent: PHP (Linux) FasaPay FasaPay-IPN FasaPay-SCI'
                                                   
                                                   ));
  

//        $header[0]  = "Accept: text/xml,application/xml,application/xhtml+xml,";
//        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
//        $header[] = "Cache-Control: max-age=0";
//        $header[] = "Connection: keep-alive";
//        $header[] = "Keep-Alive: 300";
//        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
//        $header[] = "Accept-Language: en-us,en;q=0.5";
//        $header[] = "Cookie:FPSSCI=k7lq3isb580b168a2g4a529jg2; YII_CSRF_TOKEN=496cd7f7bb9600b7eada9f8e81f60f3b458e33a4s%3A88%3A%22VXBaNGVkNDc1WWQzSnZNWTVPZVZPbmlVM09oQzZhNjciEMuCd2qt4X5KF21DcN9XgPqx59B9JWGvsOE9tMJjNA%3D%3D%22%3B";
//        $header[] = "Pragma: "; // browsers = blank
//        $header[] = "X_FORWARDED_FOR: " . $ip;
//        $header[] = "REMOTE_ADDR: " . $ip;
//        $header[] = "Host: api-proxy.my.fbs.com";
    
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
            # Get the response
            $response = curl_exec($ch);
            curl_close($ch);
//
//
//            echo $result;die;
            //                            $create = $db->createPaymentFasapay($fp_paidto, $fp_paidby , $fp_amnt ,$fp_fee_amnt , $fp_fee_mode ,  $fp_total ,$fp_currency , $fp_batchnumber ,
            //                                                              $fp_store , $fp_timestamp , $fp_merchant_ref ,$fp_hash , $fp_hash_2 );
            ////                            if ($create) {
            //
            
            $return = array(
                            "status" => 200,
                            "message" => "ok",
                            "response" => $response,
                            "data" => $data
//                            "fp_paidby" => $fp_paidby ,
//                            "fp_amnt" => $fp_amnt ,
//                            "fp_fee_amnt" => $fp_fee_amnt ,
//                            "fp_fee_mode" => $fp_fee_mode ,
//                            "fp_total" => $fp_total ,
//                            "fp_currency" => $fp_currency ,
//                            "fp_batchnumber" => $fp_batchnumber ,
//                            "fp_store" => $fp_store ,
//                            "fp_timestamp" => $fp_timestamp ,
//                            "fp_merchant_ref" => $fp_merchant_ref ,
//                            "fp_hash" => $fp_hash ,
//                            "fp_hash_2" => $fp_hash_2 ,
//                            "fp_hash_2" => $fp_hash_2
                            );
            //
//        } else {
//            $return = array(
//                            "status" => 404,
//                            "message" => "Gagal, mohon coba beberapa saat lagi!"
//                            );
//        }
        //
        //
        //
        echo json_encode($return);
    }
    if ($content == "fasapay") {
        $modeHeader = 0;
        $post = json_decode(file_get_contents("php://input"), true);
        $fp_paidto = $_POST['fp_paidto'];
        $fp_paidby = $_POST['fp_paidby'];
        $fp_amnt = $_POST['fp_amnt'];
        $fp_fee_amnt = $_POST['fp_fee_amnt'];
        $fp_fee_mode = $_POST['fp_fee_mode'];
        $fp_total = $_POST['fp_total'];
        $fp_currency= $_POST['fp_currency'];
        $fp_batchnumber = $_POST['fp_batchnumber'];
        $fp_store = $_POST['fp_store'];
        $fp_timestamp = $_POST['fp_timestamp'];
        $fp_merchant_ref = $_POST['fp_merchant_ref'];
        $fp_hash= $_POST['fp_hash'];
        $fp_hash_2 = $_POST['fp_hash_2'];
        $trx_id = $_POST['trx_id'];
         $fp_custom = $_POST['fp_custom'];
        
        $create = $db->createPaymentFasapay($fp_paidto, $fp_paidby , $fp_amnt ,$fp_fee_amnt , $fp_fee_mode ,  $fp_total ,$fp_currency , $fp_batchnumber ,
                                            $fp_store , $fp_timestamp , $fp_merchant_ref ,$fp_hash , $fp_hash_2 , $trx_id , $fp_custom );
        if ($create) {
            
            
            $return = array(
                            "status" => 200,
                            "message" => "ok",
                            //"data" => $rows
                            );
            
        } else {
            $return = array(
                            "status" => 404,
                            "message" => "Gagal, mohon coba beberapa saat lagi!"
                            );
        }
        
        
        
        echo json_encode($return);
    }
    
    if ($content == "register") {
        $modeHeader = 0;
        $referral_by = "";

        $post = json_decode(file_get_contents("php://input"), true);
        $firstname = $post['FirstName'];
        $lastname = $post['LastName'];
        $email = $post['Email'];
        $password = $post['Password'];
        $firebase_id = $post['FirebaseID'];
        $firebase_time = $post['FirebaseTime'];
        $device_brand = $post['DeviceBrand'];
        $device_model = $post['DeviceModel'];
        $device_serial = $post['DeviceSerial'];
        $device_os = $post['DeviceOS'];
        $referral_by = $post['ReferralBy'];

        if (isset($email) && isset($password)) {

            $user = $db->checkUserRegister($email);
            if (!$user) {

                $create = $db->createUser($firstname, $lastname, $email, $password, $firebase_id, $firebase_time, $device_brand, $device_model, $device_serial, $device_os, $referral_by);
                if ($create) {
                    $return = array(
                        "status" => 200,
                        "message" => "ok"
                    );
                } else {
                    $return = array(
                        "status" => 404,
                        "message" => "Registrasi gagal, mohon coba beberapa saat lagi!"
                    );
                }

            } else {
                $return = array(
                    "status" => 404,
                    "message" => "Maaf, data anda telah terdaftar. Mohon login!"
                );
            }
        } else {
            $return = array(
                "status" => 404,
                "message" => "Method not found!"
            );
        }
        echo json_encode($return);
    }
	
	 if ($content == "register_toko") {
        $modeHeader = 0;
        $referral_by = "";

        $post = json_decode(file_get_contents("php://input"), true);
        $user_id = $post['UserID'];
		$email = $post['Email'];
        $toko_name = $post['Name'];
        $address = $post['Address'];
        $phone= $post['Phone'];
        $category_toko_id = $post['CategoryTokoID'];
     

        if (isset($user_id) && isset($toko_name) && isset($address) && isset($phone) && isset($category_toko_id)) {

            $dataPhone = $db->checkPhoneTokoRegister($phone);
            if (!$dataPhone) {

                $create = $db->createToko($user_id, $toko_name ,$address , $phone , $category_toko_id , $email);
                if ($create) {
					
					  $getData = $db->getUserByEmail($email);
				

                    while ($row = $getData->fetch_assoc()) {
                        $rows[] = $row;
                    }
					
                    $return = array(
                        "status" => 200,					
                        "message" => "Selamat, Pendaftaran Toko Berhasil",
						"data" => $rows
                    );
					
                } else {
                    $return = array(
                        "status" => 404,
                        "message" => "Registrasi gagal, mohon coba beberapa saat lagi!"
                    );
                }

           } else {
                $return = array(
                    "status" => 404,
                    "message" => "Maaf, No Handphone anda telah digunakan di toko lain. Silahkan hubungi Customer Service!"
               );
            }
        } else {
            $return = array(
                "status" => 404,
                "message" => "Method not found!"
            );
        }
        echo json_encode($return);
    }



	
   
	 if ($content == "add_category") {
        $modeHeader = 0;   
        $post = json_decode(file_get_contents("php://input"), true);
        $user_id = $post['UserID'];	
		$category_name = $post['CategoryName'];
		$category_code = $post['CategoryCode'];
		
        if (isset($user_id) && isset($category_name) && isset($category_code)) {

            $checkData = $db->checkCategoryName($category_name ,$category_code );
            if (!$checkData) {
				
		 
                $create = $db->createCategory($user_id, $category_name , $category_code);
                if ($create) {
				
					
                    $return = array(
                        "status" => 200,					
                        "message" => "Kategori Produk berhasil ditambahkan",
						//"data" => $rows
                    );
					
                } else {
                    $return = array(
                        "status" => 404,
                        "message" => "Gagal, mohon coba beberapa saat lagi!"
                    );
                }

           } else {
                $return = array(
                    "status" => 404,
                    "message" => "Data yang anda masukan sudah ada"
               );
            }
        } else {
            $return = array(
                "status" => 404,
                "message" => "Method not found!"
            );
        }
        echo json_encode($return);
    }
	
	 if ($content == "add_brand") {
        $modeHeader = 0;   
        $post = json_decode(file_get_contents("php://input"), true);
        $user_id = $post['UserID'];	
		$brand_name = $post['BrandName'];
		$brand_code = $post['BrandCode'];
		
        if (isset($user_id) && isset($brand_name) && isset($brand_code)) {

            $checkData = $db->checkBrandName($brand_name ,$brand_code );
            if (!$checkData) {
				
		 
                $create = $db->createBrand($user_id, $brand_name , $brand_code);
                if ($create) {
				
					
                    $return = array(
                        "status" => 200,					
                        "message" => "Merk berhasil ditambahkan",
						//"data" => $rows
                    );
					
                } else {
                    $return = array(
                        "status" => 404,
                        "message" => "Gagal, mohon coba beberapa saat lagi!"
                    );
                }

           } else {
                $return = array(
                    "status" => 404,
                    "message" => "Data yang anda masukan sudah ada"
               );
            }
        } else {
            $return = array(
                "status" => 404,
                "message" => "Method not found!"
            );
        }
        echo json_encode($return);
    }
	
	 if ($content == "add_color") {
        $modeHeader = 0;   
        $post = json_decode(file_get_contents("php://input"), true);
        $user_id = $post['UserID'];	
		$color_name = $post['ColorName'];
		$color_code = $post['ColorCode'];
		
        if (isset($user_id) && isset($color_name) && isset($color_code)) {

            $checkData = $db->checkColorName($color_name ,$color_code );
            if (!$checkData) {
				
		 
                $create = $db->createColor($user_id, $color_name , $color_code);
                if ($create) {
				
					
                    $return = array(
                        "status" => 200,					
                        "message" => "Warna berhasil ditambahkan",
						//"data" => $rows
                    );
					
                } else {
                    $return = array(
                        "status" => 404,
                        "message" => "Gagal, mohon coba beberapa saat lagi!"
                    );
                }

           } else {
                $return = array(
                    "status" => 404,
                    "message" => "Data yang anda masukan sudah ada"
               );
            }
        } else {
            $return = array(
                "status" => 404,
                "message" => "Method not found!"
            );
        }
        echo json_encode($return);
    }
	
	 if ($content == "add_variant") {
        $modeHeader = 0;   
        $post = json_decode(file_get_contents("php://input"), true);
        $user_id = $post['UserID'];	
		$color_name = $post['ColorName'];
		$color_code = $post['ColorCode'];
		
        if (isset($user_id) && isset($color_name) && isset($color_code)) {

            $checkData = $db->checkVariantName($color_name ,$color_code );
            if (!$checkData) {
				
		 
                $create = $db->createVariant($user_id, $color_name , $color_code);
                if ($create) {
				
					
                    $return = array(
                        "status" => 200,					
                        "message" => "Warna berhasil ditambahkan",
						//"data" => $rows
                    );
					
                } else {
                    $return = array(
                        "status" => 404,
                        "message" => "Gagal, mohon coba beberapa saat lagi!"
                    );
                }

           } else {
                $return = array(
                    "status" => 404,
                    "message" => "Data yang anda masukan sudah ada"
               );
            }
        } else {
            $return = array(
                "status" => 404,
                "message" => "Method not found!"
            );
        }
        echo json_encode($return);
    }
	
	 if ($content == "add_detail_variant") {
        $modeHeader = 0;   
        $post = json_decode(file_get_contents("php://input"), true);
        $user_id = $post['UserID'];	
		$size_name = $post['SizeName'];
		$size_code = $post['SizeCode'];
		
        if (isset($user_id) && isset($size_name) && isset($size_code)) {

           // $checkData = $db->checkVariantName($color_name ,$color_code );
            //if (!$checkData) {
				
		 
                $create = $db->createDetailVariant($user_id, $size_name , $size_code);
                if ($create) {
				
					
                    $return = array(
                        "status" => 200,					
                        "message" => "Ukuran berhasil ditambahkan",
						//"data" => $rows
                    );
					
                } else {
                    $return = array(
                        "status" => 404,
                        "message" => "Gagal, mohon coba beberapa saat lagi!"
                    );
                }

			//}
		   //else {
               // $return = array(
                 //   "status" => 404,
                 //   "message" => "Data yang anda masukan sudah ada"
               //);
           // }
        } else {
            $return = array(
                "status" => 404,
                "message" => "Method not found!"
            );
        }
        echo json_encode($return);
    }
	
	
	
	
	 if ($content == "add_size") {
        $modeHeader = 0;   
        $post = json_decode(file_get_contents("php://input"), true);
        $user_id = $post['UserID'];	
		$size_name = $post['SizeName'];
		$size_code = $post['SizeCode'];
		
        if (isset($user_id) && isset($size_name) && isset($size_code)) {

            $checkData = $db->checkSizeName($size_name ,$size_code );
            if (!$checkData) {
				
		 
                $create = $db->createSize($user_id, $size_name , $size_code);
                if ($create) {
				
					
                    $return = array(
                        "status" => 200,					
                        "message" => "Ukuran berhasil ditambahkan",
						//"data" => $rows
                    );
					
                } else {
                    $return = array(
                        "status" => 404,
                        "message" => "Gagal, mohon coba beberapa saat lagi!"
                    );
                }

           } else {
                $return = array(
                    "status" => 404,
                    "message" => "Data yang anda masukan sudah ada"
               );
            }
        } else {
            $return = array(
                "status" => 404,
                "message" => "Method not found!"
            );
        }
        echo json_encode($return);
    }
	
	 if ($content == "save_variant") {
        $modeHeader = 0;   
        $post = json_decode(file_get_contents("php://input"), true);
        $user_id = $post['UserID'];	
		
	
			$size_name = $post['SizeName'];
        $color_name= $post['ColorName'];
		
		
       
		
        if (isset($user_id)) {

          //  $checkData = $db->checkUserByUserIDRegister($user_id);
           // if ($checkData) {
				
		 
                $create = $db->createVariant($user_id, $size_name , $color_name);
                if ($create) {
					
					  //$getData = $db->getUserByEmail($email);
				

                    //while ($row = $getData->fetch_assoc()) {
                       // $rows[] = $row;
                    //}
					
                    $return = array(
                        "status" => 200,					
                        "message" => "Variant berhasil ditambahkan",
						//"data" => $rows
                    );
					
                } else {
                    $return = array(
                        "status" => 404,
                        "message" => "Registrasi gagal, mohon coba beberapa saat lagi!"
                    );
                }

           //} else {
               // $return = array(
                 //   "status" => 404,
                   // "message" => "Anda tidak memiliki akses"
             //  );
           // }
        } else {
            $return = array(
                "status" => 404,
                "message" => "Method not found!"
            );
        }
        echo json_encode($return);
    }
    /*Edit by elim*/
    /**
     * Register Google
     * @param : phone, password (JSON)
     * returns data
     */
    if ($content == "register_google") {
        $modeHeader = 0;
        $referral_by = "";

        $post = json_decode(file_get_contents("php://input"), true);
		
        $firstname = $post['FirstName'];
        $lastname = $post['LastName'];
        $firebase_id = $post['FirebaseID'];
        $firebase_time = $post['FirebaseTime'];
        $device_brand = $post['DeviceBrand'];
        $device_model = $post['DeviceModel'];
        $device_serial = $post['DeviceSerial'];
        $device_os = $post['DeviceOS'];
        $google_user_id = $post['GoogleUserID'];
        $email = $post['Email'];

        if (isset($google_user_id) && isset($email)) {

            // $user = $db->checkUserRegister2($email, $google_user_id); //cek table master users by email and google_user_id
            $user = $db->checkUserRegister3($email, $google_user_id);
            $profile_complete = 0;
            if (!isset($user)) {

                $create = $db->createUser2($firstname, $lastname, $firebase_id, $firebase_time, $device_brand, $device_model, $device_serial, $device_os, $google_user_id, $email);
                if ($create) {
                    /*send email html*/
                   // $to = $email;
                   // $subject = 'Registrasi Mikropos';
                    //$message = file_get_contents('../view/invoice_email.php');
                    //$email_content = $db->getConfig('user_email_content_register')->fetch_assoc();
                   // if ($email_content) {
                   //     $message = str_replace('%content%', $email_content['Value'], $message);
                  //  }
                  //  $base_url = $BASE_URL;
                  //  $name = $firstname . ' ' . $lastname;
                   // $title = 'Registrasi Berhasil';
                    //$message = str_replace('%name%', $name, $message);
                    //$message = str_replace('%base_url%', $base_url, $message);
                    //$message = str_replace('%title%', $title, $message);
                    //$sendmail = $dbutil->send_email_html($to, $subject, $message);
                    /*end email html*/

                    $return = array(
                        "status" => 200,
                        "message" => "Registrasi sukses",
                        "data" => $create,
                        "profile_complete" => $profile_complete,
                        "is_forgot" => false,
                        "send_mail" => $sendmail

                    );
                } else {
                    $return = array(
                        "status" => 404,
                        "message" => "Registrasi gagal, mohon coba beberapa saat lagi!"
                    );
                }

            } else {
                //login
                // $data = $db->checkUserLoginGoogle($email, $google_user_id);
                $data = $db->checkUserLoginGoogle2($email, $google_user_id);
                if (isset($firebase_id)) {
                    $db->updateFirebase2($email, $google_user_id, $firebase_id);
                }

                if ($data) {
                    $is_complete = $db->checkIsProfileComplete2($email, $google_user_id);

                    // while($row = $data->fetch_assoc()){
                    // 	if($is_complete ==   1 && $row['Active'] == 1){
                    // 		$profile_complete = 1;
                    // 	}
                    // 	$rows[] = $row;
                    // }

                    while ($row = $data->fetch_assoc()) {
                        if ($is_complete == 1 && $row['Active'] == 1) {
                            $profile_complete = 1;
                        }
                        $rows[] = $row;
                    }

                    $return = array(
                        "status" => 200,
                        "message" => "Login success",
                        "is_forgot" => false,
                        "profile_complete" => $profile_complete,
                        "data" => $rows
                    );
                } else {
                    $return = array(
                        "status" => 404,
                        "message" => "Login gagal, karena email atau google user id tidak ditemukan !"
                    );
                }
            }
        } else {
            $return = array(
                "status" => 404,
                "message" => "Method not found!"
            );
        }
        echo json_encode($return);
    }

    /*End edit by elim*/

    /**
     * API Login
     * @param : email, password (JSON)
     * returns data
     */
    if ($content == "login") {
        $modeHeader = 0;
        $post = json_decode(file_get_contents("php://input"), true);
        $email = $post['Email'];
        $password = $post['Password'];
		$firebase_id = $post['FirebaseID'];

        if (isset($email) && isset($password) ) {

            $user = $db->checkUserLogin($email, $password);
            if ($user) {

            

                $getData = $db->getUserByEmail($email);
                if ($getData != null) {

                    while ($row = $getData->fetch_assoc()) {
                        $rows[] = $row;
                    }

                    $is_complete = $db->checkIsProfileComplete($email);
				 
                    $return = array(
                        "status" => 200,
                        "message" => "ok",
                        "is_forgot" => false,
                        "profile_complete" => $is_complete,
                        "data" => $rows
                    );
                } else {
                    $return = array(
                        "status" => 404,
                        "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                    );
                }

            } else {
                //check forgot password
                $check_forgot = $db->checkUserLoginByForgot($email, $password);
                if ($check_forgot) {

                    $db->updateFirebase($email, $firebase_id);

                    $getData = $db->getUserByEmail($email);
                    if ($getData != null) {

                        while ($row = $getData->fetch_assoc()) {
                            $rows[] = $row;
                        }

                        $is_complete = $db->checkIsProfileComplete($email);

                        $return = array(
                            "status" => 200,
                            "message" => "ok",
                            "is_forgot" => true,
                            "profile_complete" => $is_complete,
                            "data" => $rows
                        );
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                        );
                    }
                } else {
                    $return = array(
                        "status" => 404,
                        "message" => "Email atau Password Salah!"
                    );
                }
            }
        } else {
            $return = array(
                "status" => 404,
                "message" => "Method not found!"
            );
        }
        echo json_encode($return);
    }

    /**
     * API Confirm Activation Code
     * @param : phone, code (JSON)
     * returns data
     */
    if ($content == "confirm_code") {
        $modeHeader = 0;
        $post = json_decode(file_get_contents("php://input"), true);

        $phone = $post['Phone'];
        $code = $post['ActivationCode'];

        if (isset($code) && isset($phone) && $phone != "0" && $phone != "") {

            $check = $db->checkActivationCode($phone, $code);
            if ($check) {

                $getData = $db->getUserByEmail($phone);
                if ($getData != null) {

                    while ($row = $getData->fetch_assoc()) {
                        $rows[] = $row;
                    }

                    $return = array(
                        "status" => 200,
                        "message" => "ok",
                        "data" => $rows
                    );
                } else {
                    $return = array(
                        "status" => 404,
                        "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                    );
                }
            } else {
                $return = array(
                    "status" => 404,
                    "message" => "Mohon masukan kode yang valid!"
                );
            }
        } else {
            $return = array(
                "status" => 404,
                "message" => "Method not found!"
            );
        }

        echo json_encode($return);
    }

    /**
     * API Resend Activation Code
     * @param : phone (JSON)
     * returns data
     */
    if ($content == "resend_code") {
        $modeHeader = 0;
        $post = json_decode(file_get_contents("php://input"), true);
        $phone = $post['Phone'];

        if (isset($phone) && $phone != "0" && $phone != "") {

            $send = $db->resendActivationCode($phone);
            if ($send) {
                $return = array(
                    "status" => 200,
                    "message" => "OK"
                );
            } else {
                $return = array(
                    "status" => 404,
                    "message" => "Gagal mengirim ulang kode!"
                );
            }

        } else {
            $return = array(
                "status" => 404,
                "message" => "Method not found!"
            );
        }
        echo json_encode($return);
    }




 
    /**
     * API Forgot Password
     * @param : Phone (JSON)
     * returns data
     */
    if ($content == "forgot_password") {
        $modeHeader = 0;
        $post = json_decode(file_get_contents("php://input"), true);
        $phone = $post['Phone'];

        if (isset($phone)) {

            $check = $db->checkUserRegister($phone);
            if ($check) {

                $update_pass = $db->forgotPassword($phone);
                if ($update_pass) {

                    $return = array(
                        "status" => 200,
                        "message" => "Password baru telah dikirim ke no handphone anda"
                    );
                } else {
                    $return = array(
                        "status" => 404,
                        "message" => "Gagal memproses password anda!"
                    );
                }

            } else {
                $return = array(
                    "status" => 404,
                    "message" => "Data tidak ditemukan, pastikan data yang anda masukan benar!"
                );
            }
        } else {
            $return = array(
                "status" => 404,
                "message" => "Method not found!"
            );
        }
        echo json_encode($return);
    }

    /**
     * API Get Bank Accounts
     * @param : none
     * returns data
     */
    if ($content == "bank_account") {
        $modeHeader = 0;
        $getData = $db->getBankAccount();
        if ($getData != null) {

            while ($row = $getData->fetch_assoc()) {
                $rows[] = $row;
            }

            $return = array(
                "status" => 200,
                "message" => "ok",
                "data" => $rows
            );
        } else {
            $return = array(
                "status" => 404,
                "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
            );
        }

        echo json_encode($return);
    }

    /**
     * API BCA
     * @param :
     * returns data
     */
    if ($content == 'bca_banking_corporate_transfer') {
        $modeHeader = 0;
        $post = json_decode(file_get_contents("php://input"), true);
        $amount = $post["TotalPayment"];
        // $nomorakun 				= $post["AccountNoFrom"] ? $post["AccountNoFrom"] : '0201245680';
        $nomorakun = $post["AccountNoFrom"];
        // $nomordestinasi 		= $post["AccountNoDestination"] ? $post["AccountNoDestination"] : '0201245681';
        $nomordestinasi = $post["AccountNoDestination"];
        $nomorPO = $post["OrderNo"];
        $nomorTransaksiID = $post["TransactionID"];


        // if(isset($amount) && isset($nomorakun) && isset($nomordestinasi) && isset($nomorPO) && isset($nomorTransaksiID)){
        $res = $dbbca->bca_fund_transfer($amount, $nomorakun, $nomordestinasi, $nomorPO, $nomorTransaksiID);
        $res = json_encode($res);
        echo $res;
        $body = json_decode($res)->body;
        $headers = json_decode($res)->headers;
        // var_dump($headers);

        $return = array(
            "status" => 200,
            "data" => $body,
            "header" => $headers
        );
        // }else{
        // 	$return = array(
        // 			"status" => 404,
        // 			"message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!",
        // 	);
        // }
        // echo json_encode($return);

    }

    /**
     * API BCA
     * @param :
     * returns data
     */
    if ($content == 'update_order_transfer') {
        $modeHeader = 0;
        // $post 		= json_decode(file_get_contents("php://input"), true);

        // $data = $db->getOrderPendingTransfer();
        $data = 1;
        if ($data) {
            // $data = $data->fetch_assoc();

            //get account statement bca
            $nomorakun = '0201245680';
            $bank_account = $db->getBankAccount(1);
            if (isset($bank_account)) {
                $bank_account = $bank_account->fetch_assoc();
                // $nomorakun 		= $bank_account['AccountNumber'];
            }
            // echo $nomorakun;
            $date = date("Y-m-d");
            $startdate = '2017-06-25';
            $enddate = '2017-06-25';
            // echo $startdate.' - '.$enddate;
            $data2 = json_encode($dbbca->account_statement($nomorakun, $startdate, $enddate));
            // echo $data2;
            // $data2 = json_encode($dbbca2->index());
            $body = json_decode($data2)->body;
            // var_dump($body);
            // while($row = $data){
            // 	foreach($body->data as $b){
            // 	// 	[0]=>
            //  // object(stdClass)#30 (6) {
            //  //   ["TransactionDate"]=>
            //  //   string(5) "29/08"
            //  //   ["BranchCode"]=>
            //  //   string(4) "0000"
            //  //   ["TransactionType"]=>
            //  //   string(1) "D"
            //  //   ["TransactionAmount"]=>
            //  //   string(10) "9000000.00"
            //  //   ["TransactionName"]=>
            //  //   string(17) "TRSF E-BANKING DB"
            //  //   ["Trailer"]=>
            //  //   string(58) "2808/ACDFT/WS950519000000.00 REK KORAN DARI GIRO KE TAPRES"
            //  // }
            // 		if($row['TotalPayment'] == $b['TransactionAmount'] && $b['TransactionAmount'] != 'PEND'){
            // 			$order_status_id 	= 4;
            // 			$data3  			= $db->updateNrzOrderStatus($row['OrderID'], $order_status_id);
            // 		}
            // 	}

            // 	// $res 	= json_encode($res);
            // 	// $body 	= json_decode($res)->body;
            // 	// $headers = json_decode($res)->headers;
            // 	// var_dump($headers);

            // }

            $return = array(
                "status" => 200,
                "data" => $body,
                "header" => $headers
            );
        } else {
            $return = array(
                "status" => 404,
                "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
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
                /**
                 * API Edit User Profile
                 * @param : user_id, firstname, lastname, phone, email, birthdate, gender, nik, birthplace, address (JSON)
                 * returns data
                 */
                if ($content == "edit_profile") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $userid_header;
                    $firstname = $post['FirstName'];
                    $lastname = $post['LastName'];
                    $phone = $post['Phone'];
                    $email = $post['Email'];
                    $birthdate = $post['BirthDate'];
                    $gender = $post['Gender'];
                    $nik = $post['NIK'];
                    $birthplace = $post['BirthPlace'];
                    $address = $post['Address'];
                    $height = $post['Height'];
                    $weight = $post['Weight'];
                    $referral_by = $post['ReferralBy'];

                    if (isset($user_id) && isset($firstname) && isset($lastname) && isset($phone) && isset($email) && isset($birthdate) && isset($gender) && isset($birthplace) && isset($address) && isset($height) && isset($weight) && isset($referral_by)) {

                        $data = $db->getUserByEmailExceptUserID($user_id, $phone);
                        $data2 = $db->getUserByEmailExceptUserID($user_id, $email);
                        $data3 = $db->getUserByID($user_id);
                        $returnval = 1;
                        $flagEmail = "";
                        $sendmail = 0;

                        $Referral_by = '';
                        $data4 = $db->checkReferralByExist($user_id, $referral_by);
                        if ($data4) {
                            $returnval = 1;
                            $data4 = $data4->fetch_assoc();
                            $Referral_by = $data4['ReferralBy'];
                        } else {
                            $returnval = 0;
                            $return = array(
                                "status" => 404,
                                "message" => "Maaf, Anda tidak dapat merubah ReferralBy untuk yang ke-2 kali atau mungkin Anda memasukkan ReferralBy Code yang salah!"
                            );
                        }

                        if ($data) {
                            $returnval = 0;
                            $return = array(
                                "status" => 404,
                                "message" => "Gagal mengupdate profil, Phone sudah digunakan user lain !"
                            );
                        }

                        if ($data2) {
                            $returnval = 0;
                            $return = array(
                                "status" => 404,
                                "message" => "Gagal mengupdate profil, Email sudah digunakan user lain !"
                            );
                        }

                        if ($data3) {
                            $data3 = $data3->fetch_assoc();
                            $flagEmail = $data3['Email'];
                        }


                        if ($returnval == 1) {
                            if ($loginGoogle == 0) {
                                $update = $db->updateProfile($user_id, $firstname, $lastname, $phone, $email, $birthdate, $gender, $nik, $birthplace, $address, $height, $weight, $Referral_by);

                                if ($update) {
                                    $is_complete = $db->checkIsProfileComplete($phone);

                                    if ($flagEmail == "") {
                                        /*send email html*/
                                        $to = $email;
                                        $subject = 'Registrasi User VTal';
                                        $message = file_get_contents('../view/invoice_email.php');
                                        $email_content = $db->getConfig('user_email_content_register')->fetch_assoc();
                                        if ($email_content) {
                                            $message = str_replace('%content%', $email_content['Value'], $message);
                                        }
                                        $base_url = $BASE_URL;
                                        $name = $firstname . ' ' . $lastname;
                                        $title = 'Registrasi User VTal Berhasil';
                                        $message = str_replace('%name%', $name, $message);
                                        $message = str_replace('%base_url%', $base_url, $message);
                                        $message = str_replace('%title%', $title, $message);
                                        $sendmail = $dbutil->send_email_html($to, $subject, $message);
                                        /*end email html*/
                                    }
                                    $return = array(
                                        "status" => 200,
                                        "message" => "Profil berhasil diupdate",
                                        "profile_complete" => $is_complete,
                                        "send_mail" => $sendmail,
                                        "Active" => 1
                                    );
                                } else {
                                    $return = array(
                                        "status" => 404,
                                        "message" => "Gagal mengupdate profil!"
                                    );
                                }
                            } elseif ($loginGoogle == 1) {
                                $update = $db->updateProfileSendSMS($user_id, $firstname, $lastname, $phone, $email, $birthdate, $gender, $nik, $birthplace, $address, $height, $weight, $Referral_by);

                                if (isset($update) && $update != 'not_found') {
                                    $profile_complete = 0;
                                    $is_complete = $db->checkIsProfileComplete($phone);
                                    if ($update['Active'] == 1 && $is_complete == 1) {
                                        $profile_complete = 1;
                                    }
                                    if ($flagEmail == "") {
                                        /*send email html*/
                                        $to = $email;
                                        $subject = 'Registrasi User VTal';
                                        $message = file_get_contents('../view/invoice_email.php');
                                        $email_content = $db->getConfig('user_email_content_register')->fetch_assoc();
                                        if ($email_content) {
                                            $message = str_replace('%content%', $email_content['Value'], $message);
                                        }
                                        $base_url = $BASE_URL;
                                        $name = $firstname . ' ' . $lastname;
                                        $title = 'Registrasi User VTal Berhasil';
                                        $message = str_replace('%name%', $name, $message);
                                        $message = str_replace('%base_url%', $base_url, $message);
                                        $message = str_replace('%title%', $title, $message);
                                        $sendmail = $dbutil->send_email_html($to, $subject, $message);
                                        /*end email html*/
                                    }
                                    $return = array(
                                        "status" => 200,
                                        "message" => "Profil berhasil diupdate",
                                        "profile_complete" => $profile_complete,
                                        "Active" => $update['Active'],
                                        "send_mail" => $sendmail
                                    );
                                } elseif ($update == 'not_found') {
                                    $return = array(
                                        "status" => 404,
                                        "message" => "Profil berhasil diupdate, tetapi data user tidak ditemukan !"
                                    );
                                } else {
                                    $return = array(
                                        "status" => 404,
                                        "message" => "Gagal mengupdate profil!"
                                    );
                                }
                            }
                        }

                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return);
                }

                /**
                 * API Change Password
                 * @param : UserID, OldPassword, NewPassword (JSON)
                 * returns data
                 */
                if ($content == "change_password") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $post['UserID'];
                    $old_Password = $post['OldPassword'];
                    $new_password = $post['NewPassword'];

                    if (isset($user_id) && isset($old_Password) && isset($new_password)) {

                        $check_oldpassword = $db->checkUserPassword($user_id, $old_Password);
                        if ($check_oldpassword) {

                            $update_pass = $db->updatePassword($user_id, $new_password);
                            if ($update_pass) {

                                $return = array(
                                    "status" => 200,
                                    "message" => "Password berhasil diubah"
                                );
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Gagal mengubah password, mohon coba beberapa saat lagi!"
                                );
                            }

                        } else {

                            $check_forgotpassword = $db->checkUserPasswordForgot($user_id, $old_Password);
                            if ($check_forgotpassword) {

                                $update_pass = $db->updatePassword($user_id, $new_password);
                                if ($update_pass) {

                                    $return = array(
                                        "status" => 200,
                                        "message" => "Password berhasil diubah"
                                    );
                                } else {
                                    $return = array(
                                        "status" => 404,
                                        "message" => "Gagal mengubah password, mohon coba beberapa saat lagi!"
                                    );
                                }
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Password lama anda salah!"
                                );
                            }
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return);
                }

                /**
                 * API Upload Profile Image
                 * @param : Image Byte (Multipart Data)
                 * returns data
                 */
                if ($content == "upload_image") {

                    $image = $_FILES['image'];
                    $rand = $db->randomPassword(4);

                    if (isset($image)) {
                        // $uploaddir = '/home/vtalid01/api-dev.v-tal.id/v1/images/patients/';
                        $uploaddir = $uploaddir . '/patients/';
                        $uploadfile = $uploaddir . basename($_FILES['image']['name']);

                        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
                            $return = array(
                                "status" => 200,
                                "message" => "Upload photo success!",
                                "signature" => $rand
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Upload photo failed!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API Get Bank Account
                 * @param : none
                 * returns data
                 */
                if ($content == "payment_type") {

                    $getData = $db->getPaymentType();
                    if ($getData != null) {
                        $data = $db->getPaymentAccount();

                        $payment_account = [];
                        if ($data) {
                            while ($pa = $data->fetch_assoc()) {
                                $payment_account[] = $pa;
                            }
                        }
                        while ($row = $getData->fetch_assoc()) {
                            //push payment_account ke bank
                            if ($row['PaymentTypeID'] == 2) {
                                $row['payment_account'] = $payment_account;
                            } else {
                                $row['payment_account'] = [];
                            }
                            $rows[] = $row;
                        }

                        $return = array(
                            "status" => 200,
                            "message" => "ok",
                            "data" => $rows
                        );
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API Get Bank Account Doctor
                 * @param : none
                 * returns data
                 */
                if ($content == "payment_type_doctor") {

                    $getData = $db->getPaymentType(1);
                    if ($getData != null) {
                        $data = $db->getPaymentAccount();

                        $payment_account = [];
                        if ($data) {
                            while ($pa = $data->fetch_assoc()) {
                                $payment_account[] = $pa;
                            }
                        }
                        while ($row = $getData->fetch_assoc()) {
                            //push payment_account ke bank
                            if ($row['PaymentTypeID'] == 2) {
                                $row['payment_account'] = $payment_account;
                            } else {
                                $row['payment_account'] = [];
                            }
                            $rows[] = $row;
                        }

                        $return = array(
                            "status" => 200,
                            "message" => "ok",
                            "data" => $rows
                        );
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API Get Nurse Categories
                 * @param : none
                 * returns data
                 */
                if ($content == "categories") {

                    $getData = $db->getNurseCategories();
                    if ($getData != null) {

                        while ($row = $getData->fetch_assoc()) {
                            $rows[] = $row;
                        }

                        $return = array(
                            "status" => 200,
                            "message" => "ok",
                            "data" => $rows
                        );
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                        );
                    }

                    echo json_encode($return);
                }

/**
     * API Resend Activation Code
     * @param : phone (JSON)
     * returns data
     */
    if ($content == "deposit") {
        $modeHeader = 0;
        $post = json_decode(file_get_contents("php://input"), true);
		$payment_system_id = $post['payment_system_id']; 
        $ccard_id = $post['ccard_id'];
		$account_id = $post['account_id'];
		$amount = $post['amount'];
		$bonus_percent = $post['bonus_percent'];
		
        if (isset($payment_system_id) && isset($ccard_id)) {

      

				$data = [
				'payment_system_id' => $payment_system_id, // Receivers phone
				'ccard_id' => $ccard_id, // Message
				'account_id' => $account_id, // Message
				'amount' => $amount, // Message
				'bonus_percent' => $bonus_percent, // Message
				];
				$json = json_encode($data); // Encode data to JSON
				// URL for request POST /message
				$url = 'https://account.forex4you.com/en/trader-account/balance/deposit';
				// Make a POST request
				$options = stream_context_create(['http' => [
				'method'  => 'POST',
				'header'  => 'Content-type: application/json',
				'content' => $json
				]
				]);
				// Send a request
				$result = file_get_contents($url, false, $options);
				
                $return = array(
                    "status" => 200,
                    "message" => "OK",
					"send" => true,
					"id" => $result
                );
         

        } else {
            $return = array(
                "status" => 404,
                "message" => "Method not found!"
            );
        }
		
        echo json_encode($return);
    }
	
	/**
     * API Resend Activation Code
     * @param : phone (JSON)
     * returns data
     */
    if ($content == "deposit_confrim") {
        $modeHeader = 0;
        $post = json_decode(file_get_contents("php://input"), true);
		$fp_acc = "FP05267"; 
        $fp_acc_from = $post['fp_acc_from'];
		$fp_store = "Forex4you";
		$fp_item = $post['fp_item'];
		$fp_amnt = $post['fp_amnt'];
		$fp_fee_mode = "FiR";
		$fp_currency = "USD";
		$fp_comments = $post['fp_comments'];
		$fp_merchant_ref = $post['fp_merchant_ref'];
		
        if (isset($fp_acc) && isset($fp_acc_from)) {

      

				$data = [
				'fp_acc' => $fp_acc, // Receivers phone
				'fp_acc_from' => $fp_acc_from, // Message
				'fp_store' => $fp_store, // Message
				'fp_item' => $fp_item, // Message
				'fp_amnt' => $fp_amnt, // Message
				'fp_fee_mode' => $fp_fee_mode, // Message
				'fp_currency' => $fp_currency, // Message
				'fp_comments' => $fp_comments, // Message
				'fp_merchant_ref' => $fp_merchant_ref, // Message
				];
				$json = json_encode($data); // Encode data to JSON
				// URL for request POST /message
				$url = 'https://payment.eglobal-forex.com/en/payment-system/fasapay';
				// Make a POST request
				$options = stream_context_create(['http' => [
				'method'  => 'POST',
				'header'  => 'Content-type: application/json',
				'content' => $json
				]
				]);
				// Send a request
				$result = file_get_contents($url, true, $options);
				
                $return = array(
                    "status" => 200,
                    "message" => "OK",
					"send" => true,
					"id" => $result
                );
         

        } else {
            $return = array(
                "status" => 404,
                "message" => "Method not found!"
            );
        }
		
        echo json_encode($return);
    }
	    /**
                 * API Get Nurse Categories
                 * @param : none
                 * returns data
                 */
                if ($content == "loadData") {
		$modeHeader = 0;
        $post = json_decode(file_get_contents("php://input"), true);
		$user_id =$post['UserID']; 
		
                    $getData = $db->loadData($user_id);
                    if ($getData != null) {

                        while ($row = $getData->fetch_assoc()) {
                            $rows[] = $row;
                        }

                        $return = array(
                            "status" => 200,
                            "message" => "ok",
                            "data" => $rows
                        );
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                        );
                    }

                    echo json_encode($return);
                }

	
                /**
                 * API Get Doctor Categories
                 * @param : none
                 * returns data
                 */
                if ($content == "doctor_categories") {

                    $getData = $db->getDoctorCategories();
                    if ($getData != null) {

                        while ($row = $getData->fetch_assoc()) {
                            $rows[] = $row;
                        }

                        $return = array(
                            "status" => 200,
                            "message" => "ok",
                            "data" => $rows
                        );
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                        );
                    }

                    echo json_encode($return);
                }



               
                /**
                 * API Get Home Data (Banners & Articles)
                 * @param : none
                 * returns data
                 */
                if ($content == "home") {
					$modeHeader = 0;
					$post = json_decode(file_get_contents("php://input"), true);
					$user_id =$post['UserID']; 
					$email =$post['Email']; 
                    //$getBanners = $db->getBanners();
          
					$getProfile = $db->getProfile($user_id);
                    if ($getProfile != null) {

                       // $total = mysqli_num_rows($getBanners);
                      //  while ($row = $getBanners->fetch_assoc()) {
                      //      $rows[] = $row;
                      //  }
        
						
						 $rows3 = array();
                        $total3 = mysqli_num_rows($getProfile);
                        while ($row3 = $getProfile->fetch_assoc()) {
                            $rows3[] = $row3;
							
                        }
						
				

						$is_complete = $db->checkIsProfileComplete($email);
				 
                 
					
                        $return = array(
                            "status" => 200,
                            "message" => "ok",
							"profile_complete" => $is_complete,                   
							"profile" => $rows3
                        );
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                        );
                    }

                    echo json_encode($return);
                }


                /**
                 * API Get Articles
                 * @param : none
                 * returns data
                 */
                if ($content == "articles") {

                    $getData = $db->getArticles();

                    if ($getData != null) {

                        while ($row = $getData->fetch_assoc()) {
                            $rows[] = $row;
                        }

                        $total = mysqli_num_rows($getData);

                        $return = array(
                            "status" => 200,
                            "message" => "ok",
                            "total_rows" => $total,
                            "data" => $rows
                        );
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                        );
                    }

                    echo json_encode($return);
                }
				
		
				

 /**
                 * API Get Doctor Categories
                 * @param : none
                 * returns data
                 */
                if ($content == "GetMessageSetting") {
				    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $post['UserID'];
					
                    if (isset($user_id)) {
                        $getData = $db->getMessageSetting($user_id);
                        if ($getData != null) {

                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }

                            $total = mysqli_num_rows($getData);

                            $return = array(
                                "status" => 200,
                                "message" => "ok",
								"total_rows" => $total,
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Oops, sepertinya ada yang salah!"
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API Get Article Detail
                 * @param : none
                 * returns data
                 */
                if ($content == "article_detail") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $id = $post['ArticleID'];

                    if (isset($id)) {
                        $getData = $db->getArticleDetail($id);
                        if ($getData != null) {

                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }

                            $total = mysqli_num_rows($getData);

                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Oops, sepertinya ada yang salah!"
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API Get Featured Promos
                 * @param : none
                 * returns data
                 */
                if ($content == "featured_promo") {

                    $getData = $db->getFeaturedPromos();

                    if ($getData != null) {

                        while ($row = $getData->fetch_assoc()) {
                            $rows[] = $row;
                        }

                        $total = mysqli_num_rows($getData);

                        $return = array(
                            "status" => 200,
                            "message" => "ok",
                            "total_rows" => $total,
                            "data" => $rows
                        );
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API Get All Promos
                 * @param : none
                 * returns data
                 */
                if ($content == "promo") {

                    $getData = $db->getPromos();

                    if ($getData != null) {

                        while ($row = $getData->fetch_assoc()) {
                            $rows[] = $row;
                        }

                        $total = mysqli_num_rows($getData);

                        $return = array(
                            "status" => 200,
                            "message" => "ok",
                            "total_rows" => $total,
                            "data" => $rows
                        );
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API Get Promo Detail
                 * @param : none
                 * returns data
                 */
                if ($content == "promo_detail") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $id = $post['PromoID'];
                    if (isset($id)) {
                        $getData = $db->getPromoDetail($id);
                        if ($getData != null) {

                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }

                            $total = mysqli_num_rows($getData);

                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Oops, sepertinya ada yang salah!"
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API Post Promo Order
                 * @param : none
                 * returns data
                 */
                if ($content == "promo_order") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    $promo_id = $post['ProductID'];
                    $user_id = $post['UserID'];
                    $alamat_pengiriman = $post['AlamatPengiriman'];
                    $transport = $post['Transport'];
                    $total = $post['total_payment'];
                    $unique_code = $post['KodeUnik'];
                    $payment_type_id = $post['PaymentTypeID'];


                    if (isset($promo_id) && isset($user_id) && isset($alamat_pengiriman) && isset($transport) && isset($total) && isset($unique_code) && isset($payment_type_id)) {

                        $process = $db->processPendingOrderPromo($promo_id, $user_id, $alamat_pengiriman, $transport, $total, $unique_code, $payment_type_id);
                        if ($process) {
                            $get_id = $db->getPendingPromoOrderID($promo_id, $user_id);
                            if ($get_id != null) {
                                $return = array(
                                    "status" => 200,
                                    "message" => "Order Success!",
                                    "OrderID" => $get_id
                                );

                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Order failed!"
                                );
                            }

                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Order failed!"
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

                if ($content == "image_product") {

                    $post = json_decode(file_get_contents("php://input"), true);
                   // $user_id = $post['UserID'];
                    $product_id = $post['ProductID'];
                    if (isset($product_id)) {

                        $getData = $db->getImageProduct($product_id);
                        if ($getData != null) {

                            $total_rows = mysqli_num_rows($getData);

                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }


                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "total_rows" => $total_rows,
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 200,
                                "total_rows" => "0",
                                "message" => "Data tidak tersedia!"
                            );
                        }

                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }

                    echo json_encode($return);
                }

                if ($content == "history_promo_order_detail") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $post['UserID'];
                    $promo_id = $post['ProductID'];
                    if (isset($user_id)) {

                        $getData = $db->getPromoOrderDetail($promo_id, $user_id);
                        if ($getData != null) {

                            $total_rows = mysqli_num_rows($getData);

                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }


                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "total_rows" => $total_rows,
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 200,
                                "total_rows" => "0",
                                "message" => "Data tidak tersedia!"
                            );
                        }

                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API Process Payment - Deal with this promo - Waiting payment
                 * @param : PromoOrderID,PaymentTypeID,KodeUnik,Transport,TotalPayment (JSON)
                 * returns data
                 */
                if ($content == "process_payment_promo") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $promo_order_id = $post['OrderID'];
                    $payment_type_id = $post['PaymentTypeID'];
                    $unique_code = $post['KodeUnik'];
                    $transport = str_replace('.', '', str_replace('Rp. ', '', $post['Transport']));
                    $totalpayment = str_replace('.', '', str_replace('Rp. ', '', $post['TotalPayment']));
                    if (isset($promo_order_id) && isset($payment_type_id)) {

                        $check = $db->checkPromoOrderExist($promo_order_id);
                        if ($check) {

                            $process = $db->processPaymentPromo($promo_order_id, $payment_type_id, $unique_code);
                            // $process = true;
                            if ($process) {

                                $getData = $db->getPromoOrderData($promo_order_id);
                                while ($row = $getData->fetch_assoc()) {
                                    $rows[] = $row;
                                }


                                if ($getData) {
                                    /*send email html to patient*/
                                    $to = $rows[0]['user_email'];
//                                    $to = "mahmuddinnf@gmail.com";  //testing only
                                    $subject = 'Permintan Promo Diterima';
                                    $message = file_get_contents('../view/invoice_email.php');
                                    $email_content = $db->getConfig('user_email_content_order_waiting_payment_promo')->fetch_assoc();
                                    if ($email_content) {
                                        $message = str_replace('%content%', $email_content['Value'], $message);
                                    }
                                    $rand = $post['TotalPayment'];
                                    $name = $rows[0]['user_firstname'] . ' ' . $rows[0]['user_lastname'];
                                    $title = 'PERMINTAAN PROMO DITERIMA';
                                    $message = str_replace('%name%', $name, $message);
                                    $message = str_replace('%rand%', $rand, $message);
                                    $message = str_replace('%title%', $title, $message);
                                    $sendmail = $dbutil->send_email_html($to, $subject, $message);
                                    /*end email html to patient*/

//                                    if ($rows[0]['OwnerKategori'] == 'apt_pharmacies') {
//                                        /*send email html to pharmacy*/
//                                        // print_r($rows[0]['OwnerID']);die();
//                                        $getEmail = $db->getPharmacyDetail($rows[0]['OwnerID']);
//                                        if ($check) {
//                                            while ($row = $getEmail->fetch_assoc()) {
//                                                $rows2[] = $row;
//                                            }
//
//                                            $to = $rows2[0]['Email'];
////                                            $to = "mahmuddin.vtal@gmail.com";  //testing only
//                                            $subject = 'Pesanan Permintaan Barang Promo';
//                                            $message = file_get_contents('../view/promo_email_order.php');
//
//                                            $name = $rows[0]['user_firstname'] . ' ' . $rows[0]['user_lastname'];
//                                            $alamat = $rows[0]['AlamatPengiriman'];
//                                            $pharmacy = $rows2[0]['Name'];
//                                            $order_no = $rows[0]['PromoOrderNo'];
//                                            $barang = $rows[0]['Title'];
//                                            $harga_barang = (int)($rows[0]['TotalPayment'] - $rows[0]['KodeUnik']);
//                                            $order_date = date('d-m-Y H:i:s', strtotime($rows[0]['PromoOrderDate']));
//                                            $title = 'PERMINTAAN PESANAN BARANG "' . $rows[0]['Title'] . '"';
//                                            $message = str_replace('%name%', $name, $message);
//                                            $message = str_replace('%alamat_pengiriman%', $alamat, $message);
//                                            $message = str_replace('%penjual%', $pharmacy, $message);
//                                            $message = str_replace('%order_no%', $order_no, $message);
//                                            $message = str_replace('%order_date%', $order_date, $message);
//                                            $message = str_replace('%barang%', $barang, $message);
//                                            $message = str_replace('%title%', $title, $message);
//                                            $message = str_replace('%harga_barang%', $harga_barang, $message);
//                                            $sendmail = $dbutil->send_email_html($to, $subject, $message);
//                                            /*end email html to pharmacy*/
//                                        }
//
//                                    }
//
//                                    if ($rows[0]['OwnerKategori'] == 'lab_laboratoriums') {
//                                        /*send email html to laboratorium*/
//                                        $row3 = array();
//                                        $getEmail = $db->getLaboratoriumById($rows[0]['OwnerID']);
//                                        if ($check) {
//                                            while ($row = $getEmail->fetch_assoc()) {
//                                                $rows3[] = $row;
//                                            }
//
//                                            $to = $rows3[0]['Email'];
////                                            $to = "mahmuddin.maxxima@gmail.com";  //testing only
//                                            $subject = 'Pesanan Permintaan Barang Promo';
//                                            $message = file_get_contents('../view/promo_email_order.php');
//
//                                            $name = $rows[0]['user_firstname'] . ' ' . $rows[0]['user_lastname'];
//                                            $alamat = $rows[0]['AlamatPengiriman'];
//                                            $lab = $rows3[0]['Name'];
//                                            $order_no = $rows[0]['PromoOrderNo'];
//                                            $barang = $rows[0]['Title'];
//                                            $order_date = date('d-m-Y H:i:s', strtotime($rows[0]['PromoOrderDate']));
//                                            $title = 'PERMINTAAN PESANAN BARANG "' . $rows[0]['Title'] . '"';
//                                            $message = str_replace('%name%', $name, $message);
//                                            $message = str_replace('%alamat_pengiriman%', $alamat, $message);
//                                            $message = str_replace('%penjual%', $lab, $message);
//                                            $message = str_replace('%order_no%', $order_no, $message);
//                                            $message = str_replace('%order_date%', $order_date, $message);
//                                            $message = str_replace('%barang%', $barang, $message);
//                                            $message = str_replace('%title%', $title, $message);
//                                            $sendmail = $dbutil->send_email_html($to, $subject, $message);
//                                            /*end email html to laboratorium*/
//                                        }
//
//                                    }
//
//                                    if ($rows[0]['OwnerKategori'] == 'master_hospital') {
//                                        /*send email html to hospital*/
//                                        $row4 = array();
//                                        $getEmail = $db->getHospitalById($rows[0]['OwnerID']);
//                                        if ($check) {
//                                            while ($row = $getEmail->fetch_assoc()) {
//                                                $rows4[] = $row;
//                                            }
//
//                                            $to = $rows4[0]['HospitalEmail'];
////                                            $to = "mahmuddin.maxxima@gmail.com";  //testing only
//                                            $subject = 'Pesanan Permintaan Barang Promo';
//                                            $message = file_get_contents('../view/promo_email_order.php');
//
//                                            $name = $rows[0]['user_firstname'] . ' ' . $rows[0]['user_lastname'];
//                                            $alamat = $rows[0]['AlamatPengiriman'];
//                                            $hospital = $rows4[0]['Name'];
//                                            $order_no = $rows[0]['PromoOrderNo'];
//                                            $barang = $rows[0]['Title'];
//                                            $order_date = date('d-m-Y H:i:s', strtotime($rows[0]['PromoOrderDate']));
//                                            $title = 'PERMINTAAN PESANAN BARANG "' . $rows[0]['Title'] . '"';
//                                            $message = str_replace('%name%', $name, $message);
//                                            $message = str_replace('%alamat_pengiriman%', $alamat, $message);
//                                            $message = str_replace('%penjual%', $hospital, $message);
//                                            $message = str_replace('%order_no%', $order_no, $message);
//                                            $message = str_replace('%order_date%', $order_date, $message);
//                                            $message = str_replace('%barang%', $barang, $message);
//                                            $message = str_replace('%title%', $title, $message);
//                                            $sendmail = $dbutil->send_email_html($to, $subject, $message);
//                                            /*end email html to hospital*/
//                                        }
//
//                                    }

                                }

                                $return = array(
                                    "status" => 200,
                                    "message" => "Checkout Berhasil",
                                    "data" => $rows
                                );
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Gagal melakukan Checkout, mohon coba beberapa saat lagi!"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return);
                }

                /**
                 * API Cancel Promo Order
                 * @param : ProductID (JSON)
                 * returns data
                 */
                if ($content == "delete_product") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $product_id = $post['ProductID'];


                    $check = $db->checkProductExist($product_id);
                    if ($check) {
                        $process = $db->deleteProduct($product_id);
                        if ($process) {
                            $return = array(
                                "status" => 200,
                                "message" => "Produk telah dihapus"
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Gagal menghapus Produk!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Produk tidak ditemukan!"
                        );
                    }
                    // } else {
                    //  $return = array(
                    //      "status" => 404,
                    //      "message" => "Oops sepertinya ada yang salah!"
                    //   );
                    //}
                    echo json_encode($return);
                }

                /**
                 * API Post Promo Payment Transfer
                 * @param : none
                 * returns data
                 */
                if ($content == "confirm_transfer_promo") {


                    $user_id = $_POST['UserID'];
                    $promo_order_id = $_POST['OrderID'];
                    $payment_accound_id = $_POST['PaymentAccountID'];
                    $bank_name = $_POST['SenderBankName'];
                    $account_name = $_POST['SenderBankAccName'];
                    $account_no = $_POST['SenderBankAccNo'];
                    $trf_date = $_POST['TransferDate'];
                    $total = $_POST['Total'];
                    $image = $_FILES['image'];


                    if (isset($user_id) && isset($payment_accound_id) && isset($promo_order_id) && isset($bank_name) && isset($account_name) && isset($account_no) && isset($trf_date) && isset($total)) {
                        $check = $db->checkConfirmPaymentPendingPromo($promo_order_id);
                        if (!$check) {
                            $id = $db->confirmPaymentTransferPromo($user_id, $payment_accound_id, $promo_order_id, $bank_name, $account_name, $account_no, $trf_date, $total);
                            // $id = 24;

                            if ($id) {

                                //Upload Photo
                                $uploaddir = $uploaddir . '/patient_payments/promo_orders/';

                                // $uploaddir = '/var/www/html/public_html/image/patient_payments/nurse_orders/';
                                $uploadfile = $uploaddir . $id . ".jpg";
                                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {

                                } else {
                                    echo exec('whoami') . '<br>';
                                    echo sys_get_temp_dir() . PHP_EOL . '<br>';
                                    echo $_FILES['image']['tmp_name'];
                                    var_dump($res);
                                }

                                $user = $db->getUserByID($user_id);
                                if ($user) {
                                    $user = $user->fetch_assoc();
                                    /*send email html*/

                                    $to = $user['Email'];
                                    $subject = 'Konfirmasi Pembayaran Promo';
                                    $message = file_get_contents('../view/invoice_email.php');
                                    $email_content = $db->getConfig('user_email_content_order_waiting_verification_promo')->fetch_assoc();
                                    if ($email_content) {
                                        $message = str_replace('%content%', $email_content['Value'], $message);
                                    }
                                    $rand = $BASE_URL . '/order_home_visit/' . base64_encode($promo_order_id);
                                    $name = $user['FirstName'] . ' ' . $user['LastName'];
                                    $title = 'PEMBAYARAN ANDA SEDANG DIPROSES LEBIH LANJUT';
                                    $message = str_replace('%name%', $name, $message);
                                    $message = str_replace('%rand%', $rand, $message);
                                    $message = str_replace('%title%', $title, $message);
                                    $sendmail = $dbutil->send_email_html($to, $subject, $message);
                                    /*end email html*/
                                }

                                /*send_pusher*/
                                $type = 'NewUserPromoPayment';
                                $message = 'New user promo payment found';
                                $sendpusher = $dbutil->send_pusher($type, $message, []);
                                /*end send_pusher*/

                                $return = array(
                                    "status" => 200,
                                    "message" => "Terima kasih anda berhasil melakukan pembayaran, mohon tunggu verifikasi",
                                    "send_email" => $sendmail,
                                    "send_pusher" => $sendpusher,
                                    "uploadfile" => $uploadfile
                                );
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Gagal melakukan konfirmasi pembayaran!"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Anda telah melakukan pembayaran, mohon tunggu beberapa saat untuk proses verifikasi!"
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

                if ($content == "history_promo") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $post['UserID'];
                    // $promo_order_id = $post['PromoOrderID'];
                    if (isset($post['PormoOrderID'])) {
                        $promo_order_id = $post['PromoOrderID'];
                    } else {
                        $promo_order_id = null;
                    }

                    $page = null;
                    $limit = 10;
                    if (isset($post['Page'])) {
                        $page = $post['Page'];
                    }

                    if (isset($user_id)) {
                        $check = $db->checkUserById($user_id);

                        if ($check) {


                            $getData = $db->getOrderPromoHistory($user_id, $promo_order_id, $page, $limit);


                            if ($getData != null) {

                                $total_rows = mysqli_num_rows($getData);
                                while ($row = $getData->fetch_assoc()) {
                                    $rows[] = $row;
                                }

                                if ($promo_order_id != null) {
                                    $rows2 = array();
                                    $getDetail = $db->getPharmacyOrderAction($promo_order_id);
                                    while ($row2 = $getDetail->fetch_assoc()) {
                                        $rows2[] = $row2;
                                    }

                                    $return = array(
                                        "status" => 200,
                                        "message" => "ok",
                                        "total_rows" => $total_rows,
                                        "data" => $rows,
                                        "action" => $rows2

                                    );
                                } else {
                                    $return = array(
                                        "status" => 200,
                                        "message" => "ok",
                                        "total_rows" => $total_rows,
                                        "data" => $rows
                                    );
                                }

                            } else {
                                $return = array(
                                    "status" => 200,
                                    "total_rows" => "0",
                                    "message" => "Belum ada transaksi!",
                                    "data" => []
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 200,
                                "total_rows" => "0",
                                "message" => "Akun anda belum aktif, silahkan melengkapi data profile!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API terima barang promo
                 * @param : PromoOrderID (JSON)
                 * returns data
                 */
                if ($content == "terima_barang") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $promo_order_id = $post['PromoOrderID'];


                    $check = $db->checkPromoOrderExist($promo_order_id);
                    if ($check) {
                        $process = $db->terimaBarang($promo_order_id);
                        if ($process) {
                            $return = array(
                                "status" => 200,
                                "message" => "Order anda telah diselesaikan"
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Gagal membatalkan order!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data tidak ditemukan, atau sudah dibatalkan!"
                        );
                    }
                    // } else {
                    //  $return = array(
                    //      "status" => 404,
                    //      "message" => "Oops sepertinya ada yang salah!"
                    //   );
                    //}
                    echo json_encode($return);
                }

                /**
                 * API Get Provinsi
                 * @param :
                 * returns data
                 */
                if ($content == "get_provinsi") {

                    $provinsi = $db->getProvinsi();
                    if ($provinsi != null) {
                        while ($row = $provinsi->fetch_assoc()) {
                            $rows[] = $row;
                        }
                        $return = array(
                            "status" => 200,
                            "message" => "ok",
                            "data" => $rows
                        );
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                        );
                    }
                    echo json_encode($return);
                }

                /**
                 * API Get Kabuputen
                 * @param :
                 * returns data
                 */
                if ($content == "get_kabupaten") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    $provinsi = $post['ProvinsiId'];

                    $getdata = $db->getKabupaten($provinsi);
                    if ($getdata != null) {
                        while ($row = $getdata->fetch_assoc()) {
                            $rows[] = $row;
                        }
                        $return = array(
                            "status" => 200,
                            "message" => "ok",
                            "data" => $rows
                        );
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                        );
                    }
                    echo json_encode($return);
                }

                /**
                 * API Get Kecamatan
                 * @param :
                 * returns data
                 */
                if ($content == "get_kecamatan") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    $provinsi = $post['ProvinsiId'];
                    $kabupaten = $post['KabupatenId'];

                    $getdata = $db->getKecamatan($provinsi, $kabupaten);
                    if ($getdata != null) {
                        while ($row = $getdata->fetch_assoc()) {
                            $rows[] = $row;
                        }
                        $return = array(
                            "status" => 200,
                            "message" => "ok",
                            "data" => $rows
                        );
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                        );
                    }
                    echo json_encode($return);
                }

                /**
                 * API Get Kelurahan
                 * @param :
                 * returns data
                 */
                if ($content == "get_kelurahan") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    $provinsi = $post['ProvinsiId'];
                    $kabupaten = $post['KabupatenId'];
                    $kecamatan = $post['KecamatanId'];

                    $getdata = $db->getKelurahan($provinsi, $kabupaten, $kecamatan);
                    if ($getdata != null) {
                        while ($row = $getdata->fetch_assoc()) {
                            $rows[] = $row;
                        }
                        $return = array(
                            "status" => 200,
                            "message" => "ok",
                            "data" => $rows
                        );
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                        );
                    }
                    echo json_encode($return);
                }


                /**
                 * API Get Nurse Actions
                 * @param : CategoryID (JSON)
                 * returns data
                 */
                if ($content == "actions") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $category_id = $post['CategoryID'];

                    if (isset($category_id)) {
                        $getData = $db->getNurseActions($category_id);
                        if ($getData != null) {

                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }

                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API Get Nearest Pharmacies
                 * @param : Latitude, Longitude (JSON)
                 * returns data
                 */
                if ($content == "list_pharmacies") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $latitude = $post['Latitude'];
                    $longitude = $post['Longitude'];

                    if (isset($latitude) && isset($longitude)) {

                        $getData = $db->getPharmacies($latitude, $longitude);
                        if ($getData != null) {

                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }

                            $total = mysqli_num_rows($getData);

                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Oops, sepertinya ada yang salah!"
                        );
                    }

                    echo json_encode($return);
                }

                if ($content == "test") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $post['UserID'];

                    if (isset($user_id)) {
                        $db->sendJobOffer($user_id);
                    }
                }

                /**
                 * API Process Pending Order Pharmacy
                 * @param : user_id, latitude, longitude, location, category_id, notes (JSON)
                 * returns data
                 */
                if ($content == "pending_order_pharmacy") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $post['UserID'];
                    //$order_type		= $post['OrderType'];
                    $latitude = $post['Latitude'];
                    $longitude = $post['Longitude'];
                    $location = $post['Location'];
                    $notes = $post['Notes'];
                    $medicine_id = $post['MedicineID'];
                    $jumlah = $post['Jumlah'];

                    if (isset($user_id) && isset($latitude) && isset($longitude) && isset($location) && isset($notes) && isset($medicine_id)) {
                        $process = $db->processPendingOrderPharmacy($user_id, $longitude, $latitude, $location, $notes);
                        if ($process) {

                            $get_id = $db->getPharmacyPendingOrderID($user_id);


                            if ($get_id != null) {

                                $createDetail = $db->processPharmacyDetailOrder($medicine_id, $get_id, $jumlah);

                                if ($createDetail) {

                                    $return = array(
                                        "status" => 200,
                                        "message" => "Order Success!",
                                        "AptOrderID" => $get_id

                                    );
                                } else {
                                    $return = array(
                                        "status" => 404,
                                        "message" => "Order failed!...."
                                    );
                                }
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Order ID Tidak ditemukan!"
                                );
                            }

                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Proses gagal!"
                            );
                        }

                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return);
                }

                /**
                 * API Process Pending Order Pharmacy
                 * @param : user_id, latitude, longitude, location, category_id, notes (JSON)
                 * returns data
                 */
                if ($content == "terima_order_pharmacy") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $order_id = $post['AptOrderID'];
                    $user_id = $post['UserID'];
                    $apt_user_id = $post['AptUserID'];
                    $total_payment = $post['TotalPayment'];
                    $total_payments = str_replace('.', '', str_replace('Rp. ', '', $total_payment));
                    if (isset($order_id) && isset($user_id)) {


                        $save = $db->saveOrderPharmacy($order_id, $user_id, $total_payments, $apt_user_id);
                        if ($save) {
                            $return = array(
                                "status" => 200,
                                "message" => "Order anda berhasil disimpan, silahkan lakukan pembayaran!"
                            );
                        } else {
                            $return = array(
                                "status" => 400,
                                "message" => "Gagal memproses order anda, mohon coba beberapa saat lagi!"
                            );
                        }

                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }

                    // $return = array(
                    // 		"status" => 404,
                    // 		"message" => $details,
                    // );
                    echo json_encode($return);
                }


                /**
                 * API Process Pending Order Nurse
                 * @param : user_id, latitude, longitude, location, category_id, notes (JSON)
                 * returns data
                 */
                if ($content == "pending_order") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $post['UserID'];
                    $latitude = $post['Latitude'];
                    $longitude = $post['Longitude'];
                    $location = $post['Location'];
                    $category_id = $post['CategoryID'];
                    $notes = $post['Notes'];
                    $total_price = $post['TotalPrice'];
                    $action_id = $post['ActionID'];
                    $action_id = $post['ActionID'];

                    if (isset($user_id) && isset($latitude) && isset($longitude) && isset($location) && isset($category_id) && isset($notes) && isset($total_price) && isset($action_id)) {
                        $company_fee_percent = $db->getConfig('company_fee_percent')->fetch_assoc();
                        $company_fee_percent = $company_fee_percent['Value'];
                        $process = $db->processPendingOrder($user_id, $latitude, $longitude, $location, $category_id, $notes, $total_price, $company_fee_percent);
                        if ($process) {

                            $get_id = $db->getPendingOrderID($user_id);
                            if ($get_id != null) {

                                $createDetail = $db->processDetailOrder($action_id, $get_id);
                                if ($createDetail) {

                                    $return = array(
                                        "status" => 200,
                                        "message" => "Order Success!",
                                        "OrderID" => $get_id
                                    );
                                } else {
                                    $return = array(
                                        "status" => 404,
                                        "message" => "Order failed!...."
                                    );
                                }
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Order failed!"
                                );
                            }

                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Order failed!"
                            );
                        }

                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return);
                }


                /**
                 * API Process Pending Order Doctor
                 * @param : user_id, latitude, longitude, location, category_id, notes (JSON)
                 * returns data
                 */
                if ($content == "pending_order_doctor") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $post['UserID'];
                    $latitude = $post['Latitude'];
                    $longitude = $post['Longitude'];
                    $location = $post['Location'];
                    $category_id = $post['CategoryID'];
                    $notes = $post['Notes'];
                    $price_from = $post['RangePriceFrom'];
                    $price_to = $post['RangePriceTo'];

                    if (isset($user_id) && isset($latitude) && isset($longitude) && isset($location) && isset($category_id) && isset($notes) && isset($price_from) && isset($price_to)) {

                        $process = $db->processPendingOrderDoctor($user_id, $latitude, $longitude, $location, $category_id, $notes, $price_from, $price_to);
                        if ($process) {

                            $get_id = $db->getDoctorPendingOrderID($user_id);
                            if ($get_id != null) {

                                $return = array(
                                    "status" => 200,
                                    "message" => "Order Success!",
                                    "OrderID" => $get_id
                                );
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Order Failed!"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Order failed!"
                            );
                        }

                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return);
                }


                /**
                 * API Get Nurse Detail
                 * @param : NurseID (JSON)
                 * returns data
                 */
                if ($content == "nurse_detail") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $nurse_id = $post['NurseID'];

                    if (isset($nurse_id)) {

                        $check = $db->checkNurseById($nurse_id);
                        if ($check) {

                            $getData = $db->getNurseData($nurse_id);
                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }

                            $rows2 = array();
                            $getEdu = $db->getNurseEducation($nurse_id);
                            while ($row2 = $getEdu->fetch_assoc()) {
                                $rows2[] = $row2;
                            }

                            $rows3 = array();
                            $getExp = $db->getNurseExperience($nurse_id);
                            while ($row3 = $getExp->fetch_assoc()) {
                                $rows3[] = $row3;
                            }

                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "data" => $rows,
                                "educations" => $rows2,
                                "experiences" => $rows3
                            );

                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return);
                }

                /**
                 * API Get Doctor Detail
                 * @param : DoctorID (JSON)
                 * returns data
                 */
                if ($content == "doctor_detail") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $doctor_id = $post['DoctorID'];

                    if (isset($doctor_id)) {

                        $check = $db->checkDoctorById($doctor_id);
                        if ($check) {

                            $getData = $db->getDoctorData($doctor_id);
                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }

                            $rows2 = array();
                            $getEdu = $db->getDoctorEducation($doctor_id);
                            while ($row2 = $getEdu->fetch_assoc()) {
                                $rows2[] = $row2;
                            }

                            $rows3 = array();
                            $getExp = $db->getDoctorExperience($doctor_id);
                            while ($row3 = $getExp->fetch_assoc()) {
                                $rows3[] = $row3;
                            }

                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "data" => $rows,
                                "educations" => $rows2,
                                "experiences" => $rows3
                            );

                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return);
                }
                if ($content == "pharmacy_detail") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $pharmacy_id = $post['PharmacyID'];

                    if (isset($doctor_id)) {

                        $check = $db->checkPharmacyById($pharmacy_id);
                        if ($check) {

                            $getData = $db->getPharmacyData($pharmacy_id);
                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }


                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "data" => $rows,
                                "educations" => $rows2,
                                "experiences" => $rows3
                            );

                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return);
                }

                /**
                 * API Choose Nurse - Deal with this doctor
                 * @param : OrderID, NurseID (JSON)
                 * returns data
                 */
                if ($content == "choose_nurse") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $order_id = $post['OrderID'];
                    $nurse_id = $post['NurseID'];
                    $user_id = $userid_header;

                    if (isset($order_id) && isset($nurse_id)) {

                        $check = $db->checkOrderExist($order_id);
                        if ($check) {

                            $process = $db->chooseNurse($order_id, $nurse_id);
                            if ($process) {

                                $getData = $db->getOrderData($order_id, $nurse_id);
                                while ($row = $getData->fetch_assoc()) {
                                    $rows[] = $row;
                                }

                                $rows2 = array();
                                $getEdu = $db->getOrderAction($order_id);
                                while ($row2 = $getEdu->fetch_assoc()) {
                                    $rows2[] = $row2;
                                }

                                $rating = $db->getRating($nurse_id);
                                // Send push notif to nurse had bid an order and order give to other nurse
                                // $db->pushNotifBidCancelNurse($order_id, $user_id, $nurse_id);

                                $return = array(
                                    "status" => 200,
                                    "message" => "ok",
                                    "data" => $rows,
                                    "action" => $rows2,
                                    "rating" => $rating
                                );
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Gagal memilih perawat, mohon coba beberapa saat lagi!"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan, atau sudah dibatalkan!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return);
                }

                /**
                 * API Choose Doctor - Deal with this doctor
                 * @param : OrderID, NurseID (JSON)
                 * returns data
                 */
                if ($content == "choose_doctor") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $order_id = $post['OrderID'];
                    $doctor_id = $post['DoctorID'];

                    if (isset($order_id) && isset($doctor_id)) {

                        $check = $db->checkDoctorOrderExist($order_id);
                        if ($check) {

                            $process = $db->chooseDoctor($order_id, $doctor_id);
                            if ($process) {

                                $getData = $db->getDoctorOrderData($order_id);
                                while ($row = $getData->fetch_assoc()) {
                                    $rows[] = $row;
                                }

                                $rating = $db->getRatingDoctor($doctor_id);
                                $return = array(
                                    "status" => 200,
                                    "message" => "ok",
                                    "data" => $rows,
                                    "rating" => $rating
                                );
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Gagal memilih dokter, mohon coba beberapa saat lagi!"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan, atau sudah dibatalkan!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return);
                }

                /**
                 * API Choose Nurse - Deal with this doctor
                 * @param : OrderID, NurseID (JSON)
                 * returns data
                 */
                if ($content == "choose_pharmacy") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $order_id = $post['OrderID'];
                    $pharmacy_id = $post['PharmacyID'];
                    $user_id = $userid_header;

                    if (isset($order_id) && isset($pharmacy_id)) {

                        $check = $db->checkPharmacyOrderExist($order_id);
                        if ($check) {

                            $process = $db->choosePharmacy($order_id, $pharmacy_id);
                            if ($process) {

                                $getData = $db->getPharmacyOrderData($order_id, $pharmacy_id);
                                while ($row = $getData->fetch_assoc()) {
                                    $rows[] = $row;
                                }

                                //$rows2 = array();
                                //$getEdu = $db->getPharmacyOrderAction($order_id);
                                //while($row2 = $getEdu->fetch_assoc()){
                                //	$rows2[] = $row2;
                                //	}

                                //$rating = $db->getRating($pharmacy_id);
                                // Send push notif to nurse had bid an order and order give to other nurse
                                // $db->pushNotifBidCancelNurse($order_id, $user_id, $nurse_id);

                                $return = array(
                                    "status" => 200,
                                    "message" => "ok",
                                    "data" => $rows,
                                    "action" => $rows2,
                                    "rating" => $rating
                                );
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Gagal memilih apotek, mohon coba beberapa saat lagi!"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan, atau sudah dibatalkan!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return);
                }


                /**
                 * API Cancel Nurse Order
                 * @param : OrderID (JSON)
                 * returns data
                 */
                if ($content == "cancel_nurse") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $order_id = $post['OrderID'];
                    $user_id = $userid_header;
                    // $db->pushNotifCancelNurse($order_id, $user_id);
                    if (isset($order_id)) {

                        $check = $db->checkOrderExist2($order_id);
                        if ($check) {
                            // $order = $check->fetch_assoc();
                            $process = $db->cancelNurse($order_id);
                            if ($process) {
                                $db->pushNotifCancelNurse($order_id, $user_id);
                                $return = array(
                                    "status" => 200,
                                    "message" => "Order anda telah dibatalkan"
                                );
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Gagal membatalkan order!"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan, atau sudah dibatalkan!"
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

                /**
                 * API Cancel Doctor Order
                 * @param : OrderID (JSON)
                 * returns data
                 */
                if ($content == "cancel_doctor") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $order_id = $post['OrderID'];
                    $user_id = $userid_header;

                    if (isset($order_id)) {

                        $check = $db->checkDoctorOrderExist($order_id);
                        if ($check) {

                            $process = $db->cancelDoctor($order_id);
                            if ($process) {
                                // $db->pushNotifCancelDoctor($order_id, $user_id);
                                $return = array(
                                    "status" => 200,
                                    "message" => "Order Anda telah dibatalkan"
                                );
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Gagal membatalkan order!"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan, atau sudah dibatalkan!"
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

                /**
                 * API Process Payment - Deal with this nurse
                 * @param : OrderID (JSON)
                 * returns data
                 */
                if ($content == "process_payment") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $order_id = $post['OrderID'];
                    $total_transport = $post['TransportPrice'];
                    $total_price = $post['TotalPrice'];
                    $payment_type_id = $post['PaymentTypeID'];
                    $unique_code = $post['UniqueCode'];
                    $kode_voucher = $post['KodeVoucher'];
                    $nominal = $post['nominal'];

                    if (isset($order_id) && isset($total_transport) && isset($total_price) && isset($payment_type_id)) {

                        //PUSHER test
                        // echo 'pusher';
                        // $type = 'NewUserKanopiPayment';
                        // 			$message = 'New kanopi order found';
                        // 			$data = [];
                        // $pusher = $dbutil->send_pusher($type, $message, $data);
                        // return $pusher;

                        $check = $db->checkOrderExist($order_id);
                        if ($check) {

                            $process = $db->processPayment($order_id, $total_transport, $total_price, $payment_type_id, $unique_code, $kode_voucher, $nominal);
                            if ($process) {

                                $getData = $db->getOrderData($order_id);
                                // var_dump($getData);
                                while ($row = $getData->fetch_assoc()) {
                                    $rows[] = $row;
                                }

                                $rows2 = array();
                                $getEdu = $db->getOrderAction($order_id);
                                while ($row2 = $getEdu->fetch_assoc()) {
                                    $rows2[] = $row2;
                                }

                                if ($payment_type_id == "1") {//pembayaran tunai
                                    $email_content = $db->getConfig('user_email_content_order_payment_success')->fetch_assoc();
                                } else { //pembayaran selain tunai
                                    $email_content = $db->getConfig('user_email_content_order_waiting_payment')->fetch_assoc();
                                }

                                if ($getData) {
                                    /*send email html*/
                                    $to = $rows[0]['user_email'];
                                    $subject = 'Permintan Layanan Medis Nurse Home Visit Diterima';
                                    $message = file_get_contents('../view/invoice_email.php');

                                    if ($email_content) {
                                        $message = str_replace('%content%', $email_content['Value'], $message);
                                    }
                                    $rand = $BASE_URL . '/order_home_visit/' . base64_encode($rows[0]['OrderID']);
                                    $name = $rows[0]['user_firstname'] . ' ' . $rows[0]['user_lastname'];
                                    $title = 'PERMINTAAN LAYANAN MEDIS DITERIMA';
                                    $message = str_replace('%name%', $name, $message);
                                    $message = str_replace('%rand%', $rand, $message);
                                    $message = str_replace('%title%', $title, $message);
                                    $sendmail = $dbutil->send_email_html($to, $subject, $message);
                                    /*end email html*/

                                    // send pusher
                                    $type = 'NewUserKanopiPayment';
                                    $message = 'New kanopi order found';
                                    $data = [];
                                    $dbutil->send_pusher($type, $message, $data);
                                }

                                $return = array(
                                    "status" => 200,
                                    "message" => "Pembayaran Berhasil",
                                    "data" => $rows,
                                    "action" => $rows2,
                                    "send_email" => $sendmail
                                );
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Gagal memproses pembayaran, mohon coba beberapa saat lagi!"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan!"
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

                /**
                 * API Confirm Payment (if payment method : Transfer)
                 * @param : NurseID, PaymentAccountID, SenderBankName, SenderBankAccName, SenderBankAccNo, TransferDate, Total (JSON)
                 * returns data
                 */
                if ($content == "confirm_transfer") {

                    $order_id = $_POST['OrderID'];
                    $payment_accound_id = $_POST['PaymentAccountID'];
                    $bank_name = $_POST['SenderBankName'];
                    $account_name = $_POST['SenderBankAccName'];
                    $account_no = $_POST['SenderBankAccNo'];
                    $trf_date = $_POST['TransferDate'];
                    $total = $_POST['Total'];
                    $image = $_FILES['image'];


                    if (isset($order_id) && isset($payment_accound_id) && isset($bank_name) && isset($account_name) && isset($account_no) && isset($trf_date) && isset($total)) {

                        $check = $db->checkConfirmPaymentPending($order_id);
                        if (!$check) {

                            $id = $db->confirmPaymentTransfer($userid_header, $order_id, $payment_accound_id, $bank_name, $account_name, $account_no, $trf_date, $total);
                            // $id = 24;
                            if ($id != null) {

                                //Upload Photo
                                $uploaddir = $uploaddir . '/patient_payments/nurse_orders/';
                                // $uploaddir = '/var/www/html/public_html/image/patient_payments/nurse_orders/';
                                $uploadfile = $uploaddir . $id . ".jpg";
                                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {

                                } else {
                                    echo exec('whoami') . '<br>';
                                    echo sys_get_temp_dir() . PHP_EOL . '<br>';
                                    echo $_FILES['image']['tmp_name'];
                                    var_dump($res);
                                }

                                $user = $db->getUserByID($userid_header);
                                if ($user) {
                                    $user = $user->fetch_assoc();
                                    /*send email html*/

                                    $to = $user['Email'];
                                    $subject = 'Konfirmasi Pembayaran Nurse Home Visit';
                                    $message = file_get_contents('../view/invoice_email.php');
                                    $email_content = $db->getConfig('user_email_content_order_waiting_verification')->fetch_assoc();
                                    if ($email_content) {
                                        $message = str_replace('%content%', $email_content['Value'], $message);
                                    }
                                    $rand = $BASE_URL . '/order_home_visit/' . base64_encode($order_id);
                                    $name = $user['FirstName'] . ' ' . $user['LastName'];
                                    $title = 'PEMBAYARAN ANDA SEDANG DIPROSES LEBIH LANJUT';
                                    $message = str_replace('%name%', $name, $message);
                                    $message = str_replace('%rand%', $rand, $message);
                                    $message = str_replace('%title%', $title, $message);
                                    $sendmail = $dbutil->send_email_html($to, $subject, $message);
                                    /*end email html*/
                                }

                                /*send_pusher*/
                                $type = 'NewUserNursePayment';
                                $message = 'New user nurse payment found';
                                $sendpusher = $dbutil->send_pusher($type, $message, []);
                                /*end send_pusher*/

                                $return = array(
                                    "status" => 200,
                                    "message" => "Terima kasih anda berhasil melakukan pembayaran, mohon tunggu verifikasi",
                                    "send_email" => $sendmail,
                                    "send_pusher" => $sendpusher,
                                    "uploadfile" => $uploadfile
                                );
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Gagal melakukan konfirmasi pembayaran!"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Anda telah melakukan konfirmasi pembayaran tagihan, mohon tunggu proses verifikasi!"
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

                /**
                 * API Confirm Payment (if payment method : Transfer)
                 * @param : AptOrderID, PaymentAccountID, SenderBankName, SenderBankAccName, SenderBankAccNo, TransferDate, Total (JSON)
                 * returns data
                 */
                if ($content == "confirm_transfer_pharmacy") {

                    $order_id = $_POST['OrderID'];
                    $payment_accound_id = $_POST['PaymentAccountID'];
                    $bank_name = $_POST['SenderBankName'];
                    $account_name = $_POST['SenderBankAccName'];
                    $account_no = $_POST['SenderBankAccNo'];
                    $trf_date = $_POST['TransferDate'];
                    $total = $_POST['Total'];
                    $image = $_FILES['image'];
                    $unique_code = $_POST['UniqueCode'];


                    if (isset($order_id) && isset($payment_accound_id) && isset($bank_name) && isset($account_name) && isset($account_no) && isset($trf_date) && isset($total)) {

                        $check = $db->checkConfirmPaymentPendingPharmacy($order_id);
                        if (!$check) {

                            $id = $db->confirmPaymentTransferPharmacy($userid_header, $order_id, $payment_accound_id, $bank_name, $account_name, $account_no, $trf_date, $total, $unique_code);
                            // $id = 24;
                            if ($id != null) {

                                //Upload Photo
                                $uploaddir = $uploaddir . '/patient_payments/pharmacy_orders/';
                                // $uploaddir = '/var/www/html/public_html/image/patient_payments/nurse_orders/';
                                $uploadfile = $uploaddir . $id . ".jpg";
                                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {

                                } else {
                                    echo exec('whoami') . '<br>';
                                    echo sys_get_temp_dir() . PHP_EOL . '<br>';
                                    echo $_FILES['image']['tmp_name'];
                                    var_dump($res);
                                }

                                $user = $db->getUserByID($userid_header);
                                if ($user) {
                                    $user = $user->fetch_assoc();
                                    /*send email html*/

                                    $to = $user['Email'];
                                    $subject = 'Konfirmasi Pembayaran Pharmacy';
                                    $message = file_get_contents('../view/invoice_email.php');
                                    $email_content = $db->getConfig('user_email_content_order_waiting_verification_pharmacy')->fetch_assoc();
                                    if ($email_content) {
                                        $message = str_replace('%content%', $email_content['Value'], $message);
                                    }
                                    $rand = $BASE_URL . '/order_pharmacy/' . base64_encode($order_id);
                                    $name = $user['FirstName'] . ' ' . $user['LastName'];
                                    $title = 'PEMBAYARAN ANDA SEDANG DIPROSES LEBIH LANJUT';
                                    $message = str_replace('%name%', $name, $message);
                                    $message = str_replace('%rand%', $rand, $message);
                                    $message = str_replace('%title%', $title, $message);
                                    $sendmail = $dbutil->send_email_html($to, $subject, $message);
                                    /*end email html*/
                                }

                                /*send_pusher*/
                                $type = 'NewUserPharmacyPayment';
                                $message = 'New user pharmacy payment found';
                                $sendpusher = $dbutil->send_pusher($type, $message, []);
                                /*end send_pusher*/

                                $return = array(
                                    "status" => 200,
                                    "message" => "Terima kasih anda berhasil melakukan pembayaran, mohon tunggu verifikasi",
                                    "send_email" => $sendmail,
                                    "send_pusher" => $sendpusher,
                                    "uploadfile" => $uploadfile
                                );
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Gagal melakukan konfirmasi pembayaran!"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Anda telah melakukan konfirmasi pembayaran tagihan, mohon tunggu proses verifikasi!"
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

                /**
                 * API Process Payment - Deal with this doctor
                 * @param : OrderID (JSON)
                 * returns data
                 */
                if ($content == "process_payment_doctor") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $order_id = $post['OrderID'];
                    $payment_type_id = $post['PaymentTypeID'];
                    $unique_code = $post['UniqueCode'];

                    if (isset($order_id) && isset($payment_type_id)) {

                        $check = $db->checkDoctorOrderExist($order_id);
                        if ($check) {

                            $process = $db->processPaymentDoctor($order_id, $payment_type_id, $unique_code);
                            // $process = true;
                            if ($process) {

                                $getData = $db->getDoctorOrderData($order_id);
                                while ($row = $getData->fetch_assoc()) {
                                    $rows[] = $row;
                                }


                                if ($getData) {
                                    /*send email html*/
                                    $to = $rows[0]['user_email'];
                                    $subject = 'Permintan Layanan Medis Doctor Diterima';
                                    $message = file_get_contents('../view/invoice_email.php');
                                    $email_content = $db->getConfig('user_email_content_order_waiting_payment_doctor')->fetch_assoc();
                                    if ($email_content) {
                                        $message = str_replace('%content%', $email_content['Value'], $message);
                                    }
                                    $rand = $BASE_URL . '/order_doctor/' . base64_encode($rows[0]['OrderID']);
                                    $name = $rows[0]['user_firstname'] . ' ' . $rows[0]['user_lastname'];
                                    $title = 'PERMINTAAN LAYANAN MEDIS DITERIMA';
                                    $message = str_replace('%name%', $name, $message);
                                    $message = str_replace('%rand%', $rand, $message);
                                    $message = str_replace('%title%', $title, $message);
                                    $sendmail = $dbutil->send_email_html($to, $subject, $message);
                                    /*end email html*/
                                }

                                $return = array(
                                    "status" => 200,
                                    "message" => "Pembayaran Berhasil",
                                    "data" => $rows,
                                    "send_email" => $sendmail
                                );
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Gagal memproses pembayaran, mohon coba beberapa saat lagi!"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return);
                }

                /**
                 * API Process Payment - Get self data
                 * @param : UserID (JSON)
                 * returns data
                 */
                if ($content == "data_patient") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $post['UserID'];

                    if (isset($user_id)) {
                        $process = $db->checkUserById($user_id);
                        // $process = true;
                        if ($process) {

                            $getData = $db->getUserByID($user_id);
                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }

                            $return = array(
                                "status" => 200,
                                "message" => "Get Data Patient Berhasil",
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Gagal memperoleh data patient, mohon coba beberapa saat lagi!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return);
                }


                /**
                 * API Confirm Payment (if payment method : Transfer)
                 * @param : OrderID, PaymentAccountID, SenderBankName, SenderBankAccName, SenderBankAccNo, TransferDate, Total (JSON)
                 * returns data
                 */
                if ($content == "confirm_transfer_doctor") {

                    $order_id = $_POST['OrderID'];
                    $payment_accound_id = $_POST['PaymentAccountID'];
                    $bank_name = $_POST['SenderBankName'];
                    $account_name = $_POST['SenderBankAccName'];
                    $account_no = $_POST['SenderBankAccNo'];
                    $trf_date = $_POST['TransferDate'];
                    $total = $_POST['Total'];
                    $image = $_FILES['image'];

                    if (isset($order_id) && isset($payment_accound_id) && isset($bank_name) && isset($account_name) && isset($account_no) && isset($trf_date) && isset($total)) {

                        $check = $db->checkConfirmPaymentPendingDoctor($order_id);
                        // $check = false;
                        if (!$check) {

                            $id = $db->confirmPaymentTransferDoctor($userid_header, $order_id, $payment_accound_id, $bank_name, $account_name, $account_no, $trf_date, $total);
                            if ($id != null) {

                                //Upload Photo
                                $uploaddir = $uploaddir . '/patient_payments/doctor_orders/';
                                $uploadfile = $uploaddir . $id . ".jpg";
                                move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile);

                                $user = $db->getUserByID($userid_header);
                                if ($user) {
                                    $user = $user->fetch_assoc();
                                    /*send email html*/

                                    $to = $user['Email'];
                                    $subject = 'Konfirmasi Pembayaran Doctor';
                                    $message = file_get_contents('../view/invoice_email.php');
                                    $email_content = $db->getConfig('user_email_content_order_waiting_verification_doctor')->fetch_assoc();
                                    if ($email_content) {
                                        $message = str_replace('%content%', $email_content['Value'], $message);
                                    }
                                    $rand = $BASE_URL . '/order_home_visit/' . base64_encode($order_id);
                                    $name = $user['FirstName'] . ' ' . $user['LastName'];
                                    $title = 'PEMBAYARAN ANDA SEDANG DIPROSES LEBIH LANJUT';
                                    $message = str_replace('%name%', $name, $message);
                                    $message = str_replace('%rand%', $rand, $message);
                                    $message = str_replace('%title%', $title, $message);
                                    $sendmail = $dbutil->send_email_html($to, $subject, $message);
                                    /*end email html*/
                                }

                                /*send_pusher*/
                                $type = 'NewUserDoctorPayment';
                                $message = 'New user doctor payment found';
                                $sendpusher = $dbutil->send_pusher($type, $message, []);
                                /*end send_pusher*/

                                $return = array(
                                    "status" => 200,
                                    "message" => "Terima kasih anda berhasil melakukan pembayaran, mohon tunggu verifikasi",
                                    "send_email" => $sendmail,
                                    "send_pusher" => $sendpusher
                                );
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Gagal melakukan konfirmasi pembayaran!"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Anda telah melakukan konfirmasi pembayaran tagihan, mohon tunggu proses verifikasi!"
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

                /**
                 * API Get Nurse Order History
                 * @param : UserID (JSON)
                 * returns data
                 */
                if ($content == "history_nurse") {

                    $order_id = null;

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $post['UserID'];
                    $order_id = null;
                    if (isset($post['OrderID'])) {
                        $order_id = $post['OrderID'];
                    }

                    $page = null;
                    $limit = 10;
                    if (isset($post['Page'])) {
                        $page = $post['Page'];
                    }

                    if (isset($user_id)) {
                        $check = $db->checkUserById($user_id);
                        if ($check) {

                            $getData = $db->getOrderNurseHistory($user_id, $order_id, $page, $limit);

                            if ($getData != null) {

                                $total_rows = mysqli_num_rows($getData);
                                while ($row = $getData->fetch_assoc()) {
                                    $rows[] = $row;
                                }

                                if ($order_id != null) {
                                    $rows2 = array();
                                    $getEdu = $db->getOrderAction($order_id);
                                    while ($row2 = $getEdu->fetch_assoc()) {
                                        $rows2[] = $row2;
                                    }

                                    $return = array(
                                        "status" => 200,
                                        "message" => "ok",
                                        "total_rows" => $total_rows,
                                        "data" => $rows,
                                        "action" => $rows2

                                    );
                                } else {
                                    $return = array(
                                        "status" => 200,
                                        "message" => "ok",
                                        "total_rows" => $total_rows,
                                        "data" => $rows
                                    );
                                }

                            } else {
                                $return = array(
                                    "status" => 200,
                                    "total_rows" => "0",
                                    "message" => "Belum ada transaksi!",
                                    "data" => []
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 200,
                                "total_rows" => "0",
                                "message" => "Akun anda belum aktif, silahkan melengkapi data profile!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }

                    echo json_encode($return);
                }

                if ($content == "history_patient_detail") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $order_id = $post['AptOrderID'];
                    $user_id = $post['UserID'];

                    if (isset($user_id)) {

                        $getData = $db->getOrderPatientDetail($user_id, $order_id);
                        if ($getData != null) {

                            $total_rows = mysqli_num_rows($getData);

                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }


                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "total_rows" => $total_rows,
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 200,
                                "total_rows" => "0",
                                "message" => "Data tidak tersedia!"
                            );
                        }

                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API Get Pharmacy Order History
                 * @param : UserID (JSON)
                 * returns data
                 */
                if ($content == "history_pharmacy") {

                    $order_id = null;

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $post['UserID'];
                    $order_id = null;
                    if (isset($post['OrderID'])) {
                        $order_id = $post['OrderID'];
                    }

                    $page = null;
                    $limit = 10;
                    if (isset($post['Page'])) {
                        $page = $post['Page'];
                    }

                    if (isset($user_id)) {
                        $check = $db->checkUserById($user_id);

                        if ($check) {


                            $getData = $db->getOrderPharmacyHistory($user_id, $order_id, $page, $limit);


                            if ($getData != null) {

                                $total_rows = mysqli_num_rows($getData);
                                while ($row = $getData->fetch_assoc()) {
                                    $rows[] = $row;
                                }

                                if ($order_id != null) {
                                    $rows2 = array();
                                    $getDetail = $db->getPharmacyOrderAction($order_id);
                                    while ($row2 = $getDetail->fetch_assoc()) {
                                        $rows2[] = $row2;
                                    }

                                    $return = array(
                                        "status" => 200,
                                        "message" => "ok",
                                        "total_rows" => $total_rows,
                                        "data" => $rows,
                                        "action" => $rows2

                                    );
                                } else {
                                    $return = array(
                                        "status" => 200,
                                        "message" => "ok",
                                        "total_rows" => $total_rows,
                                        "data" => $rows
                                    );
                                }

                            } else {
                                $return = array(
                                    "status" => 200,
                                    "total_rows" => "0",
                                    "message" => "Belum ada transaksi!",
                                    "data" => []
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 200,
                                "total_rows" => "0",
                                "message" => "Akun anda belum aktif, silahkan melengkapi data profile!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API Get Pharmacy Order History
                 * @param : UserID (JSON)
                 * returns data
                 */
                if ($content == "history_ambulance") {

                    $order_id = null;

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $post['customer_id'];
                    $order_id = null;
                    if (isset($post['OrderID'])) {
                        $order_id = $post['OrderID'];
                    }

                    $page = null;
                    $limit = 10;
                    if (isset($post['Page'])) {
                        $page = $post['Page'];
                    }

                    if (isset($user_id)) {
                        $check = $db->checkUserById($user_id);
                        if ($check) {

                            $getData = $db->getOrderAmbulanceHistory($user_id, $order_id, $page, $limit);

                            if ($getData != null) {

                                $total_rows = mysqli_num_rows($getData);
                                while ($row = $getData->fetch_assoc()) {
                                    $rows[] = $row;
                                }

                                if ($order_id != null) {
                                    $rows2 = array();
                                    $getEdu = $db->getOrderAction($order_id);
                                    while ($row2 = $getEdu->fetch_assoc()) {
                                        $rows2[] = $row2;
                                    }

                                    $return = array(
                                        "status" => 200,
                                        "message" => "ok",
                                        "total_rows" => $total_rows,
                                        "data" => $rows,
                                        "action" => $rows2

                                    );
                                } else {
                                    $return = array(
                                        "status" => 200,
                                        "message" => "ok",
                                        "total_rows" => $total_rows,
                                        "data" => $rows
                                    );
                                }

                            } else {
                                $return = array(
                                    "status" => 200,
                                    "total_rows" => "0",
                                    "message" => "Belum ada transaksi!",
                                    "data" => []
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 200,
                                "total_rows" => "0",
                                "message" => "Akun anda belum aktif, silahkan melengkapi data profile!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API Get Doctor Order History
                 * @param : UserID (JSON)
                 * returns data
                 */
                if ($content == "history_doctor") {

                    $order_id = null;

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $post['UserID'];
                    $order_id = null;
                    if (isset($post['OrderID'])) {
                        $order_id = $post['OrderID'];
                    }

                    $page = null;
                    $limit = 10;
                    if (isset($post['Page'])) {
                        $page = $post['Page'];
                    }

                    if (isset($user_id)) {
                        $check = $db->checkUserById($user_id);
                        if ($check) {

                            $getData = $db->getOrderDoctorHistory($user_id, $order_id, $page, $limit);

                            if ($getData != null) {

                                $total_rows = mysqli_num_rows($getData);
                                while ($row = $getData->fetch_assoc()) {
                                    $rows[] = $row;
                                }

                                $return = array(
                                    "status" => 200,
                                    "message" => "ok",
                                    "total_rows" => $total_rows,
                                    "data" => $rows

                                );
                            } else {
                                $return = array(
                                    "status" => 200,
                                    "total_rows" => "0",
                                    "message" => "Belum ada transaksi!",
                                    "data" => []
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 200,
                                "total_rows" => "0",
                                "message" => "Akun anda belum aktif, silahkan melengkapi data profile!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }

                    echo json_encode($return);
                }


                /**
                 * API Cancel Pharmacy Order
                 * @param : OrderID (JSON)
                 * returns data
                 */
                if ($content == "cancel_order_pharmacy") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    //$order_id = $post['OrderID'];
                    //$user_id  = $post['UserID'];
                    $user_id = $userid_header;
                    $order_id = $post['AptOrderID'];
                    //$user_id  = "110";
                    //Get ID
                    //$get_id = $db->getPharmacyPendingOrderID($user_id);

                    //if (isset($get_id)) {

                    $check = $db->checkPharmacyOrderExist($order_id);
                    if ($check) {

                        $process = $db->cancelPharmacy($order_id);
                        if ($process) {
                            //$db->pushNotifCancelNurse($order_id, $user_id);
                            $return = array(
                                "status" => 200,
                                "message" => "Order anda telah dibatalkan"
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Gagal membatalkan order!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data tidak ditemukan, atau sudah dibatalkan!"
                        );
                    }
                    // } else {
                    //  $return = array(
                    //      "status" => 404,
                    //      "message" => "Oops sepertinya ada yang salah!"
                    //   );
                    //}
                    echo json_encode($return);
                }


                /**
                 * API Terima Barang Pharmacy
                 * @param : OrderID (JSON)
                 * returns data
                 */
                if ($content == "terima_barang_pharmacy") {

                    $post = json_decode(file_get_contents("php://input"), true);

                    $order_id = $post['AptOrderID'];


                    $check = $db->checkPharmacyOrderExist($order_id);
                    if ($check) {
                        $process = $db->terimaBarangPharmacy($order_id);
                        if ($process) {
                            $return = array(
                                "status" => 200,
                                "message" => "Order Pharmacy anda telah diselesaikan"
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Gagal membatalkan order!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data tidak ditemukan, atau sudah dibatalkan!"
                        );
                    }
                    echo json_encode($return);
                }


                /**
                 * API Get Nurse Chat Data
                 * @param : OrderID (JSON)
                 * returns data
                 */
                if ($content == "chat") {

                    $order_id = null;

                    $post = json_decode(file_get_contents("php://input"), true);
                    $order_id = $post['OrderID'];

                    if (isset($order_id)) {

                        $getNurse = $db->getNurseByOrderID($order_id);
                        if ($getNurse != null) {

                            $getData = $db->getChat($order_id);
                            if ($getData != null) {

                                $nrz = $getNurse->fetch_assoc();

                                $total_rows = mysqli_num_rows($getData);
                                while ($row = $getData->fetch_assoc()) {
                                    $rows[] = $row;
                                }

                                $return = array(
                                    "status" => 200,
                                    "message" => "ok",
                                    "NurseID" => $nrz['NurseID'],
                                    "NurseName" => $nrz['FirstName'] . ' ' . $nrz['LastName'],
                                    "total_rows" => $total_rows,
                                    "data" => $rows
                                );
                            } else {

                                $nrz = $getNurse->fetch_assoc();

                                $return = array(
                                    "status" => 200,
                                    "NurseID" => $nrz['NurseID'],
                                    "NurseName" => $nrz['FirstName'] . ' ' . $nrz['LastName'],
                                    "total_rows" => "0",
                                    "message" => "Belum ada riwayat chat!"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API Get Doctor Chat Data
                 * @param : OrderID (JSON)
                 * returns data
                 */
                if ($content == "chat_doctor") {

                    $order_id = null;

                    $post = json_decode(file_get_contents("php://input"), true);
                    $order_id = $post['OrderID'];

                    if (isset($order_id)) {

                        $getNurse = $db->getDoctorByOrderID($order_id);
                        if ($getNurse != null) {

                            $getData = $db->getChatDoctor($order_id);
                            if ($getData != null) {

                                $nrz = $getNurse->fetch_assoc();

                                $total_rows = mysqli_num_rows($getData);
                                while ($row = $getData->fetch_assoc()) {
                                    $rows[] = $row;
                                }

                                $return = array(
                                    "status" => 200,
                                    "message" => "ok",
                                    "DoctorID" => $nrz['DoctorID'],
                                    "DoctorName" => $nrz['FirstName'] . ' ' . $nrz['LastName'],
                                    "total_rows" => $total_rows,
                                    "data" => $rows
                                );
                            } else {

                                $nrz = $getNurse->fetch_assoc();

                                $return = array(
                                    "status" => 200,
                                    "DoctorID" => $nrz['DoctorID'],
                                    "DoctorName" => $nrz['FirstName'] . ' ' . $nrz['LastName'],
                                    "total_rows" => "0",
                                    "message" => "Belum ada riwayat chat!"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API Send Chat Data Nurse
                 * @param : UserID (JSON)
                 * returns data
                 */
                if ($content == "send_chat") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $order_id = $post['OrderID'];
                    $message = $post['Message'];
                    $from = $post['ChatFrom'];
                    $to = $post['ChatTo'];
                    $row_id = $post['RowID'];

                    if (isset($order_id) && isset($message) && isset($from) && isset($to)) {

                        $create = $db->createChat($order_id, $message, $from, $to);
                        if ($create) {
                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "RowID" => $row_id
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Gagal mengirim chat!",
                                "RowID" => $row_id
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

                /**
                 * API Send Image Chat Nurse
                 * @param : Image Byte (Multipart Data)
                 * returns data
                 */
                if ($content == "file_chat") {

                    $order_id = $_POST['OrderID'];
                    $message = $_POST['Message'];
                    $from = $_POST['ChatFrom'];
                    $to = $_POST['ChatTo'];
                    $row_id = $_POST['RowID'];
                    $image = $_FILES['image'];

                    if (isset($image) && isset($order_id) && isset($message) && isset($from) && isset($to)) {

                        //folder path
                        $uploaddir = $uploaddir . '/chats/nurse_orders/' . $order_id . '/';

                        //create if not exist
                        if (!file_exists($uploaddir)) {
                            mkdir($uploaddir, 0777, true);
                        }

                        //file name
                        $uploadfile = $uploaddir . basename($_FILES['image']['name']);

                        //Check if it's image type
                        $isImage = $db->is_image($_FILES['image']['tmp_name']);
                        if ($isImage) {

                            //Do upload to folder
                            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
                                //Save to DB
                                $createID = $db->createChatFile($order_id, $message, $from, $to, $_FILES['image']['name']);
                                if ($createID != null) {

                                    //Get filename
                                    $currentName = $db->getFileChatNurse($createID);
                                    $url = $uploaddir . "/chats/nurse_orders/" . $order_id . "/" . $currentName;

                                    $return = array(
                                        "status" => 200,
                                        "message" => "ok",
                                        "url" => $url,
                                        "RowID" => $row_id
                                    );
                                } else {
                                    $return = array(
                                        "status" => 404,
                                        "message" => "Gagal mengirim chat!",
                                        "RowID" => $row_id
                                    );
                                }
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Gagal mengirim gambar!",
                                    "RowID" => $row_id
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "File yang anda kirim bukan file gambar!",
                                "RowID" => $row_id
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Oops sepertinya ada yang salah!",
                            "RowID" => $row_id
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API Send Chat Data Doctor
                 * @param : UserID (JSON)
                 * returns data
                 */
                if ($content == "send_chat_doctor") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $order_id = $post['OrderID'];
                    $message = $post['Message'];
                    $from = $post['ChatFrom'];
                    $to = $post['ChatTo'];
                    $row_id = $post['RowID'];

                    if (isset($order_id) && isset($message) && isset($from) && isset($to)) {

                        $create = $db->createChatDoctor($order_id, $message, $from, $to);
                        if ($create) {
                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "RowID" => $row_id
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Gagal mengirim chat!",
                                "RowID" => $row_id
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!",
                            "RowID" => $row_id
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API Send Image Chat Nurse
                 * @param : Image Byte (Multipart Data)
                 * returns data
                 */
                if ($content == "file_chat_doctor") {

                    $order_id = $_POST['OrderID'];
                    $message = $_POST['Message'];
                    $from = $_POST['ChatFrom'];
                    $to = $_POST['ChatTo'];
                    $row_id = $_POST['RowID'];
                    $image = $_FILES['image'];

                    if (isset($image) && isset($order_id) && isset($message) && isset($from) && isset($to)) {

                        //folder path
                        $uploaddir = $uploaddir . '/chats/doctor_orders/' . $order_id . '/';

                        //create if not exist
                        if (!file_exists($uploaddir)) {
                            mkdir($uploaddir, 0777, true);
                        }

                        //file name
                        $uploadfile = $uploaddir . basename($_FILES['image']['name']);

                        //Check if it's image type
                        $isImage = $db->is_image($_FILES['image']['tmp_name']);
                        if ($isImage) {

                            //Do upload to folder
                            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
                                //Save to DB
                                $createID = $db->createChatFileDoctor($order_id, $message, $from, $to, $_FILES['image']['name']);
                                if ($createID != null) {

                                    //Get filename
                                    $currentName = $db->getFileChatDoctor($createID);
                                    $url = $uploaddir . $currentName;

                                    $return = array(
                                        "status" => 200,
                                        "message" => "ok",
                                        "url" => $url,
                                        "RowID" => $row_id
                                    );
                                } else {
                                    $return = array(
                                        "status" => 404,
                                        "message" => "Gagal mengirim chat!",
                                        "RowID" => $row_id
                                    );
                                }
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Gagal mengirim gambar!",
                                    "RowID" => $row_id
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "File yang anda kirim bukan file gambar!",
                                "RowID" => $row_id
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Oops sepertinya ada yang salah!",
                            "RowID" => $row_id
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API Give Rate & Feeback Nurse
                 * @param : OrderID, Rate, Feedback (JSON)
                 * returns data
                 */
                if ($content == "give_rating") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $order_id = $post['OrderID'];
                    $rate = $post['Rate'];
                    $feedback = $post['Feedback'];

                    if (isset($order_id) && isset($rate) && isset($feedback)) {

                        $check = $db->checkFinishOrder($order_id);
                        if ($check) {

                            $create = $db->giveRating($order_id, $rate, $feedback);
                            if ($create) {
                                $return = array(
                                    "status" => 200,
                                    "message" => "Terima kasih atas feedback nya"
                                );
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Gagal memberikan rating & feedback!"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API Give Rate & Feeback Doctor
                 * @param : OrderID, Rate, Feedback (JSON)
                 * returns data
                 */
                if ($content == "give_rating_doctor") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $order_id = $post['OrderID'];
                    $rate = $post['Rate'];
                    $feedback = $post['Feedback'];

                    if (isset($order_id) && isset($rate) && isset($feedback)) {

                        $check = $db->checkFinishOrderDoctor($order_id);
                        if ($check) {

                            $create = $db->giveRatingDoctor($order_id, $rate, $feedback);
                            if ($create) {
                                $return = array(
                                    "status" => 200,
                                    "message" => "Terima kasih atas feedback nya"
                                );
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Gagal memberikan rating & feedback!"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API Process Logout
                 * @param : UserID (JSON)
                 * returns data
                 */
                if ($content == "logout") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $post['UserID'];

                    if (isset($user_id)) {

                        $process = $db->processLogout($user_id);
                        if ($process) {
                            $return = array(
                                "status" => 200,
                                "message" => "ok"
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Gagal keluar dari akun, mohon coba beberapa saat lagi!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return);
                }

                /**
                 * API Get Nearest Laboratorium
                 * @param : Latitude, Longitude (JSON)
                 * returns data
                 */
                if ($content == "get_laboratorium") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $latitude = $post['Latitude'];
                    $longitude = $post['Longitude'];

                    if (isset($latitude) && isset($longitude)) {

                        $getData = $db->getLaboratoriumByLocation($latitude, $longitude);
                        if ($getData != null) {

                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }

                            $total = mysqli_num_rows($getData);

                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Oops, sepertinya ada yang salah!"
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API Get package Laboratorium
                 * @param : lab_id (JSON)
                 * returns data
                 */
                if ($content == "get_laboratorium_package") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $lab_id = $post['LabID'];

                    if (isset($lab_id)) {

                        $getData = $db->getLabPackageByLabId($lab_id);
                        if ($getData) {

                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }

                            $total = mysqli_num_rows($getData);

                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 200,
                                "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!",
                                "data" => []
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Oops, ada parameter yang kurang!"
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API create order lab package
                 * @param : lab_id (JSON)
                 * returns data
                 */
                if ($content == "create_order_lab_products") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $userid_header;
                    $lab_id = $post['LabID'];
                    $lab_products_id = $post['LabProductID'];
                    $product_name = $post['ProductName'];
                    $description = $post['Description'];
                    $price = $post['Price'];

                    if (isset($user_id) && isset($product_name) && isset($description) && isset($price) && isset($lab_id) && isset($lab_products_id)) {
                        $getData = $db->insertLabOrder($user_id, $lab_id, $lab_products_id, $description, $price);

                        if ($getData) {
                            $getData = $getData->fetch_assoc();
                            $data = $db->getLaboratoriumById($lab_id);
                            $email = '';
                            if ($data) {
                                $data = $data->fetch_assoc();
                                $email = $data['Email'];
                            }
                            if ($email != '') {
                                /*send email html*/
                                $item = '<tr class="heading">
					                                <td style="padding: 5px; vertical-align: top; background: #eee; border-bottom: 1px solid #ddd; font-weight: bold;" valign="top">
					                                    Package Description
					                                </td>
					                                
					                                <td style="padding: 5px; vertical-align: top; text-align: right; background: #eee; border-bottom: 1px solid #ddd; font-weight: bold;" valign="top" align="right">					                                   
					                                </td>
					                            </tr>
												<tr class="item">
					                                <td style="padding: 5px; vertical-align: top; border-bottom: 1px solid #eee;" valign="top" colspan="2">
					                                	' . $product_name . '
					                                    ' . $description . '
					                                </td>
					                                
					                            </tr>

						                        <tr class="total">
						                                <td style="padding: 5px; vertical-align: top;" valign="top"></td>
						                                
						                                <td style="padding: 5px; vertical-align: top; text-align: right; border-top: 2px solid #eee; font-weight: bold;" valign="top" align="right">
						                                   Total: ' . number_format($getData['Total'], 0, ',', '.') . '
						                                </td>
					                            </tr>';

                                $user = $db->getUserByID($user_id);
                                if ($user) {
                                    $user = $user->fetch_assoc();
                                    $to = $email;
                                    $subject = "Pemesanan Paket Laboratorium";
                                    $name = $user['FirstName'] . ' ' . $user['LastName'];
                                    $lab_name = $data['Name'];
                                    $title = 'PEMESANAN PAKET LABORATORIUM';
                                    $message = file_get_contents('../view/booking_lab_product.php');
                                    $message = str_replace('%name%', $name, $message);
                                    $message = str_replace('%lab_name%', $lab_name, $message);
                                    $message = str_replace('%title%', $title, $message);
                                    $message = str_replace('%item%', $item, $message);
                                    $message = str_replace('%order_no%', $getData['OrderNo'], $message);
                                    $message = str_replace('%order_date%', date_format(date_create($getData['OrderDate']), 'd-m-Y'), $message);
                                    $message = str_replace('%user_name%', $name, $message);
                                    $message = str_replace('%user_phone%', $user['Phone'], $message);
                                    $message = str_replace('%user_email%', $user['Email'], $message);
                                    $sendmail = $dbutil->send_email_html($to, $subject, $message);
                                }
                                /*end email html*/

                                $return = array(
                                    "status" => 200,
                                    "message" => "Order sukses, pemesanan anda telah dikirim ke email pihak lab",
                                    "send_email" => $sendmail
                                );
                            } else {
                                $return = array(
                                    "status" => 200,
                                    "message" => "Order sukses, pemesanan anda telah disimpan di history"
                                );
                            }

                        } else {
                            $return = array(
                                "status" => 200,
                                "message" => "Order gagal, data tidak berhasil diinput!",
                                "data" => []
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!",
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API create order lab package
                 * @param : lab_id (JSON)
                 * returns data
                 */
                if ($content == "history_lab_order") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $userid_header;

                    if (isset($user_id)) {
                        $getData = $db->getLaboratoriumOrderByUserId($user_id);

                        if ($getData) {
                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }

                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "rows" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 200,
                                "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!",
                                "data" => []
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!",
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API get user wallet
                 * @param :  (JSON)
                 * returns data
                 */
                if ($content == "get_user_wallet") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $userid_header;

                    if (isset($user_id)) {
                        $getData = $db->getUserWalletByUserId($user_id);
                        if ($getData) {
                            //BILA di datanase user_wallet terdapat UserID tersebut
                            $user = $db->getUserByID($user_id);
                            if ($user) {
                                $user = $user->fetch_assoc();
                                $password_salt = $user["PasswordSalt"];
                                if (isset($password_salt)) {
                                    while ($row = $getData->fetch_assoc()) {
                                        $row["Total"] = base64_encode($row["Total"] . $password_salt);
                                        $row["PasswordSalt"] = $user["PasswordSalt"];
                                        $rows[] = $row;
                                    }
                                    $return = array(
                                        "status" => 200,
                                        "message" => "ok",
                                        "data" => $rows
                                    );
                                } else {
                                    //if PasswordSalt user not found, use GoogleUserID
                                    $google_user_id = $user['GoogleUserID'];
                                    if (isset($google_user_id)) {
                                        while ($row = $getData->fetch_assoc()) {
                                            $row["Total"] = base64_encode($row["Total"] . $google_user_id);
                                            $row["PasswordSalt"] = $google_user_id;
                                            $rows[] = $row;
                                        }

                                        $return = array(
                                            "status" => 200,
                                            "message" => "ok",
                                            "data" => $rows
                                        );
                                    } else {
                                        $return = array(
                                            "status" => 404,
                                            "message" => "Untuk akses saldo wallet, harap isi password atau google user id anda"
                                        );
                                    }
                                }
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "User tidak ditemukan"
                                );
                            }
                        } else {
                            //BILA di datanase user_wallet belum terdapat UserID tersebut
                            $user = $db->getUserByID($user_id);
                            if ($user) {
                                $user = $user->fetch_assoc();
                                $password_salt = $user["PasswordSalt"];
                                if (isset($password_salt)) {
                                    $row_null["Total"] = base64_encode('0' . $password_salt);
                                    $row_null["PasswordSalt"] = $user["PasswordSalt"];
                                    $rows[] = $row_null;
                                    $return = array(
                                        "status" => 200,
                                        "message" => "ok",
                                        "data" => $rows
                                    );
                                } else {
                                    //if PasswordSalt user not found, use GoogleUserID
                                    $google_user_id = $user['GoogleUserID'];
                                    if (isset($google_user_id)) {

                                        $row["Total"] = base64_encode('0' . $google_user_id);
                                        $row["PasswordSalt"] = $google_user_id;
                                        $rows[] = $row;


                                        $return = array(
                                            "status" => 200,
                                            "message" => "ok",
                                            "data" => $rows
                                        );
                                    } else {
                                        $return = array(
                                            "status" => 404,
                                            "message" => "Untuk akses saldo wallet, harap isi password atau google user id anda"
                                        );
                                    }
                                }
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "User tidak ditemukan"
                                );
                            }
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!",
                        );
                    }

                    echo json_encode($return);
                }

                if ($content == 'get_nominal_topup') {

                    $getData = $db->getNominalTopUp();
                    if ($getData) {
                        while ($row = $getData->fetch_assoc()) {
                            $rows[] = $row;
                        }
                        $return = array(
                            "status" => 200,
                            "message" => "ok",
                            "data" => $rows
                        );
                    } else {
                        $return = array(
                            "status" => 200,
                            "message" => "database nominal kosong, mohon coba beberapa saat lagi!",
                            "data" => []
                        );
                    }


                    echo json_encode($return);

                }

                /**
                 * API Process Cancel Topup
                 * @param : OrderID (JSON)
                 * returns data
                 */
                if ($content == "cancel_topup_wallet") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $topup_id = $post['OrderID'];

                    if (isset($topup_id)) {

                        $check = $db->checkTopUpExist($topup_id);
                        if ($check) {

                            $getData = $db->processCancelTopUp($topup_id);
                            if ($getData) {


                                $return = array(
                                    "status" => 200,
                                    "message" => "Berhasil Membatalkan TopUp",
                                    "data" => $rows
                                );
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Gagal membatalkan topup, mohon coba beberapa saat lagi!"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan!"
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

                if ($content == "get_user_topup_history") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $post['UserID'];

                    if (isset($user_id)) {

                        $getData = $db->getTopUpUser($user_id);
                        if ($getData != null) {

                            $total_rows = mysqli_num_rows($getData);

                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }


                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "total_rows" => $total_rows,
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 200,
                                "total_rows" => "0",
                                "message" => "Data tidak tersedia!"
                            );
                        }

                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }

                    echo json_encode($return);
                }

                if ($content == "history_topup_wallet_detail") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $post['UserID'];
                    $amount = $post['Amount'];

                    if (isset($user_id) && isset($amount)) {

                        $getData = $db->getTopUpUserDetail($user_id, $amount);
                        if ($getData != null) {

                            $total_rows = mysqli_num_rows($getData);

                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }


                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "total_rows" => $total_rows,
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 200,
                                "total_rows" => "0",
                                "message" => "Data tidak tersedia!"
                            );
                        }

                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }

                    echo json_encode($return);
                }

                if ($content == 'pending_top_up_wallet') {
                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $post['UserID'];
                    $amount = $post['total_payment'];  //
                    $nominal_id = $post['ProductID'];
                    $unique_code = $post['KodeUnik'];
                    $payment_type_id = $post['PaymentTypeID'];

                    if (isset($user_id)) {
                        $topup_id = $db->pendingTopUpUserWalletByUserId($user_id, $amount, $nominal_id, $unique_code, $payment_type_id);
                        if ($topup_id) {
                            $OrderID = $db->getOrderID($topup_id);
                            if ($OrderID) {
                                while ($row = $OrderID->fetch_assoc()) {
                                    $rows[] = $row;
                                }
                                $return = array(
                                    "status" => 200,
                                    "message" => "ok",
                                    "data" => $rows
                                );

                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Top Up tidak ditemukan"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 200,
                                "message" => "Top Up Wallet gagal, mohon coba beberapa saat lagi!",
                                "data" => []
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!",
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API Process Payment Topup
                 * @param : OrderID (JSON)
                 * returns data
                 */
                if ($content == "process_payment_topup") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $order_id = $post['OrderID'];
                    $kode_unik = $post['KodeUnik'];
                    $payment_type_id = $post['PaymentTypeID'];
                    $totalpayment = str_replace('.', '', str_replace('Rp. ', '', $post['TotalPayment']));

                    if (isset($order_id) && isset($kode_unik)) {

                        $check = $db->checkTopUpExist($order_id);
                        if ($check) {

                            $getData = $db->processPaymentTopUp($order_id, $kode_unik, $payment_type_id, $totalpayment);
                            if ($getData) {
                                while ($row = $getData->fetch_assoc()) {
                                    $rows[] = $row;
                                }

//                                print_r($rows);die();

                                $email_content = $db->getConfig('user_email_content_order_waiting_payment_topup')->fetch_assoc();


                                if ($getData) {
                                    /*send email html*/
                                    $to = $rows[0]['Email'];
                                    $subject = 'Top Up VTAL';
                                    $message = file_get_contents('../view/invoice_email.php');

                                    if ($email_content) {
                                        $message = str_replace('%content%', $email_content['Value'], $message);
                                    }
                                    $total_transfer = (int)($rows[0]['Amount']);
                                    $rand = "Rp. " . number_format($total_transfer, 0, ",", ".") . "";
                                    $name = $rows[0]['FirstName'] . ' ' . $rows[0]['LastName'];
                                    $title = 'TOP UP VTAL, OrderNo: "' . $rows[0]['OrderNo'] . '"';
                                    $message = str_replace('%name%', $name, $message);
                                    $message = str_replace('%rand%', $rand, $message);
                                    $message = str_replace('%title%', $title, $message);
                                    $sendmail = $dbutil->send_email_html($to, $subject, $message);
                                    /*end email html*/

                                    $custom_data = array(
                                        'type' => '96', //Notification Chat Doctor
                                        'body' => 'Ayo segera selesaikan pembayaran Top Up Kamu',
                                        'title' => "Top Up VTAL",
                                        'UserID' => $rows[0]['UserID']
                                    );

                                    $db->sendNotification_Patient($rows[0]['FirebaseID'], $custom_data);

                                }

                                $return = array(
                                    "status" => 200,
                                    "message" => "Menuggu Pembayaran",
                                    "data" => $rows,
                                    "send_email" => $sendmail
                                );
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Gagal memproses pembayaran, mohon coba beberapa saat lagi!"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan!"
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

                /**
                 * API Confirm Payment Transfer TopUp
                 * @param : UserID, PaymentAccountID, SenderBankName, SenderBankAccName, SenderBankAccNo, TransferDate, Total (JSON)
                 * returns data
                 */
                if ($content == "confirm_transfer_topup") {

                    $topup_id = $_POST['OrderID'];
                    $payment_accound_id = $_POST['PaymentAccountID'];
                    $bank_name = $_POST['SenderBankName'];
                    $account_name = $_POST['SenderBankAccName'];
                    $account_no = $_POST['SenderBankAccNo'];
                    $trf_date = $_POST['TransferDate'];
                    $total = $_POST['Total'];
                    $image = $_FILES['image'];
                    $image = $_FILES['image'];
                    $UniqueCode = $_POST['UniqueCode'];


                    if (isset($topup_id) && isset($payment_accound_id) && isset($bank_name) && isset($account_name) && isset($account_no) && isset($trf_date) && isset($total)) {

                        $check = $db->checkConfirmPaymentPendingTopUp($topup_id);
                        if (!$check) {

                            $id = $db->confirmPaymentTransferTopUp($userid_header, $topup_id, $payment_accound_id, $bank_name, $account_name, $account_no, $trf_date, $total, $UniqueCode);
                            // $id = 24;
                            if ($id != null) {

                                //Upload Photo
                                $uploaddir = $uploaddir . '/patient_payments/topup_wallet/';
                                // $uploaddir = '/var/www/html/public_html/image/patient_payments/nurse_orders/';
                                $uploadfile = $uploaddir . $id . ".jpg";
                                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {

                                } else {
                                    echo exec('whoami') . '<br>';
                                    echo sys_get_temp_dir() . PHP_EOL . '<br>';
                                    echo $_FILES['image']['tmp_name'];
                                    var_dump($res);
                                }

                                $user = $db->getUserByID($userid_header);
                                if ($user) {
                                    $user = $user->fetch_assoc();
                                    /*send email html*/

                                    $to = $user['Email'];
                                    $subject = 'Menunggu Verifikasi Pembayaran Top Up Wallet';
                                    $message = file_get_contents('../view/invoice_email.php');
                                    $email_content = $db->getConfig('user_email_content_order_waiting_verification_topup')->fetch_assoc();
                                    if ($email_content) {
                                        $message = str_replace('%content%', $email_content['Value'], $message);
                                    }

                                    $name = $user['FirstName'] . ' ' . $user['LastName'];
                                    $title = 'PEMBAYARAN ANDA SEDANG DIPROSES LEBIH LANJUT';
                                    $message = str_replace('%name%', $name, $message);
                                    $message = str_replace('%title%', $title, $message);
                                    $sendmail = $dbutil->send_email_html($to, $subject, $message);
                                    /*end email html*/
                                }

                                /*send_pusher*/
                                $type = 'NewUserTopUpPayment';
                                $message = 'New user topup payment found';
                                $sendpusher = $dbutil->send_pusher($type, $message, []);
                                /*end send_pusher*/

                                $return = array(
                                    "status" => 200,
                                    "message" => "Terima kasih, anda berhasil melakukan pembayaran, mohon tunggu verifikasi",
                                    "send_email" => $sendmail,
                                    "send_pusher" => $sendpusher,
                                    "uploadfile" => $uploadfile
                                );
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Gagal melakukan konfirmasi pembayaran!"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Anda telah melakukan konfirmasi pembayaran tagihan, mohon tunggu proses verifikasi!"
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

                /**
                 * API get user poin
                 * @param :  (JSON)
                 * returns data
                 */
                if ($content == "get_user_poin") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $userid_header;

                    if (isset($user_id)) {
                        $getData = $db->getUserPoinByUserId($user_id);
                        if ($getData) {
                            $user = $db->getUserByID($user_id);
                            if ($user) {
                                $user = $user->fetch_assoc();
                                $password_salt = $user["PasswordSalt"];
                                if (isset($password_salt)) {
                                    while ($row = $getData->fetch_assoc()) {
                                        $row["Point"] = base64_encode($row["Point"] . $password_salt);
                                        $row["PasswordSalt"] = $user["PasswordSalt"];
                                        $rows[] = $row;
                                    }

                                    $return = array(
                                        "status" => 200,
                                        "message" => "ok",
                                        "data" => $rows
                                    );
                                } else {
                                    //if PasswordSalt user not found, use GoogleUserID
                                    $google_user_id = $user['GoogleUserID'];
                                    if (isset($google_user_id)) {
                                        while ($row = $getData->fetch_assoc()) {
                                            $row["Point"] = base64_encode($row["Point"] . $google_user_id);
                                            $row["PasswordSalt"] = $google_user_id;
                                            $rows[] = $row;
                                        }

                                        $return = array(
                                            "status" => 200,
                                            "message" => "ok",
                                            "data" => $rows
                                        );
                                    } else {
                                        $return = array(
                                            "status" => 404,
                                            "message" => "Untuk akses saldo wallet, harap isi password atau google user id anda"
                                        );
                                    }
                                }
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "User tidak ditemukan"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 200,
                                "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!",
                                "data" => []
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!",
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * API BCA
                 * @param :
                 * returns data
                 */
                if ($content == 'bca_banking_corporate_account') {
                    $post = json_decode(file_get_contents("php://input"), true);
                    $data = $dbbca->get_token();
                    var_dump($data);
                    // $access_token = $access_token["access_token"];
                    if ($data["status"] == 200) {
                        $access_token = $data["data"]->access_token;
                        $data2 = $dbbca->get_signature_balance($access_token);

                        if ($data2["status"] == 200) {
                            var_dump($data2);
                            $signature = $data2['data'][8];
                            $signature = substr($signature, strpos($signature, ":") + 2);
                            $access_token2 = $data2['data'][0];
                            $access_token2 = substr($access_token2, strpos($access_token2, ":") + 2);
                            // echo 'test: '.$access_token2;
                            $data3 = $dbbca->get_balance($access_token2, $signature);
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => $data["status"]
                        );
                    }

                    echo json_encode($return);
                }
                /**
                 * Emergency Button
                 * @param :
                 * returns data
                 */
                if ($content == 'emergency') {
                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $post['UserID'];
                    $latitude = $post['Latitude'];
                    $longitude = $post['Longitude'];

                    if (isset($user_id) && isset($latitude) && isset($longitude)) {

                        $getData = $db->getEmergencyContact($user_id, $latitude, $longitude);
                        if ($getData) {

                            /*send_pusher*/
                            $type = 'NewEmergencyButtonClick';
                            $message = 'There is someone in Emergency Condition';
                            $sendpusher = $dbutil->send_pusher($type, $message, []);
                            /*end send_pusher*/

                            $return = array(
                                "status" => 200,
                                "message" => "Data terkirim"
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data Emergency belum lengkap, silahkan ubah di profil Anda"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Oops, sepertinya ada yang salah!"
                        );
                    }

                    echo json_encode($return);
                }

                /**
                 * Add Emergency Contact
                 * @param :
                 * returns data
                 */
                if ($content == 'edit_emergency') {
                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $post['UserID'];
                    $Name = $post['Name'];
                    $Phone = $post['Phone'];
                    $Email = $post['Email'];
                    $Gender = $post['Gender'];

                    if (isset($Name) && isset($user_id) && isset($Phone)) {


                        $getData = $db->setEmergencyContact($Name, $user_id, $Phone, $Email, $Gender);
                        if ($getData) {
                            $return = array(
                                "status" => 200,
                                "message" => "Data tersimpan"
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data Emergency belum lengkap, silahkan melengkapi Nama dan No HP"
                            );
                        }


                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Oops, sepertinya ada yang salah!"
                        );
                    }

                    echo json_encode($return);
                }

                if ($content == "kode_voucher") {

                    $post = json_decode(file_get_contents("php://input"), true);

                    $kode_voucher = $post['KodeVoucher'];

                    if (isset($kode_voucher)) {


                        $nominal_voucher = $db->checkKodeVoucher($kode_voucher);

                        if ($nominal_voucher != null) {

                            $cek_table_apt_orders = $db->checkKodeVoucherAptKanopiAndNrzOrder($kode_voucher);
                            if ($cek_table_apt_orders != null) {

                                $total_rows = mysqli_num_rows($nominal_voucher);

                                while ($row = $nominal_voucher->fetch_assoc()) {
                                    $rows[] = $row;
                                }


                                $return = array(
                                    "status" => 200,
                                    "message" => "Voucher Valid",
                                    "data" => $rows,
                                );
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Kode Voucher sudah digunakan!"
                                );
                            }
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Kode Voucher tidak ditemukan!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return);

                }

                function utf8ize($d)
                {
                    if (is_array($d)) {
                        foreach ($d as $k => $v) {
                            $d[$k] = utf8ize($v);
                        }
                    } else if (is_string($d)) {
                        return utf8_encode($d);
                    }
                    return $d;
                }

                if ($content == "category_toko") {

                    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $post['UserID'];


                        $getData = $db->getMasterCategory();
                        if ($getData != null) {

                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }

                            $total = mysqli_num_rows($getData);

                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "total_rows" => $total,
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                            );
                        }

                    echo json_encode(utf8ize($return), JSON_PRETTY_PRINT);
                }
                
                if ($content == "get_suppliers") {
                    
                    $post = json_decode(file_get_contents("php://input"), true);
                     $user_id = $post['UserID'];
                    
                    
                    $getData = $db->getDataSuppliers($user_id);
                    if ($getData != null) {
                        
                        while ($row = $getData->fetch_assoc()) {
                            $rows[] = $row;
                        }
                        
                        $total = mysqli_num_rows($getData);
                        
                        $return = array(
                                        "status" => 200,
                                        "message" => "ok",
                                        "total_rows" => $total,
                                        "data" => $rows
                                        );
                    } else {
                        $return = array(
                                        "status" => 404,
                                        "message" => "Data tidak ditemukan, mohon coba beberapa saat lagi!"
                                        );
                    }
                    
                    echo json_encode(utf8ize($return), JSON_PRETTY_PRINT);
                }

              
                
                if ($content == "get_cart") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    //                    $user_id = $userid_header;
                    $user_id = $post['UserID'];
                    
                    $page = null;
                    $limit = 6;
                    if (isset($post['Page'])) {
                        $page = $post['Page'];
                    }
                    
                    if (isset($user_id)) {
                        
                        $getData = $db->getDataCart($user_id, $page, $limit);
                        if ($getData != null) {
                            
                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }
                            
                            $total = mysqli_num_rows($getData);
                            
                            $return = array(
                                            "status" => 200,
                                            "message" => "ok",
                                            "total_rows" => $total,
                                            "data" => $rows
                                            );
                        } else {
                            $return = array(
                                            "status" => 200,
                                            "total_rows" => 0,
                                            "message" => "Belum ada produk di keranjang",
                                            "data" => []
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
                
                if ($content == "product_details") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    //                    $user_id = $userid_header;
                    $product_id = $post['ProductID'];
                    $page = null;
                    $limit = 4;
                    if (isset($post['Page'])) {
                        $page = $post['Page'];
                    }
                    
                    if (isset($product_id)) {
                        
                        $getData = $db->getDataProductDetails($product_id);
                        if ($getData != null) {
                            
                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }
                            
                            $total = mysqli_num_rows($getData);
                            
                            //get Product variants
                            $getProductVariants = $db->getDataProductVariants($product_id);
                            
                            if ($getProductVariants != null) {
                            while ($rowProductVariants = $getProductVariants->fetch_assoc()) {
                                $rowsProductVariants[] = $rowProductVariants;
                            }
                            }else{
                                $rowsProductVariants = [];
                            }
                            $product_variant_id = $db->getProductVariantID($product_id);
                            //get Product Variant details
                          
                            $getProductVariantDetails = $db->getDataProductVariantDetails($product_variant_id);
                            
                         
                            if ($getProductVariantDetails !=null) {
                            while ($rowProductVariantDetails = $getProductVariantDetails->fetch_assoc()) {
                                $rowsProductVariantDetails[] = $rowProductVariantDetails;
                            }
                            }else {
                                $rowsProductVariantDetails = [];
                            }
                                
                           
                            $return = array(
                                            "status" => 200,
                                            "message" => "ok",
                                            "total_rows" => $total,
                                            "products" => $rows,
                                            "product_variants" => $rowsProductVariants,
                                            "product_variant_details" => $rowsProductVariantDetails
                                            );
                        } else {
                            $return = array(
                                            "status" => 200,
                                            "total_rows" => 0,
                                            "message" => "Belum ada Produk",
                                            "data" => []
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
				
					if ($content == "get_size") {
                    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
                    $user_id = $post['UserID'];
					
				
                    if (isset($user_id)) {

                        $getData = $db->getDataSize($user_id);
                        if ($getData != null) {

                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }

                            $total = mysqli_num_rows($getData);

						
                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "total_rows" => $total,
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 200,
								"total_rows" => 0,
                                "message" => "Belum ada ukuran",
								"data" => []
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
				
				if ($content == "get_color") {
                    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
                    $user_id = $post['UserID'];
					
				
                    if (isset($user_id)) {

                        $getData = $db->getDataColor($user_id);
                        if ($getData != null) {

                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }

                            $total = mysqli_num_rows($getData);

						
                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "total_rows" => $total,
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 200,
								"total_rows" => 0,
                                "message" => "Belum ada warna",
								"data" => []
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
				
				if ($content == "get_variants") {
                    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
                    $user_id = $post['UserID'];
					
				
                    if (isset($user_id)) {

                        $getData = $db->getDataVariants($user_id);
                        if ($getData != null) {

                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }

                            $total = mysqli_num_rows($getData);

						
                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "total_rows" => $total,
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 200,
								"total_rows" => 0,
                                "message" => "Belum ada variants",
								"data" => []
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
                
                if ($content == "get_variant_values") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    //                    $user_id = $userid_header;
                    $user_id = $post['UserID'];
                    
                    $variant_id = null;
                    if (isset($post['VariantID'])) {
                        $variant_id = $post['VariantID'];
                    }
                    
                    if (isset($user_id)) {
                        
                        $getData = $db->getDataVariantValues($user_id, $variant_id);
                        if ($getData != null) {
                            
                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }
                            
                            $total = mysqli_num_rows($getData);
                            
                            
                            $return = array(
                                            "status" => 200,
                                            "message" => "ok",
                                            "total_rows" => $total,
                                            "data" => $rows
                                            );
                        } else {
                            $return = array(
                                            "status" => 200,
                                            "total_rows" => 0,
                                            "message" => "Belum ada Variant Values",
                                            "data" => []
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
                
                
                if ($content == "get_product_variants") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    //                    $user_id = $userid_header;
               
                    
                    $product_id = null;
                    if (isset($post['ProductID'])) {
                        $product_id = $post['ProductID'];
                    }
                    
                    if (isset($product_id)) {
                        
                        $getData = $db->getDataProductVariants($product_id);
                        if ($getData != null) {
                            
                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }
                            
                            $total = mysqli_num_rows($getData);
                            
                            
                            $return = array(
                                            "status" => 200,
                                            "message" => "ok",
                                            "total_rows" => $total,
                                            "data" => $rows
                                            );
                        } else {
                            $return = array(
                                            "status" => 200,
                                            "total_rows" => 0,
                                            "message" => "Belum ada Product Variant",
                                            "data" => []
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
                
                
                
                if ($content == "get_product_id") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    //                    $user_id = $userid_header;
                    $user_id = $post['UserID'];
                    
                    
                    if (isset($user_id)) {
                        
                        $getData = $db->getProductsID($user_id);
                        if ($getData != null) {
                            
                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }
                            
                            $total = mysqli_num_rows($getData);
                            
                            
                            $return = array(
                                            "status" => 200,
                                            "message" => "ok",
                                            "total_rows" => $total,
                                            "data" => $rows
                                            );
                        } else {
                            $return = array(
                                            "status" => 200,
                                            "total_rows" => 0,
                                            "message" => "Belum ada product",
                                            "data" => $rows
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
                

				if ($content == "get_category") {
                    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
                    $user_id = $post['UserID'];
					
				
                    if (isset($user_id)) {

                        $getData = $db->getDataCategory($user_id);
                        if ($getData != null) {

                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }

                            $total = mysqli_num_rows($getData);

						
                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "total_rows" => $total,
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 200,
								"total_rows" => 0,
                                "message" => "Belum ada Category",
								"data" => []
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
				
					if ($content == "get_brand") {
                    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
                    $user_id = $post['UserID'];
					
				
                    if (isset($user_id)) {

                        $getData = $db->getDataBrand($user_id);
                        if ($getData != null) {

                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }

                            $total = mysqli_num_rows($getData);

						
                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "total_rows" => $total,
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 200,
								"total_rows" => 0,
                                "message" => "Belum ada Brand",
								"data" => []
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
				
                if ($content == "get_cashier") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    //                    $user_id = $userid_header;
                    $user_id = $post['UserID'];
                    
                
                    if (isset($user_id)) {
                        
                        $getData = $db->getOrderNoCurrent($user_id);
                        if ($getData != null) {
                            
                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }
                            
                            $total = mysqli_num_rows($getData);
                            
                            
                            $return = array(
                                            "status" => 200,
                                            "message" => "ok",
                                            "total_rows" => $total,
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
                    } else {
                        $return = array(
                                        "status" => 404,
                                        "message" => "Oops sepertinya ada yang salah!"
                                        );
                    }
                    
                    echo json_encode($return);
                }
				
				if ($content == "get_subcategory1") {
                    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
                    $user_id = $post['UserID'];
					$category_id = $post['CategoryID'];
				
                    if (isset($user_id) && isset($category_id)) {

                        $getData = $db->getDataSubCategory1($category_id);
                        if ($getData != null) {

                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }

                            $total = mysqli_num_rows($getData);

                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "total_rows" => $total,
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 200,
								"total_rows" => 0,
                                "message" => "Belum ada Category",
								"data" => []
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
				
				if ($content == "get_subcategory2") {
                    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
                    $user_id = $post['UserID'];
					$sub_category_id = $post['SubCategoryID1'];
				
                    if (isset($user_id)&& isset($sub_category_id)) {

                        $getData = $db->getDataSubCategory2($sub_category_id);
                        if ($getData != null) {

                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }

                            $total = mysqli_num_rows($getData);

                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "total_rows" => $total,
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 200,
								"total_rows" => 0,
                                "message" => "Belum ada Category",
								"data" => []
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

                if ($content == "insert_data_asuransi") {
                    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
                    $user_id = $post['UserID'];
                    $id_master_asuransi = $post['id_master_asuransi'];
                    $NoPolis = $post['NoPolis'];
                    $StartDate = $post['StartDate'];
                    $EndDate = $post['EndDate'];
                    $PackageName = $post['PackageName'];
                    $Benefit = $post['Benefit'];
                    $JenisAsuransi = $post['JenisAsuransi'];

                    if (isset($user_id) && isset($id_master_asuransi) && isset($NoPolis) && isset($StartDate) && isset($EndDate) && isset($PackageName) && isset($Benefit) && isset($JenisAsuransi)) {

                        $proses = $db->insertDataAsuransi($user_id, $id_master_asuransi, $NoPolis, $StartDate, $EndDate, $PackageName, $Benefit, $JenisAsuransi);
                        if ($proses) {
                            $return = array(
                                "status" => 200,
                                "message" => "Data tersimpan"
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Gagal menyimpan data!"
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

                if ($content == "detail_data_asuransi") {
                    $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
                    $id = $post['id'];

                    if (isset($id)) {

                        $getData = $db->getDetailDataAsuransi($id);
                        if ($getData != null) {

                            while ($row = $getData->fetch_assoc()) {
                                $rows[] = $row;
                            }


                            $return = array(
                                "status" => 200,
                                "message" => "ok",
                                "data" => $rows
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Belum ada data asuransi!"
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

                if ($content == "update_data_asuransi") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $id = $post['id'];
//                    $user_id = $userid_header;
//                    $user_id = $post['UserID'];
                    $id_master_asuransi = $post['id_master_asuransi'];
                    $NoPolis = $post['NoPolis'];
                    $StartDate = $post['StartDate'];
                    $EndDate = $post['EndDate'];
                    $PackageName = $post['PackageName'];
                    $Benefit = $post['Benefit'];
                    $JenisAsuransi = $post['JenisAsuransi'];

                    if (isset($id) && isset($id_master_asuransi) && isset($NoPolis) && isset($StartDate) && isset($EndDate) && isset($PackageName) && isset($Benefit) && isset($JenisAsuransi)) {

                        $proses = $db->updateDataAsuransi($id, $id_master_asuransi, $NoPolis, $StartDate, $EndDate, $PackageName, $Benefit, $JenisAsuransi);
                        if ($proses) {
                            $return = array(
                                "status" => 200,
                                "message" => "Data berhasil diupdate"
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Gagal mengupdate data!"
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

                if ($content == "delete_data_asuransi") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $id = $post['id'];
//                    $user_id = $userid_header;
//                    $user_id = $post['UserID'];
                    if (isset($id)) {

                        $proses = $db->deleteDataAsuransi($id);
                        if ($proses) {
                            $return = array(
                                "status" => 200,
                                "message" => "Data berhasil dihapus"
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Gagal mengahapus data!"
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

                /**
                 * API get Family of patient
                 * @param : UserID from header (JSON)
                 * returns data
                 */
                if ($content == "get_user_family_bood_request") {
                    $post = json_decode(file_get_contents("php://input"), true);
                    $UserID = $userid_header;

                    $data = $db->getUserFamily($UserID);

                    if ($data) {
                        while ($row = $data->fetch_assoc()) {
                            $rows[] = $row;
                        }

                        $total = mysqli_num_rows($data);

                        $return = array(
                            "status" => 200,
                            "message" => "ok",
                            "total_rows" => $total,
                            "data" => $rows
                        );
                    }
                    else {
                        $return = array(
                            "status" => 200,
                            "message" => "Data user patient tidak ditemukan, mohon coba beberapa saat lagi!",
                            "data" => []
                        );
                    }

                    echo json_encode($return);
                }

              
            
              
                
                /**
                 * Blood Bank Button
                 * @param :
                 * returns data
                 */
                if ($content == 'blood_bank') {
                    $post = json_decode(file_get_contents("php://input"), true);
                    $UserID = $userid_header;
                    $nama = $post['Nama'];
                    $JenisKelamin = $post['JenisKelamin'];
                    $Umur = $post['Umur'];
                    $Tinggi = $post['Tinggi'];
                    $Berat = $post['Berat'];
                    $JenisGolonganDarah = $post['JenisGolonganDarah'];
                    $Rhesus = $post['Rhesus'];
                    $Alamat = $post['Alamat'];
                    $Phone = $post['Phone'];
                    $Keterangan = $post['Keterangan'];
                    $Jumlahcc = $post['Jumlah_cc'];
                    $latitude = $post['Latitude'];
                    $longitude = $post['Longitude'];

                    if (isset($UserID) && isset($nama) && isset($JenisKelamin) && isset($Umur) && isset($Tinggi) && isset($Berat) && isset($JenisGolonganDarah) && isset($Rhesus) && isset($Alamat) && isset($Phone) && isset($Keterangan) && isset($Jumlahcc) && isset($latitude) && isset($longitude)) {

                        $getData = $db->sendBloodRequest($UserID, $nama, $JenisKelamin, $Umur,$Tinggi,$Berat,$JenisGolonganDarah,$Rhesus,$Alamat,$Phone,$Keterangan,$Jumlahcc,$latitude,$longitude);
                        if ($getData) {

                            /*send_pusher*/
                            $type = 'NewBloodRequestButtonClick';
                            $message = 'There is someone need blood';
                            $sendpusher = $dbutil->send_pusher($type, $message, []);
                            /*end send_pusher*/

                            $return = array(
                                "status" => 200,
                                "message" => "Data terkirim"
                            );
                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Oops, sepertinya ada yang salah!"
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Data yang yang diisi belum lengkap!"
                        );
                    }

                    echo json_encode($return);
                }


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
                "message" => "Oops sesi anda sudah habis!"
            );

            echo json_encode($return);
        }
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

