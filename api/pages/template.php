<?php
require '../vendor/autoload.php';
include 'functions/Connection.php';
session_start();

// Ambil filter dari GET (default: semua)
$filterDiskon = isset($_GET['diskon']) ? intval($_GET['diskon']) : 0;

// Query produk dengan voucher
$query = ['id_voucher' => ['$exists' => true, '$ne' => null]];
$produkDiskon = $db->produk->find($query);

?>

<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nafisah Bread & Cake | Promo</title>
    <link rel="icon" href="../public/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,700;1,600&display=swap" rel="stylesheet" />

    <link href="https://fonts.googleapis.com/css2?family=Knewave&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.4.24/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: "Poppins";
        }
    </style>
</head>

<body>
    <?php require_once __DIR__ . '/pages/nav.php' ?>
    <div class="flex min-h-screen flex-col">
        <main class="flex-grow pt-6 px-6">

        </main>
    </div>
</body>

</html>