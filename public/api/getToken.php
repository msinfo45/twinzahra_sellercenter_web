<?php
include "../lazada/LazopSdk.php";
//include "../lazada/lazop/LazopClient.php";
$url='https://api.lazada.com/rest';
$appkey= '112345';
$appSecret= 'qv9Y6ojEX4xREcmBV77qQnVnvQEQHHM2';
$accessToken = '50000800801dE1ee54d46cer8hBPvpwaccIUhJwDwodXBdxipuVhJHOnWhwLiSM';

$c = new LazopClient($url,$appkey,$appSecret);
$request = new LazopRequest('/products/get','GET');
$request->addApiParam('filter','live');
$request->addApiParam('offset','0');
$request->addApiParam('limit','10');
$request->addApiParam('options','1');
var_dump($c->execute($request, $accessToken));

//}
?>