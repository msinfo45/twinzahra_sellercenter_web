 
<?php

//Check request content
$content = $_GET['request'];
if (isset($content) && $content != "") {


    
          
    if ($content == "callback") {


        $post = json_decode(file_get_contents("php://input"), true);
        
	  $Merchant= $_POST['Merchant'];
	  $Reference= $_POST['Reference'];
	  $Customer= $_POST['Customer'];
	  $Amount= $_POST['Amount'];
	  $Currency= $_POST['Currency'];
	  $StatusCode= $_POST['StatusCode'];
	  $SecurityCode= $_POST['SecurityCode'];
	  $Callback_Url= $_POST['Callback_Url'];
      $Return_Url= $_POST['Return_Url'];
        

            
           $Key = md5($Merchant . '' . $Reference . '' . $Customer . '' . $Currency . '' . $StatusCode . ''. $SecurityCode);
        
    


function curl($url, $data){
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $output = curl_exec($ch); 
    curl_close($ch);      
    return $output;
}

// Data Parameter yang Dikirim oleh cURL
$data = array("Merchant"=>$Merchant,"Reference"=>$Reference,"Customer"=>$Customer,"Currency"=>$Currency,"StatusCode"=>$StatusCode,"SecurityCode"=>$SecurityCode,"Key"=>$Key);
$send = curl($Callback_Url,json_encode($data));

$Result =  json_encode(array('respon'=>$send),JSON_UNESCAPED_SLASHES);

             
            $return = array(
                            "status" => 200,
                            "message" => "ok",
                            "response" => $Result,
                            "data" => $Key

                            );

        //
        echo json_encode($return);
    }

} else {
?>

   <html>
    <form method="POST" action="http://twinzahra.com/v1/help2pay.php?request=callback">
    Merchant <input type="Text" name="Merchant" value=""><br>
    Reference <input type="Text" name="Reference" value=""><br>
    Customer <input type="Text" name="Customer" value=""><br>
    Amount <input type="Text" name="Amount" value=""><br>
    Currency <input type="Text" name="Currency" value="IDR"><br>
    StatusCode <input type="Text" name="StatusCode" value=""><br>
    SecurityCode <input type="Text" name="SecurityCode" value=""><br>
    Callback Url <input type="Text" name="Callback_Url" value=""><br>
	Return Url <input type="Text" name="Return_Url" value=""><br>
    <input name="" type="submit">
    </form>
    </html>

<?php
}
?>
