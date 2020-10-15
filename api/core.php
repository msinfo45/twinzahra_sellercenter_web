<?php
include "../config/db_connection.php";



//-------------------------------------- API Function Start Here ----------------------------------------//

//Check request content
$content = $_GET['request'];

if (isset($content) && $content != "") {

    //Load Models
    include "../config/Model_Core.php";

    $db = new Model_Core();

    if ($content == "get_base_url") {
        $modeHeader = 0;
        $post = json_decode(file_get_contents("php://input"), true);

        $getData = $db->getBaseURL();

        if ($getData != null) {

            while ($row = $getData->fetch_assoc()) {

            $rows[] = $row;
            $Base_Url = $row['baseurl'];

            }


            $return = array(
                "status" => 200,
                "message" => "ok",
                "data" => $Base_Url
            );
        } else {
            $return = array(
                "status" => 200,
                "total_rows" => 0,
                "message" => "Belum ada Produk",
                "data" => []
            );
        }


        echo json_encode($Base_Url);
    }


}

    ?>