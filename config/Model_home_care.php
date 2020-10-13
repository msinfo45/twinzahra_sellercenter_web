<?php

class Model_home_care
{

    private $conn;

    // constructor
    function __construct()
    {
        include "db_connection.php";
        include "config_type.php";
        $this->conn = $conn;
        $this->uploaddir = $UPLOAD_DIR_2;
        $this->smsuserkey = $SMS_USERKEY;
        $this->smspasskey = $SMS_PASSKEY;
        date_default_timezone_set("Asia/Jakarta");
    }

    // destructor
    function __destruct()
    {

    }

    //-------------------------------- Another Function Goes Here ------------------------//

    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password)
    {

        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }

    /**
     * Count Percentage
     * @param total, percent
     * returns salt and encrypted password
     */
    function countPercentage($angka, $persen)
    {
        $hasil = $persen * $angka / 100;
        return $hasil;
    }

    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password)
    {
        $hash = base64_encode(sha1($password . $salt, true) . $salt);
        return $hash;
    }

    /**
     * Function Get Current Time
     * @param : No
     * returns current time
     */
    function get_current_time()
    {
        // $myDateTime = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone('GMT'));
        // $myDateTime->setTimezone(new DateTimeZone('Asia/Jakarta'));
        // $now = $myDateTime->format('Y-m-d H:i:s');

        $now = date("Y-m-d H:i:s");
        return $now;
    }

    function get_chat_time()
    {
        // $myDateTime = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone('GMT'));
        // $myDateTime->setTimezone(new DateTimeZone('Asia/Jakarta'));
        // $now = $myDateTime->format('Y-m-d H:i:s');

        $now = date("Y-m-d H:i:s");
        return $now;
    }

    /**
     * Get String After (:) characters
     * @param string, charactr
     * returns string
     */
    function str_after($string, $substring)
    {
        $pos = strpos($string, $substring);
        if ($pos === false)
            return $string;
        else
            return (substr($string, $pos + strlen($substring)));
    }

    /**
     * Get String Before (:) characters
     * @param string, charactr
     * returns string
     */
    function str_before($string, $substring)
    {
        $pos = strpos($string, $substring);
        if ($pos === false)
            return $string;
        else
            return (substr($string, 0, $pos));
    }

    /**
     * Function Send SMS code
     * @param : Phone, Code, Name
     * returns boolean
     */
    function send_sms($phone, $code, $name)
    {
        $userkey = $this->smsuserkey; //userkey lihat di zenziva
        $passkey = $this->smspasskey; // set passkey di zenziva
        $message = "VTAL: Hi " . $name . ", Terima Kasih telah melakukan registrasi di VTAL. Mohon masukan kode aktivasi berikut ini: " . $code;
        $url = "https://reguler.zenziva.net/apps/smsapi.php";
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, 'userkey=' . $userkey . '&passkey=' . $passkey . '&nohp=' . $phone . '&pesan=' . urlencode($message));
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        $results = curl_exec($curlHandle);
        curl_close($curlHandle);
    }

    /**
     * Function Send SMS new password
     * @param : Phone, New Password
     * returns boolean
     */
    function send_sms_password($phone, $code)
    {
        $userkey = $this->smsuserkey; //userkey lihat di zenziva
        $passkey = $this->smspasskey; // set passkey di zenziva
        $message = "VTAL: Hi, berikut adalah password baru anda: " . $code . ", diharapkan segera ubah password anda";
        $url = "https://reguler.zenziva.net/apps/smsapi.php";
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, 'userkey=' . $userkey . '&passkey=' . $passkey . '&nohp=' . $phone . '&pesan=' . urlencode($message));
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        $results = curl_exec($curlHandle);
        curl_close($curlHandle);
    }

    /**
     * Function Generate Code Verification
     * @param : Digits
     * returns code
     */
    function generatePIN($digits = 6)
    {
        $i = 0;
        $pin = "";
        while ($i < $digits) {
            //generate a random number between 0 and 9.
            $pin .= mt_rand(0, 9);
            $i++;
        }
        return $pin;
    }

    /**
     * Function Generate Random Password (forgot password)
     * @param : Digits
     * returns code
     */
    function randomPassword($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }

    /**
     * Function Generate API Token
     * @param : Num Digits (optional)
     * returns code
     */
    function generateToken($length = 15)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Function to check image type (JPG, PNG)
     * @param : filename+path
     * returns boolean
     */
    function is_image($path)
    {
        $a = getimagesize($path);
        $image_type = $a[2];
        if (in_array($image_type, array(IMAGETYPE_JPEG, IMAGETYPE_PNG))) {
            return true;
        }
        return false;
    }

    /**
     * Function Send GCM to Nurse
     * @param : FirebaseID, Custom Data JSON
     * returns boolean
     */
    function sendNotification_Nurse($firebase_id, $custom_data)
    {

        $registrationIds = array($firebase_id);

        $fields = array(
            'registration_ids' => $registrationIds,
            'data' => $custom_data
        );

        $headers = array(
            'Authorization: key=AAAAAnMyp9o:APA91bH42xYduMpF-y0sSkT3iM63HmL-k9cKSi8O5kePGmAMJ8RUJr98bDvNKHzoatdIsM7p2WPmjPttuEZNR99uA9vXayJpmHNBWoIDxpmby6pijVOgxzlIfB5u1oSaoNb60aO0OVx-',
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        //echo $result;exit;
    }

    /**
     * Function Send GCM to Patient
     * @param : FirebaseID, Custom Data JSON
     * returns boolean
     */
    function sendNotification_Patient($firebase_id, $custom_data)
    {

        $registrationIds = array($firebase_id);

        $fields = array(
            'registration_ids' => $registrationIds,
            'data' => $custom_data
        );
        // var_dump($fields);
        $headers = array(
            'Authorization: key=AAAAg12wFPo:APA91bFG6K8jrVOr7A1n_sK_EIyKiTZvBC-SB1jncFzXhEyL3tWzd8pcVxNBqVUj0Cz8wFjSn_BOGIxI_NOtBR-C_Flh0v6vhkwnLXeHSWoPNZuKtoBI8CgFT_1ii0esL7MhSbwAVsSR',
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        // echo $result;exit;
    }

    /**
     * Function Send GCM to Doctor
     * @param : FirebaseID, Custom Data JSON
     * returns boolean
     */
    function sendNotification_Doctor($firebase_id, $custom_data)
    {

        $registrationIds = array($firebase_id);

        $fields = array(
            'registration_ids' => $registrationIds,
            'data' => $custom_data
        );

        $headers = array(
            'Authorization: key=AAAASnUGctw:APA91bES-Btw7Ufa9jH2pOUQt56hbo0wY45QlLR0527ZH-rgcPC2q_-ujEXMgVj4VhUhwL8KVesG6lmY5RL7rxo6RhrA4MB2mBJUdmVPsA5VQU5HNswXdU92Zv-EEJGvuovTLAJDo5iq',
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        //echo $result;exit;
    }

    /**
     * Get config from master_config
     * @param : $configName
     * returns boolean
     */
    public function getConfig($configName)
    {
        $q = $this->conn->query("SELECT * FROM master_config 
			WHERE ConfigName = '" . $configName . "' AND Active = 1");

        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }

    /**
     * Get user data by phone
     */
    public function getUserByID($user_id)
    {
        $query_get = $this->conn->query("SELECT * FROM master_users WHERE UserID = '" . $user_id . "' ");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }

    public function getUserFamily($user_id)
    {
        $q = $this->conn->query("SELECT * FROM master_patients WHERE UserID = " . $user_id . " ");

        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }

    public function createUserFamily($UserID, $PatientName, $Gender, $Age, $Height, $Weight)
    {
        $q = $this->conn->query(" INSERT INTO master_patients (UserID, PatientName, Gender, Age, Height, Weight) VALUES ('" . $UserID . "', '" . $PatientName . "', '" . $Gender . "', '" . $Age . "', '" . $Height . "', '" . $Weight . "') ");

        if ($q) {
            // $q2 = $this->getUserFamily($user_id);
            $q2 = $this->conn->query(" SELECT * FROM master_patients WHERE UserID = '" . $UserID . "' AND PatientName = '" . $PatientName . "' ");

            if (mysqli_num_rows($q2) > 0) {
                return $q2;
            }
        } else {
            return false;
        }
    }

    public function getKanopiNurseCategory()
    {
        $q = $this->conn->query("SELECT a.* FROM kanopi_nrz_nurse_categories a WHERE a.Active = 1");

        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }

    public function getKanopiNurse()
    {
        $q = $this->conn->query("SELECT * FROM kanopi_nrz_nurses WHERE Active = 1");

        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }

    public function getKanopiNurseById($id)
    {
        $q = $this->conn->query("SELECT *, IFNULL(CONCAT('" . $this->uploaddir . "', '/kanopinurses/', NurseID, '/', Image), '') AS image_url FROM kanopi_nrz_nurses WHERE NurseID = $id AND Active = 1");

        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }

    /**
     * Process Pending Order Nurse Kanopi
     */
    public function processPendingOrderKanopi($user_id, $latitude, $longitude, $location, $patient_condition, $environment_condition, $specific_request, $admin_fee, $company_fee_percent, $user_family_id, $nurse_id, $payment_type_id = '', $kanopi_nurse_price_min, $kanopi_nurse_price_max,$kode_voucher,$nominal)
    {

        $check_orderid = $this->conn->query("SELECT IFNULL(MAX(Right(OrderNo,8)),0) AS OrderNo
											FROM kanopi_nrz_orders_current 
											WHERE 
												DATE_FORMAT(OrderDate, '%m')='" . date('m') . "' AND 
												DATE_FORMAT(OrderDate, '%Y')='" . date('Y') . "'");

        $data_orderid = $check_orderid->fetch_assoc();

        if ($data_orderid['OrderNo'] == 0) {
            //Start From First
            $num = date('y') . date('m') . "0001";
            $order_no = "HC" . $num;
        } else {
            //Continue Number +1
            $num = $data_orderid['OrderNo'] + 1;
            $order_no = "HC" . $num;
        }

        $company_revenue = $admin_fee * $company_fee_percent / 100;
        $kanopi_revenue_percent = 100 - $company_fee_percent;
        $kanopi_revenue = $admin_fee - $company_revenue;

        $patient_id = $user_family_id;

        $mode = 2;
        if ($mode == 1) {
            $orderStatusID = 2;
        } else {
            $orderStatusID = 17;
        }

        if($nominal == ''){
            $nominal = 0;
        }

        $insert = $this->conn->query("INSERT INTO kanopi_nrz_orders_current 
									(OrderNo,
									OrderDate,
									UserID,
									Latitude,
									Longitude,
									Location,
									PatientCondition,
									EnvironmentCondition,
									SpecificRequest,
									AdminFee,
									CreatedDate, 
									PatientID,
									NurseID,
									CompanyRevenuePercent,
									CompanyRevenue,
									OrderStatusID,
									KanopiRevenuePercent,
									KanopiRevenue,
									PaymentTypeID,
									Price,
									PriceMax,
									voucher_code,
									nominal
									) 
								VALUES 
									('" . $order_no . "',
									'" . $this->get_current_time() . "',
									'" . $user_id . "',
									'" . $latitude . "',
									'" . $longitude . "',
									'" . $location . "',
									'" . $patient_condition . "',
									'" . $environment_condition . "',
									'" . $specific_request . "',
									'" . $admin_fee . "',
									'" . $this->get_current_time() . "',
									'" . $patient_id . "',
									'" . $nurse_id . "',
									'" . $company_fee_percent . "',
									'" . $company_revenue . "',
									'" . $orderStatusID . "',
									'" . $kanopi_revenue_percent . "',
									'" . $kanopi_revenue . "',
									'" . $payment_type_id . "',
									'" . $kanopi_nurse_price_min . "',
									'" . $kanopi_nurse_price_max . "',
									'" . $kode_voucher . "',
									" . $nominal . "
									) ");

        if ($insert) {
            //create order log
            $order_id = $this->conn->insert_id;
            $order_status_id = $orderStatusID;
            $description = 'Log Order Kanopi, created by sistem api';
            $this->createOrderLog($this->conn->insert_id, $order_status_id, $nurse_id, $description);

            return $order_id;
        } else {
            return false;
        }
    }

    public function createOrderLog($order_id, $order_status_id, $nurse_id, $description)
    {
        $q = $this->conn->query("INSERT INTO kanopi_nrz_orders_logs 
									(OrderID,
									CreatedDate,
									CreatedBy,
									OrderStatusID,
									KanopiNurseID,
									Description
									) 
								VALUES 
									('" . $order_id . "',
									'" . $this->get_current_time() . "',
									'9-',
									'" . $order_status_id . "',
									'" . $nurse_id . "',
									'" . $description . "'
									) ");
        if ($q) {
            return $this->conn->insert_id;
        } else {
            return false;
        }
    }

    /**
     * Get Nurse Kanopi by filter
     * @param: $PriceMin, $PriceMax, $CategoryID, $Page, $Limit
     *return array, false
     */
    public function getKanopiNurseByFilter($PriceMin, $PriceMax, $CategoryID, $Page, $Limit)
    {

        $condition = "";
        $condition2 = "";
        if ($CategoryID != '') {
            $condition .= "AND a.CategoryID = " . $CategoryID . " ";
        }

        if ($PriceMin != '' && $PriceMin != '') {
            $condition .= "AND a.Price BETWEEN " . $PriceMin . " AND " . $PriceMax . " OR a.PriceMax BETWEEN " . $PriceMin . " AND " . $PriceMax . " ";
        }

        if ($Page != '' && $Limit != '') {
            if ($Page == 1) {
                $p = 0;
            } else {
                $p = ($Page - 1) * $Limit;
            }

            $condition2 .= "LIMIT " . $Limit . " OFFSET " . $p . " ";
        }

        $q = $this->conn->query("SELECT a.NurseID, a.FirstName, a.LastName, a.Degree, a.Price, a.PriceMax, a.Location, a.BirthDate, 
    								IFNULL(a.YearExperience,'- ') AS YearExperience, 
    								a.IsAvailable, b.CategoryName, IFNULL(CONCAT('" . $this->uploaddir . "', '/kanopinurses/', a.NurseID, '/', a.Image), '') AS image_url
    		FROM kanopi_nrz_nurses a
    		JOIN kanopi_nrz_nurse_categories b ON b.CategoryID = a.CategoryID
    		WHERE a.Active = 1 " . $condition . "
    		ORDER BY a.FirstName " . $condition2);

        return $q;

    }

    public function getKanopiNurseEducation($nurse_id)
    {
        $q = $this->conn->query("SELECT * FROM kanopi_nrz_nurse_educations WHERE NurseID = $nurse_id ");

        return $q;
    }

    public function getKanopiNurseExperience($nurse_id)
    {
        $q = $this->conn->query("SELECT * FROM kanopi_nrz_nurse_experiences WHERE NurseID = $nurse_id ");

        return $q;
    }

    public function getKanopiNurseCertificate($nurse_id)
    {
        $q = $this->conn->query("SELECT CONCAT('" . $this->uploaddir . "', '/kanopinurses/', '" . $nurse_id . "','/certificate/', '/', Image) as certificate_image FROM kanopi_nrz_nurse_certificates WHERE KanopiNurseID = $nurse_id");

        return $q;
    }


    /**
     * Get last order from user nurse Kanopi
     */
    public function getOrderLastKanopi($user_id)
    {
        $mode = 2;
        if ($mode == 1) {
            $order_status = 2;
        } else {
            $order_status = 17;
        }
        $query = $this->conn->query("SELECT 
        							a.*, 
        							b.StatusName, 
        							c.PaymentType,
        							d.PatientName  
        							FROM kanopi_nrz_orders_current a
					        		LEFT JOIN kanopi_nrz_order_status b ON  b.OrderStatusID = a.OrderStatusID
					        		LEFT JOIN master_payment_type c ON c.PaymentTypeID = a.PaymentTypeID
					        		LEFT JOIN master_patients d ON d.PatientID = a.PatientID
					        		WHERE a.UserID = '" . $user_id . "' AND a.OrderStatusID= '" . $order_status . "' AND a.Active=1 
					        		ORDER BY OrderID DESC LIMIT 1 ");


        if (mysqli_num_rows($query) > 0) {
            // $row = $query->fetch_assoc();
            // $current_id = $row['OrderID'];

            return $query;
        } else {
            return false;
        }
    }

    /**
     * Check if token exist
     */
    public function checkToken($token, $user_id)
    {
        $query = $this->conn->query("SELECT * FROM master_users WHERE Token = '" . $token . "' AND UserID = '" . $user_id . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if token exist
     */
    public function checkToken2($token, $user_id)
    {
        $query = $this->conn->query("SELECT * FROM master_users WHERE Token = '" . $token . "' AND UserID = '" . $user_id . "' ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get payment account join bank
     * @param : Latitude, Longitude
     * returns boolean
     */
    public function getPaymentAccount()
    {
        $q = $this->conn->query("SELECT a.AccountName, a.AccountNumber, a.Branch,b.BankName AS Bank, b.BankID, CONCAT( '" . $this->uploaddir . "', '/banks/', b.image) as image
 			FROM master_payment_account a
 			JOIN master_bank b ON a.BankID = b.BankID
 			WHERE a.Active = 1 AND b.Active = 1");

        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }

    /**
     * Get Bank Account
     */
    public function getPaymentType($method = null)
    {
        $filter = "";
        if ($method == 1) { //doctor
            $filter = " AND PaymentTypeID NOT IN (1)";
        } elseif ($method == 2) {
            $filter = " AND PaymentTypeID IN (2)";
        }
        $query_get = $this->conn->query("SELECT *, '' AS Icon FROM master_payment_type WHERE Active = 1" . $filter);
        return $query_get;
    }

    /**
     * Check if payment confirmation exist (Home Care Order)
     */
    public function checkConfirmPaymentPending($order_id)
    {
        $query = $this->conn->query("SELECT * FROM kanopi_user_payment_transfer WHERE OrderID = '" . $order_id . "' AND Status=0 ");

        return $query;
    }

    /**
     * Create kanopi_user_payment_transfer (Home Care Order)
     */
    public function confirmPaymentTransfer($user_id, $order_id, $payment_accound_id, $bank_name, $account_name, $account_no, $trf_date, $total, $filename)
    {

        $upd = $this->conn->query("UPDATE kanopi_nrz_orders_current SET OrderStatusID = 3 WHERE OrderID = '" . $order_id . "'");
        $insert = $this->conn->query("INSERT INTO kanopi_user_payment_transfers 
										(OrderID,
										SenderBankName,
										SenderBankAccName,
										SenderBankAccNo,
										TransferDate,
										PaymentAccountID,
										Total,
										Status,
										CreatedBy,
										CreatedDate,
										Image
										) 
									VALUES 
										('" . $order_id . "',
										'" . $bank_name . "', 
										'" . $account_name . "', 
										'" . $account_no . "',
										'" . $trf_date . "',
										'" . $payment_accound_id . "',
										'" . $total . "',
										0,
										'" . $user_id . "',
										'" . $this->get_current_time() . "',
										'" . $filename . "'
										) ");

        if ($insert) {
            $insert_id = $this->conn->insert_id;
            $q = $this->conn->query("SELECT * FROM kanopi_nrz_orders_current WHERE OrderID = '" . $order_id . "'");
            if (mysqli_num_rows($q) > 0) {
                $q = $q->fetch_assoc();

                /*create order log*/
                $order_status_id = 3;
                $nurse_id = $q['NurseID'];
                $description = 'Log Order Kanopi, created by sistem api';
                $this->createOrderLog($order_id, $order_status_id, $nurse_id, $description);
                /*create order log*/

            }

            return $insert_id;
        } else {
            echo 'test';
            return null;
        }
    }

    /**
     * Check user by ID
     */
    public function checkUserById($id)
    {
        $query = $this->conn->query("SELECT * FROM master_users WHERE UserID = '" . $id . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get Order Nurse History
     */
    public function getOrderNurseHistory($user_id, $order_id = null, $page, $limit)
    {

        $filter = "";
        if ($order_id != null) {
            $filter = " AND a.OrderID = '" . $order_id . "' ";
        }

        $condition = '';
        if ($page != '' && $limit != '') {
            if ($page == 1) {
                $p = 0;
            } else {
                $p = ($page - 1) * $limit;
            }

            $condition .= "LIMIT " . $limit . " OFFSET " . $p . " ";
        }


        $query_get = $this->conn->query("SELECT   
											a.*,
											DATE(a.OrderDate) AS OrderDate,
											IFNULL(CONCAT('" . $this->uploaddir . "', '/kanopinurses/', b.NurseID, '/', b.Image), '') AS image_url,
											b.NurseID,
											b.FirstName,
											b.LastName,
											b.BirthDate,
											b.YearExperience,
											b.Location AS nurse_location,
											c.OrderStatusID,
											c.StatusName,
											IFNULL(d.PaymentType, '') AS PaymentType,
											e.CategoryID,
											IFNULL(f.PatientName, '') AS PatientName,
											IFNULL(f.Age, '') AS Age,
											IFNULL(f.Gender, '') AS Gender,
											IFNULL(f.Height, '') As Height,
											IFNULL(f.Weight, '') AS Weight,
											a.voucher_code,
											a.nominal,
											(CASE WHEN a.voucher_code = '0' THEN null WHEN a.voucher_code = '' THEN null ELSE a.voucher_code END) as voucher_code,
											(CASE WHEN a.nominal = '0' THEN null WHEN a.nominal = '' THEN null ELSE a.nominal END) as nominal   
										FROM kanopi_nrz_orders_current a
										LEFT JOIN kanopi_nrz_nurses b ON b.NurseID = a.NurseID
										LEFT JOIN kanopi_nrz_order_status c ON c.OrderStatusID = a.OrderStatusID
										LEFT JOIN master_payment_type d ON d.PaymentTypeID = a.PaymentTypeID
										LEFT JOIN kanopi_nrz_nurse_categories e ON e.CategoryID = b.CategoryID
										LEFT JOIN master_patients f ON f.PatientID = a.PatientID
										WHERE a.UserID = '" . $user_id . "' " . $filter . " AND a.OrderStatusID NOT IN (1) AND a.Active = 1
										ORDER BY a.OrderID DESC " . $condition . " ");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }

    public function updateNurseIsAvailable($nurse_id)
    {
        $update = $this->conn->query("UPDATE kanopi_nrz_nurses SET IsAvailable = 0 WHERE NurseID = $nurse_id");

        if ($update) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get Order Nurse History
     */
    public function getOrderLogs($order_id = null)
    {
        $query_get = $this->conn->query("SELECT   
											a.*,
											b.NurseID,
											b.FirstName,
											b.LastName,
											b.BirthDate,
											b.YearExperience,
											b.Location AS nurse_location
										FROM kanopi_nrz_orders_logs a
										LEFT JOIN kanopi_nrz_nurses b ON b.NurseID = a.KanopiNurseID
										WHERE a.LogID = (SELECT MAX(LogID) FROM kanopi_nrz_orders_logs WHERE OrderID = '" . $order_id . "' AND KanopiNurseID IS NOT NULL)
										ORDER BY a.LogID DESC ");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }

    public function getPaymentTransferByID($id)
    {
        $q = $this->conn->query("SELECT 
    							a.*,
    							b.AccountName,
    							b.AccountNumber,
    							b.Bank,
    							b.Branch
    							FROM kanopi_user_payment_transfers a
    							LEFT JOIN master_payment_account b ON a.PaymentAccountID = b.PaymentAccountID
    							WHERE PaymentTransferID = " . $id
        );

        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }
}