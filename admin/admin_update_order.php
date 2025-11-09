<?php
require_once '../config.php';
require_once '../functions.php';

header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Order ID and status required']);
    exit;
}

$order_id = intval($_POST['order_id']);
$status = $_POST['status'];

$result = updateOrderStatus($order_id, $status);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
}
?>