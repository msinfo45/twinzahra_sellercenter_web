<?php
// namespace Bca;
/**
 * BCA REST API Library.
 *
 * @author     Pribumi Technology
 * @license    MIT
 * @copyright  (c) 2017, Pribumi Technology
 */
class BcaHttp
{
    public static $VERSION = '2.1.2';
    private static $timezone = 'Asia/Jakarta';
    private static $port = 443;
    private static $hostName = 'sandbox.bca.co.id';
    protected $settings = array(
        'corp_id'       => '',
        'client_id'     => '6cf4a838-678e-435c-9548-448b73b73b7f',
        'client_secret' => '7d028cd5-b14d-46bb-af91-e72fc2b44a99',
        'api_key'       => '04e27d97-c58f-408e-93ee-4741b5a6e76d',
        'secret_key'    => 'f75d9721-3bea-4306-9c92-92c0310228bc',
        'scheme'        => 'https',
        'port'          => 443,
        'timezone'      => 'Asia/Jakarta',
        'timeout'       => null,
        'development'   => true,
    );
    /**
     * Default Constructor.
     *
     * @param string $corp_id nilai corp id
     * @param string $client_id nilai client key
     * @param string $client_secret nilai client secret
     * @param string $api_key niali oauth key
     * @param string $secret_key nilai oauth secret
     * @param array $options opsi ke server bca
     */
    public function __construct($corp_id, $client_id, $client_secret, $api_key, $secret_key, $options = array())
    {
        if (!isset($options['port'])) {
            $options['port'] = self::getPort();
        }
        if (!isset($options['timezone'])) {
            $options['timezone'] = self::getTimeZone();
        }
        foreach ($options as $key => $value) {
            if (isset($this->settings[$key])) {
                $this->settings[$key] = $value;
            }
        }
        if (!array_key_exists('host', $this->settings)) {
            if (array_key_exists('host', $options)) {
                $this->settings['host'] = $options['host'];
            } else {
                $this->settings['host'] = self::getHostName();
            }
        }
        $this->settings['corp_id']       = $corp_id;
        $this->settings['client_id']     = $client_id;
        $this->settings['client_secret'] = $client_secret;
        $this->settings['api_key']       = $api_key;
        $this->settings['secret_key']    = $secret_key;
        
        $this->settings['host'] =
            preg_replace('/http[s]?\:\/\//', '', $this->settings['host'], 1);
    }
    /**
     * Ambil Nilai settings.
     *
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }
    /**
     * Build the ddn domain.
     * output = 'https://sandbox.bca.co.id:443'
     * scheme = http(s)
     * host = sandbox.bca.co.id
     * port = 80 ? 443
     *
     * @return string
     */
    private function ddnDomain()
    {
        return $this->settings['scheme'] . '://' . $this->settings['host'] . ':' . $this->settings['port'] . '/';
    }
    /**
     * Generate authentifikasi ke server berupa OAUTH.
     *
     * @return \Unirest\Response
     */
    public function httpAuth()
    {
        $client_id     = $this->settings['client_id'];
        $client_secret = $this->settings['client_secret'];
        $this->validateBcaKey($client_id);
        $this->validateBcaKey($client_secret);
        
        $headerToken = base64_encode("$client_id:$client_secret");
        $headers = array('Accept' => 'application/json', 'Authorization' => "Basic $headerToken");
        $request_path = "api/oauth/token";
        $domain       = $this->ddnDomain();
        $full_url     = $domain . $request_path;
        
        \Unirest\Request::curlOpts(array(
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSLVERSION => 6,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => $this->settings['timeout'] !== 30 ? $this->settings['timeout'] : 30
        ));
        $data = array('grant_type' => 'client_credentials');
        $body = \Unirest\Request\Body::form($data);
        $response = \Unirest\Request::post($full_url, $headers, $body);
        return $response;
    }
    /**
     * Ambil informasi saldo berdasarkan nomor akun BCA.
     *
     * @param string $oauth_token nilai token yang telah didapatkan setelah login
     * @param array $sourceAccountId nomor akun yang akan dicek
     *
     * @return \Unirest\Response
     */
    public function getBalanceInfo($oauth_token, $sourceAccountId = [])
    {
        $corp_id = $this->settings['corp_id'];
        $apikey  = $this->settings['api_key'];
        $secret  = $this->settings['secret_key'];
        
        $this->validateCorpId($corp_id);
        $this->validateBcaKey($apikey);
        $this->validateBcaKey($secret);
        $this->validateArray($sourceAccountId);
        ksort($sourceAccountId);
        $arraySplit = implode(",", $sourceAccountId);
        $arraySplit = urlencode($arraySplit);
        $uriSign       = "GET:/banking/v3/corporates/$corp_id/accounts/$arraySplit";
        $isoTime       = self::generateIsoTime();
        $authSignature = self::generateSign($uriSign, $oauth_token, $secret, $isoTime, null);
        $headers                    = array();
        $headers['Accept']          = 'application/json';
        $headers['Content-Type']    = 'application/json';
        $headers['Authorization']   = "Bearer $oauth_token";
        $headers['X-BCA-Key']       = $apikey;
        $headers['X-BCA-Timestamp'] = $isoTime;
        $headers['X-BCA-Signature'] = $authSignature;
        $request_path = "banking/v3/corporates/$corp_id/accounts/$arraySplit";
        $domain       = $this->ddnDomain();
        $full_url     = $domain . $request_path;
        
        $data = array('grant_type' => 'client_credentials');
        \Unirest\Request::curlOpts(array(
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSLVERSION => 6,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => $this->settings['timeout'] !== 30 ? $this->settings['timeout'] : 30
        ));
        $body     = \Unirest\Request\Body::form($data);
        $response = \Unirest\Request::get($full_url, $headers, $body);
        return $response;
    }
    /**
     * Ambil Daftar transaksi pertanggal.
     *
     * @param string $oauth_token nilai token yang telah didapatkan setelah login
     * @param array $sourceAccount nomor akun yang akan dicek
     * @param string $startDate tanggal awal
     * @param string $endDate tanggal akhir
     * @param string $corp_id nilai CorporateID yang telah diberikan oleh pihak BCA
     *
     * @return \Unirest\Response
     */
    public function getAccountStatement($oauth_token, $sourceAccount, $startDate, $endDate)
    {
        $corp_id = $this->settings['corp_id'];
        
        $apikey = $this->settings['api_key'];
        $secret = $this->settings['secret_key'];
        
        $this->validateCorpId($corp_id);
        $this->validateBcaKey($apikey);
        $this->validateBcaKey($secret);
        $uriSign       = "GET:/banking/v3/corporates/$corp_id/accounts/$sourceAccount/statements?EndDate=$endDate&StartDate=$startDate";
        $isoTime       = self::generateIsoTime();
        $authSignature = self::generateSign($uriSign, $oauth_token, $secret, $isoTime, null);
        $headers                    = array();
        $headers['Accept']          = 'application/json';
        $headers['Content-Type']    = 'application/json';
        $headers['Authorization']   = "Bearer $oauth_token";
        $headers['X-BCA-Key']       = $apikey;
        $headers['X-BCA-Timestamp'] = $isoTime;
        $headers['X-BCA-Signature'] = $authSignature;
        $request_path = "banking/v3/corporates/$corp_id/accounts/$sourceAccount/statements?EndDate=$endDate&StartDate=$startDate";
        $domain       = $this->ddnDomain();
        $full_url     = $domain . $request_path;
        \Unirest\Request::curlOpts(array(
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSLVERSION => 6,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => $this->settings['timeout'] !== 30 ? $this->settings['timeout'] : 30
        ));
        
        $data = array('grant_type' => 'client_credentials');
        $body     = \Unirest\Request\Body::form($data);
        $response = \Unirest\Request::get($full_url, $headers, $body);
        return $response;
    }
    /**
     * Ambil informasi ATM berdasarkan lokasi GEO.
     *
     * @param string $oauth_token nilai token yang telah didapatkan setelah login
     * @param string $latitude Langitude GPS
     * @param string $longitude Longitude GPS
     * @param string $count Jumlah ATM BCA yang akan ditampilkan
     * @param string $radius Nilai radius dari lokasi GEO
     *
     * @return \Unirest\Response
     */
    public function getAtmLocation(
        $oauth_token,
        $latitude,
        $longitude,
        $count = '10',
        $radius = '20'
    ) {
        $apikey = $this->settings['api_key'];
        
        $secret = $this->settings['secret_key'];
        
        $this->validateBcaKey($apikey);
        $this->validateBcaKey($secret);
        
        $params              = array();
        $params['SearchBy']  = 'Distance';
        $params['Latitude']  = $latitude;
        $params['Longitude'] = $longitude;
        $params['Count']     = $count;
        $params['Radius']    = $radius;
        ksort($params);
        $auth_query_string = self::arrayImplode('=', '&', $params);
        $uriSign       = "GET:/general/info-bca/atm?$auth_query_string";
        $isoTime       = self::generateIsoTime();
        $authSignature = self::generateSign($uriSign, $oauth_token, $secret, $isoTime, null);
        $headers                    = array();
        $headers['Accept']          = 'application/json';
        $headers['Content-Type']    = 'application/json';
        $headers['Authorization']   = "Bearer $oauth_token";
        $headers['X-BCA-Key']       = $apikey;
        $headers['X-BCA-Timestamp'] = $isoTime;
        $headers['X-BCA-Signature'] = $authSignature;
        $request_path = "general/info-bca/atm?SearchBy=Distance&Latitude=$latitude&Longitude=$longitude&Count=$count&Radius=$radius";
        $domain       = $this->ddnDomain();
        $full_url     = $domain . $request_path;
        \Unirest\Request::curlOpts(array(
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSLVERSION => 6,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => $this->settings['timeout'] !== 30 ? $this->settings['timeout'] : 30
        ));
        
        $data = array('grant_type' => 'client_credentials');
        $body     = \Unirest\Request\Body::form($data);
        $response = \Unirest\Request::get($full_url, $headers, $body);
        return $response;
    }
    /**
     * Ambil KURS mata uang.
     *
     * @param string $oauth_token nilai token yang telah didapatkan setelah login
     * @param string $rateType type rate
     * @param string $currency Mata uang
     *
     * @return \Unirest\Response
     */
    public function getForexRate(
        $oauth_token,
        $rateType = 'e-rate',
        $currency = 'USD'
    ) {
        $apikey = $this->settings['api_key'];
        
        $secret = $this->settings['secret_key'];
        
        $this->validateBcaKey($apikey);
        $this->validateBcaKey($secret);
        
        $params             = array();
        $params['RateType'] = strtolower($rateType);
        $params['Currency'] = strtoupper($currency);
        ksort($params);
        $auth_query_string = self::arrayImplode('=', '&', $params);
        $uriSign       = "GET:/general/rate/forex?$auth_query_string";
        $isoTime       = self::generateIsoTime();
        $authSignature = self::generateSign($uriSign, $oauth_token, $secret, $isoTime, null);
        $headers                    = array();
        $headers['Accept']          = 'application/json';
        $headers['Content-Type']    = 'application/json';
        $headers['Authorization']   = "Bearer $oauth_token";
        $headers['X-BCA-Key']       = $apikey;
        $headers['X-BCA-Timestamp'] = $isoTime;
        $headers['X-BCA-Signature'] = $authSignature;
        $request_path = "general/rate/forex?$auth_query_string";
        $domain       = $this->ddnDomain();
        $full_url     = $domain . $request_path;
        \Unirest\Request::curlOpts(array(
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSLVERSION => 6,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => $this->settings['timeout'] !== 30 ? $this->settings['timeout'] : 30
        ));
        
        $data = array('grant_type' => 'client_credentials');
        $body     = \Unirest\Request\Body::form($data);
        $response = \Unirest\Request::get($full_url, $headers, $body);
        return $response;
    }
    /**
     * Transfer dana kepada akun lain dengan jumlah nominal tertentu.
     *
     * @param string $oauth_token nilai token yang telah didapatkan setelah login
     * @param int $amount nilai dana dalam RUPIAH yang akan ditransfer, Format: 13.2
     * @param string $beneficiaryAccountNumber  BCA Account number to be credited (Destination)
     * @param string $referenceID Sender's transaction reference ID
     * @param string $remark1 Transfer remark for receiver
     * @param string $remark2 ransfer remark for receiver
     * @param string $sourceAccountNumber Source of Fund Account Number
     * @param string $transactionID Transcation ID unique per day (using UTC+07 Time Zone). Format: Number
     * @param string $corp_id nilai CorporateID yang telah diberikan oleh pihak BCA [Optional]
     *
     * @return \Unirest\Response
     */
    public function fundTransfers(
        $oauth_token,
        $amount,
        $sourceAccountNumber,
        $beneficiaryAccountNumber,
        $referenceID,
        $remark1,
        $remark2,
        $transactionID
    ) {
        $corp_id = $this->settings['corp_id'];
        $apikey = $this->settings['api_key'];
        $secret = $this->settings['secret_key'];
        $this->validateCorpId($corp_id);
        $this->validateBcaKey($apikey);
        $this->validateBcaKey($secret);
        $uriSign = "POST:/banking/corporates/transfers";
        
        $isoTime = self::generateIsoTime();
        $headers                    = array();
        $headers['Accept']          = 'application/json';
        $headers['Content-Type']    = 'application/json';
        $headers['Authorization']   = "Bearer $oauth_token";
        $headers['X-BCA-Key']       = $apikey;
        $headers['X-BCA-Timestamp'] = $isoTime;
        $request_path = "banking/corporates/transfers";
        $domain       = $this->ddnDomain();
        $full_url     = $domain . $request_path;
        $bodyData                             = array();
        $bodyData['Amount']                   = $amount;
        $bodyData['BeneficiaryAccountNumber'] = $beneficiaryAccountNumber;
        $bodyData['CorporateID']              = $corp_id;
        $bodyData['CurrencyCode']             = 'IDR';
        $bodyData['ReferenceID']              = $referenceID;
        $bodyData['Remark1']                  = strtolower(str_replace(' ', '', $remark1));
        $bodyData['Remark2']                  = strtolower(str_replace(' ', '', $remark2));
        $bodyData['SourceAccountNumber']      = $sourceAccountNumber;
        $bodyData['TransactionDate']          = $isoTime;
        $bodyData['TransactionID']            = $transactionID;
        // Harus disort agar mudah kalkulasi HMAC
        ksort($bodyData);
        // Supaya jgn strip "ReferenceID" "/" jadi "/\" karena HMAC akan menjadi tidak cocok
        $encoderData = json_encode($bodyData, JSON_UNESCAPED_SLASHES);
        $authSignature = self::generateSign($uriSign, $oauth_token, $secret, $isoTime, $bodyData);
        $headers['X-BCA-Signature'] = $authSignature;
        \Unirest\Request::curlOpts(array(
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSLVERSION => 6,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => $this->settings['timeout'] !== 30 ? $this->settings['timeout'] : 30
        ));
        $body     = \Unirest\Request\Body::form($encoderData);
        $response = \Unirest\Request::post($full_url, $headers, $body);
        return $response;
    }
    
