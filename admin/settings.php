<?php
session_start();
require_once '../config.php';
require_once '../functions.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'setting_') === 0) {
            $setting_key = str_replace('setting_', '', $key);
            updateSetting($setting_key, $value);
        }
    }
    
    // Handle file uploads
    if (isset($_FILES['store_logo_file']) && $_FILES['store_logo_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/images/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($_FILES['store_logo_file']['name'], PATHINFO_EXTENSION);
        $filename = 'logo_' . time() . '.' . $file_extension;
        $logo_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['store_logo_file']['tmp_name'], $logo_path)) {
            updateSetting('store_logo', 'assets/images/' . $filename);
        }
    }
    
    $success = "Pengaturan berhasil diperbarui!";
}

// Get current settings
$settings = [];
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Vladastore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        <?php include 'admin_styles.php'; ?>
        
        .settings-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 10px;
        }
        
        .tab-btn {
            background: transparent;
            color: var(--gray);
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .tab-btn.active {
            background: var(--primary);
            color: white;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
        }
        
        .setting-group {
            background: var(--dark);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .setting-group h4 {
            color: var(--primary);
            margin-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 10px;
        }
        
        .logo-preview {
            width: 150px;
            height: 150px;
            border: 2px dashed var(--gray);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            overflow: hidden;
        }
        
        .logo-preview img {
            max-width: 100%;
            max-height: 100%;
        }
        
        .test-telegram {
            margin-top: 10px;
        }
        
        .instructions {
            background: rgba(15, 204, 206, 0.1);
            border: 1px solid rgba(15, 204, 206, 0.3);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .instructions h5 {
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .instructions ol {
            padding-left: 20px;
        }
        
        .instructions li {
            margin-bottom: 8px;
            color: var(--gray);
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
                <a href="orders.php" class="nav-link"><i class="fas fa-shopping-cart"></i> Orderan</a>
                <a href="settings.php" class="nav-link active"><i class="fas fa-cog"></i> Pengaturan</a>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="admin-main">
        <?php if (isset($success)): ?>
            <div style="background: rgba(39, 174, 96, 0.1); border: 1px solid rgba(39, 174, 96, 0.3); color: #27ae60; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i> <?= $success ?>
            </div>
        <?php endif; ?>

        <div class="settings-tabs">
            <button class="tab-btn active" onclick="showTab('general')">Umum</button>
            <button class="tab-btn" onclick="showTab('telegram')">Telegram</button>
            <button class="tab-btn" onclick="showTab('contact')">Kontak</button>
            <button class="tab-btn" onclick="showTab('notifications')">Notifikasi</button>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <!-- General Settings -->
            <div id="general" class="tab-content active">
                <div class="settings-grid">
                    <!-- Logo Settings -->
                    <div class="setting-group">
                        <h4><i class="fas fa-image"></i> Logo Toko</h4>
                        
                        <div class="logo-preview">
                            <?php if ($settings['store_logo'] && file_exists('../' . $settings['store_logo'])): ?>
                                <img src="../<?= $settings['store_logo'] ?>" alt="Current Logo">
                            <?php else: ?>
                                <div style="text-align: center; color: var(--gray);">
                                    <i class="fas fa-store" style="font-size: 3rem; margin-bottom: 10px;"></i>
                                    <div>Logo Saat Ini</div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Upload Logo Baru:</label>
                            <input type="file" name="store_logo_file" class="form-input" accept="image/*">
                            <small style="color: var(--gray);">Format: JPG, PNG, SVG. Ukuran maksimal: 2MB</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Atau URL Logo:</label>
                            <input type="url" name="setting_store_logo" class="form-input" 
                                   value="<?= htmlspecialchars($settings['store_logo']) ?>" 
                                   placeholder="https://example.com/logo.png">
                        </div>
                    </div>

                    <!-- Store Information -->
                    <div class="setting-group">
                        <h4><i class="fas fa-info-circle"></i> Informasi Toko</h4>
                        
                        <div class="form-group">
                            <label class="form-label">Nama Toko:</label>
                            <input type="text" name="setting_store_name" class="form-input" 
                                   value="<?= htmlspecialchars($settings['store_name'] ?? 'Vladastore') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Deskripsi Toko:</label>
                            <textarea name="setting_store_description" class="form-input" rows="4" placeholder="Deskripsi singkat tentang toko Anda"><?= htmlspecialchars($settings['store_description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Telegram Settings -->
            <div id="telegram" class="tab-content">
                <div class="instructions">
                    <h5><i class="fas fa-info-circle"></i> Cara Setup Bot Telegram</h5>
                    <ol>
                        <li>Buka Telegram dan cari @BotFather</li>
                        <li>Kirim perintah <code>/newbot</code> dan ikuti instruksi</li>
                        <li>Salin token bot yang diberikan</li>
                        <li>Untuk mendapatkan Chat ID, tambahkan bot ke grup/channel dan kirim pesan</li>
                        <li>Buka URL: <code>https://api.telegram.org/bot&lt;TOKEN_BOT&gt;/getUpdates</code></li>
                        <li>Cari "chat" dan salin "id" (bisa negatif untuk grup)</li>
                    </ol>
                </div>

                <div class="settings-grid">
                    <div class="setting-group">
                        <h4><i class="fab fa-telegram"></i> Konfigurasi Bot Telegram</h4>
                        
                        <div class="form-group">
                            <label class="form-label">Bot Token:</label>
                            <input type="text" name="setting_telegram_bot_token" class="form-input" 
                                   value="<?= htmlspecialchars($settings['telegram_bot_token']) ?>" 
                                   placeholder="123456789:ABCdefGhIJKlmNoPQRsTUVwxyZ">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Chat ID:</label>
                            <input type="text" name="setting_telegram_chat_id" class="form-input" 
                                   value="<?= htmlspecialchars($settings['telegram_chat_id']) ?>" 
                                   placeholder="-1001234567890">
                        </div>
                        
                        <div class="test-telegram">
                            <button type="button" onclick="testTelegram()" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Test Notifikasi
                            </button>
                        </div>
                    </div>

                    <div class="setting-group">
                        <h4><i class="fas fa-bell"></i> Template Notifikasi</h4>
                        
                        <div class="form-group">
                            <label class="form-label">Format Pesan Order Baru:</label>
                            <textarea name="setting_telegram_order_template" class="form-input" rows="8">
<?= htmlspecialchars($settings['telegram_order_template'] ?? "🆕 ORDER BARU 🆕

📦 Produk: {game} - {product}
💰 Harga: {price}
📱 WhatsApp: {whatsapp}
🆔 Order ID: #{order_id}

📋 Data Customer:
{customer_data}

⏰ {order_time}") ?>
                            </textarea>
                            <small style="color: var(--gray);">
                                Variabel yang tersedia: {game}, {product}, {price}, {whatsapp}, {order_id}, {customer_data}, {order_time}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Settings -->
            <div id="contact" class="tab-content">
                <div class="settings-grid">
                    <div class="setting-group">
                        <h4><i class="fas fa-phone"></i> Informasi Kontak</h4>
                        
                        <div class="form-group">
                            <label class="form-label">WhatsApp:</label>
                            <input type="text" name="setting_contact_whatsapp" class="form-input" 
                                   value="<?= htmlspecialchars($settings['contact_whatsapp']) ?>" 
                                   placeholder="081234567890">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Email:</label>
                            <input type="email" name="setting_contact_email" class="form-input" 
                                   value="<?= htmlspecialchars($settings['contact_email']) ?>" 
                                   placeholder="admin@vladastore.com">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Telepon:</label>
                            <input type="text" name="setting_contact_phone" class="form-input" 
                                   value="<?= htmlspecialchars($settings['contact_phone']) ?>" 
                                   placeholder="(021) 1234-5678">
                        </div>
                    </div>

                    <div class="setting-group">
                        <h4><i class="fas fa-clock"></i> Jam Operasional</h4>
                        
                        <div class="form-group">
                            <label class="form-label">Jam Buka:</label>
                            <input type="text" name="setting_business_hours" class="form-input" 
                                   value="<?= htmlspecialchars($settings['business_hours'] ?? '08.00 - 22.00 WIB') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Alamat:</label>
                            <textarea name="setting_store_address" class="form-input" rows="4"><?= htmlspecialchars($settings['store_address'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification Settings -->
            <div id="notifications" class="tab-content">
                <div class="settings-grid">
                    <div class="setting-group">
                        <h4><i class="fas fa-bell"></i> Pengaturan Notifikasi</h4>
                        
                        <div class="form-group">
                            <label class="form-label">URL Suara Notifikasi:</label>
                            <input type="url" name="setting_notification_sound" class="form-input" 
                                   value="<?= htmlspecialchars($settings['notification_sound']) ?>" 
                                   placeholder="https://example.com/notification.mp3">
                            <small style="color: var(--gray);">
                                URL file audio yang akan diputar saat ada order baru
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Auto-refresh Dashboard:</label>
                            <select name="setting_auto_refresh" class="form-input">
                                <option value="0" <?= ($settings['auto_refresh'] ?? '30') == '0' ? 'selected' : '' ?>>Tidak</option>
                                <option value="30" <?= ($settings['auto_refresh'] ?? '30') == '30' ? 'selected' : '' ?>>30 Detik</option>
                                <option value="60" <?= ($settings['auto_refresh'] ?? '30') == '60' ? 'selected' : '' ?>>1 Menit</option>
                                <option value="300" <?= ($settings['auto_refresh'] ?? '30') == '300' ? 'selected' : '' ?>>5 Menit</option>
                            </select>
                        </div>
                    </div>

                    <div class="setting-group">
                        <h4><i class="fas fa-database"></i> Pemeliharaan Data</h4>
                        
                        <div class="form-group">
                            <label class="form-label">Auto-hapus Order Sukses (hari):</label>
                            <input type="number" name="setting_auto_cleanup_days" class="form-input" 
                                   value="<?= htmlspecialchars($settings['auto_cleanup_days'] ?? '30') ?>" 
                                   min="1" max="365">
                            <small style="color: var(--gray);">
                                Order yang sudah sukses akan otomatis dihapus setelah hari yang ditentukan
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Max Order per Halaman:</label>
                            <input type="number" name="setting_orders_per_page" class="form-input" 
                                   value="<?= htmlspecialchars($settings['orders_per_page'] ?? '50') ?>" 
                                   min="10" max="200">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div style="text-align: center; margin-top: 30px;">
                <button type="submit" class="btn btn-primary" style="padding: 15px 30px; font-size: 1.1rem;">
                    <i class="fas fa-save"></i> Simpan Semua Pengaturan
                </button>
            </div>
        </form>
    </main>

    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            
            // Update tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
        }
        
        function testTelegram() {
            const token = document.querySelector('[name="setting_telegram_bot_token"]').value;
            const chatId = document.querySelector('[name="setting_telegram_chat_id"]').value;
            
            if (!token || !chatId) {
                alert('Token Bot dan Chat ID harus diisi!');
                return;
            }
            
            fetch('../api/test_telegram.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `token=${encodeURIComponent(token)}&chat_id=${encodeURIComponent(chatId)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ Notifikasi test berhasil dikirim!');
                } else {
                    alert('❌ Gagal mengirim test: ' + data.message);
                }
            })
            .catch(error => {
                alert('❌ Error: ' + error);
            });
        }
        
        // Preview logo when file is selected
        document.querySelector('[name="store_logo_file"]').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.querySelector('.logo-preview');
                    preview.innerHTML = `<img src="${e.target.result}" alt="Logo Preview">`;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>