<?php
include "../plugin/phpmailer/classes/class.phpmailer.php";

if (isset($_POST['pesan'])) {
    $pesan = $_POST['pesan'];
} else {
    $pesan = "tes";
}

if (isset($_POST['subject'])) {
    $pesan = $_POST['subject'];
} else {
    $subject = "tes";
}

$mail = new PHPMailer;
$mail->IsSMTP();
$mail->SMTPSecure = 'ssl';
$mail->Host = "mail.twinzahra.masuk.id"; //hostname masing-masing provider email
$mail->SMTPDebug = 2;
$mail->Port = 465;
$mail->SMTPAuth = true;
$mail->Username = "no_replay@twinzahra.masuk.id"; //username email
$mail->Password = "Klapaucius92!"; //password email
$mail->SetFrom("no_replay@twinzahra.masuk.id", "Twinzahra Shop"); //set email pengirim contoh "test@test.com", "Thamrin District"

$mail->Subject = 'Harga Spesial'; //subyek email //subyek email
$mail->AddAddress("twinzahrashop@gmail.com"); //tujuan email
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