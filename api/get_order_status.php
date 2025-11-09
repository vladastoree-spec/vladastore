<?php
require_once '../config.php';
require_once '../functions.php';

header('Content-Type: application/json');

if (!isset($_GET['whatsapp'])) {
    echo json_encode(['success' => false, 'message' => 'WhatsApp number required']);
    exit;
}

$whatsapp = $_GET['whatsapp'];
$orders = getOrdersByWhatsapp($whatsapp);

echo json_encode([
    'success' => true,
    'orders' => $orders
]);
?>