<?php

		$SUGGESTED_MEMO=  $_POST['SUGGESTED_MEMO'];
		$PAYMENT_ID=$_POST['PAYMENT_ID'];
		$PAYMENT_AMOUNT=$_POST['PAYMENT_AMOUNT'];
		$PAYEE_ACCOUNT=$_POST['PAYEE_ACCOUNT'];
		$PAYMENT_UNITS=$_POST['PAYMENT_UNITS'];
		$PAYEE_NAME=$_POST['PAYEE_NAME'];
		$PAYMENT_URL=$_POST['PAYMENT_URL'];
		$PAYMENT_URL_METHOD= $_POST['PAYMENT_URL_METHOD'];
		$NOPAYMENT_URL=$_POST['NOPAYMENT_URL'];
		$NOPAYMENT_URL_METHOD=$_POST['NOPAYMENT_URL_METHOD'];
		$STATUS_URL=$_POST['STATUS_URL'];


		$PAYMENT_BATCH_NUM = '65234333';
		$PAYER_ACCOUNT = 'U15512585';
		$TIMESTAMPGMT = '20200825';
		
	  $string=
      $PAYMENT_ID.':'.$PAYEE_ACCOUNT.':'.
	  $PAYMENT_AMOUNT.':'.$PAYMENT_UNITS.':'.
      $PAYMENT_BATCH_NUM.':'.
      $PAYER_ACCOUNT.':'.$PAYEE_NAME.':'.
      $TIMESTAMPGMT;

		$hash=strtoupper(md5($string));

		$V2_HASH = $hash;
		
		
		  $options = array(
        CURLOPT_RETURNTRANSFER => true,   // return web page
        CURLOPT_HEADER         => false,  // don't return headers
        CURLOPT_FOLLOWLOCATION => true,   // follow redirects
        CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
        CURLOPT_ENCODING       => "",     // handle compressed
        CURLOPT_USERAGENT      => "test", // name of client
        CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
        CURLOPT_TIMEOUT        => 120,    // time-out on response
    ); 
	
	
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $STATUS_URL);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,
						"PAYMENT_ID='".$PAYMENT_ID."'
						&PAYEE_ACCOUNT='".$PAYEE_ACCOUNT."'
						&PAYMENT_AMOUNT='".$PAYMENT_AMOUNT."'
						&PAYMENT_UNITS='".$PAYMENT_UNITS."'
						&PAYMENT_BATCH_NUM='".$PAYMENT_BATCH_NUM."'
						&TIMESTAMPGMT='".$TIMESTAMPGMT."'
						&V2_HASH='".$V2_HASH."'
						");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		 'X-Apple-Tz: 0',
		'X-Apple-Store-Front: 143444,12',
		'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
		'Accept-Encoding: gzip, deflate',
		'Accept-Language: en-US,en;q=0.5',
		'Cache-Control: no-cache',
		'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
		//'Host: www.example.com',
		//'Referer: http://www.example.com/index.php', //Your referrer address
		'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0',
		'X-MicrosoftAjax: Delta=true'
		
	
			));


			// receive server response ...
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$server_output = curl_exec ($ch);

			curl_close ($ch);
			
			return json_encode($server_output);
	
	
			// further processing ....
			if ($server_output == "OK") { 
			echo 'Success';
			} else { 
			echo $server_output;
		 }

?>
