<?php

$PAYEE_ACCOUNT= $_POST['PAYEE_ACCOUNT'];
$PAYEE_NAME=$_POST['PAYEE_NAME'];
$PAYMENT_ID=$_POST['PAYMENT_ID'];
$PAYMENT_AMOUNT=$_POST['PAYMENT_AMOUNT'];
$PAYMENT_UNITS=$_POST['PAYMENT_UNITS'];
$STATUS_URL=$_POST['STATUS_URL'];
$PAYMENT_URL = $_POST['PAYMENT_URL'];
$PAYMENT_URL_METHOD=$_POST['PAYMENT_URL_METHOD'];
$NOPAYMENT_URL=$_POST['NOPAYMENT_URL'];
$NOPAYMENT_URL_METHOD=$_POST['NOPAYMENT_URL_METHOD'];
$email=$_POST['email'];


curl -i -s -k -X $'POST' \
-H $'Host: perfectmoney.is' -H $'Connection: close' -H $'Content-Length: 376' -H $'Cache-Control: max-age=0' -H $'Upgrade-Insecure-Requests: 1' -H $'Origin: https://finrally.com' -H $'Content-Type: application/x-www-form-urlencoded' -H $'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.83 Safari/537.36' -H $'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9' -H $'Sec-Fetch-Site: cross-site' -H $'Sec-Fetch-Mode: navigate' -H $'Sec-Fetch-Dest: document' -H $'Referer: https://finrally.com/' -H $'Accept-Encoding: gzip, deflate' -H $'Accept-Language: en-US,en;q=0.9' \
--data-binary $'PAYEE_ACCOUNT=U14197965&PAYEE_NAME=Finrally&PAYMENT_ID=perfect_fr_2698340941_1605868368&PAYMENT_AMOUNT=250&PAYMENT_UNITS=USD&STATUS_URL=https%3A%2F%2Ffinrally.com%2Fapi%2Ffm%2Fperfect%2Fcallback&PAYMENT_URL=https%3A%2F%2Ffinrally.com%2Fen%2Fsuccess&PAYMENT_URL_METHOD=GET&NOPAYMENT_URL=https%3A%2F%2Ffinrally.com%2Fen%2Ffail&NOPAYMENT_URL_METHOD=GET&email=msinfo45%40gmail.com' \
$'https://perfectmoney.is/api/step1.asp'

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $STATUS_URL);
$payload = json_encode( array( "PAYEE_ACCOUNT"=> $PAYEE_ACCOUNT,
                              "PAYEE_NAME" => $PAYEE_NAME,
                              "PAYMENT_ID"=> $PAYMENT_ID,
                              "PAYMENT_AMOUNT"=> $PAYMENT_AMOUNT,
                              "PAYMENT_UNITS"=> $PAYMENT_UNITS) );
curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Host:perfectmoney.is,
Content-Type:application/json,
Content-Type:application/json,
Content-Type:application/json,
Content-Type:application/json,
Content-Type:application/json,
Content-Type:application/json,
Content-Type:application/json,
Content-Type:application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$productContent = curl_exec($ch);
curl_close($ch);

$resultProducts=json_decode($productContent);

echo $resultProducts;



?>
