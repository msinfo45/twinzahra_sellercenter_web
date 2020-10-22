<?php

class Model_pharmacy_order_driver
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
     * Process Send Job Offer to Kurir
     * * Author: Mahmuddin
     * * @param : AptOrderID, PharmacyID, AptUserID
     */
    public function processSendJobOfferKurir($apt_order_id, $pharmacy_id, $apt_user_id)
    {
        //Firts, Insert only AptOrderID into kurir_orders

        /* Generate Order No (Invoice Number) */
        //Get Last Order ID +1
        $check_orderno = $this->conn->query("SELECT IFNULL(MAX(Right(OrderNo,11)),0) AS OrderNo
											FROM kurir_orders 
											WHERE 
												DATE_FORMAT(CreatedDate, '%m')='" . date('m') . "' AND 
												DATE_FORMAT(CreatedDate, '%Y')='" . date('Y') . "'");

        $check_orderno = $check_orderno->fetch_assoc();

        if ($check_orderno['OrderNo'] == 0) {
            //Start From First
            $num = date('y') . date('m') . "1000001";
            $order_no = "KUR" . $num;
        } else {
            //Continue Number +1
            $num = $check_orderno['OrderNo'] + 1;
            $order_no = "KUR" . $num;
        }
        /* End generate */

        $insert = $this->conn->query("INSERT INTO kurir_orders 
										(
										OrderNo,
										AptOrderID,
										CreatedDate
										) 
									VALUES 
										(
										'" . $order_no . "',
										'" . $apt_order_id . "',
										'" . $this->get_current_time() . "'
										)");

        $last_id_kurir_orders = $this->conn->insert_id;

        $insert2 = $this->conn->query("INSERT INTO kurir_order_offers 
										(
										KurirOrderID,
										PharmacyID,
										AptUserID,
										OfferDate
										) 
									VALUES 
										('" . $last_id_kurir_orders . "',
										'" . $pharmacy_id . "',
										'" . $apt_user_id . "',
										'" . $this->get_current_time() . "'
										)");

        if ($insert2) {
            //create order log
            $kurir_order_id = $last_id_kurir_orders;
            $order_status_id = 1; //Mencari Kurir
            $description = 'Log Order Pharmacy find kurir, created by sistem api';
            //create order log kurir
            $this->createOrderLogKurir($kurir_order_id, $apt_order_id, $order_status_id, $description);

            //send push notif
            $this->sendJobOfferKurir($apt_order_id);


            return true;

        } else {
            return false;
        }
    }

    /**
     * Order Logs
     * *  Author: Mahmuddin
     */
    public function createOrderLogKurir($kurir_order_id = NULL, $apt_order_id, $order_status_id, $description)
    {
        $q = $this->conn->query("INSERT INTO kurir_orders_logs 
									(
									KurirOrderID,
									AptOrderID,
									CreatedDate,
									CreatedBy,
									OrderStatusID,
									Description
									) 
								VALUES 
									('" . $kurir_order_id . "',
									'" . $apt_order_id . "',
									'" . $this->get_current_time() . "',
									'9-',
									'" . $order_status_id . "',
									'" . $description . "'
									)");

        if ($q) {
            return $this->conn->insert_id;
        } else {
            return false;
        }
    }

    /**
     * Send Job Offer Notification to Kurir
     * *  Author: Mahmuddin
     */
    public function sendJobOfferKurir($apt_order_id)
    {
        //Get Data
        //apt_order_offers = accepted
        //apt_orders.AptOrderStatusID = 4 (Pembayaran Berhasil)
        $query = $this->conn->query("SELECT
                                        apt_orders.AptOrderID,
                                        apt_orders.OrderDate,
                                        apt_orders.OrderNo,
                                        apt_orders.UserID,
                                        apt_orders.Notes,
                                        apt_orders.AptOrderStatusID,
                                        apt_orders.Transport,
                                        apt_orders.Active,
                                        apt_orders.Latitude AS Latitude_tujuan_pengiriman,
                                        apt_orders.Longitude AS Longitude_tujuan_pengiriman,
                                        apt_orders.Location AS Lokasi_pengiriman,
                                        apt_order_offers.PharmacyID,
                                        apt_orders_aptoteker_accept.AptUserID,
                                        master_users.FirstName AS FirstName_patient,
                                        master_users.LastName AS LastName_patient,
                                        master_users.Address AS Address_patient,
                                        master_users.Phone AS Phone_patient,
                                        apt_users.FirstName AS FirstName_apt,
                                        apt_users.LastName AS LastName_apt,
                                        apt_users.Phone AS Phone_apt,
                                        apt_pharmacies.Address AS Address_pharmacy,
                                        apt_pharmacies.`Name` AS Nama_Pharmacy,
                                        apt_pharmacies.Latitude AS Latitude_pharmacy,
                                        apt_pharmacies.Longitude AS Longitude_pharmacy,
                                        apt_pharmacies.Phone AS Phone_pharmacy 
                                    FROM
                                        apt_orders
                                        LEFT JOIN apt_order_offers ON apt_orders.AptOrderID = apt_order_offers.AptOrderID
                                        LEFT JOIN apt_orders_aptoteker_accept ON apt_orders.AptOrderID = apt_orders_aptoteker_accept.AptOrderID
                                        LEFT JOIN master_users ON apt_orders.UserID = master_users.UserID
                                        LEFT JOIN apt_users ON apt_orders_aptoteker_accept.AptUserID = apt_users.AptUserID
                                        LEFT JOIN apt_pharmacies ON apt_order_offers.PharmacyID = apt_pharmacies.PharmacyID 
                                    WHERE
                                        apt_orders.AptOrderStatusID = 4
                                        AND apt_orders.AptOrderID = '" . $apt_order_id . "'
									");

        if (mysqli_num_rows($query) > 0) {

            $row = $query->fetch_assoc();

            $custom_data = array(
                'type' => '99',
                'body' => "Hi, ada tawaran order kurir nih",
                'title' => "Tawaran Order Kurir",
                "url" => "",
                'AptOrderID' => $apt_order_id,
                'OrderDate' => $row['OrderDate'],
                'OrderNo' => $row['OrderNo'],
                'UserID' => $row['UserID'],
                'Notes' => $row['Notes'],
                'AptOrderStatusID' => $row['AptOrderStatusID'],
                'Transport' => $row['Transport'],
                'Active' => $row['Active'],
                'Latitude_tujuan_pengiriman' => $row['Latitude_tujuan_pengiriman'],
                'Longitude_tujuan_pengiriman' => $row['Longitude_tujuan_pengiriman'],
                'Lokasi_pengiriman' => $row['Lokasi_pengiriman'],
                'PharmacyID' => $row['PharmacyID'],
                'AptUserID' => $row['AptUserID'],
                'FirstName_patient' => $row['FirstName_patient'],
                'LastName_patient' => $row['LastName_patient'],
                'Address_patient' => $row['Address_patient'],
                'Phone_patient' => $row['Phone_patient'],
                'FirstName_apt' => $row['FirstName_apt'],
                'LastName_apt' => $row['LastName_apt'],
                'Phone_apt' => $row['Phone_apt'],
                'Address_pharmacy' => $row['Address_pharmacy'],
                'Nama_Pharmacy' => $row['Nama_Pharmacy'],
                'Latitude_pharmacy' => $row['Latitude_pharmacy'],
                'Longitude_pharmacy' => $row['Longitude_pharmacy'],
                'Phone_pharmacy' => $row['Phone_pharmacy']
            );

            $Latitude_pharmacy = $row['Latitude_pharmacy'];
            $Longitude_pharmacy = $row['Longitude_pharmacy'];

            // var_dump($custom_data);
            //Notify Online Kurir, within 5KM around
            $query_kurir = $this->conn->query("SELECT 
                                                a.*, 
                                                b.Active,
												111.111 *
                                                DEGREES(ACOS(COS(RADIANS(b.Latitude))
                                                     * COS(RADIANS('" . $Latitude_pharmacy . "'))
                                                     * COS(RADIANS(b.Longitude - '" . $Longitude_pharmacy . "'))
                                                     + SIN(RADIANS(b.Latitude))
                                                     * SIN(RADIANS('" . $Latitude_pharmacy . "')))) AS distance_in_km
											FROM kurir_users a
											INNER JOIN kurir_location b ON b.KurirUserID = a.KurirUserID
											HAVING 
												distance_in_km <= 5 AND  
												a.FirebaseID IS NOT NULL AND 
												a.Active=1 AND 
												b.Active=1");

            if (mysqli_num_rows($query_kurir) > 0) {


                while ($row_kurir = $query_kurir->fetch_assoc()) {
                    $this->sendNotification_Kurir($row_kurir['FirebaseID'], $custom_data);
                }

            }
        }
    }

    /**
     * Function Send GCM to Patient
     * @param : FirebaseID, Custom Data JSON
     * Author: Mahmuddin
     * returns boolean
     */
    function sendNotification_Kurir($firebase_id, $custom_data)
    {

        $registrationIds = array($firebase_id);

        $fields = array(
            'registration_ids' => $registrationIds,
            'data' => $custom_data
        );
        //Change this header Authorization: key with Kurir Firebase Authorization key
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
     * Get pending order ID Kurir
     */
    public function getKurirPendingOrderID($apt_user_id)
    {
        $query = $this->conn->query("SELECT kurir_orders.* from kurir_orders
                                    left join kurir_order_offers on kurir_order_offers.KurirOrderID = kurir_orders.KurirOrderID
                                    WHERE 
                                    kurir_order_offers.AptUserID = '" . $apt_user_id . "' AND kurir_orders.OrderStatusID=1 AND Active=1 LIMIT 1");

        if (mysqli_num_rows($query) > 0) {
            $row = $query->fetch_assoc();
            $current_id = $row['KurirOrderID'];

            return $current_id;
        } else {
            return null;
        }
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

}


?>