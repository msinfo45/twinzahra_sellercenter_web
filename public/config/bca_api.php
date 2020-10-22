<?php 
// defined('BASEPATH') OR exit('No direct script access allowed');
class BCAAPI {
    
    function __construct(){
        include "config_type.php";
        include "../v1/vendor/odenktools/php-bca/lib/Bca.php";
        include "../v1/vendor/mashape/unirest-php/src/Unirest.php";
        include "../v1/vendor/nesbot/carbon/src/Carbon/Carbon.php";
        // include "../v1/vendor/odenktools/php-bca/lib/Bca/BCAHttp.php";


        // $BCA_API_KEY: '04e27d97-c58f-408e-93ee-4741b5a6e76d';
        // $BCA_SECRET_KEY: 'f75d9721-3bea-4306-9c92-92c0310228bc';
        // $CLIENT_ID: '6cf4a838-678e-435c-9548-448b73b73b7f';
        // $CLIENT_SECRET: '7d028cd5-b14d-46bb-af91-e72fc2b44a99';
        $this->api_key = $BCA_API_KEY; 
        $this->api_secret = $BCA_SECRET_KEY;
        $this->client_id = $CLIENT_ID;
        $this->client_secret = $CLIENT_SECRET;   

        // $options = array(
        //     'scheme'        => 'https',
        //     'port'          => 443,
        //     'host'          => 'sandbox.bca.co.id',
        //     'timezone'      => 'Asia/Jakarta',
        //     'timeout'       => 30,
        //     'debug'         => true,
        //     'development'   => true
        // );

        // Setting default timezone Anda
        \Bca\BcaHttp::setTimeZone('Asia/Jakarta');

        // ATAU
        
        // \Bca\BcaHttp::setTimeZone('Asia/Singapore');

        // $corp_id = "BCAAPI2016";
        $corp_id    = "h2hauto008";
        $client_key = $this->client_id;
        $client_secret = $this->client_secret;
        $apikey = $this->api_key;
        $secret = $this->api_secret;

        $this->bca = new \Bca\BcaHttp($corp_id, $client_key, $client_secret, $apikey, $secret);   

    }
    public function get_token(){
        
        $service_url = 'https://sandbox.bca.co.id:443/api/oauth/token';
        $curl = curl_init();
        $headers = array(
            'Authorization:Basic '.base64_encode($this->client_id.":".$this->client_secret).'', 
            'Content-Type:application/x-www-form-urlencoded'
        );
        
        curl_setopt($curl, CURLOPT_URL, $service_url); 
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        
        $result = curl_exec($curl);
        // Check HTTP status code
        if (!curl_errno($curl)) {
          switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
            case 200:  # OK
              break;
            default:
              $error = 'Unexpected HTTP code: '.$http_code;
              $return = array(
                                "status" => 404,
                                "message" => $error
                        );
              return $error;
          }
        }
        //close curl session
        curl_close($curl);
        // var_dump(json_decode($result));
        $return = array(
                        "status" => 200,
                        "message" => 'ok',
                        "data" => json_decode($result)
                );

