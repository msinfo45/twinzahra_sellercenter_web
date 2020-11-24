<?php

$PAYEE_ACCOUNT= $_POST['PAYEE_ACCOUNT'];
$PAYEE_NAME=$_POST['PAYEE_NAME'];
$PAYMENT_ID=$_POST['PAYMENT_ID'];
$PAYMENT_AMOUNT=$_POST['PAYMENT_AMOUNT'];
$PAYMENT_UNITS=$_POST['PAYMENT_UNITS'];
$STATUS_URL=$_POST['STATUS_URL'];
$PAYMENT_URL = $_POST['PAYMENT_URL'];
//$PAYMENT_URL_METHOD=$_POST['PAYMENT_URL_METHOD'];
$NOPAYMENT_URL=$_POST['NOPAYMENT_URL'];
//$NOPAYMENT_URL_METHOD=$_POST['NOPAYMENT_URL_METHOD'];
//$email=$_POST['email'];
$PAYER_ACCOUNT = "U15512585";
$passphrase = "";
$utc_str = gmdate("M d Y H:i:s", time());
$TIMESTAMPGMT = strtotime($utc_str);
$PAYMENT_BATCH_NUM = "2242444";

$base_string = $PAYMENT_ID.":".$PAYEE_ACCOUNT.":".$PAYMENT_AMOUNT.":".$PAYMENT_UNITS.":".$PAYMENT_BATCH_NUM.":".$PAYER_ACCOUNT.":".$passphrase.":".$TIMESTAMPGMT;

$V_HASH  = md5($base_string);


    $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $STATUS_URL);
	  $payload = "PAYMENT_ID=" . $PAYMENT_ID . 
									  "&PAYEE_ACCOUNT=". $PAYEE_ACCOUNT .
									  "&PAYMENT_AMOUNT=". $PAYMENT_AMOUNT .
									  "&PAYMENT_UNITS=". $PAYMENT_UNITS .
									  "&PAYMENT_BATCH_NUM=". $PAYMENT_BATCH_NUM .
									  "&PAYER_ACCOUNT=" .$PAYER_ACCOUNT .
									  "&TIMESTAMPGMT=". $TIMESTAMPGMT.
									  "&V_HASH=". $V_HASH;
									  
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded'));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $lazadacontent = curl_exec($ch);
      curl_close($ch);
      //$resultLazada = json_decode($lazadacontent);
	  
	   echo  $lazadacontent;
	   
	  $ch2 = curl_init();
      curl_setopt($ch2, CURLOPT_URL, $STATUS_URL);
      $payloadLazada2 = json_encode(array("PAYMENT_ID" => $PAYMENT_ID,
        "PAYEE_ACCOUNT" => $PAYEE_ACCOUNT,
        "PAYMENT_AMOUNT" => $PAYMENT_AMOUNT,
		"PAYMENT_UNITS" => $PAYMENT_UNITS,
		"PAYMENT_BATCH_NUM" => $PAYMENT_BATCH_NUM,
		"PAYER_ACCOUNT" => $PAYER_ACCOUNT,
		"TIMESTAMPGMT" => $TIMESTAMPGMT,
		"V_HASH" => $V_HASH));
      curl_setopt($ch2, CURLOPT_POSTFIELDS, $payloadLazada2);
      curl_setopt($ch2, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
      curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
      $lazadacontent2 = curl_exec($ch2);
      curl_close($ch2);
      $resultLazada2 = json_decode($lazadacontent2);
	  
	 echo  json_encode($lazadacontent2);
	  
	 // if ($PAYMENT_URL_METHOD == "GET") {
		   
		 // echo ' <a href='.$PAYMENT_URL.'?'.$payload.'>Payment</a>';
		 
		  
	 // }
	  
	  






?>
