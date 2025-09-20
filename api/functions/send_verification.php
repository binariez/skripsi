<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

function sendVerificationEmail($email, $token)
{
    $mail = new PHPMailer(true);
    try {
        // SMTP setting
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';   // server Gmail
        $mail->SMTPAuth   = true;
        $mail->Username   = 'kucingpeak@gmail.com';   // email kamu
        $mail->Password   = 'bpfrcbqnmhgbcofv';     // App Password Gmail
        $mail->SMTPSecure = 'tls';   // atau 'ssl'
        $mail->Port       = 587;     // TLS: 587, SSL: 465

        // pengirim & penerima
        $mail->setFrom('azhacra@gmail.com', 'Nafisah Bread&Cake');
        $mail->addAddress($email);

        // isi email
        $verifyLink = "https://b574a1d4e761.ngrok-free.app/skripsi/api/functions/verify.php?email=$email&token=$token";
        $mail->isHTML(true);
        $mail->Subject = 'Verifikasi Email Anda';
        $mail->Body    = "Klik link berikut untuk verifikasi: <a href='$verifyLink'>$verifyLink</a>";

        $mail->send();
    } catch (Exception $e) {
        echo "Email gagal dikirim: {$mail->ErrorInfo}";
    }
}
