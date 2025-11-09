<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$token = $_POST['token'] ?? '';
$chat_id = $_POST['chat_id'] ?? '';

if (!$token || !$chat_id) {
    echo json_encode(['success' => false, 'message' => 'Token and Chat ID required']);
    exit;
}

$message = "✅ Test Notifikasi Vladastore\n\nIni adalah pesan test untuk memverifikasi bahwa bot Telegram terhubung dengan benar.\n\nWaktu: " . date('d/m/Y H:i:s');

$url = "https://api.telegram.org/bot{$token}/sendMessage";
$data = [
    'chat_id' => $chat_id,
    'text' => $message,
    'parse_mode' => 'HTML'
];

$options = [
    'http' => [
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data)
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result !== false) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send message']);
}
?>