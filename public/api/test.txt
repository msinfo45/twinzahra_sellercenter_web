<?php
        $fields_string  =   "";
        $fields     =   array(
                            'api_key'       =>  '',
                            'api_secret'    =>  '',
                            'to'            =>  '+6289699935552',
                            'from'          =>  "Nuris Akbar",
                            'text'          =>  "Terima kasih Sudah Melakukan Order Tutorial DI Belajarphp.net"
        );
        $url        =   "https://rest.nexmo.com/sms/json";

        //url-ify the data for the POST
	foreach($fields as $key=>$value) { 
            $fields_string .= $key.'='.$value.'&'; 
            }
	rtrim($fields_string, '&');

		//open connection
	$ch = curl_init();

	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

	//execute post
	$result = curl_exec($ch);
	//close connection
	curl_close($ch);

        echo "<pre>";
        print_r($result); 
        echo "</pre>";
?>