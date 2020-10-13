<?php

include "../config/db_connection.php";
include "../config/config_type.php";
include "../config/Util.php";
include "../config/bca_api.php";
include "../config/BCA_api2.php";
include "../config/lazada/LazopSdk.php";

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
    include "../config/Model_ongkir.php";

    $db = new Model_ongkir();
    $dbutil = new Util();
    $dbbca = new BCAAPI();
    $dbbca2 = new bca();


				if ($content == "get_provinsi") {
					$modeHeader = 0;
                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $userid_header;

                    if (isset($user_id)) {

                        $get_provinsi = $db->GetProvinsi();

                        $return = array(
                            "status" => 200,
                            "message" => "Berhasil get data provinsi",
                            "content" => $get_provinsi

                        );


                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return, JSON_UNESCAPED_SLASHES);
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
                 * API Get Data Provinsi
                 * Method "province" digunakan untuk mendapatkan daftar propinsi yang ada di Indonesia.
                 * @param :
                 * returns data
                 */
               
                /**
                 * API Get Data Provinsi by provinsi_id
                 * Method "province" digunakan untuk mendapatkan daftar propinsi yang ada di Indonesia.
                 * @param : provinsi_id
                 * returns data
                 */
                if ($content == "get_provinsi_detail") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $userid_header;
                    $provinsi_id = $post['provinsi_id'];

                    if (isset($user_id) && isset($provinsi_id)) {

                        $get_provinsi = $db->GetProvinsiByID($provinsi_id);

                        $return = array(
                            "status" => 200,
                            "message" => "Berhasil get data provinsi",
                            "content" => $get_provinsi

                        );


                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return, JSON_UNESCAPED_SLASHES);
                }

                /**
                 * API Get Data Kabupaten
                 * Method "city" digunakan untuk mendapatkan daftar kota/kabupaten yang ada di Indonesia.
                 * @param :
                 * returns data
                 */
                if ($content == "get_kabupaten") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $userid_header;

                    if (isset($user_id)) {

                        $get_kabupaten = $db->GetKabupaten();

                        $return = array(
                            "status" => 200,
                            "message" => "Berhasil get data kabupaten",
                            "content" => $get_kabupaten

                        );


                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return, JSON_UNESCAPED_SLASHES);
                }

                /**
                 * API Get Data Kabupaten by
                 * Method "city" digunakan untuk mendapatkan daftar kota/kabupaten yang ada di Indonesia.
                 * @param : provinsi_id, kabupaten_id
                 * returns data
                 */
                if ($content == "get_kabupaten_detail") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $userid_header;
                    $provinsi_id = $post['provinsi_id'];
                    $kabupaten_id = $post['kabupaten_id'];

                    if (isset($user_id) && isset($provinsi_id) && isset($kabupaten_id)) {

                        $get_kabupaten = $db->GetKabupatenByID($provinsi_id, $kabupaten_id);

                        $return = array(
                            "status" => 200,
                            "message" => "Berhasil get data kabupaten",
                            "content" => $get_kabupaten

                        );


                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return, JSON_UNESCAPED_SLASHES);
                }

                /**
                 * API Get Data Kecamatan
                 * Method "city" digunakan untuk mendapatkan daftar kota/kabupaten yang ada di Indonesia.
                 * @param : kabupaten_id
                 * returns data
                 */
                if ($content == "get_kecamatan") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $userid_header;
                    $kabupaten_id = $post['kabupaten_id'];

                    if (isset($user_id) && isset($kabupaten_id)) {

                        $get_kacamatan = $db->GetKecamatan($kabupaten_id);

                        $return = array(
                            "status" => 200,
                            "message" => "Berhasil get data kecamatan",
                            "content" => $get_kacamatan

                        );


                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return, JSON_UNESCAPED_SLASHES);
                }

                /**
                 * API Cek Ongkir By Kurir
                 * Method cek_ongkir digunakan untuk mengetahui tarif pengiriman (ongkos kirim) dari dan ke kabupaten tujuan tertentu dengan berat tertentu pula.
                 * @param :kota_asal, kota_tujuan, berat, kurir
                 * returns data
                 */
                if ($content == "cek_ongkir_by_kabupaten_kabupaten_kurir") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $userid_header;
                    $kabupaten_asal = $post['kabupaten_asal'];
                    $kabupaten_tujuan = $post['kabupaten_tujuan'];
                    $berat = $post['berat'];
                    $kurir = $post['kurir'];

                    if (isset($user_id) && isset($kabupaten_asal) && isset($kabupaten_tujuan) && isset($berat) && isset($kurir)) {

                        $cek_ongkir = $db->CekOngkirByKabupatenKabupatenKurir($kabupaten_asal, $kabupaten_tujuan, $berat, $kurir);

                        $return = array(
                            "status" => 200,
                            "message" => "Berhasil get data cek ongkir dari kabupaten asal ke kabupaten tujuan",
                            "content" => $cek_ongkir

                        );


                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return, JSON_UNESCAPED_SLASHES);
                }

                /**
                 * API Cek Ongkir By Kurir
                 * Method cek_ongkir digunakan untuk mengetahui tarif pengiriman (ongkos kirim) dari dan ke kecamatan tujuan tertentu dengan berat tertentu pula.
                 * @param :kota_asal, kota_tujuan, berat, kurir
                 * returns data
                 */
                if ($content == "cek_ongkir_by_kabupaten_kecamatan_kurir") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $userid_header;
                    $kabupaten_asal = $post['kabupaten_asal'];
                    $kecamatan_tujuan = $post['kecamatan_tujuan'];
                    $berat = $post['berat'];
                    $kurir = $post['kurir'];

                    if (isset($user_id) && isset($kabupaten_asal) && isset($kecamatan_tujuan) && isset($berat) && isset($kurir)) {

                        $cek_ongkir = $db->CekOngkirByKabupatenKecamatanKurir($kabupaten_asal, $kecamatan_tujuan, $berat, $kurir);

                        $return = array(
                            "status" => 200,
                            "message" => "Berhasil get data cek ongkir dari kabupaten asal ke kecamatan tujuan",
                            "content" => $cek_ongkir

                        );


                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return, JSON_UNESCAPED_SLASHES);
                }

                /**
                 * API Cek Ongkir By Kurir
                 * Method cek_ongkir digunakan untuk mengetahui tarif pengiriman (ongkos kirim) dari dan ke kecamatan tujuan tertentu dengan berat tertentu pula.
                 * @param :kecamatan_asal, kecamatan_tujuan, berat, kurir
                 * returns data
                 */
                if ($content == "cek_ongkir_by_kecamatan_kecamatan_kurir") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $userid_header;
                    $kecamatan_asal = $post['kecamatan_asal'];
                    $kecamatan_tujuan = $post['kecamatan_tujuan'];
                    $berat = $post['berat'];
                    $kurir = $post['kurir'];

                    if (isset($user_id) && isset($kecamatan_asal) && isset($kecamatan_tujuan) && isset($berat) && isset($kurir)) {

                        $cek_ongkir = $db->CekOngkirByKecamatanKecamatanKurir($kecamatan_asal, $kecamatan_tujuan, $berat, $kurir);

                        $return = array(
                            "status" => 200,
                            "message" => "Berhasil get data cek ongkir dari kecamatan asal ke kecamatan tujuan",
                            "content" => $cek_ongkir

                        );


                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return, JSON_UNESCAPED_SLASHES);
                }

                /**
                 * API Cek Resi
                 * Method cek_resi digunakan melacak/mengetahui status pengiriman berdasarkan nomor resi.
                 * @param :no_resi, kurir
                 * returns data
                 */
                if ($content == "cek_resi") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $userid_header;
                    $no_resi = $post['no_resi'];
                    $kurir = $post['kurir'];

                    if (isset($user_id) && isset($user_id) && isset($kurir)) {

                        $cek_resi = $db->CekResi($no_resi, $kurir);

                        $return = array(
                            "status" => 200,
                            "message" => "Berhasil get data cek resi",
                            "content" => $cek_resi

                        );


                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return, JSON_UNESCAPED_SLASHES);
                }

                /**
                 * API InternationalOrigin
                 * Method "internationalOrigin" digunakan untuk mendapatkan daftar/nama kota yang mendukung pengiriman internasional.
                 * @param :$kabupaten_id, $provinsi_id
                 * returns data
                 */
                if ($content == "InternationalOriginAll") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $userid_header;

                    if (isset($user_id)) {

                        $InternationalOrigin = $db->InternationalOriginAll();

                        $return = array(
                            "status" => 200,
                            "message" => "Berhasil get data daftar/nama kota yang mendukung pengiriman internasional",
                            "content" => $InternationalOrigin

                        );


                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return, JSON_UNESCAPED_SLASHES);
                }

                /**
                 * API InternationalOrigin
                 * Method "internationalOrigin" digunakan untuk mendapatkan daftar/nama kota yang mendukung pengiriman internasional.
                 * @param :$kabupaten_id, $provinsi_id
                 * returns data
                 */
                if ($content == "InternationalOrigin") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $userid_header;
                    $kabupaten_id = $post['kabupaten_id'];
                    $provinsi_id = $post['provinsi_id'];

                    if (isset($user_id) && isset($kabupaten_id) && isset($provinsi_id)) {

                        $InternationalOrigin = $db->InternationalOrigin($kabupaten_id, $provinsi_id);

                        $return = array(
                            "status" => 200,
                            "message" => "Berhasil get data daftar/nama kota yang mendukung pengiriman internasional",
                            "content" => $InternationalOrigin

                        );


                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return, JSON_UNESCAPED_SLASHES);
                }

                /**
                 * API InternationalDestination
                 * Method "internationalDestination" digunakan untuk mendapatkan daftar/nama negara tujuan pengiriman internasional.
                 * @param :negara_id
                 * returns data
                 */
                if ($content == "InternationalDestinationAll") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $userid_header;

                    if (isset($user_id)) {

                        $InternationalDestination = $db->InternationalDestinationAll();

                        $return = array(
                            "status" => 200,
                            "message" => "Berhasil get data daftar/nama negara tujuan pengiriman internasional",
                            "content" => $InternationalDestination

                        );


                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return, JSON_UNESCAPED_SLASHES);
                }

                /**
                 * API InternationalDestination
                 * Method "internationalDestination" digunakan untuk mendapatkan daftar/nama negara tujuan pengiriman internasional.
                 * @param :negara_id
                 * returns data
                 */
                if ($content == "InternationalDestination") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $userid_header;
                    $negara_id = $post['negara_id'];

                    if (isset($user_id) && isset($negara_id)) {

                        $InternationalDestination = $db->InternationalDestination($negara_id);

                        $return = array(
                            "status" => 200,
                            "message" => "Berhasil get data daftar/nama negara tujuan pengiriman internasional",
                            "content" => $InternationalDestination

                        );


                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return, JSON_UNESCAPED_SLASHES);
                }

                /**
                 * API InternationalCost
                 * Method “internationalCost” digunakan untuk mengetahui tarif pengiriman (ongkos kirim) internasional dari kota-kota di Indonesia ke negara tujuan di seluruh dunia.
                 * @param :kabupaten_asal, negara_tujuan,berat,kurir
                 * returns data
                 */
                if ($content == "InternationalCost") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $userid_header;
                    $kabupaten_asal = $post['kabupaten_asal'];
                    $negara_tujuan = $post['negara_tujuan'];
                    $berat = $post['berat'];
                    $kurir = $post['kurir'];

                    if (isset($user_id) && isset($kabupaten_asal) && isset($negara_tujuan) && isset($berat) && isset($kurir)) {

                        $InternationalCost = $db->InternationalCost($kabupaten_asal, $negara_tujuan, $berat, $kurir);

                        $return = array(
                            "status" => 200,
                            "message" => "Berhasil get data tarif pengiriman (ongkos kirim) internasional dari kota-kota di Indonesia ke negara tujuan di seluruh dunia",
                            "content" => $InternationalCost

                        );


                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return, JSON_UNESCAPED_SLASHES);
                }

                /**
                 * API Currency
                 * Method "currency" digunakan untuk mendapatkan informasi nilai tukar rupiah terhadap US dollar.
                 * @param :
                 * returns data
                 */
                if ($content == "Currency") {

                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $userid_header;

                    if (isset($user_id)) {

                        $Currency = $db->Currency();

                        $return = array(
                            "status" => 200,
                            "message" => "Berhasil get data informasi nilai tukar rupiah terhadap US dollar",
                            "content" => $Currency

                        );


                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found!"
                        );
                    }
                    echo json_encode($return, JSON_UNESCAPED_SLASHES);
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