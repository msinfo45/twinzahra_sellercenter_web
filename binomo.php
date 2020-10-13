<?php
$ch = curl_init();
$payload = json_encode( array( "fp_paidto"=> "FP05267",
								"fp_paidby"=> "FP151570",
								"fp_amnt"=> "50.00",	
								"fp_fee_amnt"=> "0",	
								"fp_fee_mode"=> "FiR",	
								"fp_total"=> "50.00",	
								"fp_currency"=> "USD",	
								"fp_batchnumber"=> "KR2020072429853",	
								"fp_store"=> "Forex4you",	
								"fp_timestamp"=> "2020-07-24+00:34:46",	
								"fp_merchant_ref"=> "9569270-4305f19d60970ef0"							
								) );
curl_setopt($ch, CURLOPT_URL,"https://account.forex4you.com/en/trader-account/balance/deposit/success/fasapay");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,$payload);  //Post Fields
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$headers = [

	'Host: https://payment.eglobal-forex.com',
    'User-Agent: PHP (Linux) FasaPay FasaPay-IPN FasaPay-SCI',
    'Accept: */*',
	'Content-Length: 759',
	'Content-Type: application/x-www-form-urlencoded',
	'Referer: https://www.fasapay.com/sci'
];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$server_output = curl_exec ($ch);

curl_close ($ch);

var_dump ($server_output) ;


?>