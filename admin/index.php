<?php
session_start();
require_once '../config.php';
require_once '../functions.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$stats = getStatistics();
$pending_orders = getAllOrders('pending');
$recent_orders = getAllOrders();
$recent_orders = array_slice($recent_orders, 0, 5);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Vladastore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary: #0fccce;
            --secondary: #00a2ff;
            --dark: #16213e;
            --darker: #1a1a2e;
            --light: #fff;
            --gray: #b0b0b0;
        }

        body {
            background-color: var(--darker);
            color: var(--light);
            min-height: 100vh;
        }

        /* Header */
        .admin-header {
            background: var(--dark);
            padding: 0 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            padding: 15px 0;
        }

        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .logo-image {
            height: 40px;
        }

        .admin-nav {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-link {
            color: var(--gray);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(15, 204, 206, 0.1);
            color: var(--primary);
        }

        .logout-btn {
            background: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.3);
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: #e74c3c;
            color: white;
        }

        /* Main Content */
        .admin-main {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .welcome-section {
            background: var(--dark);
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .welcome-section h1 {
            color: var(--primary);
            margin-bottom: 10px;
        }

        .welcome-section p {
            color: var(--gray);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--dark);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .stat-card h3 {
            font-size: 0.9rem;
            color: var(--gray);
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-card .number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--light);
        }

        /* Tables */
        .card {
            background: var(--dark);
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .card-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            color: var(--primary);
        }

        .card-body {
            padding: 20px;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .table th {
            color: var(--primary);
            font-weight: 600;
            background: rgba(15, 204, 206, 0.05);
        }

        .table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .badge-pending {
            background: #f39c12;
            color: white;
        }

        .badge-proses {
            background: #3498db;
            color: white;
        }

        .badge-sukses {
            background: #27ae60;
            color: white;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: #0db9bb;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        .btn-success {
            background: #27ae60;
            color: white;
        }

        .btn-success:hover {
            background: #219653;
            transform: translateY(-2px);
        }

        .btn-warning {
            background: #f39c12;
            color: white;
        }

        .btn-warning:hover {
            background: #e67e22;
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-nav {
                flex-direction: column;
                gap: 10px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .table {
                font-size: 0.9rem;
            }

            .btn {
                padding: 6px 12px;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .header-container {
                flex-direction: column;
                gap: 15px;
            }

            .admin-nav {
                width: 100%;
                justify-content: center;
            }
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
                <a href="index.php" class="nav-link active"><i class="fas fa-home"></i> Dashboard</a>
                <a href="games.php" class="nav-link"><i class="fas fa-gamepad"></i> Game</a>
                <a href="testimonials.php" class="nav-link"><i class="fas fa-star"></i> Testimoni</a>
                <a href="orders.php" class="nav-link"><i class="fas fa-shopping-cart"></i> Orderan</a>
                <a href="settings.php" class="nav-link"><i class="fas fa-cog"></i> Pengaturan</a>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="admin-main">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <h1>Selamat Datang, Admin!</h1>
            <p>Kelola toko top up game Anda dari dashboard ini.</p>
        </section>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h3>Total Pengunjung</h3>
                <div class="number"><?= number_format($stats['visitors']) ?></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <h3>Order Pending</h3>
                <div class="number"><?= number_format($stats['pending_orders']) ?></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle"></i>
                <h3>Order Sukses</h3>
                <div class="number"><?= number_format($stats['success_orders']) ?></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-shopping-cart"></i>
                <h3>Total Order</h3>
                <div class="number"><?= number_format($stats['total_orders']) ?></div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-history"></i> Order Terbaru</h3>
                <a href="orders.php" class="btn btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                <?php if (count($recent_orders) > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Game</th>
                                <th>Produk</th>
                                <th>WhatsApp</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td>#<?= $order['id'] ?></td>
                                    <td><?= htmlspecialchars($order['game_name']) ?></td>
                                    <td><?= htmlspecialchars($order['product_name']) ?></td>
                                    <td><?= htmlspecialchars($order['whatsapp']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $order['status'] ?>">
                                            <?= strtoupper($order['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                    <td>
                                        <div style="display: flex; gap: 5px;">
                                            <a href="order_detail.php?id=<?= $order['id'] ?>" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($order['status'] == 'pending'): ?>
                                                <button onclick="updateOrderStatus(<?= $order['id'] ?>, 'proses')" class="btn btn-success btn-sm">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; color: var(--gray); padding: 20px;">Belum ada order.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-exclamation-circle"></i> Order Pending</h3>
                <a href="orders.php?status=pending" class="btn btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                <?php if (count($pending_orders) > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Game</th>
                                <th>Produk</th>
                                <th>WhatsApp</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending_orders as $order): ?>
                                <tr>
                                    <td>#<?= $order['id'] ?></td>
                                    <td><?= htmlspecialchars($order['game_name']) ?></td>
                                    <td><?= htmlspecialchars($order['product_name']) ?></td>
                                    <td><?= htmlspecialchars($order['whatsapp']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                    <td>
                                        <div style="display: flex; gap: 5px;">
                                            <a href="order_detail.php?id=<?= $order['id'] ?>" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button onclick="updateOrderStatus(<?= $order['id'] ?>, 'proses')" class="btn btn-success btn-sm">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; color: var(--gray); padding: 20px;">Tidak ada order pending.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        function updateOrderStatus(orderId, status) {
            if (confirm('Update status order ini?')) {
                fetch('../api/admin_update_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `order_id=${orderId}&status=${status}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error);
                });
            }
        }

        // Play notification sound for new orders
        let lastPendingCount = <?= count($pending_orders) ?>;
        
        function checkNewOrders() {
            fetch('../api/get_pending_count.php')
                .then(response => response.json())
                .then(data => {
                    if (data.count > lastPendingCount) {
                        // Play notification sound
                        const audio = new Audio('<?= getSetting('notification_sound') ?>');
                        audio.play().catch(e => console.log('Audio play failed:', e));
                        
                        // Show browser notification
                        if ('Notification' in window && Notification.permission === 'granted') {
                            new Notification('Order Baru!', {
                                body: 'Ada order baru yang perlu diproses.',
                                icon: '../assets/images/logo.png'
                            });
                        }
                        
                        lastPendingCount = data.count;
                        location.reload();
                    }
                })
                .catch(error => console.log('Error checking orders:', error));
        }

        // Check for new orders every 10 seconds
        setInterval(checkNewOrders, 10000);

        // Request notification permission
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    </script>
</body>
</html>