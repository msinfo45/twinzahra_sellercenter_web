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


				if ($content == "get") {
					$modeHeader = 0;
                    $post = json_decode(file_get_contents("php://input"), true);
          
					
			          
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
                                "status" => 200,
								"total_rows" => 0,
                                "message" => "Belum ada data",
								"data" => []
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
