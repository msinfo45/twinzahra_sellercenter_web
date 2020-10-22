<?php

class Model_doctor
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
    }

    // destructor
    function __destruct()
    {
    }

    /**
     * Create New Doctor
     */
    public function createDoctor($firstname, $lastname, $phone, $password, $email, $degree, $birthdate, $gender, $no_ktp, $no_str, $experience, $location, $category_id, $firebase_id, $firebase_time, $device_brand, $device_model, $device_serial, $device_os, $referral_by)
    {

        //Generate Encrypt Password
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"];
        $salt_password = $hash["salt"];

        //Generate Verification Code
        $code = $this->generatePIN();

        //Generate Token
        $token = $this->generateToken();

        $ref = "0";
        if ($referral_by != "") {
            $ref = $referral_by;
        }

        $insert = $this->conn->query("INSERT INTO doc_doctors 
									(FirstName, 
									LastName, 
									Phone,
									Password,
									PasswordSalt,
									Email,
									Degree,
									BirthDate,
									Gender,
									No_KTP,
									No_STR,
									YearExperience,
									Location,
									CategoryID,
									Token,
									ReferralBy,
									FirebaseID,
									FirebaseTime,
									DeviceBrand,
									DeviceModel,
									DeviceSerial,
									DeviceOS,
									CreatedDate,
									ActivationCode
									) 
								VALUES 
									('" . $firstname . "', 
									'" . $lastname . "',
									'" . $phone . "',
									'" . $encrypted_password . "',
									'" . $salt_password . "',
									'" . $email . "',
									'" . $degree . "',
									'" . $birthdate . "',
									'" . $gender . "',
									'" . $no_ktp . "',
									'" . $no_str . "',
									'" . $experience . "',
									'" . $location . "',
									'" . $category_id . "',
									'" . $token . "',
									'" . $ref . "',
									'" . $firebase_id . "',
									'" . $firebase_time . "',
									'" . $device_brand . "',
									'" . $device_model . "',
									'" . $device_serial . "',
									'" . $device_os . "',
									'" . $this->get_current_time() . "',
									'" . $code . "'
									) ");

        if ($insert) {
            $name = $firstname;
            $this->send_sms($phone, $code, $name);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Get promo
     */
    public function getJurnal()
    {
        $query_get = $this->conn->query("SELECT
                                        JurnalID,
                                        Title,
                                        Url,
                                        CreatedDate
                                      
                                        FROM
                                            master_jurnal
                                        WHERE
                                            Active = 1 
                                    ORDER BY
                                        CreatedDate DESC");

        return $query_get;
    }

    /**
     * Get Doctor data by phone
     */
    public function getDoctorByPhone($phone)
    {

        $check = $this->checkDoctorRegister($phone);
        if ($check) {
            $query_get = $this->conn->query("SELECT *, '' AS Password FROM doc_doctors WHERE Phone = '" . $phone . "'");
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get Doctor data by phone (only for login callback)
     */
    public function getDoctorLoginData($phone)
    {

        $check = $this->checkDoctorRegister($phone);
        if ($check) {
            //            get data only when Active dan Verified = 1
            $query_get = $this->conn->query("SELECT a.*, 
												IFNULL(a.BankAccName,'') AS BankAccName,
												IFNULL(a.BankAccNo,'') AS BankAccNo,
												IFNULL(a.BankID,'') AS BankID,
												IFNULL(a.BankBranch,'') AS BankBranch,
												'' AS Password, 
												c.Description AS CategoryName, 
												IFNULL(b.Active,'0') AS Status 
												FROM doc_doctors a 
												LEFT JOIN doc_doctor_location b ON a.DoctorID = b.DoctorID
												INNER JOIN doc_categories c ON c.CategoryID = a.CategoryID
												WHERE a.Phone = '" . $phone . "' AND a.Active=1");
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get Doctor data detail
     */
    public function getDoctorData($doctor_id)
    {

        $check = $this->checkDoctorById($doctor_id);
        if ($check) {
            $query_get = $this->conn->query("SELECT * FROM doc_doctors WHERE DoctorID = '" . $doctor_id . "' AND Active=1");
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get Doctor education
     */
    public function getDoctorEducation($doctor_id)
    {

        $check = $this->checkDoctorById($doctor_id);
        if ($check) {
            $query_get = $this->conn->query("SELECT * FROM doc_doctor_educations WHERE DoctorID = '" . $doctor_id . "' AND Active=1 ORDER BY DoctorEducationID DESC");
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get Doctor experience
     */
    public function getDoctorExperience($doctor_id)
    {

        $check = $this->checkDoctorById($doctor_id);
        if ($check) {
            $query_get = $this->conn->query("SELECT * FROM doc_doctor_experiences WHERE DoctorID = '" . $doctor_id . "' AND Active=1 ORDER BY DoctorExperienceID DESC");
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get Doctor education by educationID
     */
    public function getDoctorEducationByID($edu_id)
    {

        $check = $this->checkDoctorEducationById($edu_id);
        if ($check) {
            $query_get = $this->conn->query("SELECT * FROM doc_doctor_educations WHERE DoctorEducationID = '" . $edu_id . "'");
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get Doctor experience by experienceID
     */
    public function getDoctorExperienceByID($exp_id)
    {

        $check = $this->checkDoctorExperienceById($exp_id);
        if ($check) {
            $query_get = $this->conn->query("SELECT * FROM doc_doctor_experiences WHERE DoctorExperienceID = '" . $exp_id . "'");
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get Doctor Job offer
     */
    public function getJobOffer($doctor_id)
    {

        $category_id = "0";

        $query_doc = $this->conn->query("SELECT CategoryID FROM doc_doctors WHERE DoctorID = '" . $doctor_id . "' AND Active=1");
        $row = $query_doc->fetch_assoc();
        $category_id = $row["CategoryID"];

        $query_get = $this->conn->query("SELECT   
												a.OrderID,
												a.OrderDate,
												a.Notes,
												a.TotalPrice,
												a.DoctorID,
												b.UserID,
												b.FirstName,
												b.LastName,
												b.BirthDate,
												b.Address
											FROM doc_orders_current a
											INNER JOIN master_users b ON b.UserID = a.UserID
											WHERE 
												a.CategoryID = '" . $category_id . "' AND
												a.OrderStatusID = 1 AND 
												a.Active = 1 AND
												a.OrderID NOT IN (SELECT OrderID FROM doc_orders_doctor_accept WHERE DoctorID = '" . $doctor_id . "')  
											ORDER BY a.OrderDate DESC");
        return $query_get;
    }

    /**
     * Get Doctor Job offer Detail
     */
    public function getJobOfferDetail($order_id)
    {

        $check = $this->checkOrderExist($order_id);
        if ($check) {
            $query_get = $this->conn->query("SELECT   
												a.OrderID,
												a.OrderDate,
												a.Notes,
												a.TotalPrice,
												b.UserID,
												b.FirstName,
												b.LastName,
												b.BirthDate,
												b.Address,
												b.Weight,
												b.Height
											FROM doc_orders_current a
											INNER JOIN master_users b ON b.UserID = a.UserID
											WHERE a.OrderID = '" . $order_id . "' AND a.Active = 1 AND OrderStatusID = 1");
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get banners
     */
    public function getBanners()
    {
        $query_get = $this->conn->query("SELECT BannerID, Title, Caption, App, CONCAT('" . $this->uploaddir . "', '/banners/', BannerID,'.jpg') AS Url FROM master_banners WHERE App='1' AND Active = 1");
        return $query_get;
    }

    /**
     * Get Order History
     */
    public function getOrderHistory($doctor_id, $order_id = null)
    {

        $filter = "";
        if ($order_id != null) {
            $filter = " AND a.OrderID = '" . $order_id . "' ";
        }

        $query_get = $this->conn->query("SELECT
											a.*,
											-- a.OrderNo,
											-- a.OrderID,
											DATE(a.OrderDate) AS OrderDate,
											-- a.Notes,
											-- a.TotalPrice,
											a.Rating AS Rate,
											b.UserID,
											b.FirstName,
											b.LastName,
											b.BirthDate,
											b.Address,											
											b.Weight,
											b.Height,
											c.OrderStatusID,
											c.StatusName,
											d.PaymentType
										FROM doc_orders_current a
										INNER JOIN master_users b ON b.UserID = a.UserID
										INNER JOIN doc_order_status c ON c.OrderStatusID = a.OrderStatusID
										INNER JOIN master_payment_type d ON d.PaymentTypeID = a.PaymentTypeID
										WHERE a.DoctorID = '" . $doctor_id . "' " . $filter . " AND c.OrderStatusID IN (3,4,5,6,7,9) AND a.Active = 1
										ORDER BY a.OrderID DESC ");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get User Data From Order ID
     */
    public function getUserByOrderID($order_id)
    {

        $query_get = $this->conn->query("SELECT   
											a.OrderID,
											b.FirstName,
											b.LastName,
											b.UserID
										FROM doc_orders_current a
										INNER JOIN master_users b ON a.UserID = b.UserID 
										WHERE a.Active=1 AND a.OrderID='" . $order_id . "' ");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get doctor saldo from order
     */
    public function getSaldoFromOrder($doctor_id)
    {

        $query_get = $this->conn->query("SELECT IFNULL(SUM(TotalPrice),0) AS saldo FROM `doc_orders_current` WHERE DoctorID = '" . $doctor_id . "' AND OrderStatusID = '6' AND Active=1 ");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get Chat Data
     */
    public function getChat($order_id)
    {

        $query_get = $this->conn->query("SELECT   
											ChatID,
											Message,
											ChatDate,
											(CASE WHEN Filename IS NOT NULL THEN CONCAT('" . $this->uploaddir . "', '/chats/doctor_orders/', OrderID,'/',Filename) ELSE '' END) AS url,
											(CASE WHEN LEFT(ChatFrom, 3) = 'doc' THEN '1' ELSE '0' END) AS ChatFrom,
											(CASE WHEN LEFT(ChatFrom, 3) = 'doc' THEN 'right' ELSE 'left' END) AS Position
										FROM doc_chat
										WHERE OrderID = '" . $order_id . "'
										ORDER BY ChatID ASC");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get filename of doctor chat
     */
    public function getFileChatDoctor($chat_id)
    {
        $query = $this->conn->query("SELECT * FROM doc_chat WHERE ChatID = '" . $chat_id . "' ");

        if (mysqli_num_rows($query) > 0) {
            $row = $query->fetch_assoc();
            $filename = $row['Filename'];

            return $filename;
        } else {
            return null;
        }
    }

    /**
     * Get order ID
     */
    public function getOrderID($order_id)
    {
        $query = $this->conn->query("SELECT * FROM doc_orders_current WHERE OrderID = '" . $order_id . "' AND Active=1 LIMIT 1 ");

        if (mysqli_num_rows($query) > 0) {
            $row = $query->fetch_assoc();
            $current_id = $row['OrderID'];

            return $current_id;
        } else {
            return null;
        }
    }

    /**
     * Get doctor categories
     */
    public function getCategories()
    {
        $query_get = $this->conn->query("SELECT CategoryID, Description AS CategoryName FROM doc_categories WHERE Active = 1");
        return $query_get;
    }

    /**
     * Get dashboard orders (HOME)
     */
    public function getOrders($doctor_id)
    {
        $query = $this->conn->query("SELECT COUNT(OrderID) AS total_order FROM doc_orders_current WHERE DoctorID = '" . $doctor_id . "' AND OrderStatusID=6 AND Active=1 ");

        $row = $query->fetch_assoc();
        $orders = $row['total_order'];

        return $orders;
    }

    /**
     * Get dashboard rating (HOME)
     */
    public function getRating($doctor_id)
    {
        $query = $this->conn->query("SELECT COUNT(OrderID) AS total_order, SUM(Rating) AS total_rating FROM doc_orders_current WHERE DoctorID = '" . $doctor_id . "' AND OrderStatusID=6 AND Active=1 ");

        $row = $query->fetch_assoc();
        $order = $row['total_order'];
        $rate = $row['total_rating'];

        if ($order > 0) {
            $dataRating = round($rate / $order, 1);
            return $dataRating;
        } else {
            return 0;
        }
    }

    /**
     * Get dashboard cancelation (HOME)
     */
    public function getCancelation($doctor_id)
    {
        // get order status 4,5,6,10
        $query_total = $this->conn->query("SELECT COUNT(OrderID) AS total_order FROM doc_orders_current WHERE DoctorID = '" . $doctor_id . "' AND OrderStatusID IN (4,5,6,10) AND Active=1 ");
        $row1 = $query_total->fetch_assoc();

        $query_cancel = $this->conn->query("SELECT COUNT(a.DoctorDeclineID) AS total_cancel 
											FROM doc_orders_doctor_decline a
											INNER JOIN doc_orders_current b ON b.OrderID=a.OrderID
											WHERE b.OrderStatusID=6 AND a.DoctorID = '" . $doctor_id . "' ");
        $row2 = $query_cancel->fetch_assoc();

        $order = $row1['total_order'];
        $cancl = $row2['total_cancel'];

        if ($order > 0) {
            $res = $cancl / $order;
            $dataCancel = round((float)$res * 100);
            return $dataCancel;
        } else {
            return 0;
        }
    }

    /**
     * Get dashboard performance (HOME)
     */
    public function getPerformance($doctor_id)
    {
        // get order status 4,5,6,10
        $query_total = $this->conn->query("SELECT COUNT(OrderID) AS total_order FROM doc_orders_current WHERE DoctorID = '" . $doctor_id . "' AND OrderStatusID IN (4,5,6,10) AND Active=1 ");
        $row1 = $query_total->fetch_assoc();

        $query_cancel = $this->conn->query("SELECT COUNT(a.DoctorAcceptID) AS total_accept 
											FROM doc_orders_doctor_accept a
											INNER JOIN doc_orders_current b ON b.OrderID=a.OrderID
											WHERE b.OrderStatusID=6 AND a.DoctorID = '" . $doctor_id . "' ");
        $row2 = $query_cancel->fetch_assoc();

        $order = $row1['total_order'];
        $accept = $row2['total_accept'];

        if ($order > 0) {
            $res = $accept / $order;
            $dataAccept = round((float)$res * 100);
            return $dataAccept;
        } else {
            return 0;
        }
    }

    /**
     * Get dashboard bills (HOME)
     */
    public function getSaldo($doctor_id, $company_fee_percent)
    {
        $mode = 2;
        $company_fee_percent /= 100;
        if ($mode == 1) {
            // $query_total = $this->conn->query("SELECT IFNULL(SUM(TotalPrice),0) AS total_saldo FROM doc_orders_current WHERE DoctorID = '".$doctor_id."' AND OrderStatusID=6 AND Active=1 ");

            $query_total = $this->conn->query("SELECT IFNULL(SUM(TotalPrice) - (SUM(TotalPrice)*$company_fee_percent),0) AS total_saldo FROM doc_orders_current WHERE DoctorID = '" . $doctor_id . "' AND OrderStatusID=6 AND Active=1 ");

            $row1 = $query_total->fetch_assoc();

            $total_saldo = $row1['total_saldo'];

            if ($total_saldo > 0) {
                return $total_saldo;
            } else {
                return 0;
            }
        } else {

            $query_total = $this->conn->query("SELECT IFNULL(SUM(DoctorFeeNominal),0) AS total_saldo FROM doc_orders_current WHERE DoctorID = '" . $doctor_id . "' AND OrderStatusID=6 AND Active=1");

            $withdraw = $this->conn->query("SELECT IFNULL(SUM(GrandTotal),0) AS total_withdraw FROM doc_withdraw WHERE DoctorID = '" . $doctor_id . "' AND WithdrawStatusID != 2 AND Active=1");

            $row1 = $query_total->fetch_assoc();
            $row2 = $withdraw->fetch_assoc();
            // echo $row1['total_saldo'].' - '.$row2['total_withdraw'];
            $total_saldo = $row1['total_saldo'] - $row2['total_withdraw'];

            if ($total_saldo > 0) {
                return $total_saldo;
            } else {
                return 0;
            }
        }

    }

    /**
     * Check if Doctor exist
     */
    public function checkDoctorLogin($phone, $password)
    {
        $check = $this->conn->query("SELECT * FROM doc_doctors WHERE Phone = '" . $phone . "' AND Active=1 ");

        if (mysqli_num_rows($check) > 0) {

            $row = $check->fetch_assoc();
            $salt = $row['PasswordSalt'];
            $encrypted_password = $this->checkhashSSHA($salt, $password);

            $check_pass = $this->conn->query("SELECT * FROM doc_doctors WHERE Phone = '" . $phone . "' AND Password='" . $encrypted_password . "'");

            if (mysqli_num_rows($check_pass) > 0) {
                //insert ke table doc_doctor_login_logs
                $check_active = $this->conn->query("SELECT * FROM doc_doctors a LEFT JOIN doc_doctor_location b ON a.DoctorID = b.DoctorID WHERE a.Phone = '" . $phone . "' AND b.Active=1 ");
                if (mysqli_num_rows($check_active) > 0) {
                    $row = $check_active->fetch_assoc();
                    $doctor_id = $row['DoctorID'];
                    $get_doctor_detail_data = $this->getDoctorLoginDataById($doctor_id);
                    $DeviceBrand = $get_doctor_detail_data['DeviceBrand'];
                    $DeviceModel = $get_doctor_detail_data['DeviceModel'];
                    $DeviceSerial = $get_doctor_detail_data['DeviceSerial'];
                    $DeviceOS = $get_doctor_detail_data['DeviceOS'];

                    $insert_log = $this->conn->query("INSERT INTO doc_doctor_login_logs 
										(DoctorID,
										DeviceBrand,
										DeviceModel,
										DeviceSerial,
										DeviceOS,
										LoginDate
										) 
									VALUES 
										('" . $doctor_id . "',
										'" . $DeviceBrand . "',
										'" . $DeviceModel . "',
										'" . $DeviceSerial . "',
										'" . $DeviceOS . "',
										'" . $this->get_current_time() . "'
										) ");
                }

                //Generate new token
                $new_token = $this->generateToken();
                $upd = $this->conn->query("UPDATE doc_doctors SET IsLogin=1, Token='" . $new_token . "' WHERE Phone='" . $phone . "' ");

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
     * Check login by new generated pass (forgot pass)
     */
    public function checkDoctorLoginByForgot($phone, $password)
    {

        $now = $this->get_chat_time();

        $check_pass = $this->conn->query("SELECT * FROM doc_doctors WHERE Phone = '" . $phone . "' AND ForgotPassword='" . $password . "' AND ForgotPasswordExpired > STR_TO_DATE('" . $now . "', '%Y-%m-%d %H:%i:%s') AND Active=1 ");

        if (mysqli_num_rows($check_pass) > 0) {

            //insert ke table doc_doctor_login_logs
            $check_active = $this->conn->query("SELECT * FROM doc_doctors a LEFT JOIN doc_doctor_location b ON a.DoctorID = b.DoctorID WHERE a.Phone = '" . $phone . "' AND b.Active=1 ");
            if (mysqli_num_rows($check_active) > 0) {
                $row = $check_active->fetch_assoc();
                $doctor_id = $row['DoctorID'];
                $get_doctor_detail_data = $this->getDoctorLoginDataById($doctor_id);
                $DeviceBrand = $get_doctor_detail_data['DeviceBrand'];
                $DeviceModel = $get_doctor_detail_data['DeviceModel'];
                $DeviceSerial = $get_doctor_detail_data['DeviceSerial'];
                $DeviceOS = $get_doctor_detail_data['DeviceOS'];

                $insert_log = $this->conn->query("INSERT INTO doc_doctor_login_logs 
										(DoctorID,
										DeviceBrand,
										DeviceModel,
										DeviceSerial,
										DeviceOS,
										Action,
										LoginDate
										) 
									VALUES 
										('" . $doctor_id . "',
										'" . $DeviceBrand . "',
										'" . $DeviceModel . "',
										'" . $DeviceSerial . "',
										'" . $DeviceOS . "',
										'Login',
										'" . $this->get_current_time() . "'
										) ");
            }

            $new_token = $this->generateToken();
            $upd = $this->conn->query("UPDATE doc_doctors SET IsLogin=1, Token='" . $new_token . "' WHERE Phone='" . $phone . "' AND Active=1 ");
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
     * Check if token exist
     */
    public function checkToken($token, $doctor_id)
    {
        $query = $this->conn->query("SELECT * FROM doc_doctors WHERE Token = '" . $token . "' AND DoctorID = '" . $doctor_id . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if Doctor exist
     */
    public function checkDoctorRegister($phone)
    {
        $query = $this->conn->query("SELECT * FROM doc_doctors WHERE Phone = '" . $phone . "' AND Active=1 AND Verified != 2");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check Doctor by ID
     */
    public function checkDoctorById($id)
    {
        $query = $this->conn->query("SELECT * FROM doc_doctors WHERE DoctorID = '" . $id . "' AND Active=1 AND Verified =1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if doctor verified
     */
    public function checkDoctorVerified($doctor_id)
    {
        $query = $this->conn->query("SELECT * FROM doc_doctors WHERE DoctorID = '" . $doctor_id . "' AND Active=1 AND Verified=1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check Doctor education by ID
     */
    public function checkDoctorEducationById($edu_id)
    {
        $query = $this->conn->query("SELECT * FROM doc_doctor_educations WHERE DoctorEducationID = '" . $edu_id . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check Doctor experience by ID
     */
    public function checkDoctorExperienceById($exp_id)
    {
        $query = $this->conn->query("SELECT * FROM doc_doctor_experiences WHERE DoctorExperienceID = '" . $exp_id . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if Doctor location tracking if exist
     */
    public function checkDoctorLocationExist($doctor_id)
    {
        $query = $this->conn->query("SELECT * FROM doc_doctor_location WHERE DoctorID = '" . $doctor_id . "' ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if order exist
     */
    public function checkOrderExist($order_id)
    {
        $query = $this->conn->query("SELECT 
    									a.*, 
    									b.CategoryName, 
    									c.PaymentType
    									FROM doc_orders_current a
    									LEFT JOIN doc_categories b ON b.CategoryID = a.CategoryID
    									LEFT JOIN master_payment_type c ON c.PaymentTypeID = a.PaymentTypeID
    									WHERE OrderID = '" . $order_id . "' AND a.Active=1 ");

        if (mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return false;
        }
    }

    /**
     * Check if Doctor was accept offer
     */
    public function checkDoctorAcceptOffer($order_id, $doctor_id)
    {
        $query = $this->conn->query("SELECT * FROM doc_orders_doctor_accept WHERE DoctorID = '" . $doctor_id . "' AND OrderID = '" . $order_id . "' ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if Doctor was decline offer
     */
    public function checkDoctorDeclineOffer($order_id, $doctor_id)
    {
        $query = $this->conn->query("SELECT * FROM doc_orders_doctor_decline WHERE DoctorID = '" . $doctor_id . "' AND OrderID = '" . $order_id . "' ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check Activation Code
     */
    public function checkActivationCode($phone, $code)
    {
        $query = $this->conn->query("SELECT * FROM doc_doctors WHERE Phone = '" . $phone . "' AND ActivationCode='" . $code . "' ");

        if (mysqli_num_rows($query) > 0) {
            $referal_id = $this->generateToken(8);
            $this->conn->query("UPDATE doc_doctors SET Active = 1, IsLogin=1, ReferralID='" . $referal_id . "' WHERE Phone = '" . $phone . "' AND ActivationCode='" . $code . "' ");
            return true;
        } else {
            return false;
        }
    }

    /**
     * Resend Activation Code
     */
    public function resendActivationCode($phone)
    {
        // echo 'test2';
        $query_sms = $this->conn->query("SELECT FirstName, Phone, ActivationCode, Active FROM doc_doctors WHERE Phone = '" . $phone . "' ");
        if (mysqli_num_rows($query_sms) > 0) {
            $row_sms = $query_sms->fetch_assoc();

            $name = $row_sms['FirstName'];
            $code = $row_sms['ActivationCode'];
            $is_active = $row_sms['Active'];

            if ($is_active == "0") {
                $this->send_sms($phone, $code, $name);

                return true;
            } else {
                return false;
            }

        } else {
            return false;
        }

    }

    /**
     * Create New Doctor Education
     */
    public function createEducation($doctor_id, $university, $year, $degree)
    {

        $check = $this->checkDoctorById($doctor_id);
        if ($check) {
            $insert = $this->conn->query("INSERT INTO doc_doctor_educations 
											(DoctorID,
											University,
											Degree,											
											GraduationYear,
											CreatedDate
											) 
										VALUES 
											('" . $doctor_id . "',
											'" . $university . "', 
											'" . $degree . "', 
											'" . $year . "',
											'" . $this->get_current_time() . "'
											) ");

            if ($insert) {
                return $this->conn->insert_id;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Create New Doctor Experience
     */
    public function createExperience($doctor_id, $institute, $entry_date, $out_date, $job_desk)
    {

        $check = $this->checkDoctorById($doctor_id);
        if ($check) {
            $insert = $this->conn->query("INSERT INTO doc_doctor_experiences
											(DoctorID,
											InstituteName,
											EntryDate,											
											OutDate,
											JobDesk,
											CreatedDate
											) 
										VALUES 
											('" . $doctor_id . "',
											'" . $institute . "', 
											'" . $entry_date . "', 
											'" . $out_date . "',
											'" . $job_desk . "',
											'" . $this->get_current_time() . "'
											) ");

            if ($insert) {
                return $this->conn->insert_id;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Update Doctor Education
     */
    public function updateEducation($edu_id, $doctor_id, $university, $year, $degree)
    {

        $check = $this->checkDoctorEducationById($edu_id);
        if ($check) {
            $update = $this->conn->query("UPDATE doc_doctor_educations SET 
											University 		= '" . $university . "',
											GraduationYear 	= '" . $year . "',
											Degree 			= '" . $degree . "',
											ModifiedDate	= '" . $this->get_current_time() . "'
										WHERE 
											DoctorEducationID = '" . $edu_id . "'");

            if ($update) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Update Doctor Experience
     */
    public function updateExperience($exp_id, $doctor_id, $institute, $entry_date, $out_date, $job_desk)
    {

        $check = $this->checkDoctorExperienceById($exp_id);
        if ($check) {
            $update = $this->conn->query("UPDATE doc_doctor_experiences SET 
											InstituteName 	= '" . $institute . "',
											EntryDate	 	= '" . $entry_date . "',
											OutDate			= '" . $out_date . "',
											JobDesk			= '" . $job_desk . "',
											ModifiedDate	= '" . $this->get_current_time() . "'
										WHERE 
											DoctorExperienceID = '" . $exp_id . "'");

            if ($update) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Delete Doctor Education
     */
    public function deleteEducation($edu_id)
    {

        $check = $this->checkDoctorEducationById($edu_id);
        if ($check) {
            $update = $this->conn->query("UPDATE doc_doctor_educations SET 
											Active 	= 0,
											ModifiedDate = '" . $this->get_current_time() . "'
										WHERE 
											DoctorEducationID = '" . $edu_id . "'");

            if ($update) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Delete Doctor Education
     */
    public function deleteExperience($exp_id)
    {

        $check = $this->checkDoctorExperienceById($exp_id);
        if ($check) {
            $update = $this->conn->query("UPDATE doc_doctor_experiences SET 
											Active 	= 0,
											ModifiedDate = '" . $this->get_current_time() . "'
										WHERE 
											DoctorExperienceID = '" . $exp_id . "'");

            if ($update) {
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
    public function updateFirebase($phone, $firebase_id)
    {

        $update = $this->conn->query("UPDATE doc_doctors SET 
										FirebaseID 		= '" . $firebase_id . "'
									WHERE 
										Phone = '" . $phone . "' AND Active=1");

        if ($update) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update Doctor Profile
     */
    public function updateProfile($doctor_id, $firstname, $lastname, $phone, $email, $birthdate, $gender, $no_ktp, $no_str, $location, $price, $category_id, $npa)
    {

        $check = $this->checkDoctorById($doctor_id);
        if ($check) {
            $update = $this->conn->query("UPDATE doc_doctors SET 
											FirstName 	= '" . $firstname . "',
											LastName 	= '" . $lastname . "',
											Phone 		= '" . $phone . "',
											Email 		= '" . $email . "',
											BirthDate 	= '" . $birthdate . "',
											Gender 		= '" . $gender . "',
											No_KTP 		= '" . $no_ktp . "',
											No_STR 		= '" . $no_str . "',
											Location 	= '" . $location . "',
											Price	 	= '" . $price . "',
											CategoryID	= '" . $category_id . "',
											ModifiedBy	= '" . $doctor_id . "',
											ModifiedDate= '" . $this->get_current_time() . "',
											NPA 		= '" . $npa . "'
										WHERE 
											DoctorID = '" . $doctor_id . "'");

            if ($update) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Process Doctor Location Tracking
     */
    public function trackLocation($doctor_id, $latitude, $longitude, $accuracy)
    {

        $exist = $this->checkDoctorLocationExist($doctor_id);
        if ($exist) {
            //Update Location
            $update = $this->conn->query("UPDATE doc_doctor_location SET 
											Latitude 	= '" . $latitude . "',
											Longitude 	= '" . $longitude . "',
											Accuracy 	= '" . $accuracy . "',
											TrackDate	= '" . $this->get_current_time() . "'
										WHERE 
											DoctorID = '" . $doctor_id . "' ");

            if ($update) {
                return true;
            } else {
                return false;
            }
        } else {
            //Create Location
            $insert = $this->conn->query("INSERT INTO doc_doctor_location 
										(DoctorID,
										Latitude,
										Longitude,
										Accuracy,
										TrackDate
										) 
									VALUES 
										('" . $doctor_id . "',
										'" . $latitude . "',
										'" . $longitude . "',
										'" . $accuracy . "',
										'" . $this->get_current_time() . "'
										) ");

            if ($insert) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Forgot Password
     */
    public function forgotPassword($phone)
    {

        $new_pass = $this->randomPassword(8);
        $expired_date = date('Y-m-d h:i:s', strtotime('+1 days'));

        $update = $this->conn->query("UPDATE doc_doctors SET 
										ForgotPassword	 		= '" . $new_pass . "',
										ForgotPasswordExpired 	= '" . $expired_date . "'
									WHERE 
										Phone = '" . $phone . "' AND Active=1 ");

        if ($update) {
            $this->send_sms_password($phone, $new_pass);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Process Doctor Online/Offline
     */
    public function updateStatus($doctor_id, $status)
    {
        $get_doctor_detail_data = $this->getDoctorLoginDataById($doctor_id);
        $DeviceBrand = $get_doctor_detail_data['DeviceBrand'];
        $DeviceModel = $get_doctor_detail_data['DeviceModel'];
        $DeviceSerial = $get_doctor_detail_data['DeviceSerial'];
        $DeviceOS = $get_doctor_detail_data['DeviceOS'];

        if ($status == '1') {
            //Insert new data
            $insert_log = $this->conn->query("INSERT INTO doc_doctor_login_logs 
										(DoctorID,
										DeviceBrand,
										DeviceModel,
										DeviceSerial,
										DeviceOS,
										LoginDate
										) 
									VALUES 
										('" . $doctor_id . "',
										'" . $DeviceBrand . "',
										'" . $DeviceModel . "',
										'" . $DeviceSerial . "',
										'" . $DeviceOS . "',
										'" . $this->get_current_time() . "'
										) ");
        } else {
            //Update with insert LogoutDate
            $q_max_id = $this->conn->query("SELECT
                                        LoginLogID
                                    FROM
                                        doc_doctor_login_logs 
                                    WHERE
                                        DoctorID = '" . $doctor_id . "'
                                        ORDER BY LoginLogID DESC LIMIT 0, 1");
            if (mysqli_num_rows($q_max_id) > 0) {
                $max_id = $q_max_id->fetch_assoc();
                $LoginLogID = $max_id['LoginLogID'];
                $update_log = $this->conn->query("UPDATE doc_doctor_login_logs SET 
											DeviceBrand 		= '" . $DeviceBrand . "',
											DeviceModel 		= '" . $DeviceModel . "',
											DeviceSerial 		= '" . $DeviceSerial . "',
											DeviceOS 		= '" . $DeviceOS . "',
											LogoutDate	= '" . $this->get_current_time() . "'
										WHERE 
											LoginLogID = '" . $LoginLogID . "' ");
            }
        }


        $exist = $this->checkDoctorLocationExist($doctor_id);
        if ($exist) {
            //Update Location
            $update = $this->conn->query("UPDATE doc_doctor_location SET 
											Active 		= '" . $status . "',
											TrackDate	= '" . $this->get_current_time() . "'
										WHERE 
											DoctorID = '" . $doctor_id . "' ");

            if ($update) {
                return true;
            } else {
                return false;
            }
        } else {
            //Create Location
            $insert = $this->conn->query("INSERT INTO doc_doctor_location 
										(DoctorID,
										Latitude,
										Longitude,
										Accuracy,
										Active,
										TrackDate
										) 
									VALUES 
										('" . $doctor_id . "',
										0,
										0,
										0,
										'" . $status . "',
										'" . $this->get_current_time() . "'
										) ");

            if ($insert) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Create Doctor Accept Offer
     */
    public function acceptOffer($order_id, $doctor_id)
    {

        $exist = $this->checkDoctorAcceptOffer($order_id, $doctor_id);
        if (!$exist) {
            $insert = $this->conn->query("INSERT INTO doc_orders_doctor_accept 
											(OrderID, 
											DoctorID,
											AcceptDate) 
										VALUES 
											('" . $order_id . "', 
											'" . $doctor_id . "',
											'" . $this->get_current_time() . "'
											) ");

            if ($insert) {
                $this->sendNotif_DoctorAccept($order_id, $doctor_id);
                return true;
            } else {
                return false;
            }
        } else {
            $this->sendNotif_DoctorAccept($order_id, $doctor_id);
            return true;
        }
    }

    /**
     * Create Doctor Decline Offer
     */
    public function declineOffer($order_id, $doctor_id)
    {

        $exist = $this->checkDoctorDeclineOffer($order_id, $doctor_id);
        if (!$exist) {
            $insert = $this->conn->query("INSERT INTO doc_orders_doctor_decline 
											(OrderID, 
											DoctorID,
											DeclineDate) 
										VALUES 
											('" . $order_id . "', 
											'" . $doctor_id . "',
											'" . $this->get_current_time() . "'
											) ");

            if ($insert) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }


    public function sendNotif_DoctorAccept($order_id, $doctor_id)
    {

        //Get Data
        $query = $this->conn->query("SELECT a.DoctorID,
											a.FirstName, 
											a.LastName, 
											a.BirthDate, 
											a.YearExperience,
											a.Price
									FROM doc_doctors a
									WHERE 
										a.DoctorID = '" . $doctor_id . "' ");

        if (mysqli_num_rows($query) > 0) {

            $row = $query->fetch_assoc();
            $rating = $this->getRating($doctor_id);
            $custom_data = array(
                'type' => '31',
                'body' => "Dokter telah menerima order kamu",
                'title' => "Order Anda",
                'OrderID' => $order_id,
                'DoctorID' => $row['DoctorID'],
                'FirstName' => $row['FirstName'],
                'LastName' => $row['LastName'],
                'YearExperience' => $row['YearExperience'],
                'BirthDate' => $row['BirthDate'],
                'Price' => $row['Price'],
                'Rating' => $rating
            );

            //Notify Patient
            $query_nrz = $this->conn->query("SELECT a.OrderID, b.FirebaseID FROM doc_orders_current a INNER JOIN master_users b ON b.UserID=a.UserID WHERE a.OrderID = '" . $order_id . "' ");
            if (mysqli_num_rows($query_nrz) > 0) {
                $row_nrz = $query_nrz->fetch_assoc();

                $this->sendNotification_Patient($row_nrz['FirebaseID'], $custom_data);
            }

        }

    }

    /**
     * Create Chat Message
     */
    public function createChat($order_id, $message, $from, $to)
    {

        $insert = $this->conn->query("INSERT INTO doc_chat 
									(OrderID, 
									Message, 
									ChatFrom,
									ChatTo,
									ChatDate
									) 
								VALUES 
									('" . $order_id . "', 
									'" . $message . "',
									'" . $from . "',
									'" . $to . "',
									'" . $this->get_current_time() . "'
									) ");

        if ($insert) {
            $this->chatNotification($order_id, $message, $to);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Create Chat Image Message to Doctor
     */
    public function createChatFile($order_id, $message, $from, $to, $filename)
    {

        $insert = $this->conn->query("INSERT INTO doc_chat 
									(OrderID, 
									Message, 
									ChatFrom,
									ChatTo,
									ChatDate,
									IsFile,
									Filename
									) 
								VALUES 
									('" . $order_id . "', 
									'" . $message . "',
									'" . $from . "',
									'" . $to . "',
									'" . $this->get_current_time() . "',
									'1',
									'" . $filename . "'
									) ");

        if ($insert) {

            $url = "";
            $current_id = $this->conn->insert_id;

            $query_get = $this->conn->query("SELECT   
											ChatID,
											(CASE WHEN Filename IS NOT NULL THEN CONCAT('" . $this->uploaddir . "', '/chats/doctor_orders/', OrderID,'/',Filename) ELSE '' END) AS url
										FROM doc_chat
										WHERE ChatID = '" . $current_id . "'");

            if (mysqli_num_rows($query_get) > 0) {
                $row = $query_get->fetch_assoc();
                $url = $row['url'];
            }

            $this->chatNotification($order_id, $message, $to, $url);
            return $current_id;
        } else {
            return null;
        }
    }

    public function chatNotification($order_id, $message, $to, $url = '')
    {

        $custom_data = array(
            'type' => '32', //chat doctor
            'body' => $message,
            'title' => "Pesan Baru",
            'ChatDate' => $this->get_chat_time(),
            'ChatFrom' => '0',
            'Message' => $message,
            'OrderID' => $order_id,
            'url' => $url
        );

        $type = $this->str_before($to, ':');
        $send_to = $this->str_after($to, ':');

        if ($type == "doc") {
            //Notify Doctor
            $query_nrz = $this->conn->query("SELECT * FROM doc_doctors WHERE DoctorID='" . $send_to . "' AND Active=1 ");
            if (mysqli_num_rows($query_nrz) > 0) {
                $row_nrz = $query_nrz->fetch_assoc();

                $this->sendNotification_Doctor($row_nrz['FirebaseID'], $custom_data);
            }
        } else if ($type == "usr") {
            //Notify User
            $query_nrz = $this->conn->query("SELECT * FROM master_users WHERE UserID='" . $send_to . "' AND Active=1 ");
            if (mysqli_num_rows($query_nrz) > 0) {
                $row_nrz = $query_nrz->fetch_assoc();

                $this->sendNotification_Patient($row_nrz['FirebaseID'], $custom_data);
            }
        }
    }

    /**
     * Process Status On Finished
     */
    public function processOnFinish($order_id)
    {

        $update = $this->conn->query("UPDATE doc_orders_current SET 
										OrderStatusID = '6'
									WHERE 
										OrderID = '" . $order_id . "'");

        if ($update) {
            // create order log
            $dt = $this->getDoctorByOrderID($order_id);
            if ($dt != null) {
                $dt = $dt->fetch_assoc();

                //create order log
                $order_id = $order_id;
                $order_status_id = 6;
                $description = 'Log Order Doctor, created by sistem api';
                $doctor_id = $dt['DoctorID'];
                $this->createOrderLogDoctor($order_id, $order_status_id, $doctor_id, $description);
            }

            $this->historyNotification($order_id);
            return true;
        } else {
            return false;
        }
    }

    public function historyNotification($order_id)
    {

        $query = $this->conn->query("SELECT * FROM doc_orders_current WHERE OrderID = '" . $order_id . "' AND Active=1 LIMIT 1 ");

        if (mysqli_num_rows($query) > 0) {
            $row = $query->fetch_assoc();
            $user_id = $row['UserID'];
            $status_id = $row['OrderStatusID'];

            $message = "";
            if ($status_id == "6") {
                $message = "Telah selesai";
            }

            $custom_data = array(
                'type' => '3', //History
                'body' => $message,
                'title' => "Status Konsultasi Anda",
                'OrderID' => $order_id
            );

            //Notify User
            $query_nrz = $this->conn->query("SELECT * FROM master_users WHERE UserID='" . $user_id . "' AND Active=1 ");
            if (mysqli_num_rows($query_nrz) > 0) {
                $row_nrz = $query_nrz->fetch_assoc();

                $this->sendNotification_Patient($row_nrz['FirebaseID'], $custom_data);
            }
        }
    }

    /**
     * Get Doctor data by phone (only for login callback)
     */
    public function getDoctorLoginDataById($doctor_id)
    {


        $query_get = $this->conn->query("SELECT a.*, b.Active as ActiveLocation
												FROM doc_doctors a 
												LEFT JOIN doc_doctor_location b ON b.DoctorID = a.DoctorID
												WHERE a.DoctorID = '" . $doctor_id . "' AND a.Active=1");
        if ($query_get) {
            if (mysqli_num_rows($query_get) > 0) {
                $row_doc = $query_get->fetch_assoc();
                return $row_doc;
            }
        } else {
            return null;
        }
    }

    /**
     * Process Logout
     */
    public function processLogout($doctor_id)
    {
        $get_doctor_detail_data = $this->getDoctorLoginDataById($doctor_id);
        $DeviceBrand = $get_doctor_detail_data['DeviceBrand'];
        $DeviceModel = $get_doctor_detail_data['DeviceModel'];
        $DeviceSerial = $get_doctor_detail_data['DeviceSerial'];
        $DeviceOS = $get_doctor_detail_data['DeviceOS'];
        $active = $get_doctor_detail_data['ActiveLocation'];

        //Update with insert LogoutDate
        if($active == '1'){
            $q_max_id = $this->conn->query("SELECT
                                        LoginLogID
                                    FROM
                                        doc_doctor_login_logs 
                                    WHERE
                                        DoctorID = '" . $doctor_id . "'
                                        ORDER BY LoginLogID DESC LIMIT 0, 1");
            if (mysqli_num_rows($q_max_id) > 0) {
                $max_id = $q_max_id->fetch_assoc();
                $LoginLogID = $max_id['LoginLogID'];
                $update_log = $this->conn->query("UPDATE doc_doctor_login_logs SET 
											DeviceBrand 		= '" . $DeviceBrand . "',
											DeviceModel 		= '" . $DeviceModel . "',
											DeviceSerial 		= '" . $DeviceSerial . "',
											DeviceOS 		= '" . $DeviceOS . "',
											LogoutDate	= '" . $this->get_current_time() . "'
										WHERE 
											LoginLogID = '" . $LoginLogID . "' ");
            }
        }


        $update = $this->conn->query("UPDATE doc_doctors SET 
										IsLogin = '0',
										Token = '',
										FirebaseID = ''
									WHERE 
										DoctorID = '" . $doctor_id . "'");

        if ($update) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Process Withdraw
     */
    public function createWithdraw($doctor_id, $total, $bankFee, $grandTotal)
    {

        $q = $this->conn->query("SELECT * FROM doc_withdraw WHERE DoctorID ='" . $doctor_id . "' AND WithdrawStatusID = 0 AND Active = 1");

        if (mysqli_num_rows($q) > 0) {
            return 2;
        } else {
            /*get transaction no*/
            $check_trid = $this->conn->query("SELECT IFNULL(MAX(Right(WithdrawNo,8)),0) AS WithdrawNo
											FROM doc_withdraw 
											WHERE 
												DATE_FORMAT(CreatedDate, '%m')='" . date('m') . "' AND 
												DATE_FORMAT(CreatedDate, '%Y')='" . date('Y') . "'");
            $check_trid = $check_trid->fetch_assoc();

            $tr_no = $this->incrementTrNo('DOCWD', $check_trid['WithdrawNo']);
            /*end get transaction no*/

            $insert = $this->conn->query("INSERT INTO doc_withdraw 
										(DoctorID, 
										Total, 
										CreatedDate,
										BankFee,
										GrandTotal,
										WithdrawNo
										) 
									VALUES 
										('" . $doctor_id . "', 
										'" . $total . "',
										'" . $this->get_current_time() . "',
										'" . $bankFee . "',
										'" . $grandTotal . "',
										'" . $tr_no . "'
										) ");

            if ($insert) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    /*created by elim*/
    /**
     * Function get bank
     * @param :
     * returns data
     */
    function getBank()
    {

        $query = $this->conn->query("SELECT BankID, BankName, Image FROM master_bank WHERE Active = 1");

        if (mysqli_num_rows($query) > 0) {

            return $query;
        } else {
            return false;
        }
    }

    /**
     * Function update doctor bank account
     * @param : $bankAccName, $bankAccNo, $bankBranch, $bankAccountId,$doctorId
     * returns data
     */
    function updateDoctorBankAccount($bankAccName, $bankAccNo, $bankBranch, $bankAccountId, $doctorId)
    {
        // echo $bankAccName.'-'.$bankAccNo.'-'.$bankBranch.'-'.$doctorId;
        $upd = $this->conn->query("UPDATE doc_doctors SET 
							BankAccName= '" . $bankAccName . "', 
							BankAccNo='" . $bankAccNo . "', 
							BankBranch='" . $bankBranch . "',
							BankID='" . $bankAccountId . "'
							WHERE DoctorID='" . $doctorId . "'");
        if ($upd) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Function get doctor by doctor id
     * @param : DocotrID
     * returns data
     */
    function getDoctorById($id)
    {
        $q = $this->conn->query("SELECT * FROM doc_doctors WHERE DoctorID = '" . $id . "' LIMIT 1");

        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }

    /**
     * Function get payment account bank
     * @param :
     * returns data
     */
    function getPaymentAccountBank()
    {
        // $q = $this->conn->query("SELECT * FROM master_payment_account WHERE	Active = '1'");

        $q = $this->conn->query("SELECT a.BankID, a.BankName, a.Image AS image, b.BankID AS mpa_bank_id FROM master_bank a 
										LEFT JOIN master_payment_account b ON b.BankID = a.BankID
										WHERE (b.Active = 1 OR b.Active IS NULL) AND a.Active = 1");

        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }

    /**
     * Function get data doctor withdraw, data master_withdraw_status, data doc_doctors, data master_bank
     * @param : $doctorId
     * returns data
     */
    function getDocWithdraw($doctorId)
    {
        // $q = $this->conn->query("SELECT * FROM master_payment_account WHERE	Active = '1'");

        $q = $this->conn->query("SELECT a.GrandTotal, a.Total, a.BankFee, a.ModifiedDate, a.CreatedDate AS doc_withdraw_created_at, a.WithdrawStatusID, b.WithdrawStatus, c.BankAccNo, c.BankAccName, c.BankBranch, d.BankName 
			FROM doc_withdraw a 
			JOIN master_withdraw_status b ON a.WithdrawStatusID = b.WithdrawStatusID
			JOIN doc_doctors c ON a.DoctorID = c.DoctorID
			JOIN master_bank d ON d.BankID = c.BankID
			WHERE a.DoctorID = $doctorId
			ORDER BY a.WithdrawID DESC 
			");
        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }

    /**
     * Function get data master_bank, data master_payment_account, data doc_doctor
     * @param : $doctorId
     * returns data
     */
    function getTrsFeeStatusByDoctorBankId($doctorId)
    {
        $q = $this->conn->query("SELECT a.BankID, a.BankName, a.Image, 
										b.BankID AS mpa_bank_id, 
										c.DoctorID, c.BankID AS doctor_bank_id, c.BankAccNo AS doctor_bank_acc_no, c.BankAccName AS doctor_bank_acc_name 
										FROM master_bank a 
			JOIN master_payment_account b ON b.BankID = a.BankID
			JOIN doc_doctors c ON c.BankID =  b.BankID
			WHERE (b.Active = 1 OR b.Active IS NULL) AND a.Active = 1 AND c.DoctorID = $doctorId");

        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }

    /**
     * Function get data master_config
     * @param : $configName
     * returns data
     */
    function getConfig($configName)
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
     * Function get data master_config
     * @param : $configName
     * returns data
     */
    function getConfigForceUpdate($configName)
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
     * Update FirebaseID by DoctorID
     */
    public function updateFirebase2($doctor_id, $firebase_id)
    {

        $update = $this->conn->query("UPDATE doc_doctors SET 
										FirebaseID 		= '" . $firebase_id . "'
									WHERE 
										DoctorID = '" . $doctor_id . "' AND Active=1");
        if ($update) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get articles
     */
    public function getArticles($num = null)
    {
        $limit = "";
        if ($num != null) {
            $limit = " LIMIT " . $num;
        }
        $query_get = $this->conn->query("SELECT PublishedDate, ArticleID, Title, Caption, CreatedDate, CONCAT('" . $this->uploaddir . "', '/articles/', ArticleID,'.jpg') AS Url FROM master_articles WHERE Active = 1 AND TypeID = 1 ORDER BY PublishedDate DESC  " . $limit);

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return false;
        }

    }

    /**
     * Get articles detail by id
     * @param: id
     *return array, false
     */
    public function getArticleDetail($id)
    {
        $query_get = $this->conn->query("SELECT ArticleID, Title, Caption, Content AS Description, CreatedDate, CONCAT( '" . $this->uploaddir . "', '/articles/', ArticleID,'.jpg') AS Url, Source FROM master_articles WHERE Active = 1 AND ArticleID = " . $id);
        return $query_get;
    }
    /*end created by elim*/

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
        // echo $firebase_id.'<br>';
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
        //echo $result;exit;
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

    /**
     * Get doctor payment with transfer base on order_id
     */
    public function getPaymentTransfer($order_id)
    {
        $q = $this->conn->query("SELECT 
    								*
    								FROM doc_payment_transfers
    								WHERE OrderID = '" . $order_id . "' 
    								AND Status = 1 ");
        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }

    /*Increment Transaction No*/
    public function incrementTrNo($prefix, $tr_no)
    {
        if ($tr_no == 0) {
            //Start From First
            $num = date('y') . date('m') . "1001";
            $res = $prefix . $num;
        } else {
            //Continue Number +1
            $num = $tr_no + 1;
            $res = $prefix . $num;
        }

        return $res;
    }

    public function createOrderLogDoctor($order_id, $order_status_id, $doctor_id, $description)
    {
        $q = $this->conn->query("INSERT INTO doc_orders_logs 
									(OrderID,
									CreatedDate,
									CreatedBy,
									OrderStatusID,
									DoctorID,
									Description
									) 
								VALUES 
									('" . $order_id . "',
									'" . $this->get_current_time() . "',
									'9-',
									'" . $order_status_id . "',
									'" . $doctor_id . "',
									'" . $description . "'
									) ");
        if ($q) {
            return $this->conn->insert_id;
        } else {
            return false;
        }
    }

    /**
     * Get Doctor Data From Order ID
     */
    public function getDoctorByOrderID($order_id)
    {

        $query_get = $this->conn->query("SELECT   
											a.OrderID,
											b.FirstName,
											b.LastName,
											b.DoctorID
										FROM doc_orders_current a
										INNER JOIN doc_doctors b ON a.DoctorID = b.DoctorID 
										WHERE a.Active=1 AND a.OrderID='" . $order_id . "' ");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Function update point doctor
     * @param : $user_id, $user_total_point, $order_id, $amount
     * returns boolean
     */
    public function updateDoctorPoint($doctor_id, $total_point, $order_id, $amount)
    {
        $q = $this->conn->query("UPDATE doc_doctors SET
    							Point = " . $total_point . ",
								PointModifiedDate = '" . $this->get_current_time() . "'
								WHERE 
								DoctorID=" . $doctor_id . "");
        if ($q) {
            $description = 'created by system api';
            $q2 = $this->insertDoctorPointLog($order_id, $doctor_id, $amount, $description);

            if ($q2) {
                return $q2;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Function insert point user
     * @param : $order_id, $user_id, $amount, $description, $created_by = '9-', $modified_by = '9-'
     * returns boolean
     */
    public function insertDoctorPointLog($order_id, $doctor_id, $amount, $description, $created_by = '9-')
    {

        $q2 = $this->conn->query("INSERT INTO doc_point_log
								(
									OrderID,
    								DoctorID,
    								Amount,
    								Description,
    								CreatedDate,
    								CreatedBy
								)VALUES(
									" . $order_id . ",
									" . $doctor_id . ",
									" . $amount . ",
									'" . $description . "',
									'" . $this->get_current_time() . "',
									'" . $created_by . "'
								)");
        if ($q2) {
            return $this->conn->insert_id;
        } else {
            return false;
        }
    }

    /**
     * Function update point user
     * @param : $user_id, $user_total_point, $order_id, $amount
     * returns boolean
     */
    public function updateUserPoint($user_id, $user_total_point, $order_id, $amount)
    {
        $q = $this->conn->query("UPDATE master_users SET
    							Point = " . $user_total_point . ",
								PointModifiedDate = '" . $this->get_current_time() . "'
								WHERE 
								UserID=" . $user_id . "");

        if ($q) {
            $description = 'created by system api';
            $q2 = $this->insertUserPointLog($order_id, $user_id, $amount, $description);

            if ($q2) {
                return $q2;
            } else {
                return false;
            }
        }
    }

    /**
     * Function insert point doctore
     * @param : $order_id, $user_id, $amount, $description, $created_by = '9-', $modified_by = '9-'
     * returns boolean
     */
    public function insertUserPointLog($order_id, $user_id, $amount, $description, $created_by = '9-')
    {

        $q2 = $this->conn->query("INSERT INTO user_point_log
								(
									OrderID,
    								UserID,
    								Amount,
    								Description,
    								CreatedDate,
    								CreatedBy
								)VALUES(
									" . $order_id . ",
									" . $user_id . ",
									" . $amount . ",
									'" . $description . "',
									'" . $this->get_current_time() . "',
									'" . $created_by . "'								
								)");

        if ($q2) {
            return $this->conn->insert_id;
        } else {
            return false;
        }
    }

    /**
     * Function doctor point skp
     * @param : $doctor_id
     * returns array
     */
    public function updatePointSKP($doctor_id)
    {
        $last_log = $this->conn->query("SELECT   
											*
										FROM doc_point_skp_log
										WHERE DoctorID = " . $doctor_id . "
										ORDER BY PointSKPLogID DESC
										LIMIT 1");
        $last_id = 0;
        if (mysqli_num_rows($last_log) > 0) {
            $last_log = $last_log->fetch_assoc();
            // var_dump($last_log);
            $last_id = $last_log["LastOrderID"];
        }

        $q = $this->conn->query("SELECT 
									a.*,
									b.PointSKP
									FROM doc_orders_current a
									LEFT JOIN doc_doctors b ON a.DoctorID = b.DoctorID
									WHERE a.OrderID > " . $last_id . "
									AND a.DoctorID = " . $doctor_id . "
									AND a.OrderStatusID = 6
									ORDER BY a.OrderID DESC
									LIMIT 1
									");
        $count = mysqli_num_rows($q);
        if ($count > 0) {
            $q = $q->fetch_assoc();
            $amount_per_skp = $this->getConfig("count_transaction_per_skp_point_doctor")->fetch_assoc();
            $amount_per_skp = $amount_per_skp['Value'];
            // $amount_per_skp = 1;
            // echo $amount_per_skp;
            $point_skp = (int)($count / $amount_per_skp);
            if ($point_skp > 0) {
                $total_point_skp = $q['PointSKP'] + $point_skp;

                $q2 = $this->conn->query("UPDATE doc_doctors SET 
								PointSKP = " . $total_point_skp . ",
								PointSKPModifiedDate = '" . $this->get_current_time() . "'
								WHERE DoctorID = " . $q['DoctorID'] . "");
                if ($q2) {
                    $last_order_id = $q['OrderID'];
                    $amount_order = $total_point_skp;
                    $description = 'Created by system api';
                    $q3 = $this->insertPointSKPLog($last_order_id, $amount_order, $doctor_id, $point_skp, $description);

                    if ($q3) {
                        return $q3;
                    } else {
                        return false;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Function insert point skp log doctor
     * @param : $last_order_id, $amount_order, $doctor_id, $amount, $description, $created_by
     * returns boolean
     */
    public function insertPointSKPLog($last_order_id, $amount_order, $doctor_id, $amount, $description, $created_by = '9-')
    {

        $q2 = $this->conn->query("INSERT INTO doc_point_skp_log
								(
									LastOrderID,
									AmountOrder,
    								DoctorID,
    								Amount,
    								Description,
    								CreatedDate,
    								CreatedBy
								)VALUES(
									" . $last_order_id . ",
									" . $amount_order . ",
									" . $doctor_id . ",
									" . $amount . ",
									'" . $description . "',
									'" . $this->get_current_time() . "',
									'" . $created_by . "'								
								)");
        if ($q2) {
            return $this->conn->insert_id;
        } else {
            return false;
        }
    }

    /**
     * Check doctor password
     */
    public function checkUserPassword($doctor_id, $password)
    {
        $check = $this->conn->query("SELECT * FROM doc_doctors WHERE DoctorID = '" . $doctor_id . "' AND Active=1 ");

        if (mysqli_num_rows($check) > 0) {

            $row = $check->fetch_assoc();
            $salt = $row['PasswordSalt'];
            $encrypted_password = $this->checkhashSSHA($salt, $password);

            $check_pass = $this->conn->query("SELECT * FROM doc_doctors WHERE DoctorID = '" . $doctor_id . "' AND Password='" . $encrypted_password . "' ");
            if (mysqli_num_rows($check_pass) > 0) {
                return true;
            } else {
                return false;
            }

        } else {
            return false;
        }
    }

    /**
     * Update New Password
     */
    public function updatePassword($doctor_id, $new_password)
    {

        $hash = $this->hashSSHA($new_password);
        $encrypted_password = $hash["encrypted"]; // encrypted new password
        $salt_password = $hash["salt"]; // salt new

        $update = $this->conn->query("UPDATE doc_doctors SET 
										Password	 = '" . $encrypted_password . "',
										PasswordSalt = '" . $salt_password . "'
									WHERE 
										DoctorID = '" . $doctor_id . "' AND Active=1 ");


        if ($update) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check doctor password
     */
    public function checkUserPasswordForgot($doctor_id, $password)
    {
        $check = $this->conn->query("SELECT * FROM doc_doctors WHERE DoctorID = '" . $doctor_id . "' AND Active=1 ");

        if (mysqli_num_rows($check) > 0) {

            $check_pass = $this->conn->query("SELECT * FROM doc_doctors WHERE DoctorID = '" . $doctor_id . "' AND ForgotPassword='" . $password . "' ");

            if (mysqli_num_rows($check_pass) > 0) {
                return true;
            } else {
                return false;
            }

        } else {
            return false;
        }
    }

    /**
     * Function get doc_order_log by order_id and order_status_id
     * @param : $order_id, $order_status_id
     * returns array, boolean
     */
    public function getOrderLogDoctorByOrderId($order_id, $order_status_id)
    {
        $q = $this->conn->query("SELECT * FROM doc_orders_logs 
    							WHERE OrderID = " . $order_id . " AND OrderStatusID = " . $order_status_id . "
    							ORDER BY LogID DESC
    							LIMIT 1
    						");

        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }

    //START DOCTOR WALLET

    /**
     * Get user wallet by user_id
     * @param : $lab_id
     * returns array or false
     */
    public function getUserWalletByUserId($user_id)
    {
        $q = $this->conn->query("SELECT *
								 FROM doc_wallet 
								 WHERE DoctorID = " . $user_id . "
								 AND Active = 1");

        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }

    /**
     * Get user poin by user_id
     * @param : $user_id
     * returns array or false
     */
    public function getUserPoinByUserId($user_id)
    {
        $q = $this->conn->query("SELECT Point
								 FROM doc_doctors
								 WHERE DoctorID = " . $user_id . "
								 AND Active = 1");

        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }

    /**
     * Get Nominal TopUp
     */
    public function getNominalTopUp()
    {

        $query_get = $this->conn->query("SELECT   
											*
										FROM nominal_topup");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get Top Up User Detail
     */
    public function getTopUpUser($user_id)
    {

        $query_get = $this->conn->query("SELECT
                                            a.*,
                                            b.StatusName
                                            FROM
                                            doc_wallet_topup a
                                            LEFT JOIN doc_wallet_topup_status b ON a.TopUpStatusID = b.UserWalletStatusID 
                                            LEFT JOIN doc_doctors c ON a.DoctorID = c.DoctorID
                                            WHERE a.DoctorID = '" . $user_id . "'
                                            ORDER BY a.OrderID DESC
                                            ");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }

    public function pendingTopUpUserWalletByUserId($user_id, $amount, $nominal_id, $unique_code, $payment_type_id)
    {
        //Insert into user_wallet_topup with Status 1 (Pending)
        $total = $amount;

        //Jika user nya sudah ada, maka update Total topup nya
        $data_log = $this->conn->query("select * from doc_wallet_topup order by OrderID desc LIMIT 1");

        if (mysqli_num_rows($data_log) > 0) {
            $data_result = $data_log->fetch_assoc();
            $order_number = $data_result['OrderNo'];
        } else {
            $last_year = date('y');
            $last_month = date('m');
            $order_number = 'DCW' . $last_year . $last_month . '10000';
        }

        //USW180710000
        $prefix = 'DCW';
        $last_year = date('y');
        $last_month = date('m');
        $last_no = (int)10000;
        if ($data_log != null) {
            $last_order_no = $order_number;
            $last_no = substr($last_order_no, 7);
            $last_month = substr($last_order_no, 5, 2);
            $last_year = substr($last_order_no, 3, 2);
        }

        $generate_order_no = $this->generateOrderNoByYearMonth($prefix, $last_no, $last_year, $last_month);


        $q2 = $this->conn->query("INSERT INTO doc_wallet_topup(
									DoctorID,
									Amount,
									CreatedDate,
									TopUpStatusID,
									NominalID,
									KodeUnik,
									PaymentTypeID,
									OrderNo
								)VALUES(
									" . $user_id . ",
									" . $total . ",
									'" . $this->get_current_time() . "',
									'1',
									'" . $nominal_id . "',
									'" . $unique_code . "',
									'" . $payment_type_id . "',
									'" . $generate_order_no . "'
									)
								");

        if ($q2) {
            return $this->conn->insert_id;
        } else {
            return false;
        }
    }

    public function generateOrderNoByYearMonth($prefix, $last_no, $last_year, $last_month)
    {
        $prefix = 'DCW';
        $year = date('y');
        $month = date('m');
        $no = $last_no;

        if ($last_year == $year && $last_month == $month) {
            $no = (int)$no + 1;
        }

        $curr_order_no = $prefix . $year . $month . $no;

        return $curr_order_no;
    }

    public function getOrderWalletID($topup_id)
    {
        $q = $this->conn->query("SELECT *
								 FROM doc_wallet_topup
								 WHERE OrderID = " . $topup_id . "
								 AND Active = 1");

        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }


    /**
     * Check if payment confirmation exist (TopUp Order)
     */
    public function checkConfirmPaymentPendingTopUp($topup_id)
    {
        //0: "ready confirmation by admin";1: accepted; 2: decline
        $query = $this->conn->query("SELECT * FROM doc_wallet_topup_payment_transfer WHERE TopUpID = '" . $topup_id . "' AND Status=0 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Create Payment Confirmation (TopUp Order)
     */
    public function confirmPaymentTransferTopUp($DoctorID_header, $topup_id, $payment_accound_id, $bank_name, $account_name, $account_no, $trf_date, $total, $kode_unik)
    {
        //4 (Menunggu Verifikasi
        $upd = $this->conn->query("UPDATE doc_wallet_topup 
									SET TopUpStatusID = 4
									WHERE OrderID = '" . $topup_id . "'");
        $insert = $this->conn->query("INSERT INTO doc_wallet_topup_payment_transfer 
										(TopUpID,
										SenderBankName,
										SenderBankAccName,
										SenderBankAccNo,
										TransferDate,
										PaymentAccountID,
										Total,										
										Status,
										DoctorID,
										UniqueCode,
										CreatedBy,
										CreatedDate
										) 
									VALUES 
										('" . $topup_id . "',
										'" . $bank_name . "', 
										'" . $account_name . "', 
										'" . $account_no . "',
										'" . $trf_date . "',
										'" . $payment_accound_id . "',
										'" . $total . "',
										0,
										'" . $DoctorID_header . "',
										'" . $kode_unik . "',
										'" . $DoctorID_header . "',
										'" . $this->get_current_time() . "'
										) ");

        if ($insert) {
            $insert_id = $this->conn->insert_id;
            return $insert_id;
        } else {
            return null;
        }
    }

    /**
     * Get Top Up User Detail
     */
    public function getTopUpUserDetail($user_id, $nominal_id)
    {

        $query_get = $this->conn->query("SELECT
                                            *
                                            FROM
                                            doc_wallet_topup
                                            LEFT JOIN doc_wallet_topup_status ON doc_wallet_topup.TopUpStatusID = doc_wallet_topup_status.UserWalletStatusID 
                                            WHERE DoctorID = '" . $user_id . "' AND NominalID = '" . $nominal_id . "' ");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Check if topup exist
     */
    public function checkTopUpExist($topup_id)
    {
        $query = $this->conn->query("SELECT * FROM doc_wallet_topup WHERE OrderID = '" . $topup_id . "'");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Process Payment TopUp Patient
     */
    public function processPaymentTopUp($topup_id, $kode_unik, $payment_type_id, $totalpayment)
    {

        $update = $this->conn->query("UPDATE 
                                          doc_wallet_topup 
                                        SET 
                                        PaymentTypeID =  '" . $payment_type_id . "',
                                        Amount =  '" . $totalpayment . "',
										TopUpStatusID = '2', 
										KodeUnik = '" . $kode_unik . "'							
									WHERE 
										OrderID = '" . $topup_id . "'
										AND TopUpStatusID = '1'
										");

        if ($update) {
            $user = $this->getDoctorWalletByOrderID($topup_id);
            if ($user != null) {
                return $user;
            }
        } else {
            return false;
        }
    }

    public function getDoctorWalletByOrderID($topup_id)
    {
        $query_get = $this->conn->query("SELECT   
											a.OrderID,
											a.OrderNo,
											a.Amount,
											a.KodeUnik,
											b.FirstName,
											b.LastName,
											b.DoctorID,
											b.Email,
											b.FirebaseID
										FROM doc_wallet_topup a
										INNER JOIN doc_doctors b ON a.DoctorID = b.DoctorID
										WHERE a.OrderID='" . $topup_id . "' ");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }
    //END OF DOCTOR WALLLET
}

?>