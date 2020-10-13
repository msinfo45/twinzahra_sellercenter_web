<?php

include "../config/db_connection.php";
include "../config/config_type.php";

$rows = array();
$rows2 = array();

$path = '/home/ambulano/public_html/image/customers/';
$uploaddir = "http://vtal.id/image";
$content = $_GET['request'];
if (isset($content)) {

    //Load Util Class
    include "../config/Util.php";
    $global = new Util();

    /*----------------------------------------- Another Function ------------------------------------*/

    //Send Broadcast to pusher
    function send_broadcast($conn, $order_id, $type)
    {
        require __DIR__ . '/vendor/autoload.php';

        if ($type == "usercancel") {
            $table = "amb_emergencyorders_finish";
        } else {
            $table = "amb_emergencyorders";
        }

        //Get data
        $query_get = $conn->query("SELECT 	a.EmergencyOrderID,
											a.UserID,
											a.CreatedDate,
											a.Latitude,
											a.Longitude,
											a.PickupLocation,
											a.EmergencyStatusID,
											a.DriverID,
											a.HospitalID,
											IFNULL(c.DriverName, '') AS DriverName,
											IFNULL(e.HospitalName, '') AS HospitalName,
											d.EmergencyStatus,
											b.FirstName,
											b.LastName
									FROM " . $table . " a
									INNER JOIN master_users b ON b.UserID = a.UserID
									LEFT JOIN master_drivers c ON c.DriverID = a.DriverID
									INNER JOIN amb_emergencystatus d ON d.EmergencyStatusID = a.EmergencyStatusID
									LEFT JOIN master_hospitals e ON e.HospitalID = a.HospitalID
									WHERE a.EmergencyOrderID = '" . $order_id . "' ");

        $data_db = $query_get->fetch_assoc();

        $rowArray = [
            'createdDate' => $data_db['CreatedDate'],
            'emergencyOrderID' => $data_db['EmergencyOrderID'],
            'firstName' => $data_db['FirstName'],
            'lastName' => $data_db['LastName'],
            'latitude' => $data_db['Latitude'],
            'longitude' => $data_db['Longitude'],
            'pickupLocation' => $data_db['PickupLocation'],
            'hospitalName' => $data_db['HospitalName'],
            'emergencyStatus' => $data_db['EmergencyStatus'],
            'emergencyStatusID' => $data_db['EmergencyStatusID'],
            'UserID' => $data_db['UserID'],
            'driverName' => $data_db['DriverName'],
            'hospitalID' => $data_db['HospitalID']
        ];

        $options = array(
            // 'encrypted' => true,
            'cluster' => 'ap1'
        );
        $pusher = new Pusher\Pusher(
            '023b57443ff0a186dc02',
            'd5adc49e152a1a13b133',
            '400500',
            $options
        );

        $data['type'] = $type;
        $data['message'] = 'New Record';
        $data['row'] = $rowArray;
        $pusher->trigger('my-channel', 'my-event', $data);

        //echo "succes send broadcast check listener";
    }

    //Log Function
    function order_log($conn, $orderID, $statusID, $time)
    {

        $query = $conn->query("SELECT * FROM amb_emergencyorders_log WHERE EmergencyOrderID = '" . $orderID . "' AND EmergencyStatusID = '" . $statusID . "' ");
        if (mysqli_num_rows($query) > 0) {
            $conn->query("UPDATE amb_emergencyorders_log SET EmergencyOrderID = '" . $orderID . "', EmergencyStatusID = '" . $statusID . "', LogDate = '" . $time . "' WHERE EmergencyOrderID = '" . $orderID . "' AND EmergencyStatusID = '" . $statusID . "' ");
        } else {
            $conn->query("INSERT INTO amb_emergencyorders_log(EmergencyOrderID, EmergencyStatusID, LogDate) VALUES ('" . $orderID . "', '" . $statusID . "', '" . $time . "') ");
        }
    }

    //Temporary Save Function
    function create_order_temp($conn, $orderID, $DriverID)
    {

        $query = $conn->query("SELECT * FROM amb_emergencyorders_temp WHERE EmergencyOrderID = '" . $orderID . "' AND DriverID = '" . $DriverID . "' ");

        if (mysqli_num_rows($query) > 0) {
            $conn->query("UPDATE amb_emergencyorders_temp SET EmergencyOrderID = '" . $orderID . "', DriverID = '" . $DriverID . "' WHERE EmergencyOrderID = '" . $orderID . "' AND DriverID = '" . $DriverID . "' ");
        } else {
            $conn->query("INSERT INTO amb_emergencyorders_temp(EmergencyOrderID, DriverID) VALUES ('" . $orderID . "', '" . $DriverID . "') ");
        }
    }

    //Temporary Delete Function
    function delete_order_temp($conn, $orderID, $DriverID)
    {

        $query = $conn->query("SELECT * FROM amb_emergencyorders_temp WHERE EmergencyOrderID = '" . $orderID . "' AND DriverID = '" . $DriverID . "' ");

        if (mysqli_num_rows($query) > 0) {
            $conn->query("DELETE FROM amb_emergencyorders_temp WHERE EmergencyOrderID = '" . $orderID . "' AND DriverID = '" . $DriverID . "' ");
        }
    }

    //Delete Current ORDER Function
    function delete_current_order($conn, $orderID)
    {
        $query = $conn->query("SELECT * FROM amb_emergencyorders WHERE EmergencyOrderID = '" . $orderID . "' ");

        if (mysqli_num_rows($query) > 0) {
            $conn->query("DELETE FROM amb_emergencyorders WHERE EmergencyOrderID = '" . $orderID . "' ");
        }
    }


    /*----------------------------------- API Start Here -------------------------------------*/

    //Get Nearest hospitals API
    if ($content == "hospital_around") {

        $post = json_decode(file_get_contents("php://input"), true);

        $latitude = $post['latitude'];
        $longitude = $post['longitude'];
        $customer_id = $post['customer_id'];

        if (isset($latitude) && isset($longitude) && isset($customer_id)) {

            $now = $global->get_current_time();

            //Select hospital around of 5Km
            $query = $conn->query("SELECT 	a.HospitalID, 
											a.HospitalName, 
											a.HospitalLatitude, 
											a.HospitalLongitude, 
											a.HospitalAddress,
											(3959 * ACOS(COS(RADIANS(" . $latitude . "))*COS(RADIANS(a.HospitalLatitude))*COS(RADIANS(a.HospitalLongitude)-RADIANS(" . $longitude . ")) + SIN(RADIANS(" . $latitude . "))*SIN(RADIANS(a.HospitalLatitude)))) AS distance 
									FROM hospitals a 
									HAVING distance <= 5 
									ORDER BY distance ASC");

            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }

            $return = array(
                "status" => 200,
                "message" => "ok",
                "total" => mysqli_num_rows($query),
                "data" => $rows
            );
        } else {
            $return = array(
                "status" => 404,
                "message" => "Parameter Required!"
            );
        }

        echo json_encode($return);
    }

    //Emergency Order API
    if ($content == "emergency_order") {

        $post = json_decode(file_get_contents("php://input"), true);

        $customer_id = $post['UserID'];
        $latitude = $post['Latitude'];
        $longitude = $post['Longitude'];
        $location = $post['Location'];

        if (isset($customer_id) && isset($latitude) && isset($longitude) && isset($location)) {

            $query = $conn->query("SELECT * FROM amb_emergencyorders WHERE UserID = '" . $customer_id . "' AND EmergencyStatusID = 2 ");

            $now = $global->get_current_time();

            if (mysqli_num_rows($query) == 0) {

                //Insert Pending Order
                $query_ins = $conn->query("
					INSERT INTO amb_emergencyorders(UserID, Latitude, Longitude, PickupLocation, EmergencyStatusID, CreatedDate) 
						VALUES 
					('" . $customer_id . "','" . $latitude . "','" . $longitude . "','" . $location . "','2','" . $now . "')");

                if ($query_ins) {
                    $return = array(
                        "status" => 200,
                        "message" => "ok",
                        "order_id" => $conn->insert_id
                    );

                    send_broadcast($conn, $conn->insert_id, "add");
                    order_log($conn, $conn->insert_id, "2", $now);
                } else {
                    $return = array(
                        "status" => 404,
                        "message" => "Gagal melakukan order, mohon coba beberapa saat lagi!"
                    );
                }
            } else {
                //Update pending order
                $myRow = $query->fetch_assoc();
                $currentID = $myRow['EmergencyOrderID'];

                $upd = $conn->query("UPDATE amb_emergencyorders SET 
										PickupLocation = '" . $location . "',
										HospitalID = 0,
										DriverID = 0,
										Latitude = '" . $latitude . "',
										Longitude = '" . $longitude . "',
										ModifiedDate = '" . $now . "'
									WHERE
										EmergencyOrderID = '" . $currentID . "'
									");

                if ($upd) {
                    $return = array(
                        "status" => 200,
                        "message" => "ok",
                        "order_id" => $currentID
                    );

                    send_broadcast($conn, $currentID, "add");
                    order_log($conn, $conn->insert_id, "2", $now);
                } else {
                    $return = array(
                        "status" => 404,
                        "message" => "Gagal melakukan order, mohon coba beberapa saat lagi!"
                    );
                }
            }

        } else {
            $return = array(
                "status" => 404,
                "message" => "Mohon masukan alamat penjemputan!"
            );
        }
        echo json_encode($return);
    }

    //Cancel Order API
    if ($content == "cancel_order") {

        $reason = "";

        $post = json_decode(file_get_contents("php://input"), true);

        $customer_id = $post['customer_id'];
        $order_id = $post['order_id'];
        $reason = $post['reason'];

        if (isset($customer_id) && isset($order_id)) {
            $query = $conn->query("SELECT * FROM amb_emergencyorders WHERE EmergencyOrderID='" . $order_id . "' AND UserID = '" . $customer_id . "' AND EmergencyStatusID IN (3,4,5,6,7) ORDER BY CreatedDate DESC LIMIT 1");

            $now = $global->get_current_time();

            if (mysqli_num_rows($query) > 0) {

                $myRow = $query->fetch_assoc();
                $drv_id = $myRow['DriverID'];

                $query_upd = $conn->query("UPDATE amb_emergencyorders SET CancelReason='" . $reason . "', EmergencyStatusID = 9, ModifiedDate = '" . $now . "' WHERE UserID = '" . $customer_id . "' AND EmergencyOrderID = '" . $order_id . "' ");
                if ($query_upd) {

                    //Delete Temp Data
                    delete_order_temp($conn, $order_id, $drv_id);

                    //Delete Current Order
                    //delete_current_order($conn, $order_id);

                    $return = array(
                        "status" => 200,
                        "order_id" => $order_id,
                        "message" => "Order berhasil di batalkan"
                    );

                    send_broadcast($conn, $order_id, "usercancel");

                } else {
                    $return = array(
                        "status" => 404,
                        "message" => "Gagal membatalkan order, mohon coba beberapa saat lagi!"
                    );
                }

            } else {
                $return = array(
                    "status" => 404,
                    "message" => "Data order tidak di temukan!"
                );
            }
        } else {
            $return = array(
                "status" => 404,
                "message" => "Oops sepertinya ada yang salah!"
            );
        }

        echo json_encode($return);
    }

    //Get Current Order API
    if ($content == "hospital_accepted") {

        $post = json_decode(file_get_contents("php://input"), true);

        $customer_id = $post['customer_id'];
        $order_id = $post['order_id'];

        if (isset($customer_id) && isset($order_id)) {

            //Select hospital around of 5Km
            $query = $conn->query("SELECT 	a.EmergencyOrderID,
											a.UserID,
											a.Latitude,
											a.Longitude,
											a.HospitalID,
											a.EmergencyStatusID,
											b.HospitalName, 
											b.HospitalClass,
											b.HospitalLatitude, 
											b.HospitalLongitude, 
											b.HospitalAddress,
											IFNULL(a.DriverID, '') AS DriverID,
											IFNULL(c.DriverName, '') AS DriverName,
											IFNULL(c.DriverPhone, '') AS DriverPhone,
											CONCAT($uploaddir,'/hospitals/', a.HospitalID,'_1','.jpg') AS Url
									FROM amb_emergencyorders a
									LEFT JOIN master_hospitals b ON b.HospitalID = a.HospitalID
									LEFT JOIN master_drivers c ON c.DriverID = a.DriverID
									WHERE 
										a.EmergencyOrderID = '" . $order_id . "' AND
										a.UserID = '" . $customer_id . "' AND
										a.EmergencyStatusID IN (2,3,4,5,6,7)
									LIMIT 1");

            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }

            $return = array(
                "status" => 200,
                "message" => "ok",
                "total" => mysqli_num_rows($query),
                "data" => $rows
            );
        } else {
            $return = array(
                "status" => 404,
                "message" => "Oops sepertinya ada yang salah!"
            );
        }

        echo json_encode($return);
    }

    //Get Current Order API
    if ($content == "driver_accepted") {

        $post = json_decode(file_get_contents("php://input"), true);

        $customer_id = $post['customer_id'];
        $order_id = $post['order_id'];

        if (isset($customer_id) && isset($order_id)) {

            //Select hospital around of 5Km
            $query = $conn->query("SELECT 	a.EmergencyOrderID,
											a.UserID,
											a.HospitalID,
											a.EmergencyStatusID,
											f.EmergencyStatus,
											a.Latitude,
											a.Longitude,
											a.PickupLocation,
											b.HospitalName,
											b.HospitalLatitude, 
											b.HospitalLongitude, 
											b.HospitalAddress,
											a.DriverID,
											c.DriverName,
											c.DriverPhone,
											e.PoliceNo,
											d.Latitude AS 'DriverLatitude',
											d.Longitude AS 'DriverLongitude',
											CONCAT($uploaddir,'/hospitals/', a.HospitalID,'_1','.jpg') AS Url,
											ROUND((3959 * ACOS(COS(RADIANS(d.Latitude))*COS(RADIANS(b.HospitalLatitude))*COS(RADIANS(b.HospitalLongitude)-RADIANS(d.Longitude)) + SIN(RADIANS(d.Latitude))*SIN(RADIANS(b.HospitalLatitude)))),2) AS DistanceDriverHospital,
											ROUND((3959 * ACOS(COS(RADIANS(d.Latitude))*COS(RADIANS(a.Latitude))*COS(RADIANS(a.Longitude)-RADIANS(d.Longitude)) + SIN(RADIANS(d.Latitude))*SIN(RADIANS(a.Latitude)))),2) AS DistanceDriverUser 
									FROM amb_emergencyorders a
									INNER JOIN master_hospitals b ON a.HospitalID = b.HospitalID
									INNER JOIN master_drivers c ON c.DriverID = a.DriverID
									INNER JOIN amb_driverlocation d ON d.DriverID = a.DriverID
									INNER JOIN master_ambulances e ON e.AmbulanceID = d.AmbulanceID
									INNER JOIN amb_emergencystatus f ON f.EmergencyStatusID = a.EmergencyStatusID
									WHERE 
										a.EmergencyOrderID = '" . $order_id . "' AND
										a.UserID = '" . $customer_id . "' AND
										a.EmergencyStatusID IN (5,6,7)
									LIMIT 1");

            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }

            $return = array(
                "status" => 200,
                "message" => "ok",
                "total" => mysqli_num_rows($query),
                "data" => $rows
            );
        } else {
            $return = array(
                "status" => 404,
                "message" => "Oops sepertinya ada yang salah!"
            );
        }

        echo json_encode($return);
    }

    //Get History API
    if ($content == "history") {

        $post = json_decode(file_get_contents("php://input"), true);

        $customer_id = $post['customer_id'];

        if (isset($customer_id)) {

            $query = $conn->query("SELECT
										a.EmergencyOrderID,
										a.CreatedDate,
										a.PickupLocation,
										a.HospitalID,
										a.EmergencyStatusID,
										IFNULL(b.HospitalName,'-') AS HospitalName,
										IFNULL(d.DriverName,'-') AS DriverName,
										c.EmergencyStatus,
										a.EmergencyStatusID,
										a.Rating,
										CONCAT($uploaddir,'/hospitals/', a.HospitalID,'_1','.jpg') AS Url
									FROM amb_emergencyorders a 
										LEFT JOIN master_hospitals b ON b.HospitalID = a.HospitalID
										INNER JOIN amb_emergencystatus c ON c.EmergencyStatusID = a.EmergencyStatusID
										LEFT JOIN master_drivers d ON d.DriverID = a.DriverID
									WHERE 
										a.EmergencyStatusID NOT IN (1,2) AND 
										a.UserID = '" . $customer_id . "' 
									ORDER BY a.EmergencyOrderID DESC ");

            $rowTotal = mysqli_num_rows($query);
            if ($rowTotal > 0) {
                while ($row = $query->fetch_assoc()) {
                    $rows[] = $row;
                }

                $return = array(
                    "status" => 200,
                    "message" => "ok",
                    "total" => $rowTotal,
                    "data" => $rows
                );
            } else {
                $return = array(
                    "status" => 200,
                    "message" => "Belum ada riwayat",
                    "total" => 0
                );
            }
        } else {
            $return = array(
                "status" => 404,
                "message" => "Parameter Required!"
            );
        }

        echo json_encode($return);
    }

    //Get Current Order API
    if ($content == "history_detail") {

        $post = json_decode(file_get_contents("php://input"), true);

        $order_id = $post['order_id'];

        if (isset($order_id)) {

            //Select hospital around of 5Km
            $query = $conn->query("SELECT 	a.EmergencyOrderID,
											a.UserID,
											a.HospitalID,
											a.EmergencyStatusID,
											f.EmergencyStatus,
											a.Latitude,
											a.Longitude,
											a.PickupLocation,
											b.HospitalName,
											b.HospitalLatitude, 
											b.HospitalLongitude, 
											b.HospitalAddress,
											a.DriverID,
											c.DriverName,
											c.DriverPhone,
											e.PoliceNo,
											a.Rating,
											d.Latitude AS 'DriverLatitude',
											d.Longitude AS 'DriverLongitude',
											CONCAT($uploaddir,'/hospitals/', a.HospitalID,'_1','.jpg') AS Url
									FROM amb_emergencyorders a
									INNER JOIN master_hospitals b ON a.HospitalID = b.HospitalID
									INNER JOIN master_drivers c ON c.DriverID = a.DriverID
									INNER JOIN amb_driverlocation d ON d.DriverID = a.DriverID
									INNER JOIN master_ambulances e ON e.AmbulanceID = d.AmbulanceID
									INNER JOIN amb_emergencystatus f ON f.EmergencyStatusID = a.EmergencyStatusID
									WHERE 
										a.EmergencyOrderID = '" . $order_id . "' AND
										a.EmergencyStatusID = 8
									LIMIT 1");

            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }

            $return = array(
                "status" => 200,
                "message" => "ok",
                "total" => mysqli_num_rows($query),
                "data" => $rows
            );
        } else {
            $return = array(
                "status" => 404,
                "message" => "Oops sepertinya ada yang salah!"
            );
        }

        echo json_encode($return);
    }

    //Get History Done API
    if ($content == "history_done") {

        $post = json_decode(file_get_contents("php://input"), true);

        $customer_id = $post['customer_id'];

        if (isset($customer_id)) {

            $query = $conn->query("SELECT
										a.EmergencyOrderID,
										a.CreatedDate,
										a.PickupLocation,
										a.HospitalID,
										a.EmergencyStatusID,
										IFNULL(b.HospitalName,'-') AS HospitalName,
										IFNULL(d.DriverName,'-') AS DriverName,
										c.EmergencyStatus,
										a.EmergencyStatusID,
										a.Rating
									FROM amb_emergencyorders_finish a 
										LEFT JOIN hospitals b ON b.HospitalID = a.HospitalID
										INNER JOIN emergencystatus c ON c.EmergencyStatusID = a.EmergencyStatusID
										LEFT JOIN drivers d ON d.DriverID = a.DriverID
									WHERE 
										a.UserID = '" . $customer_id . "' AND 
										a.EmergencyStatusID IN (8,9) 
									ORDER BY a.EmergencyOrderID DESC ");

            while ($row = $query->fetch_assoc()) {
                $rows[] = $row;
            }

            $return = array(
                "status" => 200,
                "message" => "ok",
                "total" => mysqli_num_rows($query),
                "data" => $rows
            );
        } else {
            $return = array(
                "status" => 404,
                "message" => "Parameter Required!"
            );
        }

        echo json_encode($return);
    }

    //Get Articles API
    if ($content == "articles") {

        $query = $conn->query("SELECT * FROM articles WHERE Active=1 ORDER BY ArticleID DESC");

        while ($row = $query->fetch_assoc()) {
            $rows[] = $row;
        }

        $return = array(
            "status" => 200,
            "message" => "ok",
            "total" => mysqli_num_rows($query),
            "data" => $rows
        );

        echo json_encode($return);
    }

    //Get Insurance API
    if ($content == "insurance") {

        $query = $conn->query("SELECT * FROM insurances WHERE Active=1 ORDER BY InsuranceName ASC ");

        while ($row = $query->fetch_assoc()) {
            $rows[] = $row;
        }

        $return = array(
            "status" => 200,
            "message" => "ok",
            "total" => mysqli_num_rows($query),
            "data" => $rows
        );

        echo json_encode($return);
    }

    //Get Hospital API
    if ($content == "hospital_list") {

        $query = $conn->query("SELECT * FROM hospitals ORDER BY HospitalID DESC");

        while ($row = $query->fetch_assoc()) {
            $rows[] = $row;
        }

        $return = array(
            "status" => 200,
            "message" => "ok",
            "total" => mysqli_num_rows($query),
            "data" => $rows
        );

        echo json_encode($return);
    }

    //Check order if accepted by hospital, so notify the user popup
    if ($content == "check_received") {

        $post = json_decode(file_get_contents("php://input"), true);

        $order_id = $post['order_id'];

        if (isset($order_id)) {
            $query = $conn->query("SELECT 
									a.EmergencyOrderID,
									a.HospitalID,
									b.HospitalName,
									b.HospitalAddress,
									b.HospitalClass,
									CONCAT($uploaddir,'/hospitals/', a.HospitalID,'_1','.jpg') AS Url
								FROM amb_emergencyorders a 
								INNER JOIN master_hospitals b ON b.HospitalID = a.HospitalID
								WHERE a.EmergencyOrderID = '" . $order_id . "' 
								AND a.EmergencyStatusID IN (3,4)");

            if (mysqli_num_rows($query) > 0) {

                while ($row = $query->fetch_assoc()) {
                    $rows[] = $row;
                }

                $return = array(
                    "status" => 200,
                    "message" => "Kami menemukan rumah sakit terdekat",
                    "data" => $rows
                );

            } else {
                $return = array(
                    "status" => 404,
                    "message" => "Maaf, Kami tidak menemukan ambulan yang tersedia!"
                );
            }
        } else {
            $return = array(
                "status" => 404,
                "message" => "Oops sepertinya ada yang salah!"
            );
        }

        echo json_encode($return);
    }

    //Give Rating API
    if ($content == "rating") {

        $post = json_decode(file_get_contents("php://input"), true);

        $order_id = $post['order_id'];
        $rating = $post['rating'];
        $feedback = $post['feedback'];

        if (isset($order_id) && isset($rating) && isset($feedback)) {
            $now = $global->get_current_time();

            $query = $conn->query("SELECT * FROM amb_emergencyorders_finish WHERE EmergencyOrderID = '" . $order_id . "'");

            if (mysqli_num_rows($query) == 0) {
                //Data not found
                $return = array(
                    "status" => 404,
                    "message" => "Order not found!"
                );
            } else {
                //Update here
                $query_upd = $conn->query("UPDATE amb_emergencyorders_finish SET
					Rating = '" . $rating . "',
					Feedback = '" . $feedback . "'
				WHERE EmergencyOrderID = '" . $order_id . "'");

                if ($query_upd) {
                    $return = array(
                        "status" => 200,
                        "message" => "Terima kasih atas feedback nya",
                        "rating" => $rating
                    );
                } else {
                    $return = array(
                        "status" => 404,
                        "message" => "Gagal mengirim feedback"
                    );
                }
            }
        } else {
            $return = array(
                "status" => 404,
                "message" => "Parameter Required!"
            );
        }

        echo json_encode($return);
    }

    //Check Rating API
    if ($content == "check_rating") {

        $post = json_decode(file_get_contents("php://input"), true);

        $order_id = $post['order_id'];

        if (isset($order_id)) {
            $now = get_current_time();

            $query = $conn->query("SELECT * FROM amb_emergencyorders WHERE EmergencyOrderID = '" . $order_id . "' AND Rating=0");

            if (mysqli_num_rows($query) == 0) {
                $return = array(
                    "status" => 200,
                    "message" => "no",
                    "order_id" => $order_id
                );
            } else {
                $return = array(
                    "status" => 200,
                    "message" => "yes",
                    "order_id" => $order_id
                );
            }
        } else {
            $return = array(
                "status" => 404,
                "message" => "Parameter Required!"
            );
        }

        echo json_encode($return);
    }

    //Emergency Contact API
    if ($content == "emergency_contact") {

        $post = json_decode(file_get_contents("php://input"), true);

        $customer_id = $post['customer_id'];
        $name = $post['name'];
        $phone = $post['phone'];
        $id = $post['id'];

        if (isset($customer_id) && isset($name) && isset($phone) && isset($id)) {
            $now = get_current_time();

            $query = $conn->query("SELECT * FROM emergency_contacts WHERE ContactID = '" . $customer_id . "-" . $id . "'");

            if (mysqli_num_rows($query) == 0) {
                //Insert here
                $query_ins = $conn->query("INSERT INTO emergency_contacts (UserID, ContactID, ContactName, ContactPhone, CreatedDate)
											VALUES
												('" . $customer_id . "', '" . $customer_id . "-" . $id . "', '" . $name . "', '" . $phone . "', '" . $now . "') ");

                if ($query_ins) {
                    $return = array(
                        "status" => 200,
                        "id" => $id,
                        "message" => "Emergency Contact saved succesfully"
                    );
                } else {
                    $return = array(
                        "status" => 404,
                        "message" => "Failed save emergency contact!"
                    );
                }
            } else {
                //Update here
                $query_upd = $conn->query("UPDATE emergency_contacts SET
					ContactName = '" . $name . "',
					ContactPhone = '" . $phone . "',
					ModifiedDate = '" . $now . "'
				WHERE ContactID = '" . $customer_id . "-" . $id . "'");

                if ($query_upd) {
                    $return = array(
                        "status" => 200,
                        "id" => $id,
                        "message" => "Emergency Contact saved succesfully"
                    );
                } else {
                    $return = array(
                        "status" => 404,
                        "message" => "Failed update emergency contact!"
                    );
                }
            }
        } else {
            $return = array(
                "status" => 404,
                "message" => "Parameter Required!"
            );
        }

        echo json_encode($return);
    }

    //Delete Emergency Contact API
    if ($content == "del_emergency_contact") {

        $post = json_decode(file_get_contents("php://input"), true);

        $customer_id = $post['customer_id'];
        $id = $post['id'];

        if (isset($customer_id) && isset($id)) {
            $now = get_current_time();

            $query = $conn->query("SELECT * FROM emergency_contacts WHERE ContactID = '" . $customer_id . "-" . $id . "'");

            if (mysqli_num_rows($query) > 0) {
                //Do delete
                $query_del = $conn->query("DELETE FROM emergency_contacts WHERE ContactID = '" . $customer_id . "-" . $id . "' ");

                if ($query_del) {
                    $return = array(
                        "status" => 200,
                        "message" => "Emergency Contact deleted succesfully"
                    );
                } else {
                    $return = array(
                        "status" => 404,
                        "message" => "Failed delete emergency contact!"
                    );
                }
            } else {
                $return = array(
                    "status" => 404,
                    "message" => "Can't find emergency contact!"
                );
            }
        } else {
            $return = array(
                "status" => 404,
                "message" => "Parameter Required!"
            );
        }

        echo json_encode($return);
    }

    //Get Emergency Contact API
    if ($content == "get_emergency_contact") {

        $post = json_decode(file_get_contents("php://input"), true);

        $customer_id = $post['customer_id'];

        if (isset($customer_id)) {

            $name1 = "";
            $phone1 = "";
            $name2 = "";
            $phone2 = "";
            $name3 = "";
            $phone3 = "";
            $name4 = "";
            $phone4 = "";
            $name5 = "";
            $phone5 = "";

            $query1 = $conn->query("SELECT ContactID, ContactName, ContactPhone FROM emergency_contacts WHERE ContactID = '" . $customer_id . "-1' ");
            if (mysqli_num_rows($query1)) {
                $row1 = $query1->fetch_assoc();
                $name1 = $row1['ContactName'];
                $phone1 = $row1['ContactPhone'];
            }

            $query2 = $conn->query("SELECT ContactID, ContactName, ContactPhone FROM emergency_contacts WHERE ContactID = '" . $customer_id . "-2' ");
            if (mysqli_num_rows($query2)) {
                $row2 = $query2->fetch_assoc();
                $name2 = $row2['ContactName'];
                $phone2 = $row2['ContactPhone'];
            }

            $query3 = $conn->query("SELECT ContactID, ContactName, ContactPhone FROM emergency_contacts WHERE ContactID = '" . $customer_id . "-3' ");
            if (mysqli_num_rows($query3)) {
                $row3 = $query3->fetch_assoc();
                $name3 = $row3['ContactName'];
                $phone3 = $row3['ContactPhone'];
            }

            $query4 = $conn->query("SELECT ContactID, ContactName, ContactPhone FROM emergency_contacts WHERE ContactID = '" . $customer_id . "-4' ");
            if (mysqli_num_rows($query4)) {
                $row4 = $query4->fetch_assoc();
                $name4 = $row4['ContactName'];
                $phone4 = $row4['ContactPhone'];
            }

            $query5 = $conn->query("SELECT ContactID, ContactName, ContactPhone FROM emergency_contacts WHERE ContactID = '" . $customer_id . "-5' ");
            if (mysqli_num_rows($query5)) {
                $row5 = $query5->fetch_assoc();
                $name5 = $row5['ContactName'];
                $phone5 = $row5['ContactPhone'];
            }

            $return = array(
                "status" => 200,
                "message" => "ok",
                "name1" => $name1,
                "phone1" => $phone1,
                "name2" => $name2,
                "phone2" => $phone2,
                "name3" => $name3,
                "phone3" => $phone3,
                "name4" => $name4,
                "phone4" => $phone4,
                "name5" => $name5,
                "phone5" => $phone5,
            );
        } else {
            $return = array(
                "status" => 404,
                "message" => "Parameter Required!"
            );
        }

        echo json_encode($return);
    }


} else {
    //Aha, what you're looking for !!!
    $return = array(
        "status" => 404,
        "message" => "Method Not Found!"
    );

    echo json_encode($return);
}

?>