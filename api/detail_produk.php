<?php
require_once "functions/Sessions.php";

use Carbon\Carbon;

Carbon::setLocale('id');
date_default_timezone_set('Asia/Jakarta');
require_once 'functions/SessionHandlerInterface.php';
session_start();

if (!isset($_GET['prod_id'])) {
    die("ID produk tidak ada");
}
$prod_id = $_GET['prod_id'];
$produk = NSessionHandler::getProdukOne($prod_id);
if (!$produk) {
    echo "Produk tidak ditemukan";
    exit;
}

?>
<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>RIAA | <?php echo ($produk != null) ? $produk['prod_nama'] : "Produk Tidak Ditemukan"; ?></title>
    <link rel="icon" href="../public/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,700;1,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.4.24/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">

    <link rel="stylesheet" href="style.css" />
    <style>
        body {
            font-family: "Poppins";
        }
    </style>
</head>

<body>
    <div class="flex min-h-screen flex-col">
        <?php include_once "pages/nav.php" ?>
        <main class="flex-grow pt-10 mx-10 gap-2">
            <section class="flex items-center justify-center flex-wrap">
                <?php if ($produk == null) { ?>
                    <div class="flex items-center">
                        <h1 class="font-bold text-2xl">Produk Tidak Ditemukan</h1>
                    </div>
                <?php } else { ?>

                    <div class="w-full bg-slate-200 rounded-md p-5 flex gap-6">
                        <!-- Gambar produk -->
                        <div class="flex-shrink-0">
                            <img class="w-[250px] aspect-square object-cover object-center rounded-lg shadow-md"
                                src="https://img.nafisahcake.store/produk/<?= $produk['prod_gambar'] ?>"
                                alt="<?= htmlspecialchars($produk['prod_nama']) ?>">
                        </div>

                        <!-- Detail produk -->
                        <div class="flex-1">
                            <?php
                            $rating = NSessionHandler::hitungRating($produk['_id']);

                            $harga = $produk['prod_harga'];
                            $hargaSetelahDiskon = $harga;
                            $voucher = null;

                            if (!empty($produk['id_voucher'])) {
                                $voucher = $db->voucher->findOne(['_id' => $produk['id_voucher']]);
                                if ($voucher) {
                                    $diskon = intval($voucher['diskon']);
                                    $hargaSetelahDiskon = $harga - ($harga * $diskon / 100);
                                }
                            }
                            ?>

                            <div class="space-y-2">
                                <!-- Kode Produk -->
                                <div class="flex gap-x-4">
                                    <span class="font-semibold min-w-[120px]">Kode Produk</span>
                                    <span class="flex-1"><?= $produk['prod_kode'] ?></span>
                                </div>

                                <!-- Nama Produk -->
                                <div class="flex gap-x-4">
                                    <span class="font-semibold min-w-[120px]">Nama Produk</span>
                                    <span class="flex-1"><?= $produk['prod_nama'] ?></span>
                                </div>

                                <!-- Stok -->
                                <div class="flex gap-x-4">
                                    <span class="font-semibold min-w-[120px]">Stok</span>
                                    <span class="flex-1"><?= $produk['prod_stok'] ?></span>
                                </div>

                                <!-- Harga -->
                                <div class="flex gap-x-4 items-center">
                                    <span class="font-semibold min-w-[120px]">Harga</span>
                                    <span class="flex-1">
                                        <?php if ($voucher) : ?>
                                            <span class="line-through text-gray-500 mr-2">Rp<?= number_format($harga) ?></span>
                                            <span class="text-red-600 font-bold">Rp<?= number_format($hargaSetelahDiskon) ?></span>
                                            <span class="ml-2 text-sm bg-red-100 text-red-600 px-2 py-0.5 rounded-md">
                                                -<?= $voucher['diskon'] ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="font-bold">Rp<?= number_format($harga) ?></span>
                                        <?php endif; ?>
                                    </span>
                                </div>

                                <!-- Rating -->
                                <div class="flex gap-x-4 items-center">
                                    <span class="font-semibold min-w-[120px]">Rating</span>
                                    <div class="flex-1 rating" data-rateyo-rating="<?= $rating ?>"></div>
                                </div>

                                <!-- Deskripsi -->
                                <div class="flex gap-x-4">
                                    <span class="font-semibold min-w-[120px]">Deskripsi</span>
                                    <span class="flex-1"><?= $produk['prod_deskripsi'] ?></span>
                                </div>
                            </div>


                            <!-- Tombol keranjang -->
                            <div class="mt-4">
                                <?php if (!isset($_SESSION['UserLogin'])) { ?>
                                    <button onclick="login.showModal()"
                                        class="btn px-6 bg-slate-700 hover:bg-slate-600 text-white">
                                        <i class="fa-sharp fa-solid fa-cart-plus text-xl"></i>
                                    </button>
                                <?php } else { ?>
                                    <form action="keranjang.php" method="post">
                                        <input type="hidden" name="prod_id" value="<?= $produk['_id'] ?>">
                                        <input type="hidden" name="prod_nama" value="<?= $produk['prod_nama'] ?>">
                                        <input type="hidden" name="prod_harga" value="<?= $produk['prod_harga'] ?>">
                                        <input type="hidden" name="prod_gambar" value="<?= $produk['prod_gambar'] ?>">
                                        <button type="submit" name="tambah"
                                            class="btn px-6 bg-slate-700 hover:bg-slate-600 text-white">
                                            <i class="fa-sharp fa-solid fa-cart-plus text-xl"></i>
                                        </button>
                                    </form>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </section>


            <!-- REVIEW -->
            <section class="w-2/4 bg-slate-200 mt-1 rounded-md">
                <div class="ms-2 pb-2">
                    <h1 class="text-2xl pt-1 mb-2 font-bold"><i>Review</i></h1>

                    <?php
                    $prodId = new MongoDB\BSON\ObjectId($_GET['prod_id']);

                    // Ambil data review dari koleksi "review" berdasarkan id_produk, urutkan descending berdasarkan tanggal
                    $reviews = $db->review->find(
                        ['id_prod' => $prodId],
                        ['sort' => ['review_tgl' => -1]]
                    );

                    foreach ($reviews as $produkata) {
                        // Ambil user info dari koleksi "user" berdasarkan id_user di review
                        $userId = $produkata['id_plg'];
                        $user = $db->user->findOne(['_id' => $userId]);
                        $tanggal = Carbon::parse($produkata['review_tgl']);
                    ?>
                        <div class="chat chat-start mb-3">
                            <div class="chat-image avatar">
                                <div class="w-14 rounded-full">
                                    <img alt="User profile" src="https://img.nafisahcake.store/user<?= htmlspecialchars($user['user_pfp'] ?? 'default-avatar.png') ?>" />
                                </div>
                            </div>
                            <div class="chat-header">
                                <div class="flex align-center items-center gap-2">
                                    <?= ucfirst(htmlspecialchars($user['user_nama'] ?? 'Anonim')) ?>
                                    <time class="text-xs opacity-50"><?= $tanggal->translatedFormat('l, d F Y H:i')  ?></time>
                                    <div class="mb-1 review" data-rateyo-rating="<?= $produkata['review_rating'] ?>"></div>
                                </div>
                                <div class="chat-bubble"><?= htmlspecialchars($produkata['review_isi']) ?></div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </section>

        </main>
        <?php include_once "pages/footer.php" ?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
    <script>
        $(function() {

            $(".review").rateYo({
                starWidth: '16px',
                readOnly: true
            });

            $(".rating").rateYo({
                starWidth: '20px',
                readOnly: true
            });
        });
    </script>
</body>

</html>