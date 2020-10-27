<?php



class Users_Model 
{

    private $conn;

    // constructor
    function __construct()
    {
    	include "public/config/db_connection.php";
    	include "public/config/config_type.php";
 
        $this->conn = $conn;
        $this->uploaddir = $UPLOAD_DIR_2;
        $this->smsuserkey = $SMS_USERKEY;
        $this->smspasskey = $SMS_PASSKEY;


    }

    // destructor
    function __destruct()
    {

    }

     /**
     * Check if user exist
     */
    public function checkUserLogin($email, $password)
    {
        $check = $this->conn->query("SELECT * FROM users WHERE Email = '" . $email . "' AND Active=1 ");

        if (mysqli_num_rows($check) > 0) {

            $row = $check->fetch_assoc();
            //$salt = $row['PasswordSalt'];
			   $salt = '38ebeaedce';
           $encrypted_password = $this->checkhashSSHA($salt, $password);

            $check_pass = $this->conn->query("SELECT * FROM users WHERE Email = '" . $email . "' AND Password='" . $password . "' AND Active=1");

            if (mysqli_num_rows($check_pass) > 0) {
                //Generate new token
                $new_token = $this->generateToken();
                $upd = $this->conn->query("UPDATE users SET IsLogin=1, Token='" . $new_token . "' WHERE Email='" . $email . "' AND Active=1 ");

                if ($upd) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }

        } else {
            return false;
        }
    }


     /**
     * Get user data by email
     */
    public function getUserByEmail($email)
    {

        $check = $this->checkUserRegister($email);
        if ($check) {
            // $query_get = $this->conn->query("SELECT *, '' AS Password FROM master_users WHERE Phone = '".$phone."' AND Active=1 ");

            // edit by elim
            $query_get = $this->conn->query("SELECT 
                                               a.UserID,
											   a.TokoID,
											   a.FirstName,
											   a.LastName,
											   a.Email,
											   a.Password,
											   a.LevelID,
											   a.Token,
											   a.FirebaseID,
											   a.FirebaseTime,
											   a.DeviceBrand,
											   a.DeviceModel,
											   a.DeviceSerial,
											   a.DeviceOS,
											   a.ReferralBy,
											   a.GoogleUserID,
											   b.TokoName,
											   b.Address,
											   b.Phone
                                          FROM 
                                                    users a	
                                         LEFT JOIN master_toko b ON a.TokoID = b.TokoID
                                         
                                          WHERE 
                                          a.Email = '" . $email . "' 
                                          AND a.Active=1");
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Check profile is complete
     */
    public function checkIsProfileComplete($email)
    {
        $check_pass = $this->conn->query("SELECT TokoID FROM users WHERE Email='" . $email . "' AND Active=1");

        if (mysqli_num_rows($check_pass) > "0") {
            //profile not complete
            return 0;
        } else {
            //profile complete
            return 1;
        }
    }

     /**
     * Check login by new generated pass (forgot pass)
     */
    public function checkUserLoginByForgot($email, $password)
    {

        $now = $this->get_chat_time();

        $check_pass = $this->conn->query("SELECT * FROM users WHERE Email = '" . $email . "' AND ForgotPassword='" . $password . "' AND ForgotPasswordExpired > STR_TO_DATE('" . $now . "', '%Y-%m-%d %H:%i:%s') AND Active=1 ");

        if (mysqli_num_rows($check_pass) > 0) {
            $new_token = $this->generateToken();
            $upd = $this->conn->query("UPDATE users SET IsLogin=1, Token='" . $new_token . "' WHERE Email='" . $email . "' AND Active=1 ");
            if ($upd) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Update FirebaseID
     */
    public function updateFirebase($email, $firebase_id)
    {

        $update = $this->conn->query("UPDATE master_users SET 
										FirebaseID 		= '" . $firebase_id . "'
									WHERE 
										Email = '" . $email . "'");

        if ($update) {
            return true;
        } else {
            return false;
        }
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
     * Check if user exist
     */
    public function checkUserRegister($email)
    {
        $query = $this->conn->query("SELECT * FROM users WHERE Email = '" . $email . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }											 
		
			
			

    function get_chat_time()
    {
        // $myDateTime = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone('GMT'));
        // $myDateTime->setTimezone(new DateTimeZone('Asia/Jakarta'));
        // $now = $myDateTime->format('Y-m-d H:i:s');

        $now = date("Y-m-d H:i:s");
        return $now;
    }
	
	
	
	
	
	}

?>