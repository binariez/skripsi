<?php
require_once __DIR__ . '/../functions/SessionHandlerInterface.php';
session_start();
require_once __DIR__ . '/../functions/Connection.php';

header('Content-Type: application/json');

global $db;
$collection = $db->livechat;

$customer_id = $_POST['customer_id'] ?? null;
$chatText = $_POST['chat'] ?? '';

if (!$customer_id || trim($chatText) === '') {
    echo json_encode(['status' => 'error', 'message' => 'Data chat tidak lengkap']);
    exit;
}

$admin_id = $_SESSION['id'] ?? 'admin';
$admin_name = $_SESSION['nama'] ?? 'Admin';

$result = $collection->insertOne([
    'id' => new MongoDB\BSON\ObjectId($customer_id),
    'nama' => $admin_name,
    'chat' => $chatText,
    'sender_type' => 'admin',
    'created_at' => new MongoDB\BSON\UTCDateTime()
]);

if ($result->getInsertedCount() === 1) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan chat']);
}
