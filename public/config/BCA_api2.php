<?php 
/*
 * 2017 - API BCA Simple PHP Class
 * Using Account Statements for Sample Request
 * 
 * Contact : rie.projects25@gmail.com
 */
class bca{
	function __construct(){
        include "config_type.php";

        // $BCA_API_KEY: '04e27d97-c58f-408e-93ee-4741b5a6e76d';
        // $BCA_SECRET_KEY: 'f75d9721-3bea-4306-9c92-92c0310228bc';
        // $CLIENT_ID: '6cf4a838-678e-435c-9548-448b73b73b7f';
        // $CLIENT_SECRET: '7d028cd5-b14d-46bb-af91-e72fc2b44a99';
        // $this->api_key = $BCA_API_KEY; 
        // $this->api_secret = $BCA_SECRET_KEY;
        // $this->client_id = $CLIENT_ID;
        // $this->client_secret = $CLIENT_SECRET;   

        // $this->api_key = "YOUR API KEY"; 
        // $this->api_secret = "YOUR API SECRET";
        // $this->client_id = "YOUR CLIENT ID";
        // $this->client_secret = "YOUR CLIENT SECRET";       

    }
	private static $main_url = 'https://sandbox.bca.co.id'; // Change When Your Apps is Live
	private static $client_id = '6cf4a838-678e-435c-9548-448b73b73b7f'; // Fill With Your Client ID
	private static $client_secret = '7d028cd5-b14d-46bb-af91-e72fc2b44a99'; // Fill With Your Client Secret ID
	private static $api_key = '04e27d97-c58f-408e-93ee-4741b5a6e76d'; // Fill With Your API Key
	private static $api_secret = 'f75d9721-3bea-4306-9c92-92c0310228bc'; // Fill With Your API Secret Key
	private static $access_token = null;
	private static $signature = null;
	private static $timestamp = null;
	private static $corporate_id = 'BCAAPI2016'; // Fill With Your Corporate ID. BCAAPI2016 is Sandbox ID
	private static $account_number = '0201245680'; // Fill With Your Account Number. 0201245680 is Sandbox Account
	
	public function getToken(){
		$path = '/api/oauth/token';
		$headers = array(
			'Content-Type: application/x-www-form-urlencoded',
			'Authorization: Basic '.base64_encode(self::$client_id.':'.self::$client_secret));
		$data = array(
			'grant_type' => 'client_credentials'
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::$main_url.$path);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore Verify SSL Certificate
		curl_setopt_array($ch, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_POSTFIELDS => http_build_query($data),
		));
		$output = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($output,true);
		self::$access_token = $result['access_token'];
		return self::$access_token;

	}
	private function parseSignature($res){
		$explode_response = explode(',', $res);
		$explode_response_1 = explode(':', $explode_response[8]);
		self::$signature = trim($explode_response_1[1]);
	}
	private function parseTimestamp($res){
		$explode_response = explode(',', $res);
		$explode_response_1 = explode('Timestamp: ', $explode_response[3]);
		self::$timestamp = trim($explode_response_1[1]);
	}
	private function getSignature($url,$method,$data){
		$path = '/utilities/signature';
		$timestamp = date(DateTime::ISO8601);
		$timestamp = str_replace('+','.000+', $timestamp);
		$timestamp = substr($timestamp, 0,(strlen($timestamp) - 2));
		$timestamp .= ':00';
		$url_encode = $url;
		$headers = array(
			'Timestamp: '.$timestamp,
			'URI: '.$url_encode,
			'AccessToken: '.self::$access_token,
			'APISecret: '.self::$api_secret,
			'HTTPMethod: '.$method,
		);
		// var_dump($headers);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::$main_url.$path);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore Verify SSL Certificate
		curl_setopt_array($ch, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_POSTFIELDS => $data,
		));
		$output = curl_exec($ch);
		curl_close($ch);
		// echo $output;
		$this->parseSignature($output);
		$this->parseTimestamp($output);
	}

	public function index(){
		$this->getToken();
		// Change this path to your desired API Services Path
		$path = '/banking/corporates/'.self::$corporate_id.'/accounts/'.self::$account_number.'/statements?StartDate=2016-09-01&EndDate=2016-09-01';
		$method = 'GET';
		$data = array();
		$this->getSignature($path, $method, $data);
		$headers = array(
			'X-BCA-Key: '.self::$api_key,
			'X-BCA-Timestamp: '.self::$timestamp,
			'Authorization: Bearer '.self::$access_token,
			'X-BCA-Signature: '.self::$signature,
			'Content-Type: application/json',
			'Origin: '.$_SERVER['SERVER_NAME']
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::$main_url.$path);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore Verify SSL Certificate
		curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HTTPHEADER => $headers,
		));
		$output = curl_exec($ch); // This is API Response
		curl_close($ch);
		echo $output;
	}

	public function index2(){
		$this->getToken();
		// Change this path to your desired API Services Path
		$path = '/banking/corporates/transfers';
		$method = 'POST';
		$bodyData = array(
			"Amount" => "100000",
			"BeneficiaryAccountNumber" => "0201245681",
			"CorporateID" => "BCAAPI2016",
			"CurrencyCode" => "IDR",
			"ReferenceID" => "12345/PO/2016",
			"Remark1" => "transfertest",
			"Remark2" => "onlinetransfer",
			"SourceAccountNumber" => "0201245680",
			"TransactionDate" => "2018-06-11T18:59:30.520+07:00",
			"TransactionID" => "00000001"
		);
		 // Harus disort agar mudah kalkulasi HMAC
        ksort($bodyData);

        // Supaya jgn strip "ReferenceID" "/" jadi "/\" karena HMAC akan menjadi tidak cocok
        $encoderData = json_encode($bodyData, JSON_UNESCAPED_SLASHES);
        // var_dump($bodyData);
		$this->getSignature($path, $method, $encoderData);
		$headers = array(
			'X-BCA-Key: '.self::$api_key,
			'X-BCA-Timestamp: '.self::$timestamp,
			'Authorization: Bearer '.self::$access_token,
			'X-BCA-Signature: '.self::$signature,
			'Content-Type: application/json',
			'Origin: '.$_SERVER['SERVER_NAME']
		);
		var_dump($headers);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::$main_url.$path);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore Verify SSL Certificate
		curl_setopt_array($ch, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_POSTFIELDS => $encoderData,
		));
		$output = curl_exec($ch); // This is API Response
		curl_close($ch);
		echo $output;
	}
}
?>