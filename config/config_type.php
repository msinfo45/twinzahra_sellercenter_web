<?php

$NOTIF_JOB_OFFER = 1;
$NOTIF_CHAT = 2;
$NOTIF_HISTORY = 3;
$BASE_URL = 'http://119.110.66.169';
//$BASE_URL = 'http://vtal.id';
// $UPLOAD_DIR = '/var/www/public_html/dev/image';
$UPLOAD_DIR = '/var/www/html/public_html/image';
$UPLOAD_DIR_2 = $BASE_URL.'/image';
$MODE_DELETE_CHAT_FILE = 0;
$SMS_USERKEY = "ga6wxz"; //userkey lihat di zenziva
$SMS_PASSKEY = "aid.co.id"; // set passkey di zenziva

/*PUHSER CONFIG*/
$APP_ID = "400500";
$KEY = "023b57443ff0a186dc02";
$SECRET = "d5adc49e152a1a13b133";
$CLUSTER = "ap1";

/*BCA API*/
/*dummy*/
// $BCA_API_KEY= '04e27d97-c58f-408e-93ee-4741b5a6e76d';
// $BCA_SECRET_KEY= 'f75d9721-3bea-4306-9c92-92c0310228bc';
// $CLIENT_ID= '6cf4a838-678e-435c-9548-448b73b73b7f';
// $CLIENT_SECRET= '7d028cd5-b14d-46bb-af91-e72fc2b44a99';

$BCA_API_KEY		= 'dcc99ba6-3b2f-479b-9f85-86a09ccaaacf';
$BCA_SECRET_KEY		= '5e636b16-df7f-4a53-afbe-497e6fe07edc';
$CLIENT_ID			= 'b095ac9d-2d21-42a3-a70c-4781f4570704';
$CLIENT_SECRET		= 'bedd1f8d-3bd6-4d4a-8cb4-e61db41691c9';

/*Email*/
$MAIL_SMTPSecure 	= 'ssl';
$MAIL_Host 			= "mail.aid.co.id"; //hostname masing-masing provider email
$MAIL_SMTPDebug 	= 2;
$MAIL_Port 			= 465;
$MAIL_SMTPAuth 		= true;
$MAIL_Username 		= "admin@aid.co.id"; //username email
$MAIL_Password 		= "aid2018@!"; //password email

date_default_timezone_set("Asia/Jakarta");
    
//config lazada
    $lazada_url = 'https://api.lazada.co.id/rest';
    $lazada_appkey = '112345';
    $lazada_appSecret = 'qv9Y6ojEX4xREcmBV77qQnVnvQEQHHM2';
    
    
?>
