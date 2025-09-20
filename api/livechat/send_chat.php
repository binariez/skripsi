<?php
require_once __DIR__ . '/../functions/SessionHandlerInterface.php';
session_start();
require '../functions/Connection.php';
global $db;
$collection = $db->livechat;

header('Content-Type: application/json');

$chatText = $_POST['chat'] ?? '';
$plg_id = $_SESSION['UserLogin'][0]['id'] ?? null;
$plg_name = $_SESSION['UserLogin'][0]['nama'] ?? null;

if (!$plg_id || trim($chatText) === '') {
    echo json_encode(['status' => 'error', 'message' => 'Chat kosong']);
    exit;
}

$result = $collection->insertOne([
    'id' => $plg_id,
    'nama' => $plg_name,
    'chat' => $chatText,
    'sender_type' => 'pelanggan',
    'created_at' => new MongoDB\BSON\UTCDateTime()
]);

if ($result->getInsertedCount() === 1) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal kirim chat']);
}
