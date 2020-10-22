<?php

class Model_nurse
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
     * Create New Nurse
     */
    public function createNurse($firstname, $lastname, $phone, $password, $email, $degree, $birthdate, $gender, $no_ktp, $no_str, $experience, $location, $category_id, $firebase_id, $firebase_time, $device_brand, $device_model, $device_serial, $device_os, $referral_by)
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

        $insert = $this->conn->query("INSERT INTO nrz_nurses 
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

            return $this->conn->insert_id;
        } else {
            return false;
        }
    }

    /**
     * Get nurse data by phone
     */
    public function getNurseByPhone($phone)
    {

        $check = $this->checkNurseRegister($phone);
        if ($check) {
            $query_get = $this->conn->query("SELECT *, '' AS Password FROM nrz_nurses WHERE Phone = '" . $phone . "'");
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get nurse data by phone (only for login callback)
     */
    public function getNurseLoginData($phone)
    {

        $check = $this->checkNurseRegister($phone);


        if ($check) {
//            get data only when Active dan Verified = 1
            $query_get = $this->conn->query("SELECT
                                            a.*,
                                            '' AS PASSWORD,
                                            c.CategoryName AS CategoryName,
                                            IFNULL( b.Active, '0' ) AS Status,
                                            d.NurseFamilyName AS NameEmergency,
                                            d.Email AS EmailEmergency,
                                            d.Gender AS GenderEmergency,
                                            d.Telp AS PhoneEmergency 
                                        FROM
                                            nrz_nurses a
                                            LEFT JOIN nrz_nurse_location b ON a.NurseID = b.NurseID
                                            INNER JOIN nrz_categories c ON c.CategoryID = a.CategoryID
                                            LEFT JOIN nrz_nurse_family d ON d.NurseID = a.NurseID
                                            AND d.Emergency = 1 
                                        WHERE a.Phone = '" . $phone . "' 
                                            AND a.Active = 1 
                                            AND a.Verified =1");

            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get nurse data by phone (only for login callback)
     */
    public function getNurseLoginData2($phone)
    {
        $check = $this->checkNurseRegister2($phone);


        if ($check) {
            $query_get = $this->conn->query("SELECT a.*, c.CategoryName, 1 AS Status FROM kanopi_nrz_nurses a 
												LEFT JOIN kanopi_nrz_nurse_categories c ON c.CategoryID = a.CategoryID
												WHERE a.Phone = '" . $phone . "' AND a.Active=1");

            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get nurse data by registration no (only for login callback)
     */
    public function getNurseLoginData3($registration_no)
    {
        $check = $this->checkNurseRegister3($registration_no);

        if ($check) {
            $query_get = $this->conn->query("SELECT a.*, c.CategoryName, 1 AS Status FROM kanopi_nrz_nurses a 
												LEFT JOIN kanopi_nrz_nurse_categories c ON c.CategoryID = a.CategoryID
												WHERE a.RegisterNo = '" . $registration_no . "' AND a.Active=1");
            // status, status dipojok kanan atas app
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get nurse data detail
     */
    public function getNurseData($nurse_id)
    {

        $check = $this->checkNurseById($nurse_id);
        if ($check) {
            $query_get = $this->conn->query("SELECT * FROM nrz_nurses WHERE NurseID = '" . $nurse_id . "' AND Active=1");
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get nurse education
     */
    public function getNurseEducation($nurse_id, $nurse_type_header = 1)
    {

        $check = $this->checkNurseById($nurse_id, $nurse_type_header);
        if ($check) {
            if ($nurse_type_header == 2) {
                $query_get = $this->conn->query("SELECT * FROM kanopi_nrz_nurse_educations WHERE NurseID = '" . $nurse_id . "' AND Active=1 ORDER BY NurseEducationID DESC");
            } else {
                $query_get = $this->conn->query("SELECT * FROM nrz_nurse_educations WHERE NurseID = '" . $nurse_id . "' AND Active=1 ORDER BY NurseEducationID DESC");
            }

            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get nurse experience
     */
    public function getNurseExperience($nurse_id, $nurse_type_header = 1)
    {

        $check = $this->checkNurseById($nurse_id, $nurse_type_header);
        if ($check) {
            if ($nurse_type_header == 2) {
                $query_get = $this->conn->query("SELECT * FROM kanopi_nrz_nurse_experiences WHERE NurseID = '" . $nurse_id . "' AND Active=1 ORDER BY NurseExperienceID DESC");
            } else {
                $query_get = $this->conn->query("SELECT * FROM nrz_nurse_experiences WHERE NurseID = '" . $nurse_id . "' AND Active=1 ORDER BY NurseExperienceID DESC");
            }

            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get nurse education by educationID
     */
    public function getNurseEducationByID($edu_id, $nurse_type_header = 1)
    {
        $check = $this->checkNurseEducationById($edu_id, $nurse_type_header);

        if ($check) {
            if ($nurse_type_header == 2) {
                $query_get = $this->conn->query("SELECT * FROM kanopi_nrz_nurse_educations WHERE NurseEducationID = '" . $edu_id . "'");

            } else {
                $query_get = $this->conn->query("SELECT * FROM nrz_nurse_educations WHERE NurseEducationID = '" . $edu_id . "'");
            }
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get nurse experience by experienceID
     */
    public function getNurseExperienceByID($exp_id, $nurse_type_header = 1)
    {

        $check = $this->checkNurseExperienceById($exp_id, $nurse_type_header);
        if ($check) {
            if ($nurse_type_header == 2) {
                $query_get = $this->conn->query("SELECT * FROM kanopi_nrz_nurse_experiences WHERE NurseExperienceID = '" . $exp_id . "'");

            } else {
                $query_get = $this->conn->query("SELECT * FROM nrz_nurse_experiences WHERE NurseExperienceID = '" . $exp_id . "'");
            }

            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get Nurse Job offer
     */
    public function getJobOffer($nurse_id)
    {

        //get nurse location
        $getLoc = $this->conn->query("SELECT a.*, b.CategoryID 
										FROM nrz_nurse_location a 
										INNER JOIN nrz_nurses b ON b.NurseID = a.NurseID
										WHERE a.NurseID='" . $nurse_id . "' AND a.Active=1 
										LIMIT 1");

        if (mysqli_num_rows($getLoc) > 0) {

            $rowLoc = $getLoc->fetch_assoc();
            $nrz_latitude = $rowLoc['Latitude'];
            $nrz_longitude = $rowLoc['Longitude'];
            $nrz_category = $rowLoc['CategoryID'];

            $query_get = $this->conn->query("SELECT   
													a.OrderID,
													a.OrderDate,
													a.Notes,
													a.TotalPrice,
													b.UserID,
													b.FirstName,
													b.LastName,
													b.BirthDate,
													a.Location AS Address,
													a.Latitude,
													a.Longitude,
													a.OrderStatusID,
													a.CategoryID,
													a.Active,
													(3959 * acos(cos(radians(" . $nrz_latitude . "))*cos(radians(a.Latitude))*cos(radians(a.Longitude)-radians(" . $nrz_longitude . ")) + sin(radians(" . $nrz_latitude . "))*sin(radians(a.Latitude)))) AS distance
												FROM nrz_orders_current a
												INNER JOIN master_users b ON b.UserID = a.UserID
												WHERE a.OrderID NOT IN (SELECT OrderID FROM nrz_orders_nurse_accept WHERE NurseID = '" . $nurse_id . "') 
												HAVING distance <= 10 AND a.OrderStatusID = 1 AND a.CategoryID = '" . $nrz_category . "' AND a.Active = 1
												ORDER BY a.OrderDate DESC");

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
     * Get Nurse Job offer Detail
     */
    public function getJobOfferDetail($order_id, $nurse_id)
    {

        $perKM = $this->getConfig('biaya_transportasi_per_km')->fetch_assoc();
        $current_lat = 0;
        $current_lng = 0;
        $current_distance = 0;

        $check = $this->checkOrderExist($order_id);

        if ($check) {

            //Count estimation transport fare
            $queryID = $this->conn->query("SELECT * FROM nrz_orders_current WHERE OrderID = '" . $order_id . "' AND Active=1");
            $rowID = $queryID->fetch_assoc();
            $current_lat = $rowID['Latitude'];
            $current_lng = $rowID['Longitude'];

            $queryTransport = $this->conn->query("SELECT 
													(3959 * acos(cos(radians(" . $current_lat . "))*cos(radians(Latitude))*cos(radians(Longitude)-radians(" . $current_lng . ")) + sin(radians(" . $current_lat . "))*sin(radians(Latitude)))) AS distance
												FROM nrz_nurse_location 
												WHERE NurseID='" . $nurse_id . "' ");
            $rowT = $queryTransport->fetch_assoc();
            $current_distance = ceil($rowT['distance']);
            $transportTotal = ($perKM['Value'] * $current_distance);

            if($transportTotal < 7000){
                $transportTotal = 8000;
            }

            $query_get = $this->conn->query("SELECT   
												a.OrderID,
												a.OrderDate,
												a.Notes,
												a.TotalPrice,
												b.UserID,
												b.FirstName,
												b.LastName,
												b.BirthDate,
												a.Location AS Address,
												a.Latitude,
												a.Longitude,
												'" . $transportTotal . "' AS TransportPrice,
												b.Weight,
												b.Height
											FROM nrz_orders_current a
											INNER JOIN master_users b ON b.UserID = a.UserID
											WHERE a.OrderID = '" . $order_id . "' AND a.Active = 1");
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get Action - Order Detail
     */
    public function getOrderAction($order_id)
    {

        $query_get = $this->conn->query("SELECT   
											a.ActionID,
											b.ActionName,
											b.ActionPrice
										FROM nrz_orders_detail a
										INNER JOIN nrz_actions b ON b.ActionID = a.ActionID
										WHERE a.OrderID = '" . $order_id . "'");
        return $query_get;
    }

    /**
     * Get banners
     */
    public function getBanners()
    {
        $query_get = $this->conn->query("SELECT BannerID, Title, Caption, App, CONCAT('" . $this->uploaddir . "', '/banners/', BannerID,'.jpg') AS Url FROM master_banners WHERE App='2' AND Active = 1");
        return $query_get;
    }

    /**
     * Get Order History
     */
    public function getOrderHistory($nurse_id, $order_id = null, $page, $limit, $nurse_type_header = 1)
    {
        if ($nurse_type_header == 2) {
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
												b.FirstName AS nurse_firstname,
												b.LastName AS nurse_lastname,
												b.BirthDate AS nurse_birthdate,
												b.YearExperience AS nurse,
												b.Location AS nurse_location,
												CONCAT('" . $this->uploaddir . "','/nurses/',b.NurseID,'.jpg') AS nurse_image,
												c.StatusName,
												d.PaymentType,
												e.UserID,
												e.FirstName AS user_firstname,
												e.LastName AS user_lastname,
												e.BirthDate AS user_birthdate,
												e.Weight AS user_weight,
												e.Height AS user_height,
												CONCAT('" . $this->uploaddir . "','/patients/',e.UserID,'.jpg') as user_image,
												IFNULL(f.PatientName, '') AS patient_name,
												IFNULL(f.Age, '') AS patient_age,
												IFNULL(f.Gender, '') AS patient_gender,
												IFNULL(f.Height, '') As patient_height,
												IFNULL(f.Weight, '') AS patient_weight
											FROM kanopi_nrz_orders_current a
											INNER JOIN kanopi_nrz_nurses b ON b.NurseID = a.NurseID
											INNER JOIN kanopi_nrz_order_status c ON c.OrderStatusID = a.OrderStatusID
											LEFT JOIN master_payment_type d ON d.PaymentTypeID = a.PaymentTypeID
											INNER JOIN master_users e ON e.UserID = a.UserID
											LEFT JOIN master_patients f ON f.PatientID = a.PatientID
											WHERE a.NurseID = '" . $nurse_id . "' " . $filter . " AND a.OrderStatusID NOT IN (1,2,3,4,5) AND a.Active = 1
											ORDER BY a.OrderID DESC " . $condition);

        } else {
            $filter = "";
            if ($order_id != null) {
                $filter = " AND OrderID = '" . $order_id . "' ";
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
												(a.TotalPrice + a.TransportPrice ) as TotalPayment,
												-- a.OrderID,
												-- a.OrderNo,
												DATE(a.OrderDate) AS OrderDate,
												-- a.Notes,
												-- a.TotalPrice,
												a.Location AS Address,
												-- a.Latitude,
												-- a.Longitude,
												-- a.Location,
												a.Rating AS Rate,
												-- a.TransportPrice,
												b.UserID,
												b.FirstName,
												b.LastName,
												b.BirthDate,
												b.Weight,
												b.Height,
												c.OrderStatusID,
												c.StatusName,
												d.PaymentType
											FROM nrz_orders_current a
											INNER JOIN master_users b ON b.UserID = a.UserID
											INNER JOIN nrz_order_status c ON c.OrderStatusID = a.OrderStatusID
											LEFT JOIN master_payment_type d ON d.PaymentTypeID = a.PaymentTypeID
											WHERE a.NurseID = '" . $nurse_id . "' " . $filter . " AND c.OrderStatusID IN (3,4,5,6,7,8,9,10) AND a.Active = 1
											ORDER BY a.OrderID DESC " . $condition);
        }

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get Bils Confirm History
     */
    public function getBillsHistory($nurse_id)
    {

        $query_get = $this->conn->query("SELECT a.BillID,
												a.NurseID,
												a.PaymentAccountID,
												a.TransferDate,
												a.Total,
												a.Status AS StatusID,
												CONCAT(a.SenderBankName,' ',a.SenderBankAccNo,' a.n ',a.SenderBankAccName) AS FromAccount,
												CONCAT(b.Bank,' ',b.AccountNumber,' a.n ',b.AccountName) AS ToAccount,
												CASE  
													WHEN a.Status = '0' THEN 'Pending'
													WHEN a.Status = '1' THEN 'Lunas'
													WHEN a.Status = '2' THEN 'Di Tolak'
												ELSE '-'
												END as 'StatusName'
										FROM nrz_payment_confirmations a
										INNER JOIN master_payment_account b ON b.PaymentAccountID=a.PaymentAccountID
										WHERE a.NurseID = '" . $nurse_id . "'
										ORDER BY a.BillID DESC ");

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
										FROM nrz_orders_current a
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
    public function getSaldoFromOrder($nurse_id)
    {

        $query_get = $this->conn->query("SELECT IFNULL(SUM(TotalPayment),0) AS saldo FROM `nrz_orders_current` WHERE NurseID = '" . $nurse_id . "' AND OrderStatusID = '7' AND Active=1 ");

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
											(CASE WHEN Filename IS NOT NULL THEN CONCAT('" . $this->uploaddir . "', '/chats/nurse_orders/', OrderID,'/',Filename) ELSE '' END) AS url,
											(CASE WHEN LEFT(ChatFrom, 3) = 'nrz' THEN '1' ELSE '0' END) AS ChatFrom,
											(CASE WHEN LEFT(ChatFrom, 3) = 'nrz' THEN 'right' ELSE 'left' END) AS Position
										FROM nrz_chat
										WHERE OrderID = '" . $order_id . "'
										ORDER BY ChatID ASC");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get filename of nurse chat
     */
    public function getFileChat($chat_id)
    {
        $query = $this->conn->query("SELECT * FROM nrz_chat WHERE ChatID = '" . $chat_id . "' ");

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
        $query = $this->conn->query("SELECT * FROM nrz_orders_current WHERE OrderID = '" . $order_id . "' AND Active=1 LIMIT 1 ");

        if (mysqli_num_rows($query) > 0) {
            $row = $query->fetch_assoc();
            $current_id = $row['OrderID'];

            return $current_id;
        } else {
            return null;
        }
    }

    /**
     * Get dashboard orders (HOME)
     */
    public function getOrders($nurse_id)
    {
        $query = $this->conn->query("SELECT COUNT(OrderID) AS total_order FROM nrz_orders_current WHERE NurseID = '" . $nurse_id . "' AND OrderStatusID=7 AND Active=1 ");

        $row = $query->fetch_assoc();
        $orders = $row['total_order'];

        return $orders;
    }

    /**
     * Get dashboard rating (HOME)
     */
    public function getRating($nurse_id)
    {
        $query = $this->conn->query("SELECT COUNT(OrderID) AS total_order, SUM(Rating) AS total_rating FROM nrz_orders_current WHERE NurseID = '" . $nurse_id . "' AND OrderStatusID=7 AND Active=1 ");

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
    public function getCancelation($nurse_id)
    {
        // get order status 4,5,6,7,8, 11
        $query_total = $this->conn->query("SELECT COUNT(OrderID) AS total_order FROM nrz_orders_current WHERE NurseID = '" . $nurse_id . "' AND OrderStatusID IN (4,5,6,7,11) AND Active=1 ");
        $row1 = $query_total->fetch_assoc();

        $query_cancel = $this->conn->query("SELECT COUNT(a.NurseDeclineID) AS total_cancel 
											FROM nrz_orders_nurse_decline a
											INNER JOIN nrz_orders_current b ON b.OrderID=a.OrderID
											WHERE b.OrderStatusID=11  AND a.NurseID = '" . $nurse_id . "' ");
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
    public function getPerformance($nurse_id)
    {
        //get order status 4,5,6,7, 11
        $query_total = $this->conn->query("SELECT COUNT(OrderID) AS total_order FROM nrz_orders_current WHERE NurseID = '" . $nurse_id . "' AND OrderStatusID IN (4,5,6,7,11) AND Active=1");
        $row1 = $query_total->fetch_assoc();

        $query_cancel = $this->conn->query("SELECT COUNT(a.NurseAcceptID) AS total_accept 
											FROM nrz_orders_nurse_accept a
											INNER JOIN nrz_orders_current b ON b.OrderID=a.OrderID
											WHERE b.OrderStatusID=7 AND a.NurseID = '" . $nurse_id . "' ");

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
     * Get bills nurse (HOME)
     */
    public function getBills($nurse_id)
    {
        $query_total = $this->conn->query("SELECT IFNULL(SUM(CompanyRevenue),0) AS total_bill FROM nrz_orders_current WHERE NurseID = '" . $nurse_id . "' AND OrderStatusID=7 AND Active=1 AND PaymentTypeID=1 ");
        $row1 = $query_total->fetch_assoc();

        $query_paid = $this->conn->query("SELECT IFNULL(SUM(Total),0) AS total_paid FROM nrz_payment_confirmations WHERE NurseID = '" . $nurse_id . "' AND Status=1 ");
        $row2 = $query_paid->fetch_assoc();

        $total_bill = $row1['total_bill'];
        $total_paid = $row2['total_paid'];

        if ($total_bill > 0) {
            $total = ($total_bill - $total_paid);
            return $total;
        } else {
            return 0;
        }
    }

    /**
     * Get nurse categories
     */
    public function getCategories()
    {
        $query_get = $this->conn->query("SELECT * FROM nrz_categories WHERE Active = 1");
        return $query_get;
    }

    /**
     * Get Bank Account
     */
    public function getBankAccount()
    {
        $query_get = $this->conn->query("SELECT a.*, b.Image AS image FROM `master_payment_account` a INNER JOIN master_bank b ON a.BankID = b.BankID WHERE a.Active = 1");
        return $query_get;
    }

    /**
     * Check nurse password
     */
    public function checkUserPassword($nurse_id, $password, $nurse_type_header)
    {
        if ($nurse_type_header == 2) {
            $table = 'kanopi_nrz_nurses';
        } else {
            $table = 'nrz_nurses';
        }
        $check = $this->conn->query("SELECT * FROM " . $table . " WHERE NurseID = '" . $nurse_id . "' AND Active=1 ");

        if (mysqli_num_rows($check) > 0) {

            $row = $check->fetch_assoc();
            $salt = $row['PasswordSalt'];
            $encrypted_password = $this->checkhashSSHA($salt, $password);

            $check_pass = $this->conn->query("SELECT * FROM " . $table . " WHERE NurseID = '" . $nurse_id . "' AND Password='" . $encrypted_password . "' ");
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
    public function checkUserPasswordForgot($nurse_id, $password, $nurse_type_header)
    {
        if ($nurse_type_header == 2) {
            $table = 'kanopi_nrz_nurses';
        } else {
            $table = 'nrz_nurses';
        }
        $check = $this->conn->query("SELECT * FROM " . $table . " WHERE NurseID = '" . $nurse_id . "' AND Active=1 ");

        if (mysqli_num_rows($check) > 0) {

            $check_pass = $this->conn->query("SELECT * FROM " . $table . " WHERE NurseID = '" . $nurse_id . "' AND ForgotPassword='" . $password . "' ");

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
     * Check if nurse exist
     */
    public function checkNurseLogin($phone, $password)
    {
        $check = $this->conn->query("SELECT * FROM nrz_nurses WHERE Phone = '" . $phone . "' AND Active=1 ");
        if (mysqli_num_rows($check) > 0) {

            $row = $check->fetch_assoc();
            $salt = $row['PasswordSalt'];
            $encrypted_password = $this->checkhashSSHA($salt, $password);

            $check_pass = $this->conn->query("SELECT * FROM nrz_nurses WHERE Phone = '" . $phone . "' AND Password='" . $encrypted_password . "' ");

            if (mysqli_num_rows($check_pass) > 0) {
                //Generate new token
                $new_token = $this->generateToken();
                $upd = $this->conn->query("UPDATE nrz_nurses SET IsLogin=1, Token='" . $new_token . "' WHERE Phone='" . $phone . "' AND Active=1 ");

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
    public function checkNurseLoginByForgot($phone, $password)
    {

        $now = $this->get_chat_time();

        $check_pass = $this->conn->query("SELECT * FROM nrz_nurses WHERE Phone = '" . $phone . "' AND ForgotPassword='" . $password . "' AND ForgotPasswordExpired > STR_TO_DATE('" . $now . "', '%Y-%m-%d %H:%i:%s') AND Active=1 ");

        if (mysqli_num_rows($check_pass) > 0) {
            $new_token = $this->generateToken();
            $upd = $this->conn->query("UPDATE nrz_nurses SET IsLogin=1, Token='" . $new_token . "' WHERE Phone='" . $phone . "' AND Active=1 ");
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
     * Check login by new generated pass (forgot pass)
     */
    public function checkNurseLoginByForgot2($phone, $password)
    {

        $now = $this->get_chat_time();

        $check_pass = $this->conn->query("SELECT * FROM nrz_nurses WHERE Phone = '" . $phone . "' AND ForgotPassword='" . $password . "' AND ForgotPasswordExpired > STR_TO_DATE('" . $now . "', '%Y-%m-%d %H:%i:%s') AND Active=1 ");

        if (mysqli_num_rows($check_pass) > 0) {
            $new_token = $this->generateToken();
            $upd = $this->conn->query("UPDATE nrz_nurses SET IsLogin=1, Token='" . $new_token . "' WHERE Phone='" . $phone . "' AND Active=1 ");
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
     * Check login by new generated pass (forgot pass)
     */
    public function checkNurseLoginByForgot3($phone, $password)
    {

        $now = $this->get_chat_time();

        $check_pass = $this->conn->query("SELECT * FROM kanopi_nrz_nurses WHERE Phone = '" . $phone . "' AND ForgotPassword='" . $password . "' AND ForgotPasswordExpired > STR_TO_DATE('" . $now . "', '%Y-%m-%d %H:%i:%s') AND Active=1 ");

        if (mysqli_num_rows($check_pass) > 0) {
            $new_token = $this->generateToken();
            $upd = $this->conn->query("UPDATE kanopi_nrz_nurses SET IsLogin=1, Token='" . $new_token . "' WHERE Phone='" . $phone . "' AND Active=1 ");
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
     * Check login by new generated pass (forgot pass)
     */
    public function checkNurseLoginByForgot4($register_no, $password)
    {

        $now = $this->get_chat_time();

        $check_pass = $this->conn->query("SELECT * FROM kanopi_nrz_nurses WHERE RegisterNo = '" . $register_no . "' AND ForgotPassword='" . $password . "' AND ForgotPasswordExpired > STR_TO_DATE('" . $now . "', '%Y-%m-%d %H:%i:%s') AND Active=1 ");

        if (mysqli_num_rows($check_pass) > 0) {
            $new_token = $this->generateToken();
            $upd = $this->conn->query("UPDATE kanopi_nrz_nurses SET IsLogin=1, Token='" . $new_token . "' WHERE RegisterNo='" . $register_no . "' AND Active=1 ");
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
     * Check if payment confirmation exist
     */
    public function checkConfirmPaymentPending($nurse_id)
    {
        $query = $this->conn->query("SELECT * FROM nrz_payment_confirmations WHERE NurseID = '" . $nurse_id . "' AND Status=0 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if token exist
     */
    public function checkToken($token, $nurse_id, $nurse_type_header = 1)
    {
        if ($nurse_type_header == 2) {
            $query = $this->conn->query("SELECT * FROM kanopi_nrz_nurses WHERE Token = '" . $token . "' AND NurseID = '" . $nurse_id . "' AND Active=1 ");
        } else {
            $query = $this->conn->query("SELECT * FROM nrz_nurses WHERE Token = '" . $token . "' AND NurseID = '" . $nurse_id . "' AND Active=1 ");

        }

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if nurse exist
     */
    public function checkNurseRegister($phone)
    {
        $query = $this->conn->query("SELECT * FROM nrz_nurses WHERE Phone = '" . $phone . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Check if kanopi nurse exist
     */
    public function checkNurseRegister2($phone)
    {
        $query = $this->conn->query("SELECT * FROM kanopi_nrz_nurses WHERE Phone = '" . $phone . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if kanopi nurse exist by register no
     */
    public function checkNurseRegister3($register_no)
    {
        $query = $this->conn->query("SELECT * FROM kanopi_nrz_nurses WHERE RegisterNo = '" . $register_no . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if nurse verified
     */
    public function checkNurseVerified($nurse_id)
    {
        $query = $this->conn->query("SELECT * FROM nrz_nurses WHERE NurseID = '" . $nurse_id . "' AND Active=1 AND Verified=1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check nurse by ID
     */
    public function checkNurseById($id, $nurse_type_header = 1)
    {
        if ($nurse_type_header == 2) {
            $query = $this->conn->query("SELECT * FROM kanopi_nrz_nurses WHERE NurseID = '" . $id . "' AND Active=1 ");
        } else {
            $query = $this->conn->query("SELECT * FROM nrz_nurses WHERE NurseID = '" . $id . "' AND Active=1 ");
        }

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check nurse education by ID
     */
    public function checkNurseEducationById($edu_id, $nurse_type_header = 1)
    {
        if ($nurse_type_header == 2) {
            $query = $this->conn->query("SELECT * FROM kanopi_nrz_nurse_educations WHERE NurseEducationID = '" . $edu_id . "' AND Active=1 ");
        } else {
            $query = $this->conn->query("SELECT * FROM nrz_nurse_educations WHERE NurseEducationID = '" . $edu_id . "' AND Active=1 ");
        }

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check nurse experience by ID
     */
    public function checkNurseExperienceById($exp_id, $nurse_type_header = 1)
    {
        if ($nurse_type_header == 2) {
            $query = $this->conn->query("SELECT * FROM kanopi_nrz_nurse_experiences WHERE NurseExperienceID = '" . $exp_id . "' AND Active=1 ");
        } else {
            $query = $this->conn->query("SELECT * FROM nrz_nurse_experiences WHERE NurseExperienceID = '" . $exp_id . "' AND Active=1 ");
        }

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if nurse location tracking if exist
     */
    public function checkNurseLocationExist($nurse_id)
    {
        $query = $this->conn->query("SELECT * FROM nrz_nurse_location WHERE NurseID = '" . $nurse_id . "' ");

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
            $query = $this->conn->query("SELECT * FROM nrz_orders_current WHERE OrderID = '" . $order_id . "' AND Active=1 AND OrderStatusID=" . $order_status_id);
        } else {
            $query = $this->conn->query("SELECT 
    									a.*, 
    									b.CategoryName, 
    									c.PaymentType
    									FROM nrz_orders_current a
    									LEFT JOIN nrz_categories b ON b.CategoryID = a.CategoryID
    									LEFT JOIN master_payment_type c ON c.PaymentTypeID = a.PaymentTypeID
    									WHERE OrderID = '" . $order_id . "' AND a.Active=1 ");
        }

        if (mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return false;
        }
    }

    /**
     * Check if nurse was accept offer
     */
    public function checkNurseAcceptOffer($order_id, $nurse_id)
    {
        $query = $this->conn->query("SELECT * FROM nrz_orders_nurse_accept WHERE NurseID = '" . $nurse_id . "' AND OrderID = '" . $order_id . "'");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if nurse was decline offer
     */
    public function checkNurseDeclineOffer($order_id, $nurse_id)
    {
        $query = $this->conn->query("SELECT * FROM nrz_orders_nurse_decline WHERE NurseID = '" . $nurse_id . "' AND OrderID = '" . $order_id . "' ");

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
        $query = $this->conn->query("SELECT * FROM nrz_nurses WHERE Phone = '" . $phone . "' AND ActivationCode='" . $code . "' ");

        if (mysqli_num_rows($query) > 0) {
            $referal_id = $this->generateToken(8);
            $this->conn->query("UPDATE nrz_nurses SET Active = 1, IsLogin=1, ReferralID='" . $referal_id . "' WHERE Phone = '" . $phone . "' AND ActivationCode='" . $code . "' ");
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

        $query_sms = $this->conn->query("SELECT FirstName, Phone, ActivationCode, Active FROM nrz_nurses WHERE Phone = '" . $phone . "' ");
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
     * Create New Nurse Education
     */
    public function createEducation($nurse_id, $university, $year, $degree, $nurse_type_header = 1)
    {
        $check = $this->checkNurseById($nurse_id, $nurse_type_header);
        if ($check) {
            if ($nurse_type_header == 2) {
                $insert = $this->conn->query("INSERT INTO kanopi_nrz_nurse_educations 
													(NurseID,
													University,
													Degree,											
													GraduationYear,
													CreatedDate
													) 
												VALUES 
													('" . $nurse_id . "',
													'" . $university . "', 
													'" . $degree . "', 
													'" . $year . "',
													'" . $this->get_current_time() . "'
													) ");

            } else {
                $insert = $this->conn->query("INSERT INTO nrz_nurse_educations 
												(NurseID,
												University,
												Degree,											
												GraduationYear,
												CreatedDate
												) 
											VALUES 
												('" . $nurse_id . "',
												'" . $university . "', 
												'" . $degree . "', 
												'" . $year . "',
												'" . $this->get_current_time() . "'
												) ");
            }

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
     * Create New Nurse Experience
     */
    public function createExperience($nurse_id, $institute, $entry_date, $out_date, $job_desk, $nurse_type_header = 1)
    {

        $check = $this->checkNurseById($nurse_id, $nurse_type_header);
        if ($check) {
            if ($nurse_type_header == 2) {
                $insert = $this->conn->query("INSERT INTO kanopi_nrz_nurse_experiences
											(NurseID,
											InstituteName,
											EntryDate,											
											OutDate,
											JobDesk,
											CreatedDate
											) 
										VALUES 
											('" . $nurse_id . "',
											'" . $institute . "', 
											'" . $entry_date . "', 
											'" . $out_date . "',
											'" . $job_desk . "',
											'" . $this->get_current_time() . "'
											) ");

            } else {
                $insert = $this->conn->query("INSERT INTO nrz_nurse_experiences
											(NurseID,
											InstituteName,
											EntryDate,											
											OutDate,
											JobDesk,
											CreatedDate
											) 
										VALUES 
											('" . $nurse_id . "',
											'" . $institute . "', 
											'" . $entry_date . "', 
											'" . $out_date . "',
											'" . $job_desk . "',
											'" . $this->get_current_time() . "'
											) ");

            }

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
     * Update New Password
     */
    public function updatePassword($nurse_id, $new_password, $nurse_type_header = 1)
    {

        $hash = $this->hashSSHA($new_password);
        $encrypted_password = $hash["encrypted"]; // encrypted new password
        $salt_password = $hash["salt"]; // salt new

        if ($nurse_type_header == 2) {
            $update = $this->conn->query("UPDATE kanopi_nrz_nurses SET 
										Password	 = '" . $encrypted_password . "',
										PasswordSalt = '" . $salt_password . "'
									WHERE 
										NurseID = '" . $nurse_id . "' AND Active=1 ");

        } else {
            $update = $this->conn->query("UPDATE nrz_nurses SET 
										Password	 = '" . $encrypted_password . "',
										PasswordSalt = '" . $salt_password . "'
									WHERE 
										NurseID = '" . $nurse_id . "' AND Active=1 ");

        }

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

        $update = $this->conn->query("UPDATE nrz_nurses SET 
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
     * Forgot Password
     */
    public function forgotPassword2($phone)
    {

        $new_pass = $this->randomPassword(8);
        $expired_date = date('Y-m-d h:i:s', strtotime('+1 days'));

        $update = $this->conn->query("UPDATE kanopi_nrz_nurses SET 
										ForgotPassword	 		= '" . $new_pass . "',
										ForgotPasswordExpired 	= '" . $expired_date . "'
									WHERE 
										Phone = '" . $phone . "' AND Active=1 ");

        if ($update) {
            $this->send_sms_password2($phone, $new_pass);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update Nurse Education
     */
    public function updateEducation($edu_id, $nurse_id, $university, $year, $degree, $nurse_type_header = 1)
    {

        $check = $this->checkNurseEducationById($edu_id, $nurse_type_header);
        if ($check) {
            if ($nurse_type_header == 2) {
                $update = $this->conn->query("UPDATE kanopi_nrz_nurse_educations SET 
											University 		= '" . $university . "',
											GraduationYear 	= '" . $year . "',
											Degree 			= '" . $degree . "',
											ModifiedDate	= '" . $this->get_current_time() . "'
										WHERE 
											NurseEducationID = '" . $edu_id . "'");
            } else {
                $update = $this->conn->query("UPDATE nrz_nurse_educations SET 
											University 		= '" . $university . "',
											GraduationYear 	= '" . $year . "',
											Degree 			= '" . $degree . "',
											ModifiedDate	= '" . $this->get_current_time() . "'
										WHERE 
											NurseEducationID = '" . $edu_id . "'");
            }

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
     * Update Nurse Experience
     */
    public function updateExperience($exp_id, $nurse_id, $institute, $entry_date, $out_date, $job_desk, $nurse_type_header = 1)
    {

        $check = $this->checkNurseExperienceById($exp_id, $nurse_type_header);
        if ($check) {
            if ($nurse_type_header == 2) {
                $update = $this->conn->query("UPDATE kanopi_nrz_nurse_experiences SET 
											InstituteName 	= '" . $institute . "',
											EntryDate	 	= '" . $entry_date . "',
											OutDate			= '" . $out_date . "',
											JobDesk			= '" . $job_desk . "',
											ModifiedDate	= '" . $this->get_current_time() . "'
										WHERE 
											NurseExperienceID = '" . $exp_id . "'");
            } else {
                $update = $this->conn->query("UPDATE nrz_nurse_experiences SET 
											InstituteName 	= '" . $institute . "',
											EntryDate	 	= '" . $entry_date . "',
											OutDate			= '" . $out_date . "',
											JobDesk			= '" . $job_desk . "',
											ModifiedDate	= '" . $this->get_current_time() . "'
										WHERE 
											NurseExperienceID = '" . $exp_id . "'");
            }

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
     * Delete Nurse Education
     */
    public function deleteEducation($edu_id, $nurse_type_header = 1)
    {

        $check = $this->checkNurseEducationById($edu_id, $nurse_type_header);
        if ($check) {
            if ($nurse_type_header == 2) {
                $update = $this->conn->query("UPDATE kanopi_nrz_nurse_educations SET 
											Active 	= 0,
											ModifiedDate = '" . $this->get_current_time() . "'
										WHERE 
											NurseEducationID = '" . $edu_id . "'");
            } else {
                $update = $this->conn->query("UPDATE nrz_nurse_educations SET 
											Active 	= 0,
											ModifiedDate = '" . $this->get_current_time() . "'
										WHERE 
											NurseEducationID = '" . $edu_id . "'");
            }

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
     * Delete Nurse Education
     */
    public function deleteExperience($exp_id, $nurse_type_header = 1)
    {

        $check = $this->checkNurseExperienceById($exp_id, $nurse_type_header);
        if ($check) {
            if ($nurse_type_header == 2) {
                $update = $this->conn->query("UPDATE kanopi_nrz_nurse_experiences SET 
											Active 	= 0,
											ModifiedDate = '" . $this->get_current_time() . "'
										WHERE 
											NurseExperienceID = '" . $exp_id . "'");

            } else {
                $update = $this->conn->query("UPDATE nrz_nurse_experiences SET 
											Active 	= 0,
											ModifiedDate = '" . $this->get_current_time() . "'
										WHERE 
											NurseExperienceID = '" . $exp_id . "'");

            }

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

        $update = $this->conn->query("UPDATE nrz_nurses SET 
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
     * Update FirebaseID Kanopi
     */
    public function updateFirebase3($phone, $firebase_id)
    {

        $update = $this->conn->query("UPDATE kanopi_nrz_nurses SET 
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
     * Update FirebaseID Kanopi
     */
    public function updateFirebase4($register_no, $firebase_id)
    {

        $update = $this->conn->query("UPDATE kanopi_nrz_nurses SET 
										FirebaseID 		= '" . $firebase_id . "'
									WHERE 
										RegisterNo = '" . $register_no . "'");

        if ($update) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update Nurse Profile
     */
    public function updateProfile($nurse_id, $firstname, $lastname, $phone, $email, $birthdate, $gender, $no_ktp, $no_str, $location, $nurse_type_header = 1)
    {
        $check = $this->checkNurseById($nurse_id, $nurse_type_header);
        if ($check) {
            if ($nurse_type_header == 1) {
                $update = $this->conn->query("UPDATE nrz_nurses SET 
											FirstName 	= '" . $firstname . "',
											LastName 	= '" . $lastname . "',
											Phone 		= '" . $phone . "',
											Email 		= '" . $email . "',
											BirthDate 	= '" . $birthdate . "',
											Gender 		= '" . $gender . "',
											No_KTP 		= '" . $no_ktp . "',
											No_STR 		= '" . $no_str . "',
											Location 	= '" . $location . "',
											ModifiedBy	= '" . $nurse_id . "',
											ModifiedDate= '" . $this->get_current_time() . "'
										WHERE 
											NurseID = '" . $nurse_id . "'");
            } else {
                $update = $this->conn->query("UPDATE kanopi_nrz_nurses SET 
											FirstName 	= '" . $firstname . "',
											LastName 	= '" . $lastname . "',
											Phone 		= '" . $phone . "',
											Email 		= '" . $email . "',
											BirthDate 	= '" . $birthdate . "',
											Gender 		= '" . $gender . "',
											No_KTP 		= '" . $no_ktp . "',
											No_STR 		= '" . $no_str . "',
											Location 	= '" . $location . "',
											ModifiedBy	= '" . $nurse_id . "',
											ModifiedDate= '" . $this->get_current_time() . "'
										WHERE 
											NurseID = '" . $nurse_id . "'");
            }

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
     * Process Nurse Location Tracking
     */
    public function trackLocation($nurse_id, $latitude, $longitude, $accuracy)
    {

        $exist = $this->checkNurseLocationExist($nurse_id);
        if ($exist) {
            //Update Location
            $update = $this->conn->query("UPDATE nrz_nurse_location SET 
											Latitude 	= '" . $latitude . "',
											Longitude 	= '" . $longitude . "',
											Accuracy 	= '" . $accuracy . "',
											TrackDate	= '" . $this->get_current_time() . "'
										WHERE 
											NurseID = '" . $nurse_id . "' ");

            if ($update) {
                return true;
            } else {
                return false;
            }
        } else {
            //Create Location
            $insert = $this->conn->query("INSERT INTO nrz_nurse_location 
										(NurseID,
										Latitude,
										Longitude,
										Accuracy,
										TrackDate
										) 
									VALUES 
										('" . $nurse_id . "',
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
     * Process Nurse Online/Offline
     */
    public function updateStatus($nurse_id, $status)
    {

        $exist = $this->checkNurseLocationExist($nurse_id);
        if ($exist) {
            //Update Location
            $update = $this->conn->query("UPDATE nrz_nurse_location SET 
											Active 		= '" . $status . "',
											TrackDate	= '" . $this->get_current_time() . "'
										WHERE 
											NurseID = '" . $nurse_id . "' ");

            if ($update) {
                return true;
            } else {
                return false;
            }
        } else {
            //Create Location
            $insert = $this->conn->query("INSERT INTO nrz_nurse_location 
										(NurseID,
										Latitude,
										Longitude,
										Accuracy,
										Active,
										TrackDate
										) 
									VALUES 
										('" . $nurse_id . "',
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
     * Create Nurse Accept Offer
     */
    public function acceptOffer($order_id, $nurse_id)
    {

        $exist = $this->checkNurseAcceptOffer($order_id, $nurse_id);
        if (!$exist) {
            $insert = $this->conn->query("INSERT INTO nrz_orders_nurse_accept 
											(OrderID, 
											NurseID,
											AcceptDate) 
										VALUES 
											('" . $order_id . "', 
											'" . $nurse_id . "',
											'" . $this->get_current_time() . "'
											) ");

            if ($insert) {
                $this->sendNotif_NurseAccept($order_id, $nurse_id);
                return true;
            } else {
                return false;
            }
        } else {
            $this->sendNotif_NurseAccept($order_id, $nurse_id);
            return true;
        }
    }

    /**
     * Create Nurse Decline Offer
     */
    public function declineOffer($order_id, $nurse_id)
    {

        $exist = $this->checkNurseDeclineOffer($order_id, $nurse_id);
        if (!$exist) {
            $insert = $this->conn->query("INSERT INTO nrz_orders_nurse_decline 
											(OrderID, 
											NurseID,
											DeclineDate) 
										VALUES 
											('" . $order_id . "', 
											'" . $nurse_id . "',
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

    public function sendNotif_NurseAccept($order_id, $nurse_id)
    {

        //Get Data
        $query = $this->conn->query("SELECT a.NurseID,
											a.FirstName, 
											a.LastName, 
											a.BirthDate, 
											a.YearExperience
									FROM nrz_nurses a
									WHERE 
										a.NurseID = '" . $nurse_id . "' ");

        if (mysqli_num_rows($query) > 0) {

            $row = $query->fetch_assoc();
            $rating = $this->getRating($nurse_id);

            $custom_data = array(
                'type' => '1',
                'body' => "Ada perawat menerima order kamu",
                'title' => "Order Anda",
                'OrderID' => $order_id,
                'NurseID' => $row['NurseID'],
                'FirstName' => $row['FirstName'],
                'LastName' => $row['LastName'],
                'YearExperience' => $row['YearExperience'],
                'BirthDate' => $row['BirthDate'],
                'Rating' => $rating,
                'Educations' => ["Ganti Perban", "Perawatan Luka Diabetes"],
                'Experiences' => ["Ganti Perban", "Perawatan Luka Diabetes"]
            );

            //Notify Nurse
            $query_nrz = $this->conn->query("SELECT a.OrderID, b.FirebaseID FROM nrz_orders_current a INNER JOIN master_users b ON b.UserID=a.UserID WHERE a.OrderID = '" . $order_id . "' ");
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

        $insert = $this->conn->query("INSERT INTO nrz_chat 
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
     * Create Chat Image Message to Nurse
     */
    public function createChatFile($order_id, $message, $from, $to, $filename)
    {

        $insert = $this->conn->query("INSERT INTO nrz_chat 
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
											(CASE WHEN Filename IS NOT NULL THEN CONCAT('" . $this->uploaddir . "', '/chats/nurse_orders/', OrderID,'/',Filename) ELSE '' END) AS url
										FROM nrz_chat
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
            'type' => '2', //chat
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

        if ($type == "nrz") {
            //Notify Nurse
            $query_nrz = $this->conn->query("SELECT * FROM nrz_nurses WHERE NurseID='" . $send_to . "' AND Active=1 ");
            if (mysqli_num_rows($query_nrz) > 0) {
                $row_nrz = $query_nrz->fetch_assoc();

                $this->sendNotification_Nurse($row_nrz['FirebaseID'], $custom_data);
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
     * Create Payment Confirmation
     */
    public function confirmPaymentBill($nurse_id, $payment_accound_id, $bank_name, $account_name, $account_no, $trf_date, $total)
    {

        $insert = $this->conn->query("INSERT INTO nrz_payment_confirmations 
										(NurseID,
										SenderBankName,
										SenderBankAccName,
										SenderBankAccNo,
										TransferDate,
										PaymentAccountID,
										Total,
										Status
										) 
									VALUES 
										('" . $nurse_id . "',
										'" . $bank_name . "', 
										'" . $account_name . "', 
										'" . $account_no . "',
										'" . $this->get_current_time() . "',
										'" . $payment_accound_id . "',
										'" . $total . "',
										0
										) ");

        if ($insert) {
            return $this->conn->insert_id;
        } else {
            return null;
        }
    }

    /**
     * Process Status On Schedule
     */
    public function processOnSchedule($order_id)
    {

        $update = $this->conn->query("UPDATE nrz_orders_current SET 
										OrderStatusID = '5'
									WHERE 
										OrderID = '" . $order_id . "'");

        if ($update) {
            $dt = $this->getNurseByOrderID($order_id);
            if ($dt != null) {
                $dt = $dt->fetch_assoc();

                //create order log
                $order_id = $order_id;
                $order_status_id = 5;
                $description = 'Log Order Nurse, created by sistem api';
                $nurse_id = $dt['NurseID'];
                $this->createOrderLog($order_id, $order_status_id, $nurse_id, $description);
            }

            $this->historyNotification($order_id);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Process Status On Progress
     */
    public function processOnProgress($order_id)
    {

        $update = $this->conn->query("UPDATE nrz_orders_current SET 
										OrderStatusID = '6'
									WHERE 
										OrderID = '" . $order_id . "'");

        if ($update) {
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

        $update = $this->conn->query("UPDATE nrz_orders_current SET 
										OrderStatusID = '7'
									WHERE 
										OrderID = '" . $order_id . "'");

        if ($update) {
            $dt = $this->getNurseByOrderID($order_id);
            if ($dt != null) {
                $dt = $dt->fetch_assoc();

                //create order log
                $order_id = $order_id;
                $order_status_id = 7;
                $description = 'Log Order Nurse, created by sistem api';
                $nurse_id = $dt['NurseID'];
                $this->createOrderLog($order_id, $order_status_id, $nurse_id, $description);
            }
            $this->historyNotification($order_id);
            return true;
        } else {
            return false;
        }
    }

    public function historyNotification($order_id)
    {

        $query = $this->conn->query("SELECT * FROM nrz_orders_current WHERE OrderID = '" . $order_id . "' AND Active=1 LIMIT 1 ");

        if (mysqli_num_rows($query) > 0) {
            $row = $query->fetch_assoc();
            $user_id = $row['UserID'];
            $status_id = $row['OrderStatusID'];

            $message = "";
            if ($status_id == "5") {
                $message = "Sedang dalam penjadwalan";
            } else if ($status_id == "6") {
                $message = "Sedang dalam penanganan";
            } else if ($status_id == "7") {
                $message = "Telah selesai";
            }

            $custom_data = array(
                'type' => '3', //History
                'body' => $message,
                'title' => "Status Pemesanan Anda",
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
     * Process Logout
     */
    public function processLogout($nurse_id, $nurse_type_header)
    {
        if ($nurse_type_header == 2) {
            $update = $this->conn->query("UPDATE kanopi_nrz_nurses SET 
										IsLogin = '0',
										FirebaseID = '',
										Token = ''
									WHERE 
										NurseID = '" . $nurse_id . "'");

        } else {
            $update = $this->conn->query("UPDATE nrz_nurses SET 
										IsLogin = '0',
										FirebaseID = '',
										Token = ''
									WHERE 
										NurseID = '" . $nurse_id . "'");

        }

        if ($update) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Process Withdraw
     */
    public function createWithdraw($nurse_id, $total)
    {

        $insert = $this->conn->query("INSERT INTO nrz_withdraw 
									(NurseID, 
									Total, 
									WithdrawTime
									) 
								VALUES 
									('" . $nurse_id . "', 
									'" . $total . "',
									'" . $this->get_current_time() . "'
									) ");

        if ($insert) {
            return true;
        } else {
            return false;
        }
    }

    public function updateImageNurse($nurse_id, $image, $nurse_type_header)
    {
        if ($nurse_type_header == 2) {
            $update = $this->conn->query("UPDATE kanopi_nrz_nurses SET 
										image = '" . $image . "'
									WHERE 
										NurseID = '" . $nurse_id . "'");

        } else {


        }

        if ($update) {
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
    function getNurseById($id)
    {
        $q = $this->conn->query("SELECT * FROM nrz_nurses WHERE NurseID = '" . $id . "' LIMIT 1");

        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }

    /*created by elim*/
    /**
     * Function Get nurse withdraw history
     * @param : $doctorId
     * Method: Post
     * returns data
     */
    function getNrzWithdraw($nurseId)
    {
        // $q = $this->conn->query("SELECT * FROM master_payment_account WHERE	Active = '1'");

        $q = $this->conn->query("SELECT 
								a.GrandTotal, 
								a.Total, 
								a.BankFee, 
								a.ModifiedDate, 
								a.CreatedDate AS nrz_withdraw_created_at, 
								a.WithdrawStatusID,
								a.WithdrawID, 
								b.WithdrawStatus, 
								c.BankAccNo, 
								c.BankAccName,
								c.BankBranch, 
								d.BankName 
			FROM nrz_withdraw a 
			JOIN master_withdraw_status b ON a.WithdrawStatusID = b.WithdrawStatusID
			JOIN nrz_nurses c ON a.NurseID = c.NurseID
			JOIN master_bank d ON d.BankID = c.BankID
			WHERE a.NurseID = $nurseId 
			ORDER BY WithdrawID DESC
			");
        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }

    /**
     * Function update nurse bank account
     * @param : $bankAccName, $bankAccNo, $bankBranch, $bankAccountId,$doctorId
     * returns data
     */
    function updateNurseBankAccount($bankAccName, $bankAccNo, $bankBranch, $bankAccountId, $nurseId)
    {
        $upd = $this->conn->query("UPDATE nrz_nurses SET 
							BankAccName= '" . $bankAccName . "', 
							BankAccNo='" . $bankAccNo . "', 
							BankBranch='" . $bankBranch . "',
							BankID='" . $bankAccountId . "'
							WHERE NurseID='" . $nurseId . "'");

        if ($upd) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Function get nurse bank account
     * @param : $nurse_Id
     * returns data
     */
    function getNurseById2($nurse_id)
    {
        $q = $this->conn->query("SELECT * FROM nrz_nurses WHERE NurseID='" . $nurse_id . "'");

        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }

    }

    /**
     * Function get data master_bank, data master_payment_account, data doc_doctor
     * @param : $id
     * returns data
     */
    function getTrsFeeStatusByNurseBankId($id)
    {
        $q = $this->conn->query("SELECT a.BankID, 
								a.BankName, 
								a.Image, 
								b.BankID AS mpa_bank_id, 
								c.NurseID, 
								c.BankID AS nurse_bank_id, 
								c.BankAccNo AS nurse_bank_acc_no, 
								c.BankAccName AS nurse_bank_acc_name 
								FROM master_bank a 
								JOIN master_payment_account b ON b.BankID = a.BankID
								JOIN nrz_nurses c ON c.BankID =  b.BankID
								WHERE (b.Active = 1 OR b.Active IS NULL) AND a.Active = 1 AND c.NurseID = $id");

        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }

    /**
     * Process Withdraw if withdrawStatusID = 0 dan WithdrawStatusID = 1
     * @param $id , $total, $bankFee, $grandTotal
     */
    public function createWithdrawNurse($id, $total, $bankFee, $grandTotal)
    {

        $q = $this->conn->query("SELECT * FROM nrz_withdraw WHERE NurseID ='" . $id . "' AND WithdrawStatusID = 0 AND Active = 1");

        if (mysqli_num_rows($q) > 0) {
            return 2;
        } else {
            /*get transaction no*/
            $check_trid = $this->conn->query("SELECT IFNULL(MAX(Right(WithdrawNo,8)),0) AS WithdrawNo
											FROM nrz_withdraw 
											WHERE 
												DATE_FORMAT(CreatedDate, '%m')='" . date('m') . "' AND 
												DATE_FORMAT(CreatedDate, '%Y')='" . date('Y') . "'");
            $check_trid = $check_trid->fetch_assoc();

            $tr_no = $this->incrementTrNo('NRZWD', $check_trid['WithdrawNo']);
            /*end get transaction no*/

            $insert = $this->conn->query("INSERT INTO nrz_withdraw 
										(NurseID, 
										Total, 
										CreatedDate,
										BankFee,
										GrandTotal,
										WithdrawNo
										) 
									VALUES 
										('" . $id . "', 
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

    /**
     * Get dashboard saldo (HOME)
     */
    public function getSaldo($id, $company_fee_percent)
    {
        $mode = 2;
        $company_fee_percent /= 100;
        if ($mode == 1) {
            // $query_total = $this->conn->query("SELECT IFNULL(SUM(TotalPrice),0) AS total_saldo FROM nrz_orders_current WHERE NurseID = '".$id."' AND OrderStatusID=6 AND Active=1 ");

            $query_total = $this->conn->query("SELECT IFNULL(SUM(TotalPrice) - (SUM(TotalPrice)*$company_fee_percent),0) AS total_saldo FROM nrz_orders_current WHERE NurseID = '" . $id . "' AND OrderStatusID=6 AND Active=1 AND PaymentTypeID NOT IN (1) ");

            $row1 = $query_total->fetch_assoc();

            $total_saldo = $row1['total_saldo'];

            if ($total_saldo > 0) {
                return $total_saldo;
            } else {
                return 0;
            }
        } else {
            //user this
            $query_total = $this->conn->query("SELECT IFNULL(SUM(NurseRevenue),0) AS total_saldo, IFNULL(SUM(TransportPrice),0) AS total_trans FROM nrz_orders_current WHERE NurseID = '" . $id . "' AND OrderStatusID=7 AND Active=1 AND PaymentTypeID NOT IN (1)");

            $withdraw = $this->conn->query("SELECT IFNULL(SUM(GrandTotal),0) AS total_withdraw FROM nrz_withdraw WHERE NurseID = '" . $id . "' AND WithdrawStatusID != 2 AND Active=1");

            $row1 = $query_total->fetch_assoc();
            $row2 = $withdraw->fetch_assoc();
            $totalS = $row1['total_saldo'] + $row1['total_trans'];
            $total_saldo = $totalS - $row2['total_withdraw'];

            if ($total_saldo > 0) {
                return $total_saldo;
            } else {
                return $total_saldo;
            }
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
     * Function get data master_config
     * @param : $configName
     * returns data
     */
    function getConfig2($configName)
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
    function getConfig3($configName)
    {
        $q = $this->conn->query("SELECT * FROM master_config 
			WHERE ConfigName = '" . $configName . "' AND Active = 1");

        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }

    function checkNurseBillReachMaximum($nurse_id)
    {
        $bills = $this->getBills($nurse_id);
        $max_bill = $this->getConfig('nurse_maximum_bill')->fetch_assoc();

        $max_bill = $max_bill['Value'];
        if ($bills > $max_bill) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Update FirebaseID bu nurse_id
     */
    public function updateFirebase2($nurse_id, $firebase_id)
    {

        $update = $this->conn->query("UPDATE nrz_nurses SET 
										FirebaseID 		= '" . $firebase_id . "'
									WHERE 
										NurseID = '" . $nurse_id . "'");

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
        $query_get = $this->conn->query("SELECT PublishedDate, ArticleID, Title, Caption, CreatedDate, CONCAT('" . $this->uploaddir . "', '/articles/', ArticleID,'.jpg') AS Url FROM master_articles WHERE Active = 1 AND TypeID = 2 ORDER BY PublishedDate DESC " . $limit);

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

    /**
     * Get Kanopi Nurse
     * @param: id
     *return array, false
     */
    public function checkKanopiNurseLoginData($phone, $password)
    {
        $check = $this->conn->query("SELECT * FROM kanopi_nrz_nurses WHERE Phone = '" . $phone . "' AND Active=1 ");

        if (mysqli_num_rows($check) > 0) {

            $row = $check->fetch_assoc();
            $salt = $row['PasswordSalt'];
            $encrypted_password = $this->checkhashSSHA($salt, $password);
            $check_pass = $this->conn->query("SELECT *, Active = 1 as Status FROM kanopi_nrz_nurses WHERE Phone = '" . $phone . "' AND Password='" . $encrypted_password . "' ");

            if (mysqli_num_rows($check_pass) > 0) {
                //Generate new token
                $new_token = $this->generateToken();
                $upd = $this->conn->query("UPDATE kanopi_nrz_nurses SET IsLogin=1, Token='" . $new_token . "' WHERE Phone='" . $phone . "' AND Active=1 ");

                if ($upd) {
                    return $check_pass;
                } else {
                    return 'error 3';
                }
            } else {
                return 'error 2';
            }

        } else {
            return 'error 1';
        }
    }

    /**
     * Get Kanopi Nurse
     * @param: id
     *return array, false
     */
    public function checkKanopiNurseLoginData2($register_no, $password)
    {
        $check = $this->conn->query("SELECT * FROM kanopi_nrz_nurses WHERE RegisterNo = '" . $register_no . "' AND Active=1 ");

        if (mysqli_num_rows($check) > 0) {

            $row = $check->fetch_assoc();
            $salt = $row['PasswordSalt'];
            $encrypted_password = $this->checkhashSSHA($salt, $password);
            $check_pass = $this->conn->query("SELECT *, Active = 1 as Status FROM kanopi_nrz_nurses WHERE RegisterNo = '" . $register_no . "' AND Password='" . $encrypted_password . "' ");

            if (mysqli_num_rows($check_pass) > 0) {
                //Generate new token
                $new_token = $this->generateToken();
                $upd = $this->conn->query("UPDATE kanopi_nrz_nurses SET IsLogin=1, Token='" . $new_token . "' WHERE RegisterNo='" . $register_no . "' AND Active=1 ");

                if ($upd) {
                    return $check_pass;
                } else {
                    return 'error 3';
                }
            } else {
                return 'error 2';
            }

        } else {
            return 'error 1';
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

        $q = $this->conn->query("SELECT a.BankID, a.BankName, a.Image AS image, 
										b.BankID AS mpa_bank_id FROM master_bank a 
										LEFT JOIN master_payment_account b ON b.BankID = a.BankID
										WHERE b.Active = 1 OR b.Active IS NULL AND a.Active = 1");

        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
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

    public function push_notif()
    {
        $this->sendNotification_Patient();
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
     * Function Send SMS new password
     * @param : Phone, New Password
     * returns boolean
     */
    function send_sms_password2($phone, $code)
    {
        $userkey = $this->smsuserkey; //userkey lihat di zenziva
        $passkey = $this->smspasskey; // set passkey di zenziva
        $message = "VTAL: Hi, berikut adalah password baru nurse Kanopi anda: " . $code . ", diharapkan segera ubah password anda";
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

    /**
     * Get nurse payment with transfer base on order_id
     */
    public function getPaymentTransfer($order_id)
    {
        $q = $this->conn->query("SELECT 
    								*
    								FROM nrz_payment_transfers
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

    public function createOrderLog($order_id, $order_status_id, $nurse_id, $description)
    {
        $q = $this->conn->query("INSERT INTO nrz_orders_logs 
									(OrderID,
									CreatedDate,
									CreatedBy,
									OrderStatusID,
									NurseID,
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
     * Get Nurse Data From Order ID
     */
    public function getNurseByOrderID($order_id)
    {

        $query_get = $this->conn->query("SELECT   
											a.OrderID,
											b.FirstName,
											b.LastName,
											b.NurseID
										FROM nrz_orders_current a
										INNER JOIN nrz_nurses b ON a.NurseID = b.NurseID 
										WHERE a.Active=1 AND a.OrderID='" . $order_id . "' ");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get Nurse Data From Order ID
     */
    public function getNrzOrderCurrentByid($order_id)
    {

        $query_get = $this->conn->query("SELECT   
											*
										FROM nrz_orders_current
										WHERE OrderID=" . $order_id . " LIMIT 1");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Function update point nurse
     * @param : NurseID, point
     * returns boolean
     */
    public function updateNursePoint($nurse_id, $point)
    {
        $q = $this->conn->query("UPDATE  nrz_nurses
										SET 
										Point = " . $point . ",
										PointModifiedDate = '" . $this->get_current_time() . "'
										WHERE NurseID=" . $nurse_id . "");

        if ($q) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function update point nurse
     * @param : $order_id, $nurse_id, $amount, $description = '', $created_by = '9-', $modified_by = '9-'
     * returns boolean
     */
    public function insertNursePointLog($order_id, $nurse_id, $amount, $description = '', $created_by = '9-', $modified_by = '9-')
    {
        // $exist = $this->getNrzPointLogByOrderId($order_id);

        // if($exist){
        // 	return false;
        // }else{
        $q = $this->conn->query("INSERT INTO nrz_point_log 
    							(
    								OrderID,
    								NurseID,
    								Amount,
    								Description,
    								CreatedDate,
    								CreatedBy,
    								ModifiedBy
    							) VALUES (
    								" . $order_id . ", 
    								" . $nurse_id . ", 
    								" . $amount . ", 
    								'" . $description . "',
    								'" . $this->get_current_time() . "',
    								'" . $created_by . "',
    								'" . $modified_by . "'
    							)");
        if ($q) {
            return $this->conn->insert_id;
        } else {
            return false;
        }
        // }
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
     * Function insert point user
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
     * Function nurse point log by order_id
     * @param : $order_id
     * returns array
     */
    public function getNrzPointLogByOrderId($order_id)
    {

        $query_get = $this->conn->query("SELECT   
											*
										FROM nrz_point_log
										WHERE OrderID =" . $order_id . "");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return false;
        }
    }

    /**
     * Function nurse point skp
     * @param : $nurse_id
     * returns array
     */
    public function updatePointSKP($nurse_id)
    {
        $last_log = $this->conn->query("SELECT   
											*
										FROM nrz_point_skp_log
										WHERE NurseID = " . $nurse_id . "
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
									FROM nrz_orders_current a
									LEFT JOIN nrz_nurses b ON a.NurseID = b.NurseID
									WHERE a.OrderID > " . $last_id . "
									AND a.NurseID = " . $nurse_id . "
									AND a.OrderStatusID = 7
									ORDER BY a.OrderID DESC
									LIMIT 1
									");

        $count = mysqli_num_rows($q);
        // echo $count;
        if ($count > 0) {
            $q = $q->fetch_assoc();
            $amount_per_skp = $this->getConfig("count_transaction_per_skp_point_nurse")->fetch_assoc();
            $amount_per_skp = $amount_per_skp['Value'];
            $amount_per_skp = 1;
            $point_skp = (int)($count / $amount_per_skp);
            if ($point_skp > 0) {
                $total_point_skp = $q['PointSKP'] + $point_skp;

                $q2 = $this->conn->query("UPDATE nrz_nurses SET 
								PointSKP = " . $total_point_skp . ",
								PointSKPModifiedDate = '" . $this->get_current_time() . "'
								WHERE NurseID = " . $q['NurseID'] . "");

                if ($q2) {
                    $last_order_id = $q['OrderID'];
                    $amount_order = $count;
                    $description = 'Created by system api';
                    $q3 = $this->insertPointSKPLog($last_order_id, $amount_order, $nurse_id, $point_skp, $description);

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
     * Function insert point skp log nurse
     * @param : $last_order_id, $amount_order, $nurse_id, $amount, $description, $created_by
     * returns boolean
     */
    public function insertPointSKPLog($last_order_id, $amount_order, $nurse_id, $amount, $description, $created_by = '9-')
    {

        $q2 = $this->conn->query("INSERT INTO nrz_point_skp_log
								(
									LastOrderID,
									AmountOrder,
    								NurseID,
    								Amount,
    								Description,
    								CreatedDate,
    								CreatedBy
								)VALUES(
									" . $last_order_id . ",
									" . $amount_order . ",
									" . $nurse_id . ",
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
     * Function get nrz_order_log by order_id and order_status_id
     * @param : $order_id, $order_status_id
     * returns array, boolean
     */
    public function getOrderLogNurseByOrderId($order_id, $order_status_id)
    {
        $q = $this->conn->query("SELECT * FROM nrz_orders_logs 
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

    //START NURSE WALLET
    /**
     * Get user wallet by user_id
     * @param : $lab_id
     * returns array or false
     */
    public function getUserWalletByUserId($user_id)
    {
        $q = $this->conn->query("SELECT *
								 FROM nrz_wallet 
								 WHERE NurseID = " . $user_id . "
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
								 FROM nrz_nurses
								 WHERE NurseID = " . $user_id . "
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
                                            nrz_wallet_topup a
                                            LEFT JOIN nrz_wallet_topup_status b ON a.TopUpStatusID = b.UserWalletStatusID 
                                            LEFT JOIN nrz_nurses c ON a.NurseID = c.NurseID
                                            WHERE a.NurseID = '" . $user_id . "'
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
        $data_log = $this->conn->query("select * from nrz_wallet_topup order by OrderID desc LIMIT 1");

        if (mysqli_num_rows($data_log) > 0) {
            $data_result = $data_log->fetch_assoc();
            $order_number = $data_result['OrderNo'];
        } else {
            $last_year = date('y');
            $last_month = date('m');
            $order_number = 'NSW' . $last_year . $last_month . '10000';
        }

        //USW180710000
        $prefix = 'NSW';
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


        $q2 = $this->conn->query("INSERT INTO nrz_wallet_topup(
									NurseID,
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
        $prefix = 'NSW';
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
								 FROM nrz_wallet_topup
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
        $query = $this->conn->query("SELECT * FROM nrz_wallet_topup_payment_transfer WHERE TopUpID = '" . $topup_id . "' AND Status=0 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Create Payment Confirmation (TopUp Order)
     */
    public function confirmPaymentTransferTopUp($nurseid_header, $topup_id, $payment_accound_id, $bank_name, $account_name, $account_no, $trf_date, $total, $kode_unik)
    {
        //4 (Menunggu Verifikasi
        $upd = $this->conn->query("UPDATE nrz_wallet_topup 
									SET TopUpStatusID = 4
									WHERE OrderID = '" . $topup_id . "'");
        $insert = $this->conn->query("INSERT INTO nrz_wallet_topup_payment_transfer 
										(TopUpID,
										SenderBankName,
										SenderBankAccName,
										SenderBankAccNo,
										TransferDate,
										PaymentAccountID,
										Total,										
										Status,
										NurseID,
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
										'" . $nurseid_header . "',
										'" . $kode_unik . "',
										'" . $nurseid_header . "',
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
                                            nrz_wallet_topup
                                            LEFT JOIN nrz_wallet_topup_status ON nrz_wallet_topup.TopUpStatusID = nrz_wallet_topup_status.UserWalletStatusID 
                                            WHERE NurseID = '" . $user_id . "' AND NominalID = '" . $nominal_id . "' ");

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
        $query = $this->conn->query("SELECT * FROM nrz_wallet_topup WHERE OrderID = '" . $topup_id . "'");

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
                                          nrz_wallet_topup 
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
            $user = $this->getNurseWalletByOrderID($topup_id);
            if ($user != null) {
                return $user;
            }
        } else {
            return false;
        }
    }

    public function getNurseWalletByOrderID($topup_id)
    {
        $query_get = $this->conn->query("SELECT   
											a.OrderID,
											a.OrderNo,
											a.Amount,
											a.KodeUnik,
											b.FirstName,
											b.LastName,
											b.NurseID,
											b.Email,
											b.FirebaseID
										FROM nrz_wallet_topup a
										INNER JOIN nrz_nurses b ON a.NurseID = b.NurseID
										WHERE a.OrderID='" . $topup_id . "' ");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }
    //END OF NURSE WALLLET


    /**
     * Get emergency contact
     * @param : $user_id, $latitude, $longitude
     * returns array or false
     */
    public function getEmergencyContact($nurse_id, $latitude, $longitude)
    {
        $q = $this->conn->query("SELECT
                                    a.NurseFamilyName as EmergencyContactName,
                                    a.NurseFamilyID,
                                    a.Telp as EmergencyContactTelp,
                                    CONCAT(b.FirstName, ' ',b.LastName ) as NamaUser,
                                    b.NurseID,
	                                b.Phone 
                                FROM
                                    nrz_nurse_family a
                                    LEFT JOIN nrz_nurses b ON b.NurseID = a.NurseID 
								 WHERE 
								  a.NurseID = " . $nurse_id . " 
								  AND 
								  a.Emergency = 1
								 AND 
								 a.Active = 1
								 LIMIT 1");

        if (mysqli_num_rows($q) > 0) {
            $q = $q->fetch_assoc();
            //create order log
            $EmergencyContactName = $q['EmergencyContactName'];
            $EmergencyContactTelp = $q['EmergencyContactTelp'];
            $EmergencyID = $q['NurseFamilyID'];
            $NamaUser = $q['NamaUser'];
            $NurseID = $q['NurseID'];
            $Phone = $q['Phone'];
            $url_google_maps = "https://www.google.com/maps/search/?api=1&query=" . $latitude . "," . $longitude . "";
            $this->send_sms_emergency($EmergencyContactName,$EmergencyContactTelp,$NamaUser, $url_google_maps);

            $this->saveEmergencyContactLog($EmergencyContactTelp,$EmergencyID,$NurseID,$Phone);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Function Send SMS code
     * @param : Phone, Code, Name
     * returns boolean
     */
    function send_sms_emergency($EmergencyContactName,$EmergencyContactTelp,$NamaUser, $url_google_maps)
    {
        $userkey = $this->smsuserkey; //userkey lihat di zenziva
        $passkey = $this->smspasskey; // set passkey di zenziva
        $message = "VTAL - Pesan Emergency: Kepada Yth. ".$EmergencyContactName.", Keluarga Anda yang bernama " . $NamaUser . ", sedang dalam keadaan Emergency/Darurat. Lokasi terakhir yang dikirim oleh dia adalah: " . $url_google_maps;
        $url = "https://reguler.zenziva.net/apps/smsapi.php";
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, 'userkey=' . $userkey . '&passkey=' . $passkey . '&nohp=' . $EmergencyContactTelp . '&pesan=' . urlencode($message));
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        $results = curl_exec($curlHandle);
        curl_close($curlHandle);
    }

    public function saveEmergencyContactLog($EmergencyContactTelp,$EmergencyID,$NurseID,$Phone)
    {
        $insert = $this->conn->query("INSERT INTO emergency_button_log_nurse 
									(from_id, 
									from_telp, 
									to_id,
									to_telp,
									CreatedDate,
									Status
									) 
								VALUES 
									('" . $NurseID . "',
									'" . $Phone . "',
									'" . $EmergencyID . "', 
									'" . $EmergencyContactTelp . "',
									'" . $this->get_current_time() . "',
									'1'
									) ");

        if ($insert) {
            return true;
        } else {
            return false;
        }
    }

    public function checkEmergencyContactExist($nurse_id)
    {

        $insert = $this->conn->query("SELECT * FROM nrz_nurse_family
									WHERE Emergency = 1 AND NurseID = " . $nurse_id . " ");

        if (mysqli_num_rows($insert) > 0) {
            return true;

        } else {
            return false;
        }
    }


    public function setEmergencyContact($Name,$nurse_id, $Phone, $Email,$Gender)
    {
        $check_emergency_contact = $this->checkEmergencyContactExist($nurse_id);

        if($check_emergency_contact){
            $update = $this->conn->query("UPDATE nrz_nurse_family SET 
									NurseFamilyName 		= '" . $Name . "',
									Telp 		= '" . $Phone . "',
									Email 		= '" . $Email . "',
									Gender 		= '" . $Gender . "',
									ModifiedBy 			= '9-'
								WHERE 
									NurseID = '" . $nurse_id . "' ");
            if ($update) {
                return true;

            } else {
                return false;
            }
        }else{
            $insert = $this->conn->query("INSERT INTO nrz_nurse_family 
										(NurseFamilyName,
										Telp,
										Email,
										NurseID,
										Gender,
										CreatedDate,
										Emergency
										) 
									VALUES 
										('" . $Name . "',
										'" . $Phone . "',
										'" . $Email . "',
										'" . $nurse_id . "',
										'" . $Gender . "',
										'" . $this->get_current_time() . "',
										'1'
										) ");
            if ($insert) {
                return true;

            } else {
                return false;
            }
        }


    }

}

?>