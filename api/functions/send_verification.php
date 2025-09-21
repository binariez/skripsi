<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();

function sendVerificationEmail($email, $token)
{
    $mail = new PHPMailer(true);
    try {
        // SMTP setting
        $mail->isSMTP();
        $mail->Host       = 'mail.nafisahcake.store';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'admin@mail.nafisahcake.store';
        $mail->Password   = $_ENV['SMTPPASS'];
        $mail->SMTPSecure = 'ssl';   // tls/ssl
        $mail->Port       = 465;     // TLS: 587, SSL: 465

        // pengirim & penerima
        $mail->setFrom('admin@mail.nafisahcake.store', 'Nafisah Bread&Cake');
        $mail->addAddress($email);

        // isi email
        $verifyLink = "https://nafisahcake.store/verify.php?email=$email&token=$token";
        $mail->isHTML(true);
        $mail->Subject = 'Verifikasi Email Anda';
        $mail->Body    = "Klik link berikut untuk verifikasi: <a href='$verifyLink'>$verifyLink</a>";

        $mail->send();
    } catch (Exception $e) {
        echo "Email gagal dikirim: {$mail->ErrorInfo}";
    }
}
