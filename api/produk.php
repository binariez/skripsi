<?php
require_once 'functions/SessionHandlerInterface.php';
session_start();
require_once 'functions/Sessions.php';

$limit = 4;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$skip = ($page - 1) * $limit;

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

$options = [
    'skip' => $skip,
    'limit' => $limit,
    'sort' => ['_id' => -1],
];

$cari = false;
$filter = [];
if ($keyword !== '') {
    $cari = true;
    $filter = [
        'prod_nama' => new MongoDB\BSON\Regex($keyword, 'i')
    ];
}
$total = $db->produk->countDocuments($filter);
$totalPages = ceil($total / $limit);

$options = [
    'skip' => $skip,
    'limit' => $limit,
    'sort' => ['_id' => -1]
];

$data = NSessionHandler::getProdukAll($filter, $options);
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nafisah Bread & Cake | Produk</title>
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

<body>
    <div class="flex min-h-screen flex-col">
        <?php include_once "pages/nav.php" ?>
        <main class="flex-grow pt-10 mx-10">
            <?php
            if ($cari == true) { ?>
                <h3 class="mb-2 text-center">Hasil pencarian untuk "<?= htmlspecialchars($keyword) ?>"</h3><br>
            <?php
            }
            ?>
            <section class="flex items-center justify-center flex-wrap">

                <?php
                foreach ($data as $d) {
                    $rating = NSessionHandler::hitungRating($d['_id']);
                    $harga = $d['prod_harga'];
                    $stok = intval($d['prod_stok']);
                    $hargaSetelahDiskon = $harga;

                    if (!empty($d['id_voucher'])) {
                        $voucher = $db->voucher->findOne(['_id' => $d['id_voucher']]);
                        if ($voucher) {
                            $diskon = intval($voucher['diskon']); // misalnya 10 untuk 10%
                            $hargaSetelahDiskon = $harga - ($harga * $diskon / 100);
                        }
                    }
                ?>
                    <div class="card w-96 me-2 bg-base-100 shadow-xl transition-scale hover:scale-105 delay-100 duration-150">
                        <a href="detail_produk.php?prod_id=<?= $d['_id'] ?>">
                            <figure><img class="w-full sm:w-[300px] aspect-square object-cover object-center rounded-lg" src="https://img.nafisahcake.store/produk/<?= $d['prod_gambar'] ?>" alt="gambar" /></figure>
                        </a>
                        <div class="card-body">
                            <h2 class="card-title"><?= ucwords($d['prod_nama']) ?></h2>

                            <?php if (!empty($d['id_voucher']) && isset($voucher)) : ?>
                                <p class="p-2 w-fit rounded-md font-semibold bg-slate-300">
                                    <span class="line-through text-gray-500 mr-2">Rp<?= number_format($harga) ?></span>
                                    <span class="text-red-600">Rp<?= number_format($hargaSetelahDiskon) ?></span>
                                </p>
                            <?php else: ?>
                                <p class="p-2 w-fit rounded-md font-semibold bg-slate-300">
                                    Rp<?= number_format($harga) ?>
                                </p>
                            <?php endif; ?>

                            <div class="mt-1 rating" data-rateyo-rating="<?= $rating ?>"></div>
                            <div class="card-actions justify-end">
                                <?php
                                if (!isset($_SESSION['UserLogin'])) {
                                ?>
                                    <button onclick="login.showModal()" class="btn px-6 bg-slate-700 hover:bg-slate-600 text-white"><i style="font-size: 24px; background-color: transparent;" class="fa-sharp fa-solid fa-cart-plus"></i></button>
                                <?php } else { ?>
                                    <form action="keranjang.php" method="post">
                                        <input type="hidden" name="prod_id" value="<?= $d['_id'] ?>">
                                        <input type="hidden" name="prod_nama" value="<?= $d['prod_nama'] ?>">
                                        <input type="hidden" name="prod_harga" value="<?= $d['prod_harga'] ?> ">
                                        <input type="hidden" name="prod_gambar" value="<?= $d['prod_gambar'] ?> ">
                                        <?php if ($stok <= 0) { ?>
                                            <button type="button" onclick="alert('Stok Habis')" class="btn px-6 bg-slate-700 hover:bg-slate-600 text-white"><i style="font-size: 24px; background-color: transparent;" class="fa-sharp fa-solid fa-cart-plus"></i></button>
                                        <?php } else { ?>
                                            <button type="submit" name="tambah" class="btn px-6 bg-slate-700 hover:bg-slate-600 text-white"><i style="font-size: 24px; background-color: transparent;" class="fa-sharp fa-solid fa-cart-plus"></i></button>
                                        <?php } ?>
                                    </form>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                <?php } ?>
            </section>

            <!-- Pagination -->
            <div class="flex justify-center mt-8 space-x-2">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&keyword=<?= urlencode($keyword) ?>" class="btn btn-sm btn-outline">&laquo; Prev</a>
                <?php endif; ?>

                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <a href="?page=<?= $p ?>&keyword=<?= urlencode($keyword) ?>" class="btn btn-sm <?= $p == $page ? 'btn-active' : 'btn-outline' ?>"><?= $p ?></a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>&keyword=<?= urlencode($keyword) ?>" class="btn btn-sm btn-outline">>></a>
                <?php endif; ?>
            </div>
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
                starWidth: '24px',
                readOnly: true
            });
        });
    </script>
</body>

</html>