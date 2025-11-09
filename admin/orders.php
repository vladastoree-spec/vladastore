<?php
session_start();
require_once '../config.php';
require_once '../functions.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

// Build query based on filters
$where = [];
$params = [];

if ($status && $status !== 'all') {
    $where[] = "o.status = ?";
    $params[] = $status;
}

if ($search) {
    $where[] = "(g.name LIKE ? OR gp.name LIKE ? OR o.whatsapp LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$where_clause = '';
if (!empty($where)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where);
}

$sql = "
    SELECT o.*, g.name as game_name, gp.name as product_name, gp.price 
    FROM orders o 
    JOIN games g ON o.game_id = g.id 
    JOIN game_products gp ON o.product_id = gp.id 
    $where_clause
    ORDER BY o.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle bulk actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_all'])) {
        $stmt = $pdo->prepare("DELETE FROM orders");
        $stmt->execute();
        
        // Reset counts
        updateSetting('order_pending_count', '0');
        updateSetting('order_success_count', '0');
        
        header('Location: orders.php');
        exit;
    }
    
    if (isset($_POST['update_status'])) {
        $order_id = intval($_POST['order_id']);
        $status = $_POST['status'];
        
        updateOrderStatus($order_id, $status);
        header('Location: orders.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Orderan - Vladastore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Include styles from admin index */
        <?php include 'admin_styles.php'; ?>
        
        .filters {
            background: var(--dark);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: end;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-badge {
            background: var(--dark);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        
        .stat-badge .number {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary);
        }
        
        .stat-badge .label {
            font-size: 0.9rem;
            color: var(--gray);
        }
        
        .customer-data {
            max-width: 300px;
            word-wrap: break-word;
        }
        
        .copy-btn {
            background: rgba(52, 152, 219, 0.1);
            color: #3498db;
            border: 1px solid rgba(52, 152, 219, 0.3);
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            cursor: pointer;
            margin-left: 5px;
        }
        
        .copy-btn:hover {
            background: #3498db;
            color: white;
        }
        
        .bulk-actions {
            margin-top: 20px;
            padding: 15px;
            background: rgba(231, 76, 60, 0.1);
            border-radius: 8px;
            border: 1px solid rgba(231, 76, 60, 0.3);
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
        <!-- Order Statistics -->
        <div class="stats-row">
            <div class="stat-badge">
                <div class="number"><?= number_format(getStatistics()['total_orders']) ?></div>
                <div class="label">Total Order</div>
            </div>
            <div class="stat-badge">
                <div class="number"><?= number_format(getStatistics()['pending_orders']) ?></div>
                <div class="label">Pending</div>
            </div>
            <div class="stat-badge">
                <div class="number"><?= number_format(getStatistics()['success_orders']) ?></div>
                <div class="label">Sukses</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters">
            <div class="filter-group">
                <label class="form-label">Status Order:</label>
                <select id="statusFilter" class="form-input" onchange="filterOrders()">
                    <option value="all" <?= !$status ? 'selected' : '' ?>>Semua Status</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="proses" <?= $status === 'proses' ? 'selected' : '' ?>>Proses</option>
                    <option value="sukses" <?= $status === 'sukses' ? 'selected' : '' ?>>Sukses</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label class="form-label">Cari:</label>
                <input type="text" id="searchInput" class="form-input" placeholder="Cari game, produk, atau WhatsApp..." value="<?= htmlspecialchars($search) ?>">
            </div>
            
            <div class="filter-group">
                <button onclick="filterOrders()" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
                <button onclick="clearFilters()" class="btn btn-warning"><i class="fas fa-times"></i> Reset</button>
            </div>
        </div>

        <!-- Orders List -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> Daftar Orderan (<?= count($orders) ?>)</h3>
            </div>
            <div class="card-body">
                <?php if (count($orders) > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Game & Produk</th>
                                <th>Data Customer</th>
                                <th>WhatsApp</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): 
                                $customer_data = json_decode($order['customer_data'], true);
                            ?>
                                <tr>
                                    <td>#<?= $order['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($order['game_name']) ?></strong><br>
                                        <small><?= htmlspecialchars($order['product_name']) ?></small><br>
                                        <small style="color: var(--primary);"><?= htmlspecialchars($order['price']) ?></small>
                                    </td>
                                    <td class="customer-data">
                                        <?php foreach ($customer_data as $key => $value): 
                                            if ($key !== 'payment_proof'): ?>
                                                <div>
                                                    <strong><?= htmlspecialchars($key) ?>:</strong> 
                                                    <?= htmlspecialchars($value) ?>
                                                    <button class="copy-btn" onclick="copyToClipboard('<?= htmlspecialchars($value) ?>')">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </div>
                                            <?php endif;
                                        endforeach; ?>
                                        
                                        <?php if (isset($customer_data['payment_proof'])): ?>
                                            <div>
                                                <strong>Bukti Bayar:</strong>
                                                <a href="../<?= htmlspecialchars($customer_data['payment_proof']) ?>" target="_blank" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($order['whatsapp']) ?>
                                        <button class="copy-btn" onclick="copyToClipboard('<?= htmlspecialchars($order['whatsapp']) ?>')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                            <select name="status" onchange="this.form.submit()" class="form-input" style="padding: 5px; font-size: 0.9rem;">
                                                <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="proses" <?= $order['status'] === 'proses' ? 'selected' : '' ?>>Proses</option>
                                                <option value="sukses" <?= $order['status'] === 'sukses' ? 'selected' : '' ?>>Sukses</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                    </td>
                                    <td>
                                        <?= date('d/m/Y', strtotime($order['created_at'])) ?><br>
                                        <small><?= date('H:i', strtotime($order['created_at'])) ?></small>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 5px; flex-direction: column;">
                                            <a href="order_detail.php?id=<?= $order['id'] ?>" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                            <button onclick="copyOrderData(<?= $order['id'] ?>)" class="btn btn-success btn-sm">
                                                <i class="fas fa-copy"></i> Copy
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; color: var(--gray); padding: 20px;">Tidak ada orderan.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bulk-actions">
            <h4><i class="fas fa-exclamation-triangle"></i> Aksi Massal</h4>
            <p style="color: var(--gray); margin-bottom: 15px;">Hati-hati! Aksi ini tidak dapat dibatalkan.</p>
            <form method="POST" onsubmit="return confirm('Yakin ingin menghapus SEMUA orderan? Tindakan ini tidak dapat dibatalkan!')">
                <button type="submit" name="delete_all" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Hapus Semua Orderan
                </button>
            </form>
        </div>
    </main>

    <script>
        function filterOrders() {
            const status = document.getElementById('statusFilter').value;
            const search = document.getElementById('searchInput').value;
            
            let url = 'orders.php?';
            if (status && status !== 'all') url += `status=${status}&`;
            if (search) url += `search=${encodeURIComponent(search)}`;
            
            window.location.href = url;
        }
        
        function clearFilters() {
            window.location.href = 'orders.php';
        }
        
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Tersalin: ' + text);
            });
        }
        
        function copyOrderData(orderId) {
            // Get order data and format for copying
            const row = document.querySelector(`tr:has(td:first-child:contains("#${orderId}"))`);
            if (row) {
                const game = row.cells[1].querySelector('strong').textContent;
                const product = row.cells[1].querySelector('small').textContent;
                const price = row.cells[1].querySelector('small[style*="color"]').textContent;
                const whatsapp = row.cells[3].textContent.trim();
                
                let customerData = '';
                const dataElements = row.cells[2].querySelectorAll('div');
                dataElements.forEach(div => {
                    if (!div.textContent.includes('Bukti Bayar')) {
                        customerData += div.textContent.replace('copy', '').trim() + '\n';
                    }
                });
                
                const text = `ORDER #${orderId}\n\nGame: ${game}\nProduk: ${product}\nHarga: ${price}\nWhatsApp: ${whatsapp}\n\nData Customer:\n${customerData}`;
                
                copyToClipboard(text);
            }
        }
        
        // Auto-refresh every 30 seconds if there are pending orders
        <?php if (getStatistics()['pending_orders'] > 0): ?>
        setTimeout(() => {
            window.location.reload();
        }, 30000);
        <?php endif; ?>
    </script>
</body>
</html>