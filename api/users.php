<?php

include "../config/db_connection.php";
include "../config/config_type.php";
//include "../config/Util.php";
//include "../config/bca_api.php";
//include "../config/BCA_api2.php";


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


//-------------------------------------- API Function Start Here ----------------------------------------//

//Check request content
$content = $_GET['request'];

if (isset($content) && $content != "") {

    //Load Models
    include "../config/model.php";

    $db = new Model_user();
    //$dbutil = new Util();
   // $dbbca = new BCAAPI();
   // $dbbca2 = new bca();
//

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
