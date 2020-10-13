<?php



$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,"https://account.forex4you.com/en/trader-account/balance/deposit/success/fasapay_idr");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,
            "fp_paidto=FP05267&fp_paidby=FP151570&fp_amnt=10.00&fp_fee_amnt=0&fp_fee_mode=FiR&fp_total=10.00&fp_currency=USD&fp_batchnumber=TR2020071565432&fp_store=Forex4you
			&fp_timestamp=2020-07-15 11:30:45&fp_unix_time=1459485045&fp_merchant_ref=9541331-4255f0de62f77910&fp_sec_field=&fp_hash=9a832705ff57dd768cb917024da30102037d9939ced77a815050c4277d8788da
			&fp_hash_2=71bb142c0826639d640779605b4e7cf137dea6534a19e2a8319c0492a48ec7c6&fp_hash_list=&fp_hash_all=ce4402238238792575e291edd5bf913fd66d210a0debf897f3d308214abbf00c
			&fp_hmac=70df164cd5bc623a615d6f394af1550bdd13c2970783f85e40440c12e116c22610.00|TR2020071565432|USD|0|FiR|9541331-4255f0de62f77910|FP151570|FP05267|Forex4you|2020-07-15 11:30:45|10.00|1459485045|
");

// In real life you should use something like:
// curl_setopt($ch, CURLOPT_POSTFIELDS, 
//          http_build_query(array('postvar1' => 'value1')));

//curl_setopt($ch,CURLOPT_USERAGENT,'PHP (Linux) FasaPay FasaPay-IPN FasaPay-SCI');
// Receive server response ...
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec($ch);

curl_close ($ch);



var_dump($server_output);

?>