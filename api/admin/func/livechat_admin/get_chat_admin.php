<?php
session_start();
require __DIR__ . '../../../functions/Connection.php';

header('Content-Type: application/json');

global $db;
$collection = $db->livechat;

$customer_id = $_GET['customer_id'] ?? null;
$lastTime = isset($_GET['lastTime']) ? (int)$_GET['lastTime'] : 0;

if (!$customer_id) {
    echo json_encode(['chats' => [], 'lastTime' => $lastTime]);
    exit;
}

// Query dasar: berdasarkan customer id
$query = ['id' => new MongoDB\bson\ObjectId($customer_id)];

// Jika lastTime > 0, tambahkan filter created_at
if ($lastTime > 0) {
    // MongoDB UTCDateTime menerima integer dalam milidetik
    $query['created_at'] = ['$gt' => new MongoDB\BSON\UTCDateTime($lastTime)];
}

$options = ['sort' => ['created_at' => 1]];

$chatsCursor = $collection->find($query, $options);
$data = [];
$newLastTime = $lastTime;

foreach ($chatsCursor as $c) {
    // ambil waktu dalam milidetik
    $time = (int)$c['created_at']->toDateTime()->format('U') * 1000;

    // simpan chat
    $data[] = [
        'chat' => $c['chat'],
        'sender_type' => $c['sender_type'],
        'created_at' => $time
    ];

    // update lastTime hanya jika chat baru
    if ($time > $newLastTime) $newLastTime = $time;
}

echo json_encode(['chats' => $data, 'lastTime' => $newLastTime]);
