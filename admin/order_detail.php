<?php
session_start();
require_once '../config.php';
require_once '../functions.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$order_id = intval($_GET['id']);

// Get order details
$stmt = $pdo->prepare("
    SELECT o.*, g.name as game_name, gp.name as product_name, gp.price, gp.description as product_description
    FROM orders o 
    JOIN games g ON o.game_id = g.id 
    JOIN game_products gp ON o.product_id = gp.id 
    WHERE o.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: orders.php');
    exit;
}

$customer_data = json_decode($order['customer_data'], true);

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $status = $_POST['status'];
    updateOrderStatus($order_id, $status);
    header("Location: order_detail.php?id=$order_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Order #<?= $order_id ?> - Vladastore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        <?php include 'admin_styles.php'; ?>
        
        .order-header {
            background: var(--dark);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .order-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-card {
            background: var(--dark);
            padding: 20px;
            border-radius: 8px;
        }
        
        .info-card h4 {
            color: var(--primary);
            margin-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 10px;
        }
        
        .info-item {
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
        }
        
        .info-item .label {
            color: var(--gray);
        }
        
        .info-item .value {
            font-weight: 600;
        }
        
        .customer-data-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }
        
        .data-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 10px;
            border-radius: 6px;
        }
        
        .data-item .label {
            font-size: 0.9rem;
            color: var(--gray);
            margin-bottom: 5px;
        }
        
        .data-item .value {
            font-weight: 600;
            word-break: break-all;
        }
        
        .copy-all-btn {
            margin-top: 15px;
            width: 100%;
        }
        
        .status-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .payment-proof {
            text-align: center;
            margin-top: 20px;
        }
        
        .payment-proof img {
            max-width: 300px;
            max-height: 300px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="admin-header">
        <div class="header-container">
            <a href="index.php" class="logo">
                <?php
                $logo = getSetting('store_logo');
                if ($logo && file_exists($logo)): ?>
                    <img src="../<?= $logo ?>" alt="Vladastore Logo" class="logo-image">
                <?php else: ?>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #0fccce 0%, #00a2ff 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #16213e; font-size: 18px;">V</div>
                        <div style="font-size: 24px; font-weight: bold; color: #0fccce;">Vladastore Admin</div>
                    </div>
                <?php endif; ?>
            </a>

            <nav class="admin-nav">
                <a href="index.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a>
                <a href="games.php" class="nav-link"><i class="fas fa-gamepad"></i> Game</a>
                <a href="testimonials.php" class="nav-link"><i class="fas fa-star"></i> Testimoni</a>
                <a href="orders.php" class="nav-link active"><i class="fas fa-shopping-cart"></i> Orderan</a>
                <a href="settings.php" class="nav-link"><i class="fas fa-cog"></i> Pengaturan</a>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="admin-main">
        <!-- Order Header -->
        <div class="order-header">
            <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 15px;">
                <h1>Detail Order #<?= $order_id ?></h1>
                <a href="orders.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
            
            <form method="POST" class="status-form">
                <label style="color: var(--light); font-weight: 600;">Status:</label>
                <select name="status" class="form-input" style="width: auto;">
                    <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="proses" <?= $order['status'] === 'proses' ? 'selected' : '' ?>>Proses</option>
                    <option value="sukses" <?= $order['status'] === 'sukses' ? 'selected' : '' ?>>Sukses</option>
                </select>
                <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
            </form>
        </div>

        <!-- Order Information -->
        <div class="order-info-grid">
            <!-- Product Information -->
            <div class="info-card">
                <h4><i class="fas fa-gamepad"></i> Informasi Produk</h4>
                <div class="info-item">
                    <span class="label">Game:</span>
                    <span class="value"><?= htmlspecialchars($order['game_name']) ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Produk:</span>
                    <span class="value"><?= htmlspecialchars($order['product_name']) ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Deskripsi:</span>
                    <span class="value"><?= htmlspecialchars($order['product_description']) ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Harga:</span>
                    <span class="value" style="color: var(--primary);"><?= htmlspecialchars($order['price']) ?></span>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="info-card">
                <h4><i class="fas fa-user"></i> Informasi Customer</h4>
                <div class="info-item">
                    <span class="label">WhatsApp:</span>
                    <span class="value">
                        <?= htmlspecialchars($order['whatsapp']) ?>
                        <button class="copy-btn" onclick="copyToClipboard('<?= htmlspecialchars($order['whatsapp']) ?>')">
                            <i class="fas fa-copy"></i>
                        </button>
                    </span>
                </div>
                <div class="info-item">
                    <span class="label">Status:</span>
                    <span class="value">
                        <span class="badge badge-<?= $order['status'] ?>">
                            <?= strtoupper($order['status']) ?>
                        </span>
                    </span>
                </div>
                <div class="info-item">
                    <span class="label">Tanggal Order:</span>
                    <span class="value"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Terakhir Update:</span>
                    <span class="value"><?= date('d/m/Y H:i', strtotime($order['updated_at'])) ?></span>
                </div>
            </div>
        </div>

        <!-- Customer Data -->
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-list-alt"></i> Data Customer</h4>
                <button onclick="copyAllCustomerData()" class="btn btn-success">
                    <i class="fas fa-copy"></i> Copy Semua Data
                </button>
            </div>
            <div class="card-body">
                <div class="customer-data-grid">
                    <?php foreach ($customer_data as $key => $value): 
                        if ($key !== 'payment_proof'): ?>
                            <div class="data-item">
                                <div class="label"><?= htmlspecialchars($key) ?></div>
                                <div class="value">
                                    <?= htmlspecialchars($value) ?>
                                    <button class="copy-btn" onclick="copyToClipboard('<?= htmlspecialchars($value) ?>')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endif;
                    endforeach; ?>
                </div>
                
                <?php if (isset($customer_data['payment_proof'])): ?>
                    <div class="payment-proof">
                        <h4 style="color: var(--primary); margin-bottom: 15px;">Bukti Pembayaran</h4>
                        <a href="../<?= htmlspecialchars($customer_data['payment_proof']) ?>" target="_blank">
                            <img src="../<?= htmlspecialchars($customer_data['payment_proof']) ?>" 
                                 alt="Bukti Pembayaran" 
                                 onerror="this.style.display='none'">
                        </a>
                        <div>
                            <a href="../<?= htmlspecialchars($customer_data['payment_proof']) ?>" 
                               download 
                               class="btn btn-primary">
                                <i class="fas fa-download"></i> Download Bukti
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-bolt"></i> Aksi Cepat</h4>
            </div>
            <div class="card-body">
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button onclick="sendWhatsAppMessage()" class="btn btn-success">
                        <i class="fas fa-comment"></i> Chat WhatsApp
                    </button>
                    <button onclick="copyOrderSummary()" class="btn btn-primary">
                        <i class="fas fa-copy"></i> Copy Ringkasan
                    </button>
                    <button onclick="markAsCompleted()" class="btn btn-warning">
                        <i class="fas fa-check"></i> Tandai Selesai
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                showAlert('Tersalin: ' + text);
            });
        }
        
        function copyAllCustomerData() {
            let text = `ORDER #<?= $order_id ?>\n\n`;
            text += `Game: <?= htmlspecialchars($order['game_name']) ?>\n`;
            text += `Produk: <?= htmlspecialchars($order['product_name']) ?>\n`;
            text += `Harga: <?= htmlspecialchars($order['price']) ?>\n`;
            text += `WhatsApp: <?= htmlspecialchars($order['whatsapp']) ?>\n\n`;
            text += `Data Customer:\n`;
            
            <?php foreach ($customer_data as $key => $value): 
                if ($key !== 'payment_proof'): ?>
                    text += `<?= htmlspecialchars($key) ?>: <?= htmlspecialchars($value) ?>\n`;
                <?php endif;
            endforeach; ?>
            
            copyToClipboard(text);
        }
        
        function copyOrderSummary() {
            const text = `Order #<?= $order_id ?> - <?= htmlspecialchars($order['game_name']) ?> - <?= htmlspecialchars($order['product_name']) ?> - <?= htmlspecialchars($order['price']) ?> - Status: <?= $order['status'] ?>`;
            copyToClipboard(text);
        }
        
        function sendWhatsAppMessage() {
            const phone = '<?= htmlspecialchars($order['whatsapp']) ?>';
            const message = `Halo! Terima kasih telah order di Vladastore. Orderan Anda (#<?= $order_id ?>) sedang dalam proses.`;
            const url = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
            window.open(url, '_blank');
        }
        
        function markAsCompleted() {
            if (confirm('Tandai order ini sebagai selesai?')) {
                document.querySelector('select[name="status"]').value = 'sukses';
                document.querySelector('button[name="update_status"]').click();
            }
        }
        
        function showAlert(message) {
            const alert = document.createElement('div');
            alert.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #27ae60;
                color: white;
                padding: 15px 20px;
                border-radius: 6px;
                z-index: 10000;
                box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            `;
            alert.textContent = message;
            document.body.appendChild(alert);
            
            setTimeout(() => {
                alert.remove();
            }, 3000);
        }
        
        // Auto-focus on status select if order is pending
        <?php if ($order['status'] === 'pending'): ?>
        document.querySelector('select[name="status"]').focus();
        <?php endif; ?>
    </script>
</body>
</html>