<?php
require_once __DIR__ . '/../vendor/autoload.php';
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
    <title>Nafisah Bread & Cake | Kontak</title>
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
        <main class="flex-grow pt-10 px-6">
            <!-- Hero / Header -->
            <section class="text-center mb-10">
                <h1 class="text-4xl font-extrabold text-gray-800">Kontak Kami</h1>
                <p class="text-gray-600 mt-2">Hubungi atau kunjungi toko kami melalui informasi berikut.</p>
            </section>

            <section class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <!-- Google Maps -->
                <div class="bg-white shadow-lg rounded-xl overflow-hidden">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.4092048474067!2d99.65917067412425!3d2.983824496992252!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30324daa1840aae5%3A0x3f45de56fdb5a2ed!2sNafisah%20Bread%20%26%20Cake!5e0!3m2!1sid!2sid!4v1755614247534!5m2!1sid!2sid"
                        width="100%" height="450"
                        class="border-0 w-full h-full"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                <!-- Informasi Kontak -->
                <div class="bg-white shadow-lg rounded-xl p-8 flex flex-col justify-between">
                    <h2 class="text-2xl font-bold mb-6 text-rose-600">Informasi Kontak</h2>

                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <i class="fa-solid fa-location-dot text-rose-500 text-xl"></i>
                            <span>
                                <p class="font-semibold">Alamat</p>
                                <p class="text-gray-600">Jl. Budi Utomo, Siumbut Baru, Depan Masjid Haji Kasim, Kisaran Timur
                                </p>
                            </span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fa-solid fa-phone text-rose-500 text-xl"></i>
                            <span>
                                <p class="font-semibold">Telepon</p>
                                <p class="text-gray-600">+62 812 3456 7890</p>
                            </span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fa-solid fa-envelope text-rose-500 text-xl"></i>
                            <span>
                                <p class="font-semibold">Email</p>
                                <p class="text-gray-600">info@nafisahcake.com</p>
                            </span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fa-solid fa-clock text-rose-500 text-xl"></i>
                            <span>
                                <p class="font-semibold">Jam Operasional</p>
                                <p class="text-gray-600">Setiap Hari, 08.00 - 20.00</p>
                            </span>
                        </li>
                    </ul>
                </div>
            </section>
        </main>
        <?php include_once "pages/footer.php" ?>
    </div>
</body>

</html>