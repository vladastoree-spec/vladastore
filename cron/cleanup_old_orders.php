<?php
require_once '../config.php';

// Hapus orders yang sudah sukses lebih dari 30 hari
$stmt = $pdo->prepare("DELETE FROM orders WHERE status = 'sukses' AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
$stmt->execute();

// Hapus orders pending yang lebih dari 7 hari
$stmt = $pdo->prepare("DELETE FROM orders WHERE status = 'pending' AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)");
$stmt->execute();

// Update statistics
$visitor_stmt = $pdo->query("SELECT COUNT(*) as total FROM visitors");
$visitor_count = $visitor_stmt->fetch(PDO::FETCH_ASSOC)['total'];

$pending_stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
$pending_count = $pending_stmt->fetch(PDO::FETCH_ASSOC)['total'];

$success_stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'sukses'");
$success_count = $success_stmt->fetch(PDO::FETCH_ASSOC)['total'];

$update_stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
$update_stmt->execute([$visitor_count, 'visitor_count']);
$update_stmt->execute([$pending_count, 'order_pending_count']);
$update_stmt->execute([$success_count, 'order_success_count']);

echo "Cleanup completed at " . date('Y-m-d H:i:s') . "\n";
?>