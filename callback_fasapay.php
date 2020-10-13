<?php

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
$Additional_field = $_POST['Additional_field'];
$fp_hash = $_POST['fp_hash'];
$fp_hash_2 = $_POST['fp_hash_2'];
$fp_hash_list = $_POST['fp_hash_list'];
$fp_hash_all = $_POST['fp_hash_all'];
$fp_hmac = $_POST['fp_hmac'];
   
$servername = "localhost";
$database = "twinzahra";
$username = "alanbk92";
$password = "klapaucius92";





// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
}
 
echo "Connected successfully";
 
$sql = "INSERT INTO fasapay3
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
									Additional_field,
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
									'" . $Additional_field . "',
									'" . $fp_hash . "',
									'" . $fp_hash_2 . "',
									'" . $fp_hash_list . "',
									'" . $fp_hash_all . "',
									'" . $fp_hmac . "'
									) ";
if (mysqli_query($conn, $sql)) {
      echo "New record created successfully";
} else {
      echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
mysqli_close($conn);
	


















?>