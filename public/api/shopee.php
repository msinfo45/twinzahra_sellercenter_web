<?php

include "../config/lazada/LazopSdk.php";

include "../config/db_connection.php";
include "../config/config_type.php";

$url='https://api.lazada.co.id/rest';

		
$rowsLazada = array();
    $content = $_GET['request'];
    if (isset($content) && $content != "") {
        
          //Load Models
    include "../config/model.php";

    $db = new Model_user();
	
	  if ($content == "auth_partner") {
	 
					$modeHeader = 0;
                    $post = json_decode(file_get_contents("php://input"), true);
					
					$tgl="Y-m-d"; 
					$waktu="H:i:s";
					$waktu_sekarang=date("$tgl $waktu"); 
					$ditambah_5_menit = date("$tgl $waktu", strtotime('+5 minutes'));
					
					
					$partner_id = "841409";
					$redirect = "google.com";
					$timestamp=strtotime($ditambah_5_menit); 
					$sign ="31219c72afbe5794a50d30d86389942b0fa95355efc401739708667cfd07df9d";
					
				echo  "https://partner.uat.shopeemobile.com/api/v2/shop/auth_partner?partner_id=".$partner_id."&redirect=".$redirect."&imestamp=".$timestamp."&sign=".$sign."";die;
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
	
	
	} else {
    //Aha, what you're looking for !!!
    $return = array(
        "status" => 404,
        "message" => "Method Not Found!"
    );

    echo json_encode($return);
}


?>
