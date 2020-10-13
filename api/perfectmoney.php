 
<?php

//Check request content
$content = $_GET['request'];
if (isset($content) && $content != "") {


    
          
    if ($content == "proccess") {


        $post = json_decode(file_get_contents("php://input"), true);
        
       // $PAYMENT_ID = $_POST['PAYMENT_ID'];
      //  $PAYEE_ACCOUNT = $_POST['PAYEE_ACCOUNT'];
       // $PAYMENT_AMOUNT = $_POST['PAYMENT_AMOUNT'];
       // $PAYMENT_UNITS = $_POST['PAYMENT_UNITS'];
        $STATUS_URL= $_POST['STATUS_URL'];
        
            $now = new DateTime();
			
              $PAYMENT_ID = "a2b3608e56f85f9600fc6aab0336f98a";
            $PAYER_ACCOUNT = "U15512585";
        $PAYEE_ACCOUNT = "U15276734";
        $PAYMENT_AMOUNT = "9.64";
        $PAYMENT_UNITS = "USD";
		
            $PAYMENT_BATCH_NUM =  date('Ymd') . $now->getTimestamp() ;
    
            $TIMESTAMPGMT = $now->getTimestamp();
            
       $string=
      $PAYMENT_ID.':'.$PAYEE_ACCOUNT.':'.
      $PAYMENT_AMOUNT.':'.$PAYMENT_UNITS.':'.
      $PAYMENT_BATCH_NUM.':'.
      $PAYER_ACCOUNT.':'.ALTERNATE_PHRASE_HASH.':'.
      $TIMESTAMPGMT;

		$hash=strtoupper(md5($string));

            
           $V2_HASH = md5($PAYMENT_ID . ':' . $PAYEE_ACCOUNT . ':' . $PAYMENT_AMOUNT . ':' . $PAYMENT_UNITS . ':' . $PAYMENT_BATCH_NUM . ':' . $PAYER_ACCOUNT . ':' . $TIMESTAMPGMT );
        
    

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,"https://binomoworld.com/cashier/process_success/guid/a2b3608e56f85f9600fc6aab0336f98a");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,
            "postvar1=value1&postvar2=value2&postvar3=value3");

// In real life you should use something like:
// curl_setopt($ch, CURLOPT_POSTFIELDS, 
//          http_build_query(array('postvar1' => 'value1')));

// Receive server response ...
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec($ch);

curl_close ($ch);




            
            $return = array(
                            "status" => 200,
                            "message" => "ok",
                            "response" => $server_output,
                            "data" => $data

                            );

        //
        echo json_encode($return);
    }

} else {
?>

   <html>
    <form method="POST" action="http://twinzahra.com/v1/perfectmoney.php?request=proccess">
    PAYEE_ACCOUNT <input type="Text" name="PAYEE_ACCOUNT" value=""><br>
    PAYEE_NAME <input type="Text" name="PAYEE_NAME" value=""><br>
    PAYMENT_AMOUNT <input type="Text" name="PAYMENT_AMOUNT" value=""><br>
    PAYMENT_UNITS <input type="Text" name="PAYMENT_UNITS" value="USD"><br>
    PAYMENT_ID <input type="Text" name="PAYMENT_ID" value=""><br>
    STATUS_URL <input type="Text" name="STATUS_URL" value=""><br>
    BAGGAGE_FIELDS <input type="Text" name="BAGGAGE_FIELDS" value=""><br>
    SUGGESTED_MEMO <input type="Text" name="SUGGESTED_MEMO" value=""><br>

    <input name="" type="submit">
    </form>
    </html>

<?php
}
?>
