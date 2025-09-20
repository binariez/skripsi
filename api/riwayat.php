<?php
require_once 'functions/Sessions.php';
global $db;

use Carbon\Carbon;

Carbon::setLocale('id');
date_default_timezone_set('Asia/Jakarta');
require_once 'functions/SessionHandlerInterface.php';
session_start();

if (!isset($_SESSION['UserLogin'][0]['id'])) {
    header('location: index.php');
    exit;
}
$userid = $_SESSION['UserLogin'][0]['id'];

$transaksi = $db->transaksi->find(
    ['plg_id' => $userid],
    ['sort' => ['trx_tgl' => -1]]
);

if (isset($_POST['berireview'])) {
    $item_index = $_POST['item_index'];
    $trx_id = new MongoDB\BSON\ObjectId($_POST['trx_id']);
    $bintang = (float) $_POST['bintang'];
    $komen = $_POST['komentar'];

    $transaksi = $db->transaksi->findOne(['_id' => $trx_id]);
    $prod_id = new mongodb\BSON\ObjectId($_POST['prod_id']);

    // Simpan ke koleksi review
    $db->review->insertOne([
        'id_plg'        => $userid,
        'id_trx'        => $trx_id,
        'id_prod'       => $prod_id,
        'review_isi'    => $komen,
        'review_rating' => $bintang,
        'review_tgl'    => date('Y-m-d H:i', strtotime('NOW'))
    ]);

    // Update field review di transaksi (penanda sudah direview)
    $i = "items.$item_index.review";
    $db->transaksi->updateOne(
        ['_id' => $trx_id],
        ['$set' => [$i => true]]
    );

    header('Location: riwayat.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Riwayat Pembelian</title>
    <link rel="icon" href="../public/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,700;1,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.4.24/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">

    <style>
        body {
            font-family: "Poppins";
        }
    </style>
</head>

<body class="fontf">
    <div class="flex min-h-screen flex-col">
        <?php include_once "pages/nav.php" ?>
        <main class="flex-grow pt-10 mx-10">
            <h1 class="mb-5 text-center text-2xl font-bold">Riwayat Pembelian</h1>
            <hr class="mx-2 w-full mb-5">
            <div class="flex flex-col items-center justify-center">
                <?php
                $found = false;
                foreach ($transaksi as $trx) {
                    $found = true;
                ?>
                    <?php
                    foreach ($trx['items'] as $i => $item) {
                        $prod = $db->produk->findOne(['_id' => $item['id_prod']]);
                        if (!$prod) continue;
                        $gambar = $prod && isset($prod['prod_gambar']) ? $prod['prod_gambar'] : 'no-image.png';
                        $tanggal = Carbon::parse($trx['trx_tgl']);

                    ?>
                        <div class="rounded-lg md:w-2/3">
                            <div class="mb-6 rounded-lg bg-white p-4 shadow-md flex flex-col sm:flex-row sm:justify-start sm:items-start gap-4">
                                <!-- Gambar Produk -->
                                <div class="w-full sm:w-auto">
                                    <a href="detail_produk.php?prod_id=<?= $item['id_prod'] ?>">
                                        <img src="https://img.nafisahcake.store/produk/<?= $gambar ?>"
                                            alt="gambar"
                                            class="w-full sm:w-[150px] aspect-square object-cover object-center rounded-lg" />
                                    </a>
                                </div>

                                <!-- Info Produk -->
                                <div class="flex flex-col justify-between w-full">
                                    <!-- Nama & Tanggal -->
                                    <div>
                                        <a href="detail_produk.php?prod_id=<?= $item['id_prod'] ?>">
                                            <h2 class="text-lg text-center md:text-left font-bold text-gray-900"><?= $prod['prod_nama'] ?></h2>
                                            <h5 class="text-sm text-gray-900"><?= $tanggal->translatedFormat('l, d F Y H:i') ?></h5>
                                        </a>
                                    </div>

                                    <!-- Harga, Pcs, Subtotal & Tombol Review -->
                                    <div class="mt-3 flex flex-col sm:flex-row sm:items-center sm:justify-between sm:gap-x-4 gap-y-2">
                                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                                            <p class="text-lg font-bold text-gray-900">Rp<?= number_format($prod['prod_harga']) ?></p>
                                            <p class="text-lg font-bold text-gray-900">x<?= $item['qty'] ?> Pcs:</p>
                                            <p class="text-lg bg-base-300 px-2 rounded-sm font-semibold">Rp<?= number_format($item['subtotal']) ?></p>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                                            <p class="text-md text-center font-bold text-gray-900">Status: <?= $trx['paket_status'] ?></p>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <?php
                                            $rev = isset($item['review']) && $item['review'] === true ? 1 : 0;
                                            $lunas = isset($trx['trx_status']) && strtoupper($trx['trx_status']) === 'SETTLEMENT';
                                            $selesai = isset($trx['paket_status']) && strtoupper($trx['paket_status']) === 'SELESAI';
                                            if ($rev == 0 && $lunas == 'SETTLEMENT' && $selesai == 'SELESAI') {
                                            ?>
                                                <button class="btn_review btn btn-neutral"
                                                    onclick="openReviewModal(this)"
                                                    data-trx-id="<?= htmlspecialchars($trx['_id']) ?>"
                                                    data-item-index="<?= htmlspecialchars($i) ?>"
                                                    data-prod-id="<?= htmlspecialchars($prod['_id']) ?>">
                                                    <i class="fa-solid fa-star-half-stroke mr-1"></i> Berikan review
                                                </button>
                                            <?php } else if ($rev == 1) { ?>
                                                <button class="btn btn-active btn-accent btn-no-animation">
                                                    <i class="fa-regular fa-thumbs-up mr-1"></i> Sudah review
                                                </button>
                                            <?php } else if ($lunas != 'SETTLEMENT') { ?>
                                                <button class="btn btn-active btn-error btn-no-animation ">
                                                    <i class="fa-solid fa-circle-exclamation mr-1"></i> Belum bayar
                                                </button>
                                            <?php } else { ?>
                                                <button class="btn btn-active btn-warning btn-no-animation">
                                                    <i class="fa-regular fa-clock mr-1"></i> Belum Tiba
                                                </button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                <?php
                    }
                }
                ?>
                <style>
                    .total table {
                        width: 100%;
                        max-width: 500px;
                        border-top: 3px solid slategrey;
                    }
                </style>
                <?php if (!$found) {
                    echo "<p>Belum ada riwayat pembelian</p>";
                } ?>

            </div>

        </main>
        <?php include_once "pages/footer.php" ?>

        <!-- modal REVIEW -->
        <dialog id="review" class="modal modal-bottom sm:modal-middle">
            <div class="modal-box flex flex-col gap-4">
                <form class="flex flex-col gap-4" action="riwayat.php" method="post">
                    <h3 class="font-bold text-lg mb-2">Berikan review</h3>

                    <div class="rateyo" id="rating" data-rateyo-half-star="true" data-rateyo-rating="4.5" data-rateyo-num-stars="5"></div>
                    <span class="hasil">Rating: 4.5</span>
                    <input type="number" style="display: none;" step=".01" value="4.5" name="bintang">
                    <input type="hidden" name="trx_id">
                    <input type="hidden" name="item_index">
                    <input type="hidden" name="prod_id">

                    <input type="text" class="input input-bordered input-md w-full" name="komentar" placeholder="Komentar anda..." autocomplete="off">
                    <button type="submit" name="berireview" class="btn">Beri review</button>
                </form>
            </div>

            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>

    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
    <script>
        function openReviewModal(button) {
            let trxId = button.getAttribute('data-trx-id');
            let itemIndex = button.getAttribute('data-item-index');
            let prodId = button.getAttribute('data-prod-id');

            // Masukkan ke input hidden di modal
            document.querySelector('input[name="trx_id"]').value = trxId;
            document.querySelector('input[name="item_index"]').value = itemIndex;
            document.querySelector('input[name="prod_id"]').value = prodId;

            document.getElementById('review').showModal();
        }

        $(".rateyo").rateYo().on("rateyo.change", function(e, data) {
            var rating = data.rating;
            $(this).parent().find('.skor').text('Skor: ' + $(this).attr('data-rateyo-score'));
            $(this).parent().find('.hasil').text('Rating: ' + rating);
            $(this).parent().find('input[name=bintang]').val(rating);
        });
    </script>

</body>

</html>