<?php
require_once __DIR__ . '/Connection.php';

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    $user = $db->user->findOne([
        "plg_email" => $email,
        "token" => $token,
        "terverifikasi" => false
    ]);

    if ($user) {
        $db->user->updateOne(
            ["plg_email" => $email],
            ['$set' => ["terverifikasi" => true, "token" => ""]]
        );
        echo "Email berhasil diverifikasi! Silakan login.";
    } else {
        echo "Link verifikasi tidak valid atau sudah digunakan.";
    }
}
