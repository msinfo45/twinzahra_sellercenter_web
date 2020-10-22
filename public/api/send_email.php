<?php
include "../plugin/phpmailer/classes/class.phpmailer.php";
if (isset($_POST['pesan'])) {
    $pesan = $_POST['pesan'];
} else {
    $pesan = "Special Price Request";
}

if (isset($_POST['type'])) {
    $subject = "Promo Spesial";
} else {
    $subject = "Special Price";
}

$mail = new PHPMailer;
$mail->IsSMTP();
$mail->SMTPSecure = 'ssl';
$mail->Host = "v-tal.id"; //hostname masing-masing provider email
$mail->SMTPDebug = 2;
$mail->Port = 465;
$mail->SMTPAuth = true;
$mail->Username = "admin@v-tal.id"; //username email
$mail->Password = "testdevelop2018!"; //password email
$mail->SetFrom("admin@v-tal.id", "Harga Spesial2 !"); //set email pengirim contoh "test@test.com", "Thamrin District"

$mail->Subject = 'Harga Spesial'; //subyek email //subyek email
$mail->AddAddress("elimsuhendra@gmail.com"); //tujuan email
$mail->IsHTML(True);
$mail->Body = 'Name: name<br />
	Email: test<br />	
	Telepon: phone<br />
	Pesan: ' . $pesan . '<br />';
if ($mail->Send()) {
    echo 'Email Send';
} else {
    echo 'Email Not Send';
}

?>