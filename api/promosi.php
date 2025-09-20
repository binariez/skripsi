<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once 'functions/Connection.php';
require_once 'functions/SessionHandlerInterface.php';
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
            <!-- Banner Promo -->
            <section class="bg-gradient-to-r from-rose-400 via-red-400 to-pink-500 text-white rounded-2xl shadow-lg p-10 mb-8">
                <h1 class="text-4xl font-extrabold mb-2">🎉 Promo Spesial Kami!</h1>
                <p class="text-lg">Nikmati potongan harga terbaik untuk produk pilihan. Jangan sampai kehabisan!</p>
            </section>

            <!-- Filter Diskon -->
            <section class="mb-6 flex flex-wrap items-center gap-3">
                <span class="font-semibold text-gray-700">Filter diskon:</span>
                <a href="?diskon=0" class="btn btn-sm <?= $filterDiskon === 0 ? 'btn-primary' : 'btn-outline' ?>">Semua</a>
                <a href="?diskon=10" class="btn btn-sm <?= $filterDiskon === 10 ? 'btn-primary' : 'btn-outline' ?>">≥10%</a>
                <a href="?diskon=20" class="btn btn-sm <?= $filterDiskon === 20 ? 'btn-primary' : 'btn-outline' ?>">≥20%</a>
                <a href="?diskon=30" class="btn btn-sm <?= $filterDiskon === 30 ? 'btn-primary' : 'btn-outline' ?>">≥30%</a>
                <a href="?diskon=50" class="btn btn-sm <?= $filterDiskon === 50 ? 'btn-primary' : 'btn-outline' ?>">≥50%</a>
            </section>

            <!-- Daftar Produk Promo -->
            <section>
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Produk dengan Diskon</h2>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($produkDiskon as $p) :
                        $harga = $p['prod_harga'];
                        $voucher = $db->voucher->findOne(['_id' => $p['id_voucher']]);

                        if (!$voucher) continue;

                        $diskon = intval($voucher['diskon']);
                        $hargaDiskon = $harga - ($harga * $diskon / 100);

                        // Skip produk jika tidak memenuhi filter diskon
                        if ($filterDiskon > 0 && $diskon < $filterDiskon) continue;
                    ?>
                        <div class="card bg-base-100 shadow-xl hover:scale-105 transition duration-200">
                            <figure>
                                <img src="https://img.nafisahcake.store/produk/<?= $p['prod_gambar'] ?>"
                                    alt="<?= ucwords(htmlspecialchars($p['prod_nama'])) ?>"
                                    class="w-full h-56 object-cover rounded-t-xl" />
                            </figure>
                            <div class="card-body">
                                <h3 class="card-title text-lg"><?= ucwords($p['prod_nama']) ?></h3>
                                <p class="text-gray-600 text-sm"><?= htmlspecialchars(substr($p['prod_deskripsi'], 0, 40)) . "..." ?></p>

                                <!-- Harga -->
                                <div class="mt-3">
                                    <span class="line-through text-gray-400 text-sm mr-2">
                                        Rp<?= number_format($harga) ?>
                                    </span>
                                    <span class="text-xl font-bold text-red-600">
                                        Rp<?= number_format($hargaDiskon) ?>
                                    </span>
                                </div>

                                <!-- Info Voucher -->
                                <div class="mt-2 bg-red-100 text-red-600 text-sm px-3 py-1 rounded-lg w-fit">
                                    Diskon <?= $voucher['diskon'] ?>% - <?= ucwords(htmlspecialchars($voucher['voucher_judul'] ?? '')) ?>
                                </div>

                                <div class="card-actions justify-end mt-4">
                                    <a href="detail_produk.php?prod_id=<?= $p['_id'] ?>"
                                        class="btn bg-rose-500 hover:bg-rose-400 text-white">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>
        <?php include_once "pages/footer.php" ?>
    </div>
</body>

</html>