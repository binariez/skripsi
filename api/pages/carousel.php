<?php
$transaksi = $db->transaksi;
$produk = $db->produk;

// cari produk terlaris (berdasarkan qty terbanyak)
$pipeline = [
    ['$unwind' => '$items'],
    [
        '$group' => [
            '_id' => '$items.id_prod',
            'total_qty' => ['$sum' => '$items.qty'],
            'total_sales' => ['$sum' => '$items.subtotal']
        ]
    ],
    ['$sort' => ['total_qty' => -1]],
    ['$limit' => 1]
];

$result = $transaksi->aggregate($pipeline)->toArray();
$topProduct = $result[0] ?? null;

$detailProduct = null;
if ($topProduct) {
    $detailProduct = $produk->findOne([
        '_id' => new MongoDB\BSON\ObjectId((string)$topProduct['_id'])
    ]);
}
?>

<!-- Hero Section -->
<section class="hero flex flex-col md:flex-row items-center justify-center gap-48 py-16 px-6 text-center md:text-left">
    <?php if ($detailProduct): ?>
        <!-- Kiri: Gambar dan Judul -->
        <div class="flex flex-col items-start md:items-start px-24">
            <h1 class="text-3xl font-bold mb-4 ms-10" style="font-family: Knewave; font-weight: 400;">
                Nafisah Bread & Cake
            </h1>
            <!-- Gambar produk -->
            <img src="https://img.nafisahcake.store/produk/<?= htmlspecialchars($detailProduct['prod_gambar']) ?>"
                alt="<?= htmlspecialchars($detailProduct['prod_nama']) ?>"
                class="w-96 h-64 object-cover border rounded-lg"
                onerror="this.src='https://img.nafisahcake.store/produk/no-image.png';" />
            <p class="mt-2 text-md text-gray-600 italic">
                <?= htmlspecialchars($detailProduct['prod_nama']) ?>
            </p>
        </div>

        <!-- Kanan: Deskripsi dan Tombol -->
        <div class="flex flex-col items-center md:items-start">
            <p class="text-lg ms-2 font-semibold mb-4">Sedang Populer</p>
            <!-- <p class="text-gray-700 mb-4"><?= htmlspecialchars($detailProduct['prod_deskripsi']) ?></p> -->
            <!-- <span class="text-lg font-bold text-red-600 mb-4">Rp <?= number_format($detailProduct['prod_harga'], 0, ',', '.') ?></span> -->
            <a href="detail_produk.php?prod_id=<?= $detailProduct['_id'] ?>"
                class="inline-block bg-blue-600 text-white px-5 py-2 rounded shadow hover:bg-blue-700">
                Order Sekarang!
            </a>
        </div>
    <?php else: ?>
        <p>Belum ada transaksi</p>
    <?php endif; ?>
</section>

<style>
    .hero {
        background: #ffffff;
        background: linear-gradient(90deg, rgba(255, 255, 255, 1) 0%, rgba(214, 214, 214, 1) 92%);
    }
</style>