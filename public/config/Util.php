<?php

class Util
{

    // constructor
    function __construct()
    {
        include "db_connection.php";
        include "config_type.php";
        include "../v1/vendor/phpmailer/classes/class.phpmailer.php";
        include "../v1/vendor/pusher/pusher-php-server/src/Pusher.php";

        $this->conn = $conn;
        $this->uploaddir = $UPLOAD_DIR_2;
        $this->smsuserkey = $SMS_USERKEY;
        $this->smspasskey = $SMS_PASSKEY;

        // Pusher Config
        $this->APP_ID_PUSHER = $APP_ID;
        $this->KEY_PUSHER = $KEY;
        $this->SECRET_PUSHER = $SECRET;
        $this->CLUSTER_PUSHER = $CLUSTER;

        $this->MAIL_SMTPSecure = $MAIL_SMTPSecure;
        $this->MAIL_Host = "mail.aid.co.id"; //hostname masing-masing provider email
        $this->MAIL_SMTPDebug = 2;
        $this->MAIL_Port = 465;
        $this->MAIL_SMTPAuth = true;
        $this->MAIL_Username = "admin@aid.co.id"; //username email
        $this->MAIL_Password = "aid2018@!"; //password email
    }

    // destructor
    function __destruct()
    {
    }

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
    // function get_current_time(){
    // 	// $myDateTime = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone('GMT'));
    // 	// $myDateTime->setTimezone(new DateTimeZone('Asia/Jakarta'));
    // 	// $now = $myDateTime->format('Y-m-d H:i:s');

    // 	$now = date("Y-m-d H:i:s");
    // 	return $now;
    // }

    // function get_chat_time(){
    // 	// $myDateTime = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone('GMT'));
    // 	// $myDateTime->setTimezone(new DateTimeZone('Asia/Jakarta'));
    // 	// $now = $myDateTime->format('Y-m-d H:i:s');

    // 	$now = date("Y-m-d H:i:s");
    // 	return $now;
    // }

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
        //echo $result;exit;
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
     * Function Send Email
     * @param : $to, $subject, $message, $headers2 = []
     * returns boolean
     */
    function send_email_html($to, $subject, $message, $headers2 = [])
    {
        // return 'disabled';
        $mail = new PHPMailer;
        $mail->IsSMTP();
        // $this->MAIL_SMTPSecure 	= $MAIL_SMTPSecure;
        // $this->MAIL_Host 		= "mail.aid.co.id"; //hostname masing-masing provider email
        // $this->MAIL_SMTPDebug 	= 2;
        // $this->MAIL_Port 		= 465;
        // $this->MAIL_SMTPAuth 	= true;
        // $this->MAIL_Username 	= "admin@aid.co.id"; //username email
        // $this->MAIL_Password 	= "aid2018@!"; //password email
        // echo 'test: '.$this->MAIL_SMTPSecure;
        $mail->SMTPSecure = $this->MAIL_SMTPSecure;
        $mail->Host = $this->MAIL_Host; //hostname masing-masing provider email
        $mail->SMTPDebug = $this->MAIL_SMTPDebug;
        $mail->Port = $this->MAIL_Port;
        $mail->SMTPAuth = $this->MAIL_SMTPAuth;
        $mail->Username = $this->MAIL_Username; //username email
        $mail->Password = $this->MAIL_Password; //password email
        $mail->SetFrom("admin@aid.co.id", $subject); //set email pengirim contoh "test@test.com", "Thamrin District"

        $mail->Subject = $subject; //subyek email //subyek email
        if (is_array($to)) {
            // var_dump($to);
            foreach ($to as $key => $val) {
                $mail->AddAddress($val); //tujuan email
            }
        } else {
            $mail->AddAddress($to); //tujuan email
        }
        $mail->IsHTML(True);
        $mail->Body = $message;
        if ($mail->Send()) {
            return 'Email Send';
        } else {
            return 'Email Not Send';
        }
    }

    //Send Broadcast to pusher
    function send_pusher($type, $message, $data)
    {
        require '../v1/vendor/autoload.php';
        $options = array(
            // 'encrypted' => true,
            'cluster' => $this->CLUSTER_PUSHER
        );
        $pusher = new Pusher\Pusher(
            $this->KEY_PUSHER,
            $this->SECRET_PUSHER,
            $this->APP_ID_PUSHER,
            $options
        );

        $data['type'] = $type;
        $data['message'] = $message;
        $data['data'] = $data;
        // $pusher->trigger('my-channel', 'my-event', $data);

        if ($pusher->trigger('my-channel', 'my-event', $data)) {
            return 1;
        } else {
            return 0;
        }
    }

}

?>