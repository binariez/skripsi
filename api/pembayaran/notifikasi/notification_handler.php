<?php

namespace Midtrans;

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../functions/Connection.php';
Config::$isProduction = false;
Config::$serverKey = 'Mid-server-ipOUcsUcaSR3CpCGluYgRTb';

$json = file_get_contents('php://input');
$data = json_decode($json, true);
$type = $data['payment_type'];
$order_id = $data['order_id'];
$trans = $data['transaction_status'];

global $db;
// Update status transaksi
$updateStatus = $db->transaksi->updateOne(
    ['orderid' => $order_id],
    ['$set' => ['trx_status' => strtoupper($trans)]]
);

if ($updateStatus->getModifiedCount() > 0) {
    if (strtolower($trans) == 'settlement') {
        // Ambil data transaksi untuk dapatkan user dan total belanja
        $transaksi = $db->transaksi->findOne(['orderid' => $order_id]);

        if ($transaksi) {
            $user_id = $transaksi['plg_id'];
            $total_belanja = $transaksi['trx_belanja'];

            // Hitung poin, 3% dari total belanja
            $poin_baru = intval($total_belanja * 0.03);

            // Update poin user
            $db->user->updateOne(
                ['_id' => $user_id],
                ['$inc' => ['plg_poin' => $poin_baru]]
            );
        }
    }
}
