<?php
session_start();
require '../../../functions/Connection.php';

header('Content-Type: application/json');

global $db;
$collection = $db->livechat;

// Ambil daftar pelanggan unik beserta waktu chat terakhir
$pipeline = [
    ['$match' => ['sender_type' => ['$ne' => 'admin']]], // exclude admin
    ['$sort' => ['created_at' => -1]],                  // urut dari terbaru
    ['$group' => [
        '_id' => '$id',                                 // id pelanggan
        'nama' => ['$first' => '$nama'],                // nama pelanggan
        'last_time' => ['$first' => '$created_at']      // waktu chat terakhir
    ]],
    ['$sort' => ['last_time' => -1]]                   // urut pelanggan berdasarkan chat terbaru
];

$customers = $collection->aggregate($pipeline);

// $data = [];
// foreach ($customers as $c) {
//     $data[] = [
//         'id' => (string)$c['_id'],
//         'nama' => ucfirst($c['nama'])
//     ];
// }


$data = [];
foreach ($customers as $c) {
    $data[] = [
        'id' => (string)$c['_id'],
        'nama' => ucfirst($c['nama']),
        'last_time' => (int)($c['last_time']->toDateTime()->format('U') * 1000)
    ];
}



echo json_encode($data);
