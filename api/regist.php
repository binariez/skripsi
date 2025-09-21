<?php
require_once __DIR__ . "/functions/Template.php";
require_once __DIR__ . "/functions/Connection.php";
require_once __DIR__ . "/functions/send_verification.php";

if (isset($_POST['daftar'])) {
    global $db;

    // Cek username & email
    $existingUser  = $db->user->findOne(['plg_uname' => $_POST['username']]);
    $existingEmail = $db->user->findOne(['plg_email' => $_POST['email']]);

    if ($existingUser) {
        echo "<p>Username sudah digunakan, silakan pilih username lain.</p>";
        header("refresh:3;url=index.php");
        exit;
    }

    if ($existingEmail) {
        echo "<p>Email sudah digunakan, silakan pilih email lain.</p>";
        header("refresh:3;url=index.php");
        exit;
    }

    // Tangkap data dari form
    $uname  = strtolower(trim($_POST['username']));
    $pwd    = $_POST['password'];
    $role   = "PLG";
    $nama   = $_POST['nama'];
    $jk     = $_POST['jk'];
    $alamat = $_POST['alamat'];
    $hp     = $_POST['nohp'];
    $email  = $_POST['email'];
    $poin   = 0;

    // Upload foto profil ke subdomain storage
    $pfp = "";
    if (isset($_FILES['pfp']) && $_FILES['pfp']['error'] === 0) {
        $uploadUrl = "https://img.nafisahcake.store/upload_user.php";
        $cfile = new CURLFile(
            $_FILES['pfp']['tmp_name'],
            $_FILES['pfp']['type'],
            $_FILES['pfp']['name']
        );
        $data = ["file" => $cfile];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uploadUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        if ($result && $result['status'] === "success") {
            $pfp = $result['filename']; // simpan hanya nama file
        }
    }

    // Generate token unik untuk verifikasi email
    $token = bin2hex(random_bytes(16));

    // Susun data user
    $user = [
        "user_uname"   => $uname,
        "user_pwd"     => $pwd,
        "user_role"    => $role,
        "user_pfp"     => $pfp,
        "user_nama"    => $nama,
        "plg_jk"       => $jk,
        "plg_alamat"   => $alamat,
        "plg_hp"       => $hp,
        "plg_email"    => $email,
        "plg_poin"     => $poin,
        "token"        => $token,
        "terverifikasi" => false,
        "created_at"   => new MongoDB\BSON\UTCDateTime()
    ];

    // Insert ke database
    $hasil = $db->user->insertOne($user);
    if ($hasil) {
        // Kirim email verifikasi
        sendVerificationEmail($email, $token);

        echo "<p class='bg-success'>Berhasil daftar akun! ID: " . $hasil->getInsertedId() . "</p>";
        echo "<p class='bg-warning'>Silahkan cek email anda di: " . $email . " untuk verifikasi.</p>";
    } else {
        echo "Gagal mendaftar";
    }
}
