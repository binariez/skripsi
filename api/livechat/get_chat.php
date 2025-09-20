<?php
session_start();
require '../functions/Connection.php';

header('Content-Type: application/json');

global $db;
$collection = $db->livechat;
// Ambil plg_id dari session
$plg_id = $_SESSION['UserLogin'][0]['id'] ?? null;

if (!$plg_id) {
    echo json_encode(['chats' => [], 'lastTime' => 0]);
    exit;
}

$lastTime = isset($_GET['lastTime']) ? (int)$_GET['lastTime'] : 0;

// Ambil chat dari admin dan pelanggan
$query = ['id' => $plg_id];
if ($lastTime) {
    $query['created_at'] = ['$gt' => new MongoDB\BSON\UTCDateTime($lastTime)];
}

$options = ['sort' => ['created_at' => 1]];

$chats = $collection->find($query, $options);

$data = [];
$newLastTime = $lastTime;

foreach ($chats as $c) {
    $time = (int)$c['created_at']->toDateTime()->format('U') * 1000;
    if ($time > $newLastTime) $newLastTime = $time;

    $data[] = [
        '_id' => (string)$c['_id'],
        'chat' => $c['chat'],
        'sender_type' => $c['sender_type'],
        'created_at' => $time
    ];
}

echo json_encode([
    'chats' => $data,
    'lastTime' => $newLastTime
]);
