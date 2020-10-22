<?php
    private $conn;

    // constructor
    function __construct()
    {
        include "db_connection.php";
        include "config_type.php";
        $this->conn = $conn;
    
    }

    // destructor
    function __destruct()
    {

    }
	
$fp_paidto = $_POST['fp_paidto'];
$fp_paidby = $_POST['fp_paidby'];
$fp_amnt = $_POST['fp_amnt'];
$fp_fee_amnt = $_POST['fp_fee_amnt'];
$fp_fee_mode = $_POST['fp_fee_mode'];
$fp_total = $_POST['fp_total'];
$fp_currency = $_POST['fp_currency'];
$fp_batchnumber = $_POST['fp_batchnumber'];
$fp_store = $_POST['fp_store'];
$fp_timestamp = $_POST['fp_timestamp'];
$fp_unix_time = $_POST['fp_unix_time'];
$fp_merchant_ref = $_POST['fp_merchant_ref'];
$fp_sec_field = $_POST['fp_sec_field'];
$Additional field = $_POST['Additional field'];
$fp_hash = $_POST['fp_hash'];
$fp_hash_2 = $_POST['fp_hash_2'];
$fp_hash_list = $_POST['fp_hash_list'];
$fp_hash_all = $_POST['fp_hash_all'];
$fp_hmac = $_POST['fp_hmac'];




  $insert = $this->conn->query("INSERT INTO fasapay 
									(fp_paidto, 
									fp_paidby, 
									fp_amnt,
									fp_fee_amnt,
									fp_fee_mode,
									fp_total,
									fp_currency,
									fp_batchnumber,
									fp_store,
									fp_timestamp,
									fp_unix_time,
									fp_merchant_ref,
									fp_sec_field,
									Additional field,
									fp_hash,
									fp_hash_2,
									fp_hash_list,
									fp_hash_all,
									fp_hmac
									) 
								VALUES 
									('" . $fp_paidto . "', 
									'" . $fp_paidby . "',
									'" . $fp_amnt . "',
									'" . $fp_fee_amnt . "',
									'" . $fp_fee_mode . "',
									'" . $fp_total . "',
									'" . $fp_currency . "',
									'" . $fp_batchnumber . "',
									'" . $fp_store . "',
									'" . $fp_timestamp . "',
									'" . $fp_unix_time . "',
									'" . $fp_merchant_ref . "',
									'" . $fp_sec_field . "',
									'" . $fp_hash . "',
									'" . $fp_hash_2 . "',
									'" . $fp_hash_list . "',
									'" . $fp_hash_all . "',
									'" . $fp_hmac . "'
									) ");

        if ($insert) {
           // $name = $firstname;
           //$this->send_sms($email, $code, $name);

            return true;
        } else {
            return false;
        }

















?>