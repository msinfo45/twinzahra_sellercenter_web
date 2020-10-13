<?php

/**
 * API Model Kurir
 * Author: Mahmuddin
 */
class Model_kurir
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
     * Create New Kurir
     */
    public function createKurir($firstname, $lastname, $phone, $password, $email, $plat_no, $gender, $no_ktp, $location, $firebase_id, $firebase_time, $device_brand, $device_model, $device_serial, $device_os)
    {

        //Generate Encrypt Password
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"];
        $salt_password = $hash["salt"];

        //Generate Verification Code
        $code = $this->generatePIN();

        //Generate Token
        $token = $this->generateToken();

        $insert = $this->conn->query("INSERT INTO kurir_users 
									(FirstName, 
									LastName, 
									Phone,
									Password,
									PasswordSalt,
									Email,
									Gender,
									Plat_nomor,
									NIK,
									Address,
									Token,
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
									'" . $gender . "',
									'" . $plat_no . "',
									'" . $no_ktp . "',
									'" . $location . "',
									'" . $token . "',
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

            return $this->conn->insert_id;
        } else {
            return false;
        }
    }

    /**
     * Get nurse data by phone (only for login callback)
     */
    public function getKurirLoginData($phone)
    {

        $check = $this->checkKurirRegister($phone);


        if ($check) {
            $query_get = $this->conn->query("SELECT a.*, '' AS Password, IFNULL(b.Active,'0') AS Status FROM kurir_users a 
												LEFT JOIN kurir_location b ON a.KurirUserID = b.KurirUserID
												WHERE a.Phone = '" . $phone . "' AND a.Active=1");

            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get Kurir Job offer
     */
    public function getJobOffer($kurir_id)
    {

        //get kurir location
        $getLoc = $this->conn->query("SELECT a.* 
										FROM kurir_location a
										WHERE a.KurirUserID='" . $kurir_id . "' AND a.Active=1 
										LIMIT 1");

        if (mysqli_num_rows($getLoc) > 0) {

            $rowLoc = $getLoc->fetch_assoc();
            $kurir_latitude = $rowLoc['Latitude'];
            $kurir_longitude = $rowLoc['Longitude'];

            $query_get = $this->conn->query("SELECT
                                                d.AptOrderID,
                                                d.OrderDate,
                                                d.OrderNo,
                                                d.UserID,
                                                d.Notes,
                                                d.AptOrderStatusID,
                                                d.Transport,
                                                d.Active,
                                                d.Latitude AS Latitude_tujuan_pengiriman,
                                                d.Longitude AS Longitude_tujuan_pengiriman,
                                                d.Location AS Lokasi_pengiriman,
                                                e.PharmacyID,
                                                e.IsAccepted,
                                                f.AptUserID,
                                                g.FirstName AS FirstName_patient,
                                                g.LastName AS LastName_patient,
                                                g.Address AS Address_patient,
                                                g.Phone AS Phone_patient,
                                                h.FirstName AS FirstName_apt,
                                                h.LastName AS LastName_apt,
                                                i.Address AS Address_pharmacy,
                                                i.`Name` AS Nama_Pharmacy,
                                                i.Latitude AS Latitude_pharmacy,
                                                i.Longitude AS Longitude_pharmacy,
                                                i.Phone AS Phone_apt,
                                                a.OrderNo,
                                                a.OrderNo,
                                                a.OrderStatusID,
                                                a.Active,
													(3959 * acos(cos(radians(" . $kurir_latitude . "))*cos(radians(i.Latitude))*cos(radians(i.Longitude)-radians(" . $kurir_longitude . ")) + sin(radians(" . $kurir_latitude . "))*sin(radians(i.Latitude)))) AS distance
												FROM kurir_orders a
												LEFT JOIN apt_orders d ON d.AptOrderID = a.AptOrderID
                                               LEFT JOIN apt_order_offers e ON d.AptOrderID = e.AptOrderID
                                               LEFT JOIN apt_orders_aptoteker_accept f ON d.AptOrderID = f.AptOrderID
                                               LEFT JOIN master_users g ON d.UserID = g.UserID
                                               LEFT JOIN apt_users h ON f.AptUserID = h.AptUserID
                                               LEFT JOIN apt_pharmacies i ON e.PharmacyID = i.PharmacyID 
												WHERE a.KurirOrderID NOT IN (SELECT KurirOrderID FROM kurir_orders_accept WHERE KurirUserID = '" . $kurir_id . "') 
												HAVING distance <= 5 AND a.OrderStatusID = 1 AND a.Active = 1
												ORDER BY a.CreatedDate DESC");
            if (mysqli_num_rows($query_get) > 0) {
                return $query_get;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Get banners
     */
    public function getBanners()
    {
        $query_get = $this->conn->query("SELECT BannerID, Title, Caption, App, CONCAT('" . $this->uploaddir . "', '/banners/', BannerID,'.jpg') AS Url FROM master_banners WHERE App='9' AND Active = 1");
        return $query_get;
    }

    /**
     * Get Order History
     */
    public function getOrderHistory($kurir_id, $order_id = null, $page, $limit)
    {

        $filter = "";
        if ($order_id != null) {
            $filter = " AND a.KurirOrderID = '" . $order_id . "' ";
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
												a.Location as Address,
												a.Rating AS Rate,
												b.UserID,
												b.FirstName,
												b.LastName,
												c.OrderStatusID,
												c.StatusName
											FROM kurir_orders a
											INNER JOIN master_users b ON b.UserID = a.UserID
											INNER JOIN kurir_order_status c ON c.OrderStatusID = a.OrderStatusID
											WHERE a.KurirUserID = '" . $kurir_id . "' " . $filter . " AND c.OrderStatusID IN (3,4,5,6,7,8) AND a.Active = 1
											ORDER BY a.KurirOrderID DESC " . $condition);


        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get dashboard orders (HOME)
     */
    public function getOrders($kurir_id)
    {
        $query = $this->conn->query("SELECT COUNT(KurirOrderID) AS total_order FROM kurir_orders WHERE KurirUserID = '" . $kurir_id . "' AND OrderStatusID=6 AND Active=1 ");

        $row = $query->fetch_assoc();
        $orders = $row['total_order'];

        return $orders;
    }

    /**
     * Get dashboard rating (HOME)
     */
    public function getRating($kurir_id)
    {
        $query = $this->conn->query("SELECT COUNT(KurirOrderID) AS total_order, SUM(Rating) AS total_rating FROM kurir_orders WHERE KurirUserID = '" . $kurir_id . "' AND OrderStatusID=6 AND Active=1 ");

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
    public function getCancelation($kurir_id)
    {
        // get order status 4,5,6,9
        $query_total = $this->conn->query("SELECT COUNT(KurirOrderID) AS total_order FROM kurir_orders WHERE KurirUserID = '" . $kurir_id . "' AND OrderStatusID IN (4,5,6,9) AND Active=1 ");
        $row1 = $query_total->fetch_assoc();

        //dibatalkan oleh admin
        $query_cancel = $this->conn->query("SELECT COUNT(a.KurirDeclineID) AS total_cancel 
											FROM kurir_orders_decline a
											INNER JOIN kurir_orders b ON b.KurirOrderID=a.KurirOrderID
											WHERE b.OrderStatusID=9  AND a.KurirUserID = '" . $kurir_id . "' ");
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
    public function getPerformance($kurir_id)
    {
        //get order status 4,5,6,9
        $query_total = $this->conn->query("SELECT COUNT(KurirOrderID) AS total_order FROM kurir_orders WHERE KurirUserID = '" . $kurir_id . "' AND OrderStatusID IN (4,5,6,9) AND Active=1");
        $row1 = $query_total->fetch_assoc();

        //status 6 = selesai transaksi
        $query_finish_order = $this->conn->query("SELECT COUNT(a.KurirAcceptID) AS total_accept 
											FROM kurir_orders_accept a
											INNER JOIN kurir_orders b ON b.KurirOrderID=a.KurirOrderID
											WHERE b.OrderStatusID=6 AND a.KurirUserID = '" . $kurir_id . "' ");

        $row2 = $query_finish_order->fetch_assoc();

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
     * Check nurse password
     */
    public function checkUserPassword($kurir_id, $password)
    {

        $table = 'kurir_users';

        $check = $this->conn->query("SELECT * FROM " . $table . " WHERE KurirUserID = '" . $kurir_id . "' AND Active=1 ");

        if (mysqli_num_rows($check) > 0) {

            $row = $check->fetch_assoc();
            $salt = $row['PasswordSalt'];
            $encrypted_password = $this->checkhashSSHA($salt, $password);

            $check_pass = $this->conn->query("SELECT * FROM " . $table . " WHERE KurirUserID = '" . $kurir_id . "' AND Password='" . $encrypted_password . "' ");
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
     * Check nurse password
     */
    public function checkUserPasswordForgot($kurir_id, $password)
    {

        $table = 'kurir_users';

        $check = $this->conn->query("SELECT * FROM " . $table . " WHERE KurirUserID = '" . $kurir_id . "' AND Active=1 ");

        if (mysqli_num_rows($check) > 0) {

            $check_pass = $this->conn->query("SELECT * FROM " . $table . " WHERE KurirUserID = '" . $kurir_id . "' AND ForgotPassword='" . $password . "' ");

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
     * Check if kurir exist
     */
    public function checkKurirLogin($phone, $password)
    {
        $check = $this->conn->query("SELECT * FROM kurir_users WHERE Phone = '" . $phone . "' AND Active=1");
        if (mysqli_num_rows($check) > 0) {

            $row = $check->fetch_assoc();
            $salt = $row['PasswordSalt'];
            $encrypted_password = $this->checkhashSSHA($salt, $password);

            $check_pass = $this->conn->query("SELECT * FROM kurir_users WHERE Phone = '" . $phone . "' AND Password='" . $encrypted_password . "' ");

            if (mysqli_num_rows($check_pass) > 0) {
                //Generate new token
                $new_token = $this->generateToken();
                $upd = $this->conn->query("UPDATE kurir_users SET IsLogin=1, Token='" . $new_token . "' WHERE Phone='" . $phone . "' AND Active=1 ");

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
    public function checkKurirLoginByForgot($phone, $password)
    {

        $now = $this->get_current_time();

        $check_pass = $this->conn->query("SELECT * FROM kurir_users WHERE Phone = '" . $phone . "' AND ForgotPassword='" . $password . "' AND ForgotPasswordExpired > STR_TO_DATE('" . $now . "', '%Y-%m-%d %H:%i:%s') AND Active=1 ");

        if (mysqli_num_rows($check_pass) > 0) {
            $new_token = $this->generateToken();
            $upd = $this->conn->query("UPDATE kurir_users SET IsLogin=1, Token='" . $new_token . "' WHERE Phone='" . $phone . "' AND Active=1 ");
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
    public function checkToken($token, $kurir_id)
    {

        $query = $this->conn->query("SELECT * FROM kurir_users WHERE Token = '" . $token . "' AND KurirUserID = '" . $kurir_id . "' AND Active=1 ");


        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if kurir exist
     */
    public function checkKurirRegister($phone)
    {
        $query = $this->conn->query("SELECT * FROM kurir_users WHERE Phone = '" . $phone . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Check if kurir verified
     */
    public function checkKurirVerified($kurir_id)
    {
        $query = $this->conn->query("SELECT * FROM kurir_users WHERE KurirUserID = '" . $kurir_id . "' AND Active=1 AND Verified=1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check kurir by ID
     */
    public function checkKurirById($id)
    {

        $query = $this->conn->query("SELECT * FROM kurir_users WHERE KurirUserID = '" . $id . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if nurse location tracking if exist
     */
    public function checkKurirLocationExist($kurir_id)
    {
        $query = $this->conn->query("SELECT * FROM kurir_location WHERE KurirUserID = '" . $kurir_id . "' ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if order exist
     */
    public function checkOrderExist($order_id, $order_status_id = null)
    {
        if ($order_status_id != null) {
            $query = $this->conn->query("SELECT kurir_orders.*, apt_orders.SubTotal, apt_orders.Transport FROM kurir_orders LEFT JOIN apt_orders ON apt_orders.AptOrderID =  kurir_orders.AptOrderID WHERE kurir_orders.KurirOrderID = '" . $order_id . "' AND kurir_orders.Active=1 AND kurir_orders.OrderStatusID=" . $order_status_id);
        } else {
            $query = $this->conn->query("SELECT 
    									kurir_orders.*, apt_orders.SubTotal, apt_orders.Transport
    									FROM kurir_orders
    									LEFT JOIN apt_orders ON apt_orders.AptOrderID =  kurir_orders.AptOrderID
    									WHERE kurir_orders.KurirOrderID = '" . $order_id . "' AND kurir_orders.Active=1");
        }

        if (mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return false;
        }
    }

    /**
     * Check if kurir was accept offer
     */
    public function checkKurirAcceptOffer($order_id, $kurir_id)
    {
        $query = $this->conn->query("SELECT * FROM kurir_orders_accept WHERE KurirUserID = '" . $kurir_id . "' AND KurirOrderID = '" . $order_id . "'");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Check if nurse was decline offer
     */
    public function checkKurirDeclineOffer($order_id, $kurir_id)
    {
        $query = $this->conn->query("SELECT * FROM kurir_orders_decline WHERE KurirUserID = '" . $kurir_id . "' AND KurirOrderID = '" . $order_id . "' ");

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
        $query = $this->conn->query("SELECT * FROM kurir_users WHERE Phone = '" . $phone . "' AND ActivationCode='" . $code . "' ");

        if (mysqli_num_rows($query) > 0) {
            $this->conn->query("UPDATE kurir_users SET Active = 1, IsLogin=1 WHERE Phone = '" . $phone . "' AND ActivationCode='" . $code . "' ");
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

        $query_sms = $this->conn->query("SELECT FirstName, Phone, ActivationCode, Active FROM kurir_users WHERE Phone = '" . $phone . "' ");
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
     * Update New Password
     */
    public function updatePassword($kurir_id, $new_password)
    {

        $hash = $this->hashSSHA($new_password);
        $encrypted_password = $hash["encrypted"]; // encrypted new password
        $salt_password = $hash["salt"]; // salt new


        $update = $this->conn->query("UPDATE kurir_users SET 
										Password	 = '" . $encrypted_password . "',
										PasswordSalt = '" . $salt_password . "'
									WHERE 
										KurirUserID = '" . $kurir_id . "' AND Active=1 ");


        if ($update) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Forgot Password
     */
    public function forgotPassword($phone)
    {

        $new_pass = $this->randomPassword(8);
        $expired_date = date('Y-m-d h:i:s', strtotime('+1 days'));

        $update = $this->conn->query("UPDATE kurir_users SET 
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
     * Update FirebaseID
     */
    public function updateFirebase($phone, $firebase_id)
    {

        $update = $this->conn->query("UPDATE kurir_users SET 
										FirebaseID 		= '" . $firebase_id . "'
									WHERE 
										Phone = '" . $phone . "'");

        if ($update) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update Kurir Profile
     */
    public function updateProfile($kurir_id, $firstname, $lastname, $phone, $email, $plat_no, $gender, $no_ktp, $location)
    {
        $check = $this->checkKurirById($kurir_id);
        if ($check) {
            $update = $this->conn->query("UPDATE kurir_users SET 
											FirstName 	= '" . $firstname . "',
											LastName 	= '" . $lastname . "',
											Phone 		= '" . $phone . "',
											Email 		= '" . $email . "',
											Plat_nomor 	= '" . $plat_no . "',
											Gender 		= '" . $gender . "',
											NIK 		= '" . $no_ktp . "',
											Address 	= '" . $location . "',
											ModifiedBy	= '" . $kurir_id . "',
											ModifiedDate= '" . $this->get_current_time() . "'
										WHERE 
											KurirUserID = '" . $kurir_id . "'");


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
     * Process Kurir Location Tracking
     */
    public function trackLocation($kurir_id, $latitude, $longitude, $accuracy)
    {

        $exist = $this->checkKurirLocationExist($kurir_id);
        if ($exist) {
            //Update Location
            $update = $this->conn->query("UPDATE kurir_location SET 
											Latitude 	= '" . $latitude . "',
											Longitude 	= '" . $longitude . "',
											Accuracy 	= '" . $accuracy . "',
											TrackDate	= '" . $this->get_current_time() . "'
										WHERE 
											KurirUserID = '" . $kurir_id . "'");

            if ($update) {
                return true;
            } else {
                return false;
            }
        } else {
            //Create Location
            $insert = $this->conn->query("INSERT INTO kurir_location 
										(KurirUserID,
										Latitude,
										Longitude,
										Accuracy,
										TrackDate
										) 
									VALUES 
										('" . $kurir_id . "',
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
     * Process Kurir Online/Offline
     */
    public function updateStatus($kurir_id, $status)
    {

        $exist = $this->checkKurirLocationExist($kurir_id);
        if ($exist) {
            //Update Location
            $update = $this->conn->query("UPDATE kurir_location SET 
											Active 		= '" . $status . "',
											TrackDate	= '" . $this->get_current_time() . "'
										WHERE 
											KurirUserID = '" . $kurir_id . "' ");

            if ($update) {
                return true;
            } else {
                return false;
            }
        } else {
            //Create Location
            $insert = $this->conn->query("INSERT INTO kurir_location 
										(KurirUserID,
										Latitude,
										Longitude,
										Accuracy,
										Active,
										TrackDate
										) 
									VALUES 
										('" . $kurir_id . "',
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
     * Create Kurir Accept Offer
     */
    public function acceptOffer($order_id, $kurir_id)
    {

        $exist = $this->checkKurirAcceptOffer($order_id, $kurir_id);
        if (!$exist) {
            $insert = $this->conn->query("INSERT INTO kurir_orders_accept 
											(KurirOrderID, 
											KurirUserID,
											AcceptDate) 
										VALUES 
											('" . $order_id . "', 
											'" . $kurir_id . "',
											'" . $this->get_current_time() . "'
											) ");

            if ($insert) {
                $this->sendNotif_KurirAccept($order_id, $kurir_id);
                return true;
            } else {
                return false;
            }
        } else {
            $this->sendNotif_KurirAccept($order_id, $kurir_id);
            return true;
        }
    }


    /**
     * Create Kurir Decline Offer
     */
    public function declineOffer($order_id, $kurir_id)
    {

        $exist = $this->checkKurirDeclineOffer($order_id, $kurir_id);
        if (!$exist) {
            $insert = $this->conn->query("INSERT INTO kurir_orders_decline 
											(KurirOrderID, 
											KurirUserID,
											DeclineDate) 
										VALUES 
											('" . $order_id . "', 
											'" . $kurir_id . "',
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

    public function sendNotif_KurirAccept($order_id, $kurir_id)
    {

        //Get Data
        $query = $this->conn->query("SELECT a.KurirUserID,
											a.FirstName, 
											a.LastName, 
											a.Plat_nomor, 
											a.Gender, 
											a.Phone
									FROM kurir_users a
									WHERE 
										a.KurirUserID = '" . $kurir_id . "' ");

        if (mysqli_num_rows($query) > 0) {

            $row = $query->fetch_assoc();
            $rating = $this->getRating($kurir_id);

            $custom_data = array(
                'type' => '1',
                'body' => "Ada Kurir menerima order kamu",
                'title' => "Order Anda",
                'KurirOrderID' => $order_id,
                'KurirUserID' => $row['KurirUserID'],
                'FirstName' => $row['FirstName'],
                'LastName' => $row['LastName'],
                'Plat_nomor' => $row['Plat_nomor'],
                'Gender' => $row['Gender'],
                'Phone' => $row['Phone'],
                'Rating' => $rating
            );

            //Notify to Patient
            $query_kurir = $this->conn->query("SELECT a.KurirOrderID, b.FirebaseID FROM kurir_orders a INNER JOIN kurir_users b ON b.KurirUserID=a.KurirUserID WHERE a.KurirOrderID = '" . $order_id . "' ");
            if (mysqli_num_rows($query_kurir) > 0) {
                $row_kurir = $query_kurir->fetch_assoc();

                $this->sendNotification_Patient($row_kurir['FirebaseID'], $custom_data);
            }

        }

    }

    /**
     * Process Status On Progress
     */
    public function processOnProgress($order_id)
    {

        $update = $this->conn->query("UPDATE kurir_orders SET 
										OrderStatusID = '3'
									WHERE 
										KurirOrderID = '" . $order_id . "'");

        if ($update) {
            $dt = $this->getKurirByOrderID($order_id);
            if ($dt != null) {
                $dt = $dt->fetch_assoc();

                //create order log
                $order_id = $order_id;
                $order_status_id = 3;
                $description = 'Log Order Kurir Di proses oleh Kurir, created by sistem api';
                $kurir_id = $dt['KurirUserID'];
                $this->createOrderLog($order_id, $order_status_id, $kurir_id, $description);
            }

            $this->historyNotification($order_id);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Process Status On Finished
     */
    public function processOnFinish($order_id)
    {

        $update = $this->conn->query("UPDATE kurir_orders SET 
										OrderStatusID = '6', Active = '0'
									WHERE 
										KurirOrderID = '" . $order_id . "'");

        if ($update) {
            $dt = $this->getKurirByOrderID($order_id);
            if ($dt != null) {
                $dt = $dt->fetch_assoc();

                //create order log
                $order_id = $order_id;
                $order_status_id = 6;
                $description = 'Log Order Kurir pesanan sampai ditujuan selesai, created by sistem api';
                $kurir_id = $dt['KurirUserID'];
                $this->createOrderLog($order_id, $order_status_id, $kurir_id, $description);
            }
            $this->historyNotification($order_id);
            return true;
        } else {
            return false;
        }
    }

    public function historyNotification($order_id)
    {

        $query = $this->conn->query("SELECT * FROM kurir_orders WHERE KurirOrderID = '" . $order_id . "' AND Active=1 LIMIT 1 ");

        if (mysqli_num_rows($query) > 0) {
            $row = $query->fetch_assoc();
            $user_id = $row['UserID'];
            $status_id = $row['OrderStatusID'];

            $message = "";
            if ($status_id == "3") {
                $message = "Sedang diproses oleh kurir";
            } else if ($status_id == "4") {
                $message = "Sedang di pickup oleh Kurir";
            } else if ($status_id == "5") {
                $message = "Sedang dalam menuju perjalanan ke tempat Anda";
            } else if ($status_id == "7") {
                $message = "Pesanan Anda telah sampai, terima kasih sudah menggunakan aplikasi kami";
            }

            $custom_data = array(
                'type' => '3', //History
                'body' => $message,
                'title' => "Status Pemesanan Anda Telah Sampai",
                'OrderID' => $order_id
            );

            //Notify User
            $query_kurir = $this->conn->query("SELECT * FROM master_users WHERE UserID='" . $user_id . "' AND Active=1 ");
            if (mysqli_num_rows($query_kurir) > 0) {
                $row_kurir = $query_kurir->fetch_assoc();

                $this->sendNotification_Patient($row_kurir['FirebaseID'], $custom_data);
                $this->sendNotification_Pharmacy($row_kurir['FirebaseID'], $custom_data);
            }
        }
    }

    /**
     * Process Logout
     */
    public function processLogout($kurir_id)
    {

        $update = $this->conn->query("UPDATE kurir_users SET 
										IsLogin = '0',
										FirebaseID = '',
										Token = ''
									WHERE 
										KurirUserID = '" . $kurir_id . "'");


        if ($update) {
            return true;
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
     * Update FirebaseID bu KurirUserID
     */
    public function updateFirebase2($kurir_id, $firebase_id)
    {

        $update = $this->conn->query("UPDATE kurir_users SET 
										FirebaseID 		= '" . $firebase_id . "'
									WHERE 
										KurirUserID = '" . $kurir_id . "'");

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
        $query_get = $this->conn->query("SELECT PublishedDate, ArticleID, Title, Caption, CreatedDate, CONCAT('" . $this->uploaddir . "', '/articles/', ArticleID,'.jpg') AS Url FROM master_articles WHERE Active = 1 AND TypeID = 9 ORDER BY PublishedDate DESC " . $limit);

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

    /**
     * Function Send SMS code
     * @param : Phone, Code, Name
     * returns boolean
     */
    function send_sms($phone, $code, $name)
    {
        $userkey = $this->smsuserkey; //userkey lihat di zenziva
        $passkey = $this->smspasskey; // set passkey di zenziva
        $message = "VTAL: Hi " . $name . ", Terima Kasih telah melakukan registrasi di VTAL Kurir. Mohon masukan kode aktivasi berikut ini: " . $code;
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
     * Function Send GCM to Pharmacy
     * @param : FirebaseID, Custom Data JSON
     * returns boolean
     */
    function sendNotification_Pharmacy($firebase_id, $custom_data)
    {

        $registrationIds = array($firebase_id);

        $fields = array(
            'registration_ids' => $registrationIds,
            'data' => $custom_data
        );

        $headers = array(
            'Authorization: key=AAAAvDmzVp8:APA91bG1hwgkf10mYwAtax_n1NOKFSDMCzxnzZXG_BxL5TxJn8hHM6ywdCnvf0Gg6bJAqGhaD_wk_PUS0i3xTZRC2WCmeVZnFODU2JB7CknAdE_oDGYsz5GWoNQ_D-m7rbOUVycJ_6_3',
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
     * Get user data by id
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

    public function createOrderLog($order_id, $order_status_id, $kurir_id, $description)
    {
        $q = $this->conn->query("INSERT INTO kurir_orders_logs 
									(KurirOrderID,
									CreatedDate,
									CreatedBy,
									OrderStatusID,
									KurirUserID,
									Description
									) 
								VALUES 
									('" . $order_id . "',
									'" . $this->get_current_time() . "',
									'9-',
									'" . $order_status_id . "',
									'" . $kurir_id . "',
									'" . $description . "'
									) ");

        if ($q) {
            return $this->conn->insert_id;
        } else {
            return false;
        }
    }

    /**
     * Get Kurir Data From Order ID
     */
    public function getKurirByOrderID($order_id)
    {

        $query_get = $this->conn->query("SELECT   
											a.KurirOrderID,
											b.FirstName,
											b.LastName,
											b.KurirUserID
										FROM kurir_orders a
										INNER JOIN kurir_users b ON a.KurirUserID = b.KurirUserID 
										WHERE a.Active=1 AND a.KurirOrderID='" . $order_id . "' ");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }
}

?>