        return $return;
    }


    public function get_signature_balance($acces_token){
        $datetime    =  date("Y-m-d"."T"."H:i:s".'+07:00');
        // echo $acces_token.' - '.$datetime;
        $service_url = 'https://sandbox.bca.co.id:443/utilities/signature';
        $curl = curl_init();
        $curl_post_data = array();
        $timestamp =  date("Y-m-d H:i:s"); //2017-03-01T16:23:00.000+07:00
        $headers = array(
            'Timestamp: 2017-03-01T16:23:00.000+07:00',
            'URI: /banking/corporates/BCAAPI2016/accounts/0201245680',
            'AccessToken: '.$acces_token,
            'APISecret: '.$this->api_secret,
            // 'APISecret: '.$this->api_secret,
            'HTTPMethod: GET',
            'Content-Type: application/x-www-form-urlencoded'
        );
         
        curl_setopt($curl, CURLOPT_URL, $service_url); 
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
        $result = curl_exec($curl);
        $result = explode(",", $result);
        curl_close($curl);

        $return = array(
                        "status" => 200,
                        "message" => 'ok',
                        "data" => $result
                );
        return $return;
    }

    public function get_signature_transfer($acces_token){
        $datetime    =  date("Y-m-d"."T"."H:i:s".'+07:00');
        // echo $acces_token.' - '.$datetime;
        $service_url = 'https://sandbox.bca.co.id:443/utilities/signature';
        $curl = curl_init();
        $bodyData = array(
            "CorporateID" => "BCAAPI2016",
            "SourceAccountNumber" => "0201245680",
            "TransactionID" => "00000001",
            "TransactionDate" => "2018-06-11",
            "ReferenceID" => "12345/PO/2016",
            "CurrencyCode" => "IDR",
            "Amount" => "100000.00",
            "BeneficiaryAccountNumber" => "0201245681",
            "Remark1" => "Transfer Test",
            "Remark2" => "Online Transfer"
        );
        
        $method = 'POST';
        $timestamp = date(DateTime::ISO8601);
        $timestamp = str_replace('+','.000+', $timestamp);
        $timestamp = substr($timestamp, 0,(strlen($timestamp) - 2));
        $timestamp .= ':00';
        $headers = array(
            'Timestamp: '.$timestamp,
            'URI: /banking/corporates/transfers',
            'AccessToken: 6Yck9oT1NMC7UQgb2FBbzdwx8Jw3i4fHWZ5tSTTBn4c1yAwOOUUZ20',
            'APISecret: '.$this->api_secret,
            // 'APISecret: '.$this->api_secret,
            'HTTPMethod: POST',
            // 'Content-Type: application/x-www-form-urlencoded'
        );
        var_dump($headers); 
        curl_setopt($curl, CURLOPT_URL, $service_url); 
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($bodyData));
        $result = curl_exec($curl);
        echo 'test '.$result;
        $result = explode(",", $result);
        curl_close($curl);

        $return = array(
                        "status" => 200,
                        "message" => 'ok',
                        "data" => $result
                );
        return $return;
    }

    public function get_signature_statement(){
        $service_url = 'https://sandbox.bca.co.id/utilities/signature';
        $curl = curl_init();
        $curl_post_data = array();
        $headers = array(
            'Timestamp: 2017-03-01T16:10:00.000+07:00',
            'URI: /banking/corporates/BCAAPI2016/accounts/0201245680/statements?StartDate=2016-09-01&EndDate=2016-09-01',
            'AccessToken: d6E3b3OSJKKtsvZ67wkSJc99VvZtSyJGeEZ1nMDjCXODJZsywU1q20',
            'APISecret: ab36ba8e-3110-4789-96e2-d466d458935a',
            // 'APISecret: '.$this->api_secret,
            'HTTPMethod: GET',
            'Content-Type: application/x-www-form-urlencoded'
        );
         
        curl_setopt($curl, CURLOPT_URL, $service_url); 
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
        curl_exec($curl);
        curl_close($curl);
    }
    public function get_balance($access_token, $signature){
        $service_url = 'https://sandbox.bca.co.id/banking/corporates/BCAAPI2016/accounts/0201245680';
        $curl = curl_init();
        $curl_post_data = array();
        $headers = array(
            'Authorization:Bearer '.$access_token,
            'X-BCA-Key:'.$this->api_key,
            'X-BCA-Signature:'.$signature,
            'X-BCA-Timestamp::2017-03-01T16:23:00.000+07:00'
        );
         
        curl_setopt($curl, CURLOPT_URL, $service_url); 
        curl_setopt($curl, CURLOPT_HTTPGET, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_exec($curl);
        curl_close($curl);
        $result = $curl;
         $return = array(
                        "status" => 200,
                        "message" => 'ok',
                        "data" => $result
                );

        return $return;
    }
    public function get_statement(){
        $service_url = 'https://sandbox.bca.co.id/banking/corporates/BCAAPI2016/accounts/0201245680/statements?StartDate=2016-09-01&EndDate=2016-09-01';
        $curl = curl_init();
        $curl_post_data = array();
        $headers = array(
            'Authorization:Bearer d6E3b3OSJKKtsvZ67wkSJc99VvZtSyJGeEZ1nMDjCXODJZsywU1q20',
            'X-BCA-Key:3ff80577-aab2-4dea-82a5-900141b4f501',
            'X-BCA-Signature:2f61c8731d13711bf9cc017da49101c6086550f6be4b920cfbeb633029cd7f4f',
            'X-BCA-Timestamp:2017-03-01T16:10:00.000+07:00'
        );
         
        curl_setopt($curl, CURLOPT_URL, $service_url); 
        curl_setopt($curl, CURLOPT_HTTPGET, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_exec($curl);
        curl_close($curl);
    }

    private function parseTimestamp($res){
        $explode_response = explode(',', $res);
        $explode_response_1 = explode('Timestamp: ', $explode_response[3]);
        self::$timestamp = trim($explode_response_1[1]);
    }
    
    private function parseSignature($res){
        $explode_response = explode(',', $res);
        $explode_response_1 = explode(':', $explode_response[8]);
        self::$signature = trim($explode_response_1[1]);
    }

    public function bca_fund_transfer($amount, $nomorakun, $nomordestinasi, $nomorPO, $nomorTransaksiID){
        // Request Login dan dapatkan nilai OAUTH
        $response = $this->bca->httpAuth();

        // LIHAT HASIL OUTPUT
        // echo json_encode($response);

        // Nilai token yang dihasilkan saat login
        // var_dump($response);
        $response = json_decode($response->raw_body);
        // $response = $this->cvf_convert_object_to_array($response);
        $token = $response->access_token;
        // echo $token;
        // $token = "cCXXANU3eA1CzDcFwNcYfziVXe7EmniCzapWPXbWD9pDHvg09t3cfj";
        // var_dump($token);

        // $amount = '100000';

        // // Nilai akun bank anda
        // $nomorakun = '0201245680';

        // // Nilai akun bank yang akan ditransfer
        // $nomordestinasi = '0201245681';

        // // Nomor PO, silahkan sesuaikan
        // $nomorPO = '12345/PO/2016';

        // // Nomor Transaksi anda, Silahkan generate sesuai kebutuhan anda
        // $nomorTransaksiID = '00000001';
        // echo $amount;
        $response = $this->bca->fundTransfers($token, 
                            $amount,
                            $nomorakun,
                            $nomordestinasi,
                            $nomorPO,
                            'Transfer Test',
                            'Online Transfer',
                            $nomorTransaksiID);

        // echo json_encode($response);
        return $response;

    }

    public function account_statement($nomorakun, $startdate, $enddate){
         $response = $this->bca->httpAuth();

        // LIHAT HASIL OUTPUT
        // echo json_encode($response);

        // Nilai token yang dihasilkan saat login
        // var_dump($response);
        $response = json_decode($response->raw_body);
        // $response = $this->cvf_convert_object_to_array($response);
        $token = $response->access_token;
        
        // Nilai akun bank anda
        // $nomorakun = '0201245680';
        
        // // Tanggal start transaksi anda
        // $startdate = '2016-08-29';
        
        // // Tanggal akhir transaksi anda
        // $enddate = '2016-08-29';

        $response = $this->bca->getAccountStatement($token, $nomorakun, $startdate, $enddate);

        return $response;
    }

    function cvf_convert_object_to_array($data) {

        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (is_array($data)) {
            return array_map(__FUNCTION__, $data);
        }
        else {
            return $data;
        }
    }
}

?>