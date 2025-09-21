<?php
require_once "functions/Template.php";
require_once "functions/Connection.php";
require_once "functions/send_verification.php";

if (isset($_POST['daftar'])) {
    global $db;
    $existingUser = $db->user->findOne(['plg_uname' => $_POST['username']]);
    if ($existingUser) {
        echo "<p>Username sudah digunakan, silakan pilih username lain.</p>";
        header("refresh:3;url=index.php");
        exit;
    }

    $existingEmail = $db->user->findOne(['plg_email' => $_POST['email']]);
    if ($existingEmail) {
        echo "<p>Email sudah digunakan, silakan pilih email lain.</p>";
        header("refresh:3;url=index.php");
        exit;
    }


    // tangkap data dari form inputan
    $uname  = strtolower(trim($_POST['username']));
    $pwd    = $_POST['password'];
    $role   = "PLG";
    $nama   = $_POST['nama'];
    $jk     = $_POST['jk'];
    $alamat = $_POST['alamat'];
    $hp     = $_POST['nohp'];
    $email  = $_POST['email'];
    $poin   = 0;

    // upload gambar produk
    $pfp = "";
    if (isset($_FILES['pfp']) && $_FILES['pfp']['error'] === 0) {
        $upload_dir = "../public/user_gambar/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $basename = basename($_FILES['pfp']['name']);
        $pfp = uniqid() . "_" . $basename;
        move_uploaded_file($_FILES['pfp']['tmp_name'], $upload_dir . $pfp);
    }

    // generate token unik untuk verifikasi email
    $token = bin2hex(random_bytes(16));

    // susun data user
    $user = [
        "user_uname"   => $uname,
        "user_pwd"     => $pwd,
        "user_role"    => $role,
        "user_pfp"     => $pfp,
        "user_nama"    => $nama,
        "plg_jk"      => $jk,
        "plg_alamat"  => $alamat,
        "plg_hp"      => $hp,
        "plg_email"   => $email,
        "plg_poin"    => $poin,
        "token"        => $token,
        "terverifikasi"  => false,
        "created_at"   => new MongoDB\BSON\UTCDateTime()
    ];

    // input ke db
    if ($hasil = $db->user->insertOne($user)) {
        // kirim email verifikasi
        sendVerificationEmail($email, $token);

        echo "<p class='bg-success'>Berhasil daftar akun! ID: " . $hasil->getInsertedId() . "</p>";
        echo "<p class='bg-warning'>Silahkan cek email anda di: " . $email . " untuk verifikasi.</p>";
    } else {
        echo "Gagal mendaftar";
    }
}
