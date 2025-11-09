<?php
require_once 'config.php';

// Telegram notification function
function sendTelegramNotification($message) {
    $bot_token = getSetting('telegram_bot_token');
    $chat_id = getSetting('telegram_chat_id');
    
    if (!$bot_token || !$chat_id) {
        return false;
    }
    
    $url = TELEGRAM_API_URL . $bot_token . "/sendMessage";
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
    
    return $result !== false;
}

// Get all games
function getGames($limit = null) {
    global $pdo;
    $sql = "SELECT * FROM games WHERE is_active = TRUE ORDER BY name";
    if ($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get game by ID
function getGame($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM games WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get game products
function getGameProducts($game_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM game_products WHERE game_id = ? AND is_active = TRUE ORDER BY id");
    $stmt->execute([$game_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get game form fields
function getGameFormFields($game_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM game_form_fields WHERE game_id = ? ORDER BY id");
    $stmt->execute([$game_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get testimonials
function getTestimonials($limit = null) {
    global $pdo;
    $sql = "SELECT * FROM testimonials WHERE is_active = TRUE ORDER BY created_at DESC";
    if ($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Create new order
function createOrder($game_id, $product_id, $customer_data, $whatsapp) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO orders (game_id, product_id, customer_data, whatsapp) VALUES (?, ?, ?, ?)");
    $stmt->execute([$game_id, $product_id, json_encode($customer_data), $whatsapp]);
    
    // Update pending order count
    $count_stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
    $count = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    updateSetting('order_pending_count', $count);
    
    return $pdo->lastInsertId();
}

// Get orders by WhatsApp
function getOrdersByWhatsapp($whatsapp) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT o.*, g.name as game_name, gp.name as product_name, gp.price 
        FROM orders o 
        JOIN games g ON o.game_id = g.id 
        JOIN game_products gp ON o.product_id = gp.id 
        WHERE o.whatsapp = ? 
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$whatsapp]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all orders for admin
function getAllOrders($status = null) {
    global $pdo;
    $sql = "
        SELECT o.*, g.name as game_name, gp.name as product_name, gp.price 
        FROM orders o 
        JOIN games g ON o.game_id = g.id 
        JOIN game_products gp ON o.product_id = gp.id 
    ";
    
    if ($status) {
        $sql .= " WHERE o.status = ?";
    }
    
    $sql .= " ORDER BY o.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($status ? [$status] : []);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Update order status
function updateOrderStatus($order_id, $status) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $result = $stmt->execute([$status, $order_id]);
    
    // Update counts
    if ($result && $status == 'sukses') {
        $count_stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'sukses'");
        $count = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        updateSetting('order_success_count', $count);
        
        $pending_stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
        $pending_count = $pending_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        updateSetting('order_pending_count', $pending_count);
    }
    
    return $result;
}

// Get statistics
function getStatistics() {
    global $pdo;
    
    $stats = [];
    
    // Visitor count
    $stats['visitors'] = getSetting('visitor_count');
    
    // Order counts
    $pending_stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
    $stats['pending_orders'] = $pending_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $success_stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'sukses'");
    $stats['success_orders'] = $success_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $total_stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
    $stats['total_orders'] = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    return $stats;
}
?>