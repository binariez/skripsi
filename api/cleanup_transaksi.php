<?php
// File ini berfungsi untuk mengubah status transaksi yang masih pending
// 15 menit setelah checkout menjadi CANCELLED

require_once "functions/Connection.php";

global $db;

// Hitung waktu 15 menit yang lalu
$limitTime = new MongoDB\BSON\UTCDateTime((time() - 1 * 60) * 1000);

// Ambil transaksi yang akan dibatalkan
$kadaluarsa = $db->transaksi->find([
    'trx_status' => 'PENDING',
    'created_at' => ['$lt' => $limitTime]
]);

foreach ($kadaluarsa as $trx) {
    // Kembalikan stok produk sesuai qty di transaksi
    foreach ($trx['items'] as $item) {
        $db->produk->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($item['id_prod'])],
            ['$inc' => ['prod_stok' => (int)$item['qty']]]
        );
    }

    // Refund poin yang digunakan jika ada
    if ($trx['trx_pointerpakai'] > 0) {
        $db->user->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($trx['plg_id'])],
            ['$inc' => ['plg_poin' => (int)$trx['trx_pointerpakai']]]
        );
    }
}

$filter = [
    'trx_status' => 'PENDING',
    'created_at' => ['$lt' => $limitTime]
];
$options = [
    '$set' => [
        'trx_status' => 'EXPIRED',
        'paket_status' => 'BATAL',
    ]
];

// Batalkan transaksi yang belum lunas setelah 15 menit
$updateeResult = $db->transaksi->updateMany($filter, $options);

echo "Transaksi kadaluarsa dibatalkan: " . $updateeResult->getModifiedCount() . "\n";
