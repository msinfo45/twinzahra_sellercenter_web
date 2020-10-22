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
$userid_header = "";
$modeHeader = 1;
$uploaddir = $UPLOAD_DIR;
// $uploaddir2 = '/home/vtalid01/api-dev.v-tal.id/v1/images/chats/nurse_orders/'.$order_id.'/';

//-------------------------------------- API Function Start Here ----------------------------------------//

//Check request content
$content = $_GET['request'];
if (isset($content) && $content != "") {

    //Load Models
    include "../config/Model_user.php";
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
    if ($content == "bca_transfer") {
        $modeHeader = 0;
        $referral_by = "";


        echo 'bca_transfer';
    }
}