    /**
     * Realtime deposit untuk produk BCA.
     *
     * @param string $oauth_token nilai token yang telah didapatkan setelah login
     *
     * @return \Unirest\Response
     */
    public function getDepositRate($oauth_token)
    {
        $corp_id = $this->settings['corp_id'];
        $apikey  = $this->settings['api_key'];
        $secret  = $this->settings['secret_key'];
        
        $this->validateCorpId($corp_id);
        $this->validateBcaKey($apikey);
        $this->validateBcaKey($secret);
        $uriSign       = "GET:/general/rate/deposit";
        $isoTime       = self::generateIsoTime();
        $authSignature = self::generateSign($uriSign, $oauth_token, $secret, $isoTime, null);
        $headers                    = array();
        $headers['Accept']          = 'application/json';
        $headers['Content-Type']    = 'application/json';
        $headers['Authorization']   = "Bearer $oauth_token";
        $headers['X-BCA-Key']       = $apikey;
        $headers['X-BCA-Timestamp'] = $isoTime;
        $headers['X-BCA-Signature'] = $authSignature;
        $request_path = "general/rate/deposit";
        $domain       = $this->ddnDomain();
        $full_url     = $domain . $request_path;
        $data = array('grant_type' => 'client_credentials');
        
        \Unirest\Request::curlOpts(array(
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSLVERSION => 6,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => $this->settings['timeout'] !== 30 ? $this->settings['timeout'] : 30
        ));
        
        $body     = \Unirest\Request\Body::form($data);
        $response = \Unirest\Request::get($full_url, $headers, $body);
        return $response;
    }
    
