<?php

class Model_pharmacy
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

        define('RAJAONGKIR_KEY', '11557807308cfd0f9da09876b5a63e28');
    }

    // destructor
    function __destruct()
    {


    }

    /**
     * Check if token exist
     */
    public function checkToken($token, $user_id)
    {
        $query = $this->conn->query("SELECT * FROM apt_users WHERE Token = '" . $token . "' AND AptUserID = '" . $user_id . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
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
     * Function get doctor by apt user id
     * @param : AptUserID
     * returns data
     */
    function getPharmacyUserById($id)
    {
        $q = $this->conn->query("SELECT * FROM apt_users WHERE AptUserID = '" . $id . "' LIMIT 1");

        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }


    /**
     * Get medicine list
     */
    public function getMedicines()
    {
        $query_get = $this->conn->query("SELECT
                                            MedicineID,
                                            MedicineType,
                                            MedicineDesc,
                                            factory,
                                            HET,
                                            Active,
                                            Image,
                                            MedicinePackaging,
                                            MedicineSymbol,
                                            JenisBarang,
                                        CASE
                                                
                                                WHEN MedicineName IS NULL THEN
                                                NamaAlkes ELSE MedicineName 
                                            END AS `MedicineName` 
	                                    FROM 
	                                        apt_medicines 
	                                    WHERE 
	                                        Active = 1 
	                                    ORDER BY 
	                                        MedicineName ASC");
        return $query_get;
    }

    /**
     * Get user list
     */
    public function getUsers($id, $pharmacy_id)
    {
        $query_get = $this->conn->query("SELECT AptUserID, FirstName, LastName, Role, Phone, Email FROM apt_users WHERE AptUserID NOT IN (" . $id . ") AND PharmacyID='" . $pharmacy_id . "' AND Active = 1");
        return $query_get;
    }

    /**
     * Get user list
     */
    public function getDetailUsers($id, $pharmacy_id)
    {
        $query_get = $this->conn->query("SELECT
                    apt_users.AptUserID,
                    apt_users.PharmacyID,
                    apt_users.FirstName,
                    apt_users.LastName,
                    apt_users.Email,
                    apt_users.Phone,
                    apt_users.`Password`,
                    apt_users.PasswordSalt,
                    apt_users.ForgotPassword,
                    apt_users.ForgotPasswordExpired,
                    apt_users.NIK,
                    apt_users.Address,
                    apt_users.Gender,
                    apt_users.BirthPlace,
                    apt_users.BirthDate,
                    apt_users.Height,
                    apt_users.Weight,
                    apt_users.ActivationCode,
                    apt_users.Token,
                    apt_users.ReferralID,
                    apt_users.ReferralBy,
                    apt_users.FirebaseID,
                    apt_users.FirebaseTime,
                    apt_users.DeviceBrand,
                    apt_users.DeviceModel,
                    apt_users.DeviceSerial,
                    apt_users.DeviceOS,
                    apt_users.IsLogin,
                    apt_users.GoogleUserID,
                    apt_users.Active,
                    apt_users.CreatedBy,
                    apt_users.CreatedDate,
                    apt_users.ModifiedBy,
                    apt_users.ModifiedDate,
                    apt_users.Role,
                    apt_users.Image,
                    apt_users.Point,
                    apt_users.PointModifiedDate,
                    apt_users.Verified,
                    apt_users.VerifiedBy,
                    apt_users.VerifiedDate,
                    apt_users.RejectReason,
                    apt_users.remember_token,
                    apt_users.BankAccName,
                    apt_users.BankAccNo,
                    apt_users.BankID,
                    apt_users.BankBranch,
                    apt_pharmacies.`Name`,
                    apt_pharmacies.Address AS AddressPharmacy,
                    apt_pharmacies.Latitude,
                    apt_pharmacies.Longitude,
                    apt_pharmacies.Phone AS PhonePharmacy,
                    apt_pharmacies.Mobile AS MobilePharmacy,
                    apt_pharmacies.`Owner`,
                    apt_pharmacies.SIA_No,
                    apt_pharmacies.SIPA_No,
                    apt_pharmacies.STRA_No,
                    apt_pharmacies.Priority,
                    apt_pharmacies.Email AS EmailPharmacy
                    FROM
                    apt_users
                    INNER JOIN apt_pharmacies ON apt_users.PharmacyID = apt_pharmacies.PharmacyID
                    WHERE apt_users.AptUserID = '" . $id . "' AND apt_users.Active = 1");
        return $query_get;
    }

    /**
     * Get pending order ID Nurse
     */
    public function getPendingOrderID($user_id)
    {
        $query = $this->conn->query("SELECT * FROM apt_orders WHERE UserID = '" . $user_id . "' AND AptOrderStatusID=1 AND Active=1 LIMIT 1 ");

        if (mysqli_num_rows($query) > 0) {
            $row = $query->fetch_assoc();
            $current_id = $row['AptOrderID'];

            return $current_id;
        } else {
            return null;
        }
    }

    /**
     * Get banners
     */
    public function getBanners()
    {

        $query_get = $this->conn->query("SELECT BannerID, Title, Caption, App, CONCAT('" . $this->uploaddir . "', '/banners/', BannerID,'.jpg') AS Url FROM master_banners WHERE App='8' AND Active = 1");

        return $query_get;
    }

    /**
     * Get role user pharmacy
     */
    public function getRole($user_id)
    {

        $query_get = $this->conn->query("SELECT Role FROM apt_users WHERE AptUserID='" . $user_id . "'");

        return $query_get;
    }


    /**
     * Get dashboard bills (HOME)
     */
    public function getSaldo($user_id, $company_fee_percent)
    {
        $mode = 2;
        $company_fee_percent /= 100;
        if ($mode == 1) {

            $query_total = $this->conn->query("SELECT IFNULL((SUM(SubTotal) + SUM(Transport)) - (SUM(SubTotal)*$company_fee_percent),0) AS total_saldo FROM apt_orders WHERE AptUserID = '" . $user_id . "' AND AptOrderStatusID=6 AND Active=1 ");

            $row1 = $query_total->fetch_assoc();

            $total_saldo = $row1['total_saldo'];

            if ($total_saldo > 0) {
                return $total_saldo;
            } else {
                return 0;
            }
        } else {
            $query_total = $this->conn->query("SELECT IFNULL((SUM(SubTotal) + SUM(Transport)) - (SUM(SubTotal)*$company_fee_percent),0) AS total_saldo FROM apt_orders WHERE AptUserID = '" . $user_id . "' AND AptOrderStatusID=6 AND Active=1");

            $withdraw = $this->conn->query("SELECT IFNULL(SUM(GrandTotal),0) AS total_withdraw FROM apt_withdraw WHERE AptUserID = '" . $user_id . "' AND WithdrawStatusID != 2 AND Active=1");

            $row1 = $query_total->fetch_assoc();
            $row2 = $withdraw->fetch_assoc();
//             echo $row1['total_saldo'].' - '.$row2['total_withdraw'];die();
            $total_saldo = $row1['total_saldo'] - $row2['total_withdraw'];

            if ($total_saldo > 0) {
                return $total_saldo;
            } else {
                return 0;
            }
        }

    }

    /**
     * Get dashboard orders (HOME)
     */
    public function getOrders($user_id)
    {
        $query = $this->conn->query("SELECT COUNT(AptOrderID) AS total_order FROM apt_orders WHERE AptUserID = '" . $user_id . "' AND AptOrderStatusID=6 AND Active=1 ");

        $row = $query->fetch_assoc();
        $orders = $row['total_order'];

        return $orders;
    }

    /**
     * Get dashboard performance (HOME)
     */
    public function getPerformance($user_id)
    {
        // get order status 4,5,6,10
        $query_total = $this->conn->query("SELECT COUNT(AptOrderID) AS total_order FROM apt_orders WHERE AptUserID = '" . $user_id . "' AND AptOrderStatusID IN (4,5,6,10) AND Active=1 ");
        $row1 = $query_total->fetch_assoc();

        $query_cancel = $this->conn->query("SELECT COUNT(a.AptAcceptID) AS total_accept 
											FROM apt_orders_aptoteker_accept a
											INNER JOIN apt_orders b ON b.AptOrderID=a.AptOrderID
											WHERE b.AptOrderStatusID=6 AND a.AptUserID = '" . $user_id . "' ");
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
     * Function update apt user bank account
     * @param : $bankAccName, $bankAccNo, $bankBranch, $bankAccountId,$user_id
     * returns data
     */
    function updatePharmacyUserBankAccount($bankAccName, $bankAccNo, $bankBranch, $bankAccountId, $user_id)
    {
        // echo $bankAccName.'-'.$bankAccNo.'-'.$bankBranch.'-'.$doctorId;
        $upd = $this->conn->query("UPDATE apt_users SET 
							BankAccName= '" . $bankAccName . "', 
							BankAccNo='" . $bankAccNo . "', 
							BankBranch='" . $bankBranch . "',
							BankID='" . $bankAccountId . "'
							WHERE AptUserID='" . $user_id . "'");
        if ($upd) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Function get data apt user withdraw, data master_withdraw_status, data apt_users, data master_bank
     * @param : $user_id
     * returns data
     */
    function getPharmacyUserWithdraw($user_id)
    {
        $q = $this->conn->query("SELECT a.GrandTotal, a.Total, a.BankFee, a.ModifiedDate, a.CreatedDate AS phr_withdraw_created_at, a.WithdrawStatusID, b.WithdrawStatus, c.BankAccNo, c.BankAccName, c.BankBranch, d.BankName 
			FROM apt_withdraw a 
			JOIN master_withdraw_status b ON a.WithdrawStatusID = b.WithdrawStatusID
			JOIN apt_users c ON a.AptUserID = c.AptUserID
			JOIN master_bank d ON d.BankID = c.BankID
			WHERE a.AptUserID = $user_id
			ORDER BY a.WithdrawID DESC 
			");
        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }

    /**
     * Function get data master_bank, data master_payment_account, data apt_users
     * @param : $doctorId
     * returns data
     */
    function getTrsFeeStatusByPharmacyBankId($user_id)
    {
        $q = $this->conn->query("SELECT a.BankID, a.BankName, a.Image, 
										b.BankID AS mpa_bank_id, 
										c.AptUserID, c.BankID AS apt_user_bank_id, c.BankAccNo AS apt_user_bank_acc_no, c.BankAccName AS apt_user_bank_acc_name 
										FROM master_bank a 
			JOIN master_payment_account b ON b.BankID = a.BankID
			JOIN apt_users c ON c.BankID =  b.BankID
			WHERE (b.Active = 1 OR b.Active IS NULL) AND a.Active = 1 AND c.AptUserID = $user_id");

        if (mysqli_num_rows($q) > 0) {
            return $q;
        } else {
            return false;
        }
    }

    /**
     * Process Withdraw
     */
    public function createWithdraw($user_id, $total, $bankFee, $grandTotal)
    {

        $q = $this->conn->query("SELECT * FROM apt_withdraw WHERE AptUserID ='" . $user_id . "' AND WithdrawStatusID = 0 AND Active = 1");

        if (mysqli_num_rows($q) > 0) {
            return 2;
        } else {
            //Create Firebase to User Pharmacy
            $custom_data = array(
                'type' => '52', //Job Offer Pharmacy
                'body' => "Dana berhasil diajukan, mohon tunggu verifikasi",
                'title' => "Tawaran Anda",
                'AptUserID' => $user_id
            );

            $query_apt_user = $this->conn->query("SELECT
                                                    a.FirebaseID	
                                                  FROM
                                                    apt_users a
	                                                WHERE a.AptUserID = '" . $user_id . "' AND a.Active = 1 ");

            if (mysqli_num_rows($query_apt_user) > 0) {
                while ($row_user = $query_apt_user->fetch_assoc()) {
                    $this->sendNotification_Pharmacy($row_user['FirebaseID'], $custom_data);
                }

            }
            //End Firebase to User Pharmacy
            /*get transaction no*/
            $check_trid = $this->conn->query("SELECT IFNULL(MAX(Right(WithdrawNo,8)),0) AS WithdrawNo
											FROM apt_withdraw 
											WHERE 
												DATE_FORMAT(CreatedDate, '%m')='" . date('m') . "' AND 
												DATE_FORMAT(CreatedDate, '%Y')='" . date('Y') . "'");
            $check_trid = $check_trid->fetch_assoc();

            $tr_no = $this->incrementTrNo('PHRWD', $check_trid['WithdrawNo']);
            /*end get transaction no*/

            $insert = $this->conn->query("INSERT INTO apt_withdraw 
										(AptUserID, 
										Total, 
										CreatedDate,
										BankFee,
										GrandTotal,
										WithdrawNo
										) 
									VALUES 
										('" . $user_id . "', 
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


    /**
     * Get dashboard cancelation (HOME)
     */
    public function getCancelation($user_id)
    {
        // get order status 4,5,6,10
        $query_total = $this->conn->query("SELECT COUNT(AptOrderID) AS total_order FROM apt_orders WHERE AptUserID = '" . $user_id . "' AND AptOrderStatusID IN (4,5,6,10) AND Active=1 ");
        $row1 = $query_total->fetch_assoc();

        $query_cancel = $this->conn->query("SELECT COUNT(a.AptDeclineID) AS total_cancel 
											FROM apt_orders_apoteker_decline a
											INNER JOIN apt_orders b ON b.AptOrderID=a.AptOrderID
											WHERE b.AptOrderStatusID=7 AND a.AptUserID = '" . $user_id . "' ");
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
     * Get History Order
     */
    public function getOrderHistory($pharmacy_id)
    {

        $query_get = $this->conn->query("SELECT a.*, b.FirstName, b.LastName, b.Phone, b.Address, b.BirthDate, c.StatusName 
											FROM apt_orders a
											INNER JOIN master_users b ON b.UserID = a.UserID
											INNER JOIN apt_order_status c ON c.AptOrderStatusID = a.AptOrderStatusID
											WHERE 
												a.PharmacyID = '" . $pharmacy_id . "' AND
												a.Active = 1 AND
												a.AptOrderStatusID IN (2,3,4,5,6,7,8,9)
											ORDER BY a.AptOrderID DESC
											");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }


    /**
     * Get Order Offer Detail
     */
    public function getOrderOfferDetail($order_id, $apt_user_id)
    {

        $perKM = $this->getConfig('biaya_transportasi_per_km')->fetch_assoc();
        $current_lat = 0;
        $current_lng = 0;
        $current_distance = 0;
        $check = $this->checkOrderExist($order_id);
        if ($check) {

            //Count estimation transport fare
            $queryID = $this->conn->query("SELECT * FROM apt_orders WHERE AptOrderID = '" . $order_id . "' AND Active=1");
            $rowID = $queryID->fetch_assoc();
            $current_lat = $rowID['Latitude'];
            $current_lng = $rowID['Longitude'];
            $queryTransport = $this->conn->query("SELECT 
													(3959 * acos(cos(radians(" . $current_lat . "))*cos(radians(Latitude))*cos(radians(Longitude)-radians(" . $current_lng . ")) + sin(radians(" . $current_lat . "))*sin(radians(Latitude)))) AS distance
												FROM apt_user_location 
												WHERE AptUserID='" . $apt_user_id . "' ");
            $rowT = $queryTransport->fetch_assoc();
            $current_distance = ceil($rowT['distance']);
            $transportTotal = ($perKM['Value'] * $current_distance);

            if ($transportTotal < 7000) {
                $transportTotal = 8000;
            }

            $query_get = $this->conn->query("SELECT a.*, b.FirstName, b.LastName, b.Phone, b.Address, b.BirthDate, c.StatusName , '" . $transportTotal . "' AS TransportPrice
											FROM apt_orders a
											INNER JOIN master_users b ON b.UserID = a.UserID
											INNER JOIN apt_order_status c ON c.AptOrderStatusID = a.AptOrderStatusID
											WHERE 
												a.AptOrderID = '" . $order_id . "' AND
												a.Active = 1 AND
												a.AptOrderStatusID = 1
											");
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get Order Offer Detail
     */
    public function getOrderOfferDetailTemp($order_id, $apt_user_id)
    {
        $query_get = $this->conn->query("SELECT
                                            temp_apt_order_offers.TempID,
                                            temp_apt_order_offers.AptOrderID,
                                            temp_apt_order_offers.MedicineID,
                                            temp_apt_order_offers.Price,
                                            CASE
                                                WHEN apt_medicines.MedicineName IS NULL THEN
                                                apt_medicines.NamaAlkes ELSE apt_medicines.MedicineName 
                                            END AS `MedicineName`,
                                            apt_order_detail.Jumlah
                                            FROM
                                            temp_apt_order_offers
                                            INNER JOIN apt_order_detail ON temp_apt_order_offers.AptOrderID = apt_order_detail.AptOrderID AND temp_apt_order_offers.MedicineID = apt_order_detail.MedicineID
                                            INNER JOIN apt_medicines ON apt_order_detail.MedicineID = apt_medicines.MedicineID WHERE temp_apt_order_offers.AptOrderID = '" . $order_id . "'
                                            AND temp_apt_order_offers.AptUserID = '" . $apt_user_id . "'
                                            ");

        if ($query_get) {
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get Order Offer Detail
     */
    public function getOrderPharmacyDetail($pharmacy_id, $order_id)
    {

        $query_get = $this->conn->query("SELECT
                                            i.FirstName,
                                            i.LastName,	
                                            a.Location AS 'Address',
                                            CASE
                                                WHEN e.MedicineName IS NULL THEN
                                                e.NamaAlkes ELSE e.MedicineName 
                                            END AS `MedicineName`,
                                            d.Price AS 'PriceMedicine',
                                            a.Transport,
                                            a.TotalPayment,
                                            ( SELECT Jumlah FROM apt_order_detail WHERE apt_order_detail.AptOrderID = '" . $order_id . "' AND apt_order_detail.MedicineID = d.MedicineID ) AS Jumlah,
                                            IFNULL( a.UniqueCode, 0 ) AS 'UniqueCode',
                                            a.SubTotal,
                                            a.UserID,
                                            a.AptOrderStatusID,
                                            a.Notes,
                                            a.PaymentTypeID,
                                            a.PharmacyID,
                                            a.OrderNo,
                                            g.PaymentType,
                                            h.StatusName,
                                            a.NoResi,
                                            j.JasaPengiriman                                             
                                        FROM
                                            apt_orders AS a
                                            LEFT JOIN apt_pharmacies b ON b.PharmacyID = a.PharmacyID
                                            LEFT JOIN apt_order_offers c ON c.AptOrderID = a.AptOrderID
                                            LEFT JOIN apt_order_offer_detail d ON d.OrderOfferID = c.OrderOfferID
                                            LEFT JOIN apt_medicines e ON e.MedicineID = d.MedicineID
                                            LEFT JOIN master_payment_type g ON g.PaymentTypeID = a.PaymentTypeID
                                            LEFT JOIN apt_order_status h ON h.AptOrderStatusID = a.AptOrderStatusID 
                                            LEFT JOIN master_users i ON i.UserID = a.UserID 
                                            LEFT JOIN jasa_pengiriman j ON j.JasaPengirimanID = a.JasaPengiriman 
                                        WHERE
                                            a.PharmacyID =  '" . $pharmacy_id . "' AND
                                            a.Active = 1 and a.AptOrderID =  '" . $order_id . "' AND
                                            c.IsAccepted = 1
											");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get Order Offer Detail
     */
    public function getPharmacyDetail($order_id, $pharmacy_id)
    {

        $query_get = $this->conn->query("SELECT 
                                            d.name as 'Name', 
                                            d.address as 'Address', 
                                            b.MedicineID, 
                                            CASE
                                                WHEN c.MedicineName IS NULL THEN
                                                c.NamaAlkes ELSE c.MedicineName 
                                            END AS `MedicineName`, 
                                            b.Price as 'PriceMedicine',
                                            a.Transport, 
                                            a.SubTotal, 
                                            e.Notes, 
                                            a.PharmacyID, 
                                            (select Jumlah from apt_order_detail where apt_order_detail.AptOrderID = '" . $order_id . "' AND apt_order_detail.MedicineID =b.MedicineID) as Jumlah
											FROM apt_order_offers a
											LEFT JOIN apt_order_offer_detail b ON b.OrderOfferID = a.OrderOfferID
											LEFT JOIN apt_medicines c ON c.MedicineID = b.MedicineID
                                            LEFT JOIN apt_pharmacies d on d.PharmacyID = a.PharmacyID
                                            LEFT JOIN apt_orders e on e.AptOrderID = a.AptOrderID 
                                            LEFT JOIN apt_order_detail f ON f.AptOrderID = a.AptOrderID
                                            WHERE 
												a.AptOrderID = '" . $order_id . "' AND
												a.PharmacyID = '" . $pharmacy_id . "'
											GROUP BY b.MedicineID 
											");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get Total Order Offer Detail
     */
    public function getPharmacyID($AptUserid)
    {

        $query_get = $this->conn->query("select * from apt_users
											WHERE 
												AptUserID = '" . $AptUserid . "'
											");

        if (mysqli_num_rows($query_get) > 0) {
            $row = $query_get->fetch_assoc();
            return $row['PharmacyID'];
        } else {
            return 0;
        }
    }


    /**
     * Get Total Order Offer Detail
     */
    public function getTotalPrice($order_id, $pharmacy_id)
    {

        $query_get = $this->conn->query("SELECT SUM(b.Price) AS Total
											FROM apt_order_offers a
											INNER JOIN apt_order_offer_detail b ON b.OrderOfferID = a.OrderOfferID
											WHERE 
												a.AptOrderID = '" . $order_id . "' AND
												a.PharmacyID = '" . $pharmacy_id . "' 
											");

        if (mysqli_num_rows($query_get) > 0) {
            $row = $query_get->fetch_assoc();
            return $row['Total'];
        } else {
            return 0;
        }
    }

    /**
     * Get Blast Order Items Data
     */
    public function getOrderBlastItems($order_id)
    {

        $query_get = $this->conn->query("SELECT a.*, 
                                            CASE
                                                
                                                WHEN b.MedicineName IS NULL THEN
                                                b.NamaAlkes ELSE b.MedicineName 
                                            END AS `MedicineName`  
											FROM apt_order_detail a
											INNER JOIN apt_medicines b ON b.MedicineID = a.MedicineID
											WHERE 
												a.AptOrderID = '" . $order_id . "'
											");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get user data by phone
     */
    public function getUserByPhone($phone)
    {

        $check = $this->checkUserRegister($phone);
        if ($check) {
            $query_get = $this->conn->query("SELECT 
                                                apt_users.*,
                                                apt_pharmacies.Name as Apotek
                                                FROM apt_users
                                                LEFT JOIN apt_pharmacies ON apt_pharmacies.PharmacyID = apt_users.PharmacyID 
                                                WHERE 
                                                apt_users.Phone = '" . $phone . "' 
                                                AND 
                                                apt_users.Active=1 ");
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get user data by phone (only for login callback)
     */
    public function getLoginData($phone)
    {

        $check = $this->checkUserRegister($phone);
        if ($check) {
            $query_get = $this->conn->query("SELECT
                                                    a.*,
                                                    '' AS PASSWORD,
                                                    IFNULL( b.Active, '0' ) AS Status,
                                                  c.Name as Apotek	
                                                FROM
                                                    apt_users a
                                                    LEFT JOIN apt_user_location b ON a.AptUserID = b.AptUserID 
                                                    LEFT JOIN apt_pharmacies c ON a.PharmacyID = c.PharmacyID
                                                WHERE
												a.Phone = '" . $phone . "' AND a.Active=1");

            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Get bills nurse (HOME)
     */
    public function getBills($pharmacy_id)
    {
        $query_total = $this->conn->query("SELECT IFNULL(SUM(CompanyRevenue),0) AS total_bill FROM apt_orders WHERE PharmacyID = '" . $pharmacy_id . "' AND OrderStatusID=7 AND Active=1 AND PaymentTypeID=1 ");
        $row1 = $query_total->fetch_assoc();

        $query_paid = $this->conn->query("SELECT IFNULL(SUM(Total),0) AS total_paid FROM apt_payment_confirmations WHERE PharmacyID = '" . $pharmacy_id . "' AND Status=1 ");
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

    function checkPharmacyBillReachMaximum($pharmacy_id)
    {
        $bills = $this->getBills($pharmacy_id);
        $max_bill = $this->getConfig('pharmacy_maximum_bill')->fetch_assoc();

        $max_bill = $max_bill['Value'];
        if ($bills > $max_bill) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Function get data master_config
     * @param : $configName
     * returns data
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
     * Check if nurse verified
     */
    public function checkPharmacyVerified($pharmacy_id)
    {
        $query = $this->conn->query("SELECT * FROM apt_users WHERE PharmacyID = '" . $pharmacy_id . "' AND Active=1 AND Verified=1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }


//	public function getOrderOffer($checkPharmacyID) {
    public function getOrderOffer($pharmacy_id)
    {

        //get pharmacy location
        $getLoc = $this->conn->query("SELECT a.* 
										FROM apt_pharmacies a 
										WHERE a.PharmacyID='" . $pharmacy_id . "' AND a.Active=1 
										LIMIT 1");

        if (mysqli_num_rows($getLoc) > 0) {

            $rowLoc = $getLoc->fetch_assoc();
            $apt_latitude = $rowLoc['Latitude'];
            $apt_longitude = $rowLoc['Longitude'];

            // get order from user in distance 10 km
            $query_get = $this->conn->query("SELECT   
													a.AptOrderID,
													a.OrderDate,
													a.Notes,
													b.UserID,
													b.FirstName,
													b.LastName,
													b.BirthDate,
													a.Location AS Address,
													a.Latitude,
													a.Longitude,
													a.AptOrderStatusID,
													a.Active,
													(3959 * acos(cos(radians(" . $apt_latitude . "))*cos(radians(a.Latitude))*cos(radians(a.Longitude)-radians(" . $apt_longitude . ")) + sin(radians(" . $apt_latitude . "))*sin(radians(a.Latitude)))) AS distance
												FROM apt_orders a
												INNER JOIN master_users b ON b.UserID = a.UserID
												WHERE a.AptOrderID NOT IN (SELECT AptOrderID FROM apt_order_offers WHERE PharmacyID = '" . $pharmacy_id . "') 
												HAVING distance <= 10 AND a.AptOrderStatusID = 1 AND a.Active = 1
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
     * Get Order Data
     */
    public function getOrderData($order_id, $pharmacy_id, $total_price)
    {

        //Count estimation transport fare
        $perKM = $this->getConfig('biaya_transportasi_per_km')->fetch_assoc();
        $current_price = 0;
        $current_lat = 0;
        $current_lng = 0;
        $current_distance = 0;

        $queryID = $this->conn->query("SELECT * FROM apt_orders WHERE AptOrderID = '" . $order_id . "' AND Active=1");
        $rowID = $queryID->fetch_assoc();
        $current_lat = $rowID['Latitude'];
        $current_lng = $rowID['Longitude'];
        $current_price = $total_price;
        //get distance from nurse location to user location
        $queryTransport = $this->conn->query("SELECT 
												(3959 * acos(cos(radians(" . $current_lat . "))*cos(radians(Latitude))*cos(radians(Longitude)-radians(" . $current_lng . ")) + sin(radians(" . $current_lat . "))*sin(radians(Latitude)))) AS distance
											FROM apt_pharmacies 
											WHERE PharmacyID='" . $pharmacy_id . "' ");
        $rowT = $queryTransport->fetch_assoc();
        $current_distance = ceil($rowT['distance']);
        // transaport price nurse per KM
        $transportTotal = ($perKM['Value'] * $current_distance);

        if ($transportTotal < 7000) {
            $transportTotal = 8000;
        }

        $orderTotal = ($current_price + $transportTotal);

        $query_get = $this->conn->query("SELECT   
											a.AptOrderID,
											a.OrderDate,
											a.Notes,
											" . $total_price . " AS TotalBid,
											a.AptOrderStatusID,
											a.Location,
											a.OrderNo,
											b.PharmacyID,
											b.Name,
											b.Address,
											" . $transportTotal . " AS TransportPrice,
											" . $orderTotal . " AS TotalPayment,
											c.FirstName AS user_firstname,
											c.LastName AS user_lastname,
											c.Email AS user_email
										FROM apt_orders a
										INNER JOIN apt_pharmacies b ON b.PharmacyID = a.PharmacyID
										INNER JOIN master_users c ON c.UserID = a.UserID
										WHERE a.AptOrderID = '" . $order_id . "' AND a.Active = 1");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }


    /**
     * Get user data by id
     */
    public function getUserByID($id)
    {
        $query_get = $this->conn->query("SELECT * FROM apt_users WHERE AptUserID = '" . $id . "' AND Active=1 ");
        return $query_get;
    }

    /**
     * Get pharmacy data by id
     */
    public function getPhramcyByAptID($AptUserid)
    {
        $query_get = $this->conn->query("SELECT * FROM apt_users WHERE AptUserID = '" . $AptUserid . "' AND Active=1 ");


        if (mysqli_num_rows($query_get) > 0) {
            $row = $query_get->fetch_assoc();
            $PharmacyID = $row['PharmacyID'];

            return $PharmacyID;
        } else {
            return null;
        }
    }

    /**
     * Check if apoteker was accept offer
     */
    public function checkPharmacyAcceptOffer($order_id, $apt_user_id)
    {
        $query = $this->conn->query("SELECT * FROM apt_orders_aptoteker_accept WHERE AptUserID = '" . $apt_user_id . "' AND AptOrderID = '" . $order_id . "'");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Create Pharmacy Accept Offer
     */
    public function acceptOffer($order_id, $apt_user_id)
    {
        $pharmacy_id = $this->getPharmacyID($apt_user_id);
        $exist = $this->checkPharmacyAcceptOffer($order_id, $apt_user_id);

        if (!$exist) {
            $insert = $this->conn->query("INSERT INTO apt_orders_aptoteker_accept 
											(AptOrderID, 
											AptUserID,
											AcceptDate) 
										VALUES 
											('" . $order_id . "', 
											'" . $apt_user_id . "',
											'" . $this->get_current_time() . "'
											) ");

            if ($insert) {
                $this->sendNotif_PharmacyAccept($order_id, $pharmacy_id, $apt_user_id);
                return true;
            } else {
                return false;
            }
        } else {
            $this->sendNotif_PharmacyAccept($order_id, $pharmacy_id, $apt_user_id);
            return true;
        }
    }

    /**
     * Check if pharmacy was decline offer
     */
    public function checkPharmacyDeclineOffer($order_id, $apt_user_id)
    {
        $query = $this->conn->query("SELECT * FROM apt_orders_apoteker_decline WHERE AptUserID = '" . $apt_user_id . "' AND AptOrderID = '" . $order_id . "' ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Create Pharmacy Decline Offer
     */
    public function declineOffer($order_id, $apt_user_id)
    {

        $exist = $this->checkPharmacyDeclineOffer($order_id, $apt_user_id);
        if (!$exist) {
            $insert = $this->conn->query("INSERT INTO apt_orders_apoteker_decline 
											(AptOrderID, 
											AptUserID,
											DeclineDate) 
										VALUES 
											('" . $order_id . "', 
											'" . $apt_user_id . "',
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

    /**
     * Create New User
     */
    public function createUser($firstname, $lastname, $phone, $password, $email, $role, $pharmacy_id)
    {
        $check_admin_exist = $this->CheckAdminExist($pharmacy_id);

        if ($check_admin_exist && $role == 'Admin') {
            return false;
        } else {
            //Generate Encrypt Password
            $hash = $this->hashSSHA($password);
            $encrypted_password = $hash["encrypted"];
            $salt_password = $hash["salt"];

            $insert = $this->conn->query("INSERT INTO apt_users 
									(FirstName, 
									LastName, 
									Phone,
									Password,
									PasswordSalt,
									Email,
									PharmacyID,
									Role,
									CreatedDate
									) 
								VALUES 
									('" . $firstname . "', 
									'" . $lastname . "',
									'" . $phone . "',
									'" . $encrypted_password . "',
									'" . $salt_password . "',
									'" . $email . "',
									'" . $pharmacy_id . "',
									'" . $role . "',
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
     * Edit User
     */
    public function editUser($user_id, $firstname, $lastname, $phone, $email, $role, $pharmacy_id)
    {
        $check_admin_exist = $this->CheckAdminExist($pharmacy_id);

        $check_is_admin = $this->CheckIsAdmin($user_id, $pharmacy_id);
        if ($check_admin_exist) {
            if ($check_is_admin === 'not_admin' && $role == 'Admin') {
                //Jika Admin sudah exist dan dia mau menset sebagai Admin, maka return false
                return false;
            } else {
                //Jika Dia Admin mungkin bisa saja dia mau menjadi User biasa
                $update = $this->conn->query("  UPDATE apt_users SET
											FirstName = '" . $firstname . "',
											LastName = '" . $lastname . "',
											Phone = '" . $phone . "',
											Email = '" . $email . "',
											Role = '" . $role . "',
											ModifiedDate = '" . $this->get_current_time() . "'
										WHERE
											AptUserID = '" . $user_id . "'
									");
                if ($update) {
                    return true;
                } else {
                    return false;
                }
            }

        } else {
            //Jika belum ada Admin sama sekali
            $update = $this->conn->query("  UPDATE apt_users SET
											FirstName = '" . $firstname . "',
											LastName = '" . $lastname . "',
											Phone = '" . $phone . "',
											Email = '" . $email . "',
											Role = '" . $role . "',
											ModifiedDate = '" . $this->get_current_time() . "'
										WHERE
											AptUserID = '" . $user_id . "'
									");

            if ($update) {
                return true;
            } else {
                return false;
            }
        }


    }

    public function CheckAdminExist($pharmacy_id)
    {
        $query_get = $this->conn->query("SELECT * FROM apt_users WHERE PharmacyID = '" . $pharmacy_id . "' AND Role= 'Admin' AND Active=1 ");


        if (mysqli_num_rows($query_get) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function CheckIsAdmin($user_id, $pharmacy_id)
    {
        $query_get = $this->conn->query("SELECT * FROM apt_users WHERE AptUserID = '" . $user_id . "' AND PharmacyID = '" . $pharmacy_id . "' AND Role= 'Admin' AND Active=1 ");


        if (mysqli_num_rows($query_get) > 0) {
            return true;
        } else {
            return 'not_admin';
        }
    }

    /**
     * Delete User
     */
    public function deleteUser($user_id)
    {
        $delete = $this->conn->query("  DELETE FROM apt_users WHERE AptUserID = '" . $user_id . "'");


        if ($delete) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Process Pending Order
     */
    public function processPendingOrder($user_id, $latitude, $longitude, $location, $notes)
    {

        $order_no = 0;

        //Count Transport Fare
        $transport_price = 0;

        //Lets process
        $exist = $this->checkPendingOrder($user_id);
        // var_dump($exist->fetch_assoc());
        if ($exist) {
            //Order Pending Exist
            $update = $this->conn->query("UPDATE apt_orders SET 
											OrderNo 		= '" . $order_no . "',
											Latitude 		= '" . $latitude . "',
											Longitude 		= '" . $longitude . "',
											Location 		= '" . $location . "',
										
											Notes 			= '" . $notes . "',
											OrderDate		= '" . $this->get_current_time() . "',
											ModifiedDate	= '" . $this->get_current_time() . "'
										WHERE 
											UserID = '" . $user_id . "' AND AptOrderStatusID=1 AND Active=1 ");
            if ($update) {
                $exist = $exist->fetch_assoc();
                //create order log
                $order_id = $exist['AptOrderID'];
                $order_status_id = 1;
                //create order log
                $this->createOrderLog($order_id, $order_status_id);
                // push notif
                $this->sendJobOffer($user_id, $latitude, $longitude);
                return true;
            } else {
                return false;
            }
        } else {
            //Create New Order
            $insert = $this->conn->query("INSERT INTO apt_orders 
										(OrderNo,
										OrderDate,
										UserID,
										Latitude,
										Longitude,
										Location,
										OrderType,
										Notes,
										SubTotal,
										Transport,
										TotalPayment,
										CreatedDate
										) 
									VALUES 
										('" . $order_no . "',
										'" . $this->get_current_time() . "',
										'" . $user_id . "',
										'" . $latitude . "',
										'" . $longitude . "',
										'" . $location . "',
										
										'" . $notes . "',
										'0',
										'0',
										'0',
										'" . $this->get_current_time() . "'
										) ");
            if ($insert) {
                //create order log
                $order_id = $this->conn->insert_id;
                $order_status_id = 1;
                $this->createOrderLog($order_id, $order_status_id);

                // send push notif
                $this->sendJobOffer($user_id, $latitude, $longitude);
                return true;

            } else {
                return false;
            }
        }
    }

    /**
     * Get Order Data Doctor
     */
    public function getPharmacyOrderData($order_id)
    {

        $query_get = $this->conn->query("SELECT   
											a.*,
											-- a.AptOrderID,
											-- a.OrderDate,
											-- a.Notes,
											-- a.TotalPrice,
											-- a.OrderNo,
											-- a.OrderStatusID,
											-- a.UserID,
											b.PharmacyID,
											b.FirstName,
											b.LastName,
											b.BirthDate,
											-- b.Location,
											b.Email,
											c.Firstname AS 'user_firstname',
											c.LastName AS 'user_lastname',
											c.Email AS 'user_email'
										FROM apt_orders a
										LEFT JOIN apt_users b ON b.PharmacyID = a.PharmacyID
										LEFT JOIN master_users c ON c.UserID = a.UserID
										WHERE a.AptOrderID = '" . $order_id . "' AND a.Active = 1");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Process Payment Doctor Status By User
     */
    public function processPaymentPharmacy($order_id, $payment_type_id, $unique_code, $kode_voucher, $nominal)
    {

        /* Generate Order No (Invoice Number) */
        //Get Last Order ID +1
        $check_orderid = $this->conn->query("SELECT IFNULL(MAX(Right(OrderNo,8)),0) AS OrderNo
											FROM apt_orders
											WHERE 
												DATE_FORMAT(OrderDate, '%m')='" . date('m') . "' AND 
												DATE_FORMAT(OrderDate, '%Y')='" . date('Y') . "'");

        $data_orderid = $check_orderid->fetch_assoc();

        if ($data_orderid['OrderNo'] == 0) {
            //Start From First
            $num = date('y') . date('m') . "1001";
            $order_no = "PHR" . $num;
        } else {
            //Continue Number +1
            $num = $data_orderid['OrderNo'] + 1;
            $order_no = "PHR" . $num;
        }
        /* End generate */

        //Check If its free or no
        //echo "SELECT * FROM apt_orders WHERE AptOrderID='" . $order_id . "' ";die();
        $check_price = $this->conn->query("SELECT * FROM apt_orders WHERE AptOrderID='" . $order_id . "' ");
        $data_price = $check_price->fetch_assoc();
        $order_price = $data_price['SubTotal'];
        $transport = $data_price['Transport'];
        if($nominal == ''){
            $nominal = 0;
        }
        $total_payment = $order_price + $unique_code + $transport - $nominal;

        // if order price '0', payment type id cash
        if ($order_price == 0) {
            $myStatus = "4";
        } else {
            $myStatus = "3";
        }

        //Update
        $update = $this->conn->query("UPDATE apt_orders SET 
										AptOrderStatusID = '" . $myStatus . "',
										OrderNo = '" . $order_no . "',
										PaymentTypeID = '" . $payment_type_id . "',
										UniqueCode = " . $unique_code . ",
										TotalPayment = " . $total_payment . ",
										voucher_code = '" . $kode_voucher . "',
										nominal = " . $nominal . "
									WHERE 
										AptOrderID = '" . $order_id . "'");

        if ($update) {
            $dt = $this->getPharmacyByOrderID($order_id);
            if ($dt != null) {
                $dt = $dt->fetch_assoc();

                //create order log
                $order_id = $order_id;
                $order_status_id = $myStatus;
                $description = 'Log Order Pharmacy, created by sistem api';
                $pharmacy_id = $dt['PharmacyID'];
                $this->createOrderLogPharmacy($order_id, $order_status_id, $pharmacy_id, $description);
            }
            // $this->dealNotificationPharmacy($order_id);
            //$this->NoDealNotificationDoctor($order_id);
            return true;
        } else {
            return false;
        }
    }


    public function dealNotificationPharmacy($order_id)
    {

        $query = $this->conn->query("SELECT * FROM apt_orders WHERE AptOrderID = '" . $order_id . "' AND Active=1 LIMIT 1 ");

        if (mysqli_num_rows($query) > 0) {
            $row = $query->fetch_assoc();
            $doctor_id = $row['PharmacyID'];
            $q = $this->conn->query("SELECT * FROM master_users WHERE UserID = '" . $row['UserID'] . "'");
            $user_name = '';
            if (mysqli_num_rows($q) > 0) {
                $q = $q->fetch_assoc();
                $user_name = $q['FirstName'] . ' ' . $q['LastName'];
            }
            $custom_data = array(
                'type' => '33', //History Order Doctor
                'body' => "Selamat anda terpilih untuk mengerjakan order atas nama " . $user_name,
                'title' => "Konfirmasi Pekerjaan",
                'AptOrderID' => $order_id
            );

            //Notify User
            $query_user = $this->conn->query("SELECT * FROM doc_doctors WHERE DoctorID='" . $doctor_id . "' AND Active=1 ");
            if (mysqli_num_rows($query_user) > 0) {
                $row_user = $query_user->fetch_assoc();

                $this->sendNotification_Doctor($row_user['FirebaseID'], $custom_data);
            }
        }
    }

    public function createOrderLogPharmacy($order_id, $order_status_id, $pharmacy_id, $description)
    {
        $q = $this->conn->query("INSERT INTO apt_orders_logs 
									(AptOrderID,
									CreatedDate,
									CreatedBy,
									OrderStatusID,
									PharmacyID,
									Description
									) 
								VALUES 
									('" . $order_id . "',
									'" . $this->get_current_time() . "',
									'9-',
									'" . $order_status_id . "',
									'" . $pharmacy_id . "',
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
    public function getPharmacyByOrderID($order_id)
    {


        $query_get = $this->conn->query("SELECT   
											a.AptOrderID,
											b.FirstName,
											b.LastName,
											b.PharmacyID,
											a.UserID
										FROM apt_orders a
										INNER JOIN apt_users b ON a.PharmacyID = b.PharmacyID
										WHERE a.Active=1 AND a.AptOrderID='" . $order_id . "' ");

        if (mysqli_num_rows($query_get) > 0) {
            return $query_get;
        } else {
            return null;
        }
    }

    /**
     * Check if order doctor exist
     */
    public function checkPharmacyOrderExist($order_id)
    {
        $query = $this->conn->query("SELECT * FROM apt_orders WHERE AptOrderID = '" . $order_id . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function saveOrderOfferDetail($order_id, $pharmacy_id, $detail, $apt_user_id)
    {
        $exist = $this->checkOrderOfferDetail($order_id, $apt_user_id);

        if ($exist != null) {
            //delete first
            $del = $this->conn->query("DELETE FROM temp_apt_order_offers WHERE AptOrderID='" . $order_id . "' AND AptUserID='" . $apt_user_id . "'");

            //process insert batch
            $sql = array();
            $medicine = array();
            foreach ($detail[0] as $med_id => $price) {
                $sql[] = '("' . $order_id . '","' . $apt_user_id . '", "' . $med_id . '", ' . $price . ')';
                $medicine[] = $med_id;
            }


            $ins = $this->conn->query("INSERT INTO temp_apt_order_offers (AptOrderID, AptUserID, MedicineID, Price) VALUES " . implode(',', $sql));
            if ($ins) {
                return true;
            } else {
                return false;
            }

        } else {
            //process insert batch
            $sql = array();
            $medicine = array();
            foreach ($detail[0] as $med_id => $price) {
                $sql[] = '("' . $order_id . '","' . $apt_user_id . '", "' . $med_id . '", ' . $price . ')';
                $medicine[] = $med_id;
            }


            $ins = $this->conn->query("INSERT INTO temp_apt_order_offers (AptOrderID, AptUserID , MedicineID, Price) VALUES " . implode(',', $sql));
            if ($ins) {
                return true;
            } else {
                return false;
            }
        }


    }

    /**
     * Check Order Offer Detail temp
     */
    public function checkOrderOfferDetail($order_id, $apt_user_id)
    {

        $query = $this->conn->query("SELECT * FROM temp_apt_order_offers WHERE AptOrderID = '" . $order_id . "' AND AptUserID = '" . $apt_user_id . "'");

        if (mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return null;
        }
    }

    /**
     * Process Order Offer
     */
    public function saveOrderOffer($order_id, $pharmacy_id, $detail, $transport, $apt_user_id, $sub_total)
    {


        //Lets process
        $exist = $this->checkOrderOffer($order_id, $pharmacy_id);

        if ($exist != null) {
            //Order Offer Exist
            $order_status_id = 2;
            $update = $this->conn->query("UPDATE apt_order_offers SET 
											AptOrderID 		= '" . $order_id . "',
											PharmacyID 		= '" . $pharmacy_id . "',
											SubTotal		= '" . $sub_total . "',
											Transport 		= '" . $transport . "',
											OfferDate		= '" . $this->get_current_time() . "'
										WHERE 
											PharmacyID = '" . $pharmacy_id . "' ");

            if ($update) {
                $data = $exist->fetch_assoc();

                //create details
                $this->processDetailOffer($data['OrderOfferID'], $detail);


                //  $updatePharmacyID = $this->conn->query("UPDATE apt_orders SET
                //		AptUserID 		= '" . $apt_user_id . "',
                //	PharmacyID 		= '" . $pharmacy_id . "',
                //	SubTotal = '" . $sub_total . "',
                //	Transport = '" . $transport . "'

                //WHERE
                //	AptOrderID = '" . $order_id . "' ");
                //create order log
                // $order_id = $data['AptOrderID'];
                //$order_status_id = 2;
                $this->createOrderLog($order_id, $order_status_id, $pharmacy_id);

                //send notif to patient

                $this->sendNotif_PharmacyAccept($order_id, $pharmacy_id, $apt_user_id, $transport, $sub_total);
                return true;
            } else {
                return false;
            }
        } else {
            $order_status_id = 2;
            //Create New Offer


            $insert = $this->conn->query("INSERT INTO apt_order_offers
										(AptOrderID,
										PharmacyID,
										Transport,
										SubTotal,
										OfferDate,
										AptUSerID
										) 
									VALUES 
										('" . $order_id . "',
										'" . $pharmacy_id . "',
										'" . $transport . "',
										'" . $sub_total . "',
										'" . $this->get_current_time() . "',
										'" . $apt_user_id . "'
										) ");


            if ($insert) {
                //create details
                $this->processDetailOffer($this->conn->insert_id, $detail);

                //$updatePharmacyID = $this->conn->query("UPDATE apt_orders SET

                //	PharmacyID 		= '" . $pharmacy_id . "',
                //AptUserID 		= '" . $apt_user_id . "',
                //SubTotal = '" . $sub_total . "',
                //Transport = '" . $transport . "'

                //WHERE
                //AptOrderID = '" . $order_id . "' ");


                //send notif to patient
                $this->sendNotif_PharmacyAccept($order_id, $pharmacy_id, $apt_user_id, $transport, $sub_total);
                //create order log
                $order_id = $this->conn->insert_id;
                //$order_status_id = 2;
                $this->createOrderLog($order_id, $order_status_id, $pharmacy_id);


                return true;
            } else {
                return false;
            }
        }
    }

    //Order Offer to Pharmacy
    public function sendJobOffer($user_id, $latitude, $longitude)
    {
        //Get ID
        $order_id = $this->getPendingOrderID($user_id);

        $custom_data = array(
            'type' => '91', //Job Offer Pharmacy
            'body' => "Hi, ada tawaran order nih",
            'title' => "Tawaran Order",
            'OrderID' => $order_id
        );

        //Notify Online Pharmacy, within 10KM around
        $query_user = $this->conn->query("SELECT a.*, a.Active,b.Active,c.Active,c.Verified,
												(3959 * acos(cos(radians(" . $latitude . "))*cos(radians(c.Latitude))*cos(radians(c.Longitude)-radians(" . $longitude . ")) + sin(radians(" . $latitude . "))*sin(radians(c.Latitude)))) AS distance
											FROM apt_users a
											INNER JOIN apt_user_location b ON b.AptUserID = a.AptUserID
											INNER JOIN apt_pharmacies c ON c.PharmacyID = a.PharmacyID
											HAVING 
												distance <= 10 AND  
												a.FirebaseID IS NOT NULL AND 
												a.Active=1 AND 
												b.Active=1 AND 
												c.Active=1 AND
												c.Verified=1 ");
        if (mysqli_num_rows($query_user) > 0) {
            while ($row_user = $query_user->fetch_assoc()) {
                $this->sendNotification_Pharmacy($row_user['FirebaseID'], $custom_data);
            }

        }
    }

    public function sendNotif_PharmacyAccept($order_id, $pharmacy_id, $apt_user_id, $sub_total, $transport)
    {

        //Get Data
        $query = $this->conn->query("SELECT a.PharmacyID,
											a.Name, 
											a.Address,
                                            apt_orders.AptOrderStatusID,
                                            apt_orders.PaymentTypeID,
                                            apt_orders.Notes,
                                            apt_orders.OrderNo
									FROM
                                    apt_pharmacies AS a
                                    INNER JOIN apt_order_offers ON a.PharmacyID = apt_order_offers.PharmacyID
                                    INNER JOIN apt_orders ON apt_order_offers.AptOrderID = apt_orders.AptOrderID
									WHERE 
										a.PharmacyID = '" . $pharmacy_id . "' ");


        if (mysqli_num_rows($query) > 0) {

            $row = $query->fetch_assoc();

            $custom_data = array(
                'type' => '1',
                'body' => "Ada Apotek menerima order anda",
                'title' => "Order Anda",
                'AptOrderID' => $order_id,
                'PharmacyID' => $pharmacy_id,
                'AptUserID' => $apt_user_id,
                'Name' => $row['Name'],
                'Address' => $row['Address'],
                'SubTotal' => $sub_total,
                'Transport' => $transport,
                'sound' => "mysound.wav"
            );

            // var_dump($custom_data);
            //Notify Patient
            $query_user = $this->conn->query("SELECT a.AptOrderID, b.FirebaseID FROM apt_orders a INNER JOIN master_users b ON b.UserID=a.UserID WHERE a.AptOrderID = '" . $order_id . "' ");

            if (mysqli_num_rows($query_user) > 0) {
                $row_user = $query_user->fetch_assoc();
                $this->sendNotification_Patient($row_user['FirebaseID'], $custom_data);
            }
        }
    }

    /**
     * Process Detail Order
     */
    public function processDetailOrder($medicine_id, $order_id)
    {

        //delete first
        $del = $this->conn->query("DELETE FROM apt_order_detail WHERE AptOrderID='" . $order_id . "' ");

        //process insert batch
        $sql = array();

        foreach ($medicine_id as $row) {
            $sql[] = '("' . $row . '", ' . $order_id . ')';
        }

        $ins = $this->conn->query('INSERT INTO apt_order_detail (MedicineID, AptOrderID) VALUES ' . implode(',', $sql));
        if ($ins) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Process Detail Offer
     */
    public function processDetailOffer($order_offer_id, $detail)
    {
        //delete first
        $del = $this->conn->query("DELETE FROM apt_order_offer_detail WHERE OrderOfferID='" . $order_offer_id . "' ");


        //process insert batch
        $sql = array();
        $medicine = array();
        foreach ($detail[0] as $med_id => $price) {
            $sql[] = '("' . $order_offer_id . '", "' . $med_id . '", ' . $price . ')';
            $medicine[] = $med_id;
        }

        $ins = $this->conn->query("INSERT INTO apt_order_offer_detail (OrderOfferID, MedicineID, Price) VALUES " . implode(',', $sql));
        if ($ins) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Process Cancel Order
     */
    public function cancelOrder($order_id)
    {

        $update = $this->conn->query("UPDATE apt_orders SET 
										AptOrderStatusID = '7'
									WHERE 
										AptOrderID = '" . $order_id . "'");

        if ($update) {
            $order_status_id = 7;
            $this->createOrderLog($order_id, $order_status_id);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Process Cancel Order
     */
    public function cancelTempOrder($order_id, $apt_user_id)
    {

        $del = $this->conn->query("DELETE FROM temp_apt_order_offers WHERE AptOrderID='" . $order_id . "' AND AptUserID='" . $apt_user_id . "'");

        if ($del) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update User Pharmacy Profile
     */
    public function updateProfile($user_id, $firstname, $lastname, $phone, $email)
    {

        $check = $this->checkUserById($user_id);
        if ($check) {
            $update = $this->conn->query("UPDATE apt_users SET 
										FirstName 	= '" . $firstname . "',
										LastName 	= '" . $lastname . "',
										Phone 		= '" . $phone . "',
										Email 		= '" . $email . "',
										ModifiedDate= '" . $this->get_current_time() . "'
									WHERE 
										AptUserID = '" . $user_id . "'");

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
     * Update New Password
     */
    public function updatePassword($user_id, $new_password)
    {

        $hash = $this->hashSSHA($new_password);
        $encrypted_password = $hash["encrypted"]; // encrypted new password
        $salt_password = $hash["salt"]; // salt new

        $update = $this->conn->query("UPDATE apt_users SET 
									Password	 = '" . $encrypted_password . "',
									PasswordSalt = '" . $salt_password . "'
								WHERE 
									AptUserID = '" . $user_id . "' AND Active=1 ");

        if ($update) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check nurse by ID
     */
    public function checkUserById($id)
    {

        $query = $this->conn->query("SELECT * FROM apt_users WHERE AptUserID = '" . $id . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if user exist
     */
    public function checkUserRegister($phone)
    {
        $query = $this->conn->query("SELECT * FROM apt_users WHERE Phone = '" . $phone . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check Order Offer
     */
    public function checkOrderOffer($order_id, $pharmacy_id)
    {

        $query = $this->conn->query("SELECT * FROM apt_order_offers WHERE AptOrderID = '" . $order_id . "' AND PharmacyID = '" . $pharmacy_id . "' ");

        if (mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return null;
        }
    }

    /**
     * Check if user exist
     */
    public function checkUserRegisterByPharmacy($phone, $pharmacy_id)
    {
        $query = $this->conn->query("SELECT * FROM apt_users WHERE Phone = '" . $phone . "' AND PharmacyID='" . $pharmacy_id . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if user exist
     */
    public function checkUserLogin($phone, $password)
    {
        $check = $this->conn->query("SELECT * FROM apt_users WHERE Phone = '" . $phone . "' AND Active=1 ");

        if (mysqli_num_rows($check) > 0) {

            $row = $check->fetch_assoc();
            $salt = $row['PasswordSalt'];
            $encrypted_password = $this->checkhashSSHA($salt, $password);

            $check_pass = $this->conn->query("SELECT * FROM apt_users WHERE Phone = '" . $phone . "' AND Password='" . $encrypted_password . "' AND Active=1 ");

            if (mysqli_num_rows($check_pass) > 0) {
                //Generate new token
                $new_token = $this->generateToken();
                $upd = $this->conn->query("UPDATE apt_users SET IsLogin=1, Token='" . $new_token . "' WHERE Phone='" . $phone . "' AND Active=1 ");

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
    public function checkUserLoginByForgot($phone, $password)
    {

        $now = $this->get_current_time();

        $check_pass = $this->conn->query("SELECT * FROM apt_users WHERE Phone = '" . $phone . "' AND ForgotPassword='" . $password . "' AND ForgotPasswordExpired > STR_TO_DATE('" . $now . "', '%Y-%m-%d %H:%i:%s') AND Active=1 ");

        if (mysqli_num_rows($check_pass) > 0) {
            $new_token = $this->generateToken();
            $upd = $this->conn->query("UPDATE apt_users SET IsLogin=1, Token='" . $new_token . "' WHERE Phone='" . $phone . "' AND Active=1 ");
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
     * Check pharmacy user password
     */
    public function checkUserPassword($user_id, $password)
    {

        $check = $this->conn->query("SELECT * FROM apt_users WHERE AptUserID = '" . $user_id . "' AND Active=1 ");

        if (mysqli_num_rows($check) > 0) {

            $row = $check->fetch_assoc();
            $salt = $row['PasswordSalt'];
            $encrypted_password = $this->checkhashSSHA($salt, $password);

            $check_pass = $this->conn->query("SELECT * FROM apt_users WHERE AptUserID = '" . $user_id . "' AND Password='" . $encrypted_password . "' ");

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
     * Check pharmacy user password
     */
    public function checkUserPasswordForgot($user_id, $password)
    {

        $check = $this->conn->query("SELECT * FROM apt_users WHERE AptUserID = '" . $user_id . "' AND Active=1 ");

        if (mysqli_num_rows($check) > 0) {
            $check_pass = $this->conn->query("SELECT * FROM apt_users WHERE AptUserID = '" . $user_id . "' AND ForgotPassword='" . $password . "' ");
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
     * Update FirebaseID
     */
    public function updateFirebase($phone, $firebase_id)
    {

        $update = $this->conn->query("UPDATE apt_users SET 
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
     * Process Nurse Online/Offline
     */
    public function updateStatus($user_id, $status)
    {

        $exist = $this->checkUserLocationExist($user_id);
        if ($exist) {
            //Update Location
            $update = $this->conn->query("UPDATE apt_user_location SET 
											Active 		= '" . $status . "',
											TrackDate	= '" . $this->get_current_time() . "'
										WHERE 
											AptUserID = '" . $user_id . "' ");

            if ($update) {
                return true;
            } else {
                return false;
            }
        } else {
            //Create Location
            $insert = $this->conn->query("INSERT INTO apt_user_location 
										(AptUserID,
										Latitude,
										Longitude,
										Accuracy,
										Active,
										TrackDate
										) 
									VALUES 
										('" . $user_id . "',
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
     * Process Nurse Location Tracking
     */
    public function trackLocation($user_id, $latitude, $longitude, $accuracy)
    {

        $exist = $this->checkUserLocationExist($user_id);
        if ($exist) {
            //Update Location
            $update = $this->conn->query("UPDATE apt_user_location SET 
											Latitude 	= '" . $latitude . "',
											Longitude 	= '" . $longitude . "',
											Accuracy 	= '" . $accuracy . "',
											TrackDate	= '" . $this->get_current_time() . "'
										WHERE 
											AptUserID = '" . $user_id . "' ");

            if ($update) {
                return true;
            } else {
                return false;
            }
        } else {
            //Create Location
            $insert = $this->conn->query("INSERT INTO apt_user_location 
										(AptUserID,
										Latitude,
										Longitude,
										Accuracy,
										TrackDate
										) 
									VALUES 
										('" . $user_id . "',
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
     * Check if nurse location tracking if exist
     */
    public function checkUserLocationExist($user_id)
    {

        $query = $this->conn->query("SELECT * FROM apt_user_location WHERE AptUserID = '" . $user_id . "' ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check pending order by AptOrderID
     */
    public function checkPendingOrderByAptOrderId($order_id)
    {
        $query = $this->conn->query("SELECT * FROM apt_orders WHERE AptOrderID = '" . $order_id . "' AND AptOrderStatusID=1 AND Active=1 ");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check pending order
     */
    public function checkPendingOrder($user_id)
    {
        $query = $this->conn->query("SELECT * FROM apt_orders WHERE UserID = '" . $user_id . "' AND AptOrderStatusID=1 AND Active=1 ");

        if (mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return false;
        }
    }

    /**
     * Check if order nurse exist
     */
    public function checkOrderExist($order_id, $order_status_id = null)
    {
        if ($order_status_id != null) {
            $query = $this->conn->query("SELECT * FROM apt_orders WHERE AptOrderID = '" . $order_id . "' AND Active=1 AND AptOrderStatusID=" . $order_status_id);
        } else {
            $query = $this->conn->query("SELECT
                                        a.*,
                                        c.PaymentType 
                                    FROM
                                        apt_orders a
                                        LEFT JOIN master_payment_type c ON c.PaymentTypeID = a.PaymentTypeID 
                                    WHERE
                                        AptOrderID = '" . $order_id . "' AND a.Active=1 ");
        }
        if (mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return false;
        }
    }

    /**
     * Order Logs
     */
    public function createOrderLog($order_id, $order_status_id, $pharmacy_id = NULL)
    {
        $q = $this->conn->query("INSERT INTO apt_order_logs 
									(AptOrderID,
									CreatedDate,
									AptOrderStatusID,
									PharmacyID
									) 
								VALUES 
									('" . $order_id . "',
									'" . $this->get_current_time() . "',
									'" . $order_status_id . "',
									'" . $pharmacy_id . "'
									) ");
        if ($q) {
            return $this->conn->insert_id;
        } else {
            return false;
        }
    }

    /**
     * Choose Pharmacy By User
     */
    public function updateOrderOffer($order_id, $pharmacy_id, $apt_user_id)
    {
        $update = $this->conn->query("UPDATE apt_order_offers SET 
										IsAccepted 			= '1'
							
									WHERE 
										AptOrderID = '" . $order_id . "' and PharmacyID = '" . $pharmacy_id . "' and AptUserID = '" . $apt_user_id . "'");

        if ($update) {
            // create order log
            $order_status_id = 3;
            $this->createOrderLog($order_id, $order_status_id, $pharmacy_id);
            //Send notification to pharmacy
            $this->sendNotifToPharmacy($order_id, $pharmacy_id, $apt_user_id);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Proses Kirim barang, input no resi dan Jasa engiriman berdaasarkan AptOrderID
     */
    public function prosesKirimBarang($order_id, $no_resi, $jasa_pengiriman)
    {
        //4 = Pembayaran Berhasil
        //5 = Dalam pengiriman
        $query_update = $this->conn->query("UPDATE apt_orders 
                                            SET 
                                            NoResi = '" . $no_resi . "',
                                            JasaPengiriman = '" . $jasa_pengiriman . "',
                                            AptOrderStatusID = 5
                                            WHERE 
											AptOrderID = '" . $order_id . "' AND AptOrderStatusID=4 AND Active=1  ");

        if ($query_update) {
            $dt = $this->getPharmacyByOrderID($order_id);
            if ($dt != null) {
                $dt = $dt->fetch_assoc();
                $order_id = $order_id;
                $order_status_id = 5;
                $description = 'Log Order Pharmacy, created by sistem api';
                $pharmacy_id = $dt['PharmacyID'];
                $this->createOrderLogPharmacy($order_id, $order_status_id, $pharmacy_id, $description);
            }

            $custom_data = array(
                'type' => '94',
                'body' => "Order Anda sedang dalam pengiriman oleh kurir, cek no resi berikut",
                'title' => "Order Anda sedang dikirim",
                'AptOrderID' => $order_id,
                'UserID' => $dt['UserID'],
                'sound' => 'mysound.wav'
            );

            //Notify Patient
            $query_user = $this->conn->query("SELECT a.AptOrderID, b.UserID, b.FirebaseID FROM apt_orders a LEFT JOIN master_users b ON b.UserID=a.UserID WHERE a.AptOrderID = '" . $order_id . "' ");

            if (mysqli_num_rows($query_user) > 0) {
                $row_user = $query_user->fetch_assoc();
                $this->sendNotification_Patient($row_user['FirebaseID'], $custom_data);
            }

            return true;
        } else {
            return false;
        }
    }

    public function sendNotifToPharmacy($order_id, $pharmacy_id, $apt_user_id)
    {
        //Notify Another Pharmacy that offer has accepted by another pharmacy
        $custom_data = array(
            'type' => '3', //Job Offer Pharmacy
            'body' => "Maaf Order sudah diterima oleh Apotek lain",
            'title' => "Tawaran Anda",
            'OrderID' => $order_id
        );
        //Query untuk memilih phrmacy yang melakukan penawaran tetapi tidak diterima penawarannya oleh pasient (IsAccepted = 0),
        // lalu di notif ke mereka karena sudah ada phrmacy yang mengambil orderan mereka
        $query_apt_user = $this->conn->query("SELECT
                                                    a.AptOrderID,
                                                    b.FirebaseID,
                                                    c.PharmacyID,
                                                    c.AptUserID 	
                                                  FROM
                                                    apt_orders a
                                                    LEFT JOIN apt_order_offers c ON a.AptOrderID = c.AptOrderID 
	                                                LEFT JOIN apt_users b ON b.AptUserID = c.AptUserID  
	                                                WHERE a.AptOrderID = '" . $order_id . "' AND c.IsAccepted = 0 ");

        if (mysqli_num_rows($query_apt_user) > 0) {
            while ($row_apt_user = $query_apt_user->fetch_assoc()) {
                $this->sendNotification_Pharmacy($row_apt_user['FirebaseID'], $custom_data);
            }
        }

        //Notify Another Pharmacy that offer has accepted by another pharmacy
        $custom_data2 = array(
            'type' => '5', //Job Offer Pharmacy
            'body' => "Pasien telah memilih tawaran dari Apotek Anda",
            'title' => "Tawaran Anda",
            'AptOrderID' => $order_id,
            'AptUserID' => $apt_user_id,
            'PharmacyID' => $pharmacy_id,

        );
        //Query untuk memilih phrmacy yang melakukan penawaran dan diterima penawarannya oleh pasient (IsAccepted = 1),
        // lalu di notif ke pharmacy kalau user telah menerima order dari pahrmacy tersebut
        $query_apt_user_accepted = $this->conn->query("SELECT
                                                    a.AptOrderID,
                                                    b.FirebaseID,
                                                    c.PharmacyID,
                                                    c.AptUserID 	
                                                  FROM
                                                    apt_orders a
                                                    LEFT JOIN apt_order_offers c ON a.AptOrderID = c.AptOrderID 
	                                                LEFT JOIN apt_users b ON b.AptUserID = c.AptUserID  
	                                                WHERE a.AptOrderID = '" . $order_id . "' AND c.IsAccepted = 1 ");

        if (mysqli_num_rows($query_apt_user_accepted) > 0) {
            while ($row_apt_user = $query_apt_user_accepted->fetch_assoc()) {
                $this->sendNotification_Pharmacy($row_apt_user['FirebaseID'], $custom_data2);
            }
        }
    }


    /**
     * Choose Pharmacy By User
     */
    public function choosePharmacy($order_id, $pharmacy_id, $total_price, $apt_user_id, $sub_total, $transport)
    {


        $update = $this->conn->query("UPDATE apt_orders SET 
										PharmacyID 			= '" . $pharmacy_id . "',
										TotalPayment 			= '" . $total_price . "',
										AptUserID 			= '" . $apt_user_id . "',
										SubTotal			= '" . $sub_total . "',
										Transport 			= '" . $transport . "',
										AptOrderStatusID 	= 3
									WHERE 
										AptOrderID = '" . $order_id . "'");

        if ($update) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Process Logout
     */
    public function processLogout($user_id)
    {

        $update = $this->conn->query("UPDATE apt_users SET 
									IsLogin = '0',
									FirebaseID = '',
									Token = ''
								WHERE 
									AptUserID = '" . $user_id . "'");

        if ($update) {
            return true;
        } else {
            return false;
        }
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
        if ($result == FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
//        echo $result;exit;
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
     * Check if pharmacy user exist
     */
    public function checkPharmacyRegister($phone)
    {
        $query = $this->conn->query("SELECT * FROM apt_users WHERE Phone = '" . $phone . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0) {
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

        $update = $this->conn->query("UPDATE apt_users SET 
										ForgotPassword	 		= '" . $new_pass . "',
										ForgotPasswordExpired 	= '" . $expired_date . "'
									WHERE 
										Phone = '" . $phone . "' AND Active=1 ");

        if ($update) {
            $this->send_sms_password($phone, $new_pass);
            return $new_pass;
        } else {
            return false;
        }
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
        $message = "VTAL Pharmacy: Hi, berikut adalah password baru anda: " . $code . ", diharapkan segera ubah password anda";
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
     * Check email exist by_phone_number
     */
    public function checkEmailbyPhone($phone)
    {
        $query = $this->conn->query("SELECT Email, FirstName, LastName FROM apt_users WHERE Phone = '" . $phone . "' AND Active=1 limit 1");
        $row = $query->fetch_assoc();

        if (mysqli_num_rows($query) > 0) {
            return $row;
        } else {
            return false;
        }
    }

    /**
     * Check referral code exist on table referral_code_perusahaan
     */
    public function checkReferralCodeExist($pharmacy_id)
    {
        $query = $this->conn->query("SELECT ReferralID FROM referral_code_perusahaan WHERE OwnerID = '" . $pharmacy_id . "' AND Owner='apt_pharmacies' limit 1");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * select referral code on table referral_code_perusahaan
     */
    public function selectReferralCode($pharmacy_id)
    {
        $query = $this->conn->query("SELECT
                                        ( @cnt := @cnt + 1 ) AS rowNumber,
                                        a.ReferralID as rowRef,
                                        count( b.ReferralBy ) AS rowUsed,
                                        DATE_FORMAT(a.CreatedDate, '%d-%m-%Y %H:%i:%s') as CreatedDate
                                    FROM
                                        referral_code_perusahaan a
                                        LEFT JOIN master_users b ON b.ReferralBy = a.ReferralID AND b.Active = 1
                                        CROSS JOIN ( SELECT @cnt := 0 ) AS dummy 
                                        WHERE
                                        a.OwnerID = '" . $pharmacy_id . "' 
                                        AND a.OWNER = 'apt_pharmacies'  
                                        GROUP BY a.ReferralID
                                    ORDER BY
                                        ( @cnt := @cnt + 1 )");

        if (mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return false;
        }
    }

    public function checkPromoExistByPharmacyID($pharmacy_id)
    {
        $query = $this->conn->query("SELECT
                                        ( @cnt := @cnt + 1 ) AS rowNumber,
                                        a.Title as rowJudul,
                                        a.Harga as rowHarga,
                                        strip_tags ( REPLACE ( REPLACE ( a.Content, '&nbsp;', '' ), '\r\n', '' )) AS rowDeskripsi,
                                        DATE_FORMAT(a.CreatedDate, '%d-%m-%Y %H:%i:%s') as CreatedDate
                                    FROM
                                        master_promo a
                                        CROSS JOIN ( SELECT @cnt := 0 ) AS dummy 
                                        WHERE
                                        a.OwnerKategori = 'apt_pharmacies'
                                        AND
                                        a.OwnerID = '" . $pharmacy_id . "'
                                    ORDER BY
                                        ( @cnt := @cnt + 1 )");

        if (mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return false;
        }
    }

    function CekResi($no_resi, $kurir)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/waybill",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "waybill=$no_resi&courier=$kurir",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                "key: " . RAJAONGKIR_KEY
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return json_decode($response);
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
        //TypeID = 8, Pharamcy
        $query_get = $this->conn->query("SELECT PublishedDate, ArticleID, Title, Caption, CreatedDate, CONCAT('" . $this->uploaddir . "', '/articles/', ArticleID,'.jpg') AS Url FROM master_articles WHERE Active = 1 AND TypeID = 8 ORDER BY PublishedDate DESC " . $limit);

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

}


?>