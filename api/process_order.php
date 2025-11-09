<?php
require_once '../config.php';
require_once '../functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$game_id = intval($_POST['game_id']);
$product_id = intval($_POST['product_id']);
$whatsapp = $_POST['whatsapp'];
$custom_data = json_decode($_POST['custom_data'], true);

// Validate required fields
if (!$game_id || !$product_id || !$whatsapp) {
    echo json_encode(['success' => false, 'message' => 'Required fields missing']);
    exit;
}

// Handle file upload
$payment_proof_path = null;
if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../assets/uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $file_extension = pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION);
    $filename = 'proof_' . time() . '_' . uniqid() . '.' . $file_extension;
    $payment_proof_path = $upload_dir . $filename;
    
    if (!move_uploaded_file($_FILES['payment_proof']['tmp_name'], $payment_proof_path)) {
        echo json_encode(['success' => false, 'message' => 'Failed to upload payment proof']);
        exit;
    }
}

// Add payment proof to custom data
if ($payment_proof_path) {
    $custom_data['payment_proof'] = str_replace('../', '', $payment_proof_path);
}

// Create order
$order_id = createOrder($game_id, $product_id, $custom_data, $whatsapp);

if ($order_id) {
    // Get order details for Telegram notification
    $game = getGame($game_id);
    $product_stmt = $pdo->prepare("SELECT * FROM game_products WHERE id = ?");
    $product_stmt->execute([$product_id]);
    $product = $product_stmt->fetch(PDO::FETCH_ASSOC);
    
    // Prepare Telegram message
    $message = "🆕 ORDER BARU 🆕\n\n";
    $message .= "📦 Produk: " . $game['name'] . " - " . $product['name'] . "\n";
    $message .= "💰 Harga: " . $product['price'] . "\n";
    $message .= "📱 WhatsApp: " . $whatsapp . "\n";
    $message .= "🆔 Order ID: #" . $order_id . "\n\n";
    
    $message .= "📋 Data Customer:\n";
    foreach ($custom_data as $key => $value) {
        $message .= "• " . $key . ": " . $value . "\n";
    }
    
    $message .= "\n⏰ " . date('d/m/Y H:i:s');
    
    // Send to Telegram
    sendTelegramNotification($message);
    
    echo json_encode(['success' => true, 'order_id' => $order_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to create order']);
}
?>