    /**
     * Generate Signature.
     *
     * @param string $url Url yang akan disign
     * @param string $auth_token string nilai token dari login
     * @param string $secret_key string secretkey yang telah diberikan oleh BCA
     * @param string $isoTime string Waktu ISO8601
     * @param array $bodyToHash array Body yang akan dikirimkan ke Server BCA
     *
     * @return string
     */
    public static function generateSign($url, $auth_token, $secret_key, $isoTime, $bodyToHash)
    {
        $hash = null;
        if (is_array($bodyToHash)) {
            ksort($bodyToHash);
            $encoderData = json_encode($bodyToHash, JSON_UNESCAPED_SLASHES);
            $hash        = hash("sha256", $encoderData);
        } else {
            $hash = hash("sha256", "");
        }
        $stringToSign   = $url . ":" . $auth_token . ":" . $hash . ":" . $isoTime;
        $auth_signature = hash_hmac('sha256', $stringToSign, $secret_key, false);
        return $auth_signature;
    }
    /**
     * Set TimeZone.
     *
     * @param string $timeZone Time yang akan dipergunakan.
     *
     * @return string
     */
    public static function setTimeZone($timeZone)
    {
        self::$timezone = $timeZone;
    }
    /**
     * Get TimeZone.
     *
     * @return string
     */
    public static function getTimeZone()
    {
        return self::$timezone;
    }
    /**
     * Set nama domain BCA yang akan dipergunakan.
     *
     * @param string $hostName nama domain BCA yang akan dipergunakan.
     *
     * @return string
     */
    public static function setHostName($hostName)
    {
        self::$hostName = $hostName;
    }
    /**
     * Ambil nama domain BCA yang akan dipergunakan.
     *
     * @return string
     */
    public static function getHostName()
    {
        return self::$hostName;
    }
    /**
     * Set BCA port
     *
     * @param int $port Port yang akan dipergunakan
     *
     * @return int
     */
    public static function setPort($port)
    {
        self::$port = $port;
    }
    /**
     * Get BCA port
     *
     * @return int
     */
    public static function getPort()
    {
        return self::$port;
    }
    /**
     * Generate ISO8601 Time.
     *
     * @param string $timeZone Time yang akan dipergunakan
     *
     * @return string
     */
    public static function generateIsoTime()
    {
        $date = \Carbon\Carbon::now(self::getTimeZone());
        date_default_timezone_set(self::getTimeZone());
        $fmt     = $date->format('Y-m-d\TH:i:s');
        $ISO8601 = sprintf("$fmt.%s%s", substr(microtime(), 2, 3), date('P'));
        return $ISO8601;
    }
    /**
     * Validasi CORP_ID yang telah diberikan pihahk BCA.
     *
     * @param string $corpId
     *
     * @return bool
     */
    private function validateCorpId($corpId)
    {
        if (!preg_match('/\A[-a-zA-Z0-9_=@,.;]+\z/', $corpId)) {
            throw new BcaHttpException('Invalid CorpId' . $corpId);
        }
        return true;
    }
    /**
     * Validasi Key yang telah BCA tentukan.
     * Format 1234567-1234-1234-1345-123456789123
     *
     * @param string $key
     *
     * @return bool
     */
    private function validateBcaKey($key)
    {
        if (!preg_match('/\A([-a-zA-Z0-9]{7,})+([\-\s])+([-a-zA-Z0-9]{4,})+([\-\s])+([-a-zA-Z0-9]{4,})+([\-\s])+([-a-zA-Z0-9]{4,})+([\-\s])+([-a-zA-Z0-9]{12,})+\z/', $key)) {
            throw new BcaHttpException('Format `Key` tidak valid' . $key);
        }
        return true;
    }
    /**
     * Validasi jika clientsecret telah di-definsikan.
     *
     * @param array $sourceAccountId
     *
     * @return bool
     */
    private function validateArray($sourceAccountId = [])
    {
        if (empty($sourceAccountId)) {
            throw new BcaHttpException('AccountNumber tidak boleh kosong.');
        } else {
            if (count($sourceAccountId) > 20) {
                throw new BcaHttpException('Maksimal Account Number ' . 20);
            }
        }
        return true;
    }
    
    /**
     * Implode an array with the key and value pair giving
     * a glue, a separator between pairs and the array
     * to implode.
     *
     * @param string $glue      The glue between key and value
     * @param string $separator Separator between pairs
     * @param array  $array     The array to implode
     *
     * @return string The imploded array
     */
    public static function arrayImplode($glue, $separator, $array)
    {
        if (!is_array($array)) {
            throw new BcaHttpException('Data harus array.');
        }
        $string = array();
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $val = implode(',', $val);
            }
            $string[] = "{$key}{$glue}{$val}";
        }
        return implode($separator, $string);
    }
}