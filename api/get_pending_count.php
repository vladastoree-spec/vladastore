<?php
require_once '../config.php';
require_once '../functions.php';

header('Content-Type: application/json');

$stmt = $pdo->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
$result = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'count' => intval($result['count'])
]);
?>