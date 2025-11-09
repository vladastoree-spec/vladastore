<?php
require_once 'config.php';
require_once 'functions.php';

$games = getGames();
$testimonials = getTestimonials();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vladastore - Top Up Game Terpercaya</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* CSS dari file sebelumnya - dipertahankan dengan beberapa penyesuaian */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #1a1a2e;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background-color: #16213e;
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px 0;
        }

        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .logo-image {
            height: 45px;
            transition: transform 0.3s ease;
        }

        .logo:hover .logo-image {
            transform: scale(1.05);
        }

        .nav-menu {
            display: flex;
            list-style: none;
        }

        .nav-item {
            margin-left: 30px;
        }

        .nav-link {
            color: #e6e6e6;
            text-decoration: none;
            font-size: 18px;
            font-weight: 500;
            padding: 8px 12px;
            border-radius: 4px;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: #0fccce;
            background-color: rgba(15, 204, 206, 0.1);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background-color: #0fccce;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 80%;
        }

        .burger {
            display: none;
            cursor: pointer;
            flex-direction: column;
            justify-content: space-between;
            width: 30px;
            height: 21px;
        }

        .burger-line {
            height: 3px;
            width: 100%;
            background-color: #e6e6e6;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        /* Main Content Styles */
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px 60px;
            flex: 1;
        }

        .section-title {
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 50px;
            color: #0fccce;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #0fccce, #00a2ff);
            border-radius: 2px;
        }

        /* Games Grid Styles */
        .games-section {
            margin-bottom: 80px;
        }

        .games-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .game-card {
            background: #16213e;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            width: 180px;
            flex-shrink: 0;
            cursor: pointer;
        }

        .game-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
        }

        .game-image {
            width: 100%;
            height: 0;
            padding-bottom: 100%;
            position: relative;
            overflow: hidden;
        }

        .game-image img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .game-card:hover .game-image img {
            transform: scale(1.05);
        }

        .game-info {
            padding: 15px;
        }

        .game-title {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #fff;
            line-height: 1.3;
        }

        .game-description {
            color: #b0b0b0;
            line-height: 1.4;
            font-size: 0.8rem;
        }

        /* Testimonials Section */
        .testimonials-section {
            margin-bottom: 80px;
        }

        .testimonials-slider {
            position: relative;
            max-width: 320px;
            margin: 0 auto;
            overflow: hidden;
            border-radius: 16px;
        }

        .testimonials-track {
            display: flex;
            transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .testimonial-slide {
            min-width: 100%;
            padding: 0 15px;
            display: flex;
            justify-content: center;
        }

        .testimonial-image {
            width: 100%;
            max-width: 200px;
            height: 0;
            padding-bottom: 150%;
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
            margin: 0 auto;
            transition: transform 0.3s ease;
        }

        .testimonial-image:hover {
            transform: scale(1.02);
        }

        .testimonial-image img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .testimonial-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-top: 30px;
        }

        .testimonial-nav-btn {
            background: rgba(15, 204, 206, 0.2);
            color: #0fccce;
            border: 2px solid #0fccce;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 18px;
            font-weight: bold;
        }

        .testimonial-nav-btn:hover {
            background: #0fccce;
            color: #16213e;
            transform: scale(1.1);
        }

        .testimonial-nav-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
            transform: none;
        }

        .testimonial-nav-btn:disabled:hover {
            background: rgba(15, 204, 206, 0.2);
            color: #0fccce;
        }

        .testimonial-indicator {
            color: #b0b0b0;
            font-size: 0.9rem;
            font-weight: 500;
            min-width: 80px;
            text-align: center;
        }

        /* Order Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .order-modal {
            background: #16213e;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }

        .modal-overlay.active .order-modal {
            transform: translateY(0);
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.5rem;
            color: #0fccce;
            font-weight: 700;
        }

        .close-modal {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close-modal:hover {
            color: #0fccce;
        }

        .modal-body {
            padding: 20px;
        }

        .order-steps {
            background: rgba(15, 204, 206, 0.1);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .order-steps h3 {
            color: #0fccce;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .order-steps ol {
            padding-left: 20px;
            color: #b0b0b0;
            line-height: 1.6;
        }

        .order-steps li {
            margin-bottom: 8px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #fff;
            font-weight: 600;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #0fccce;
            background: rgba(15, 204, 206, 0.1);
        }

        .form-input::placeholder {
            color: #b0b0b0;
        }

        .price-display {
            background: rgba(15, 204, 206, 0.1);
            border-radius: 6px;
            padding: 12px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .price-text {
            color: #fff;
            font-weight: 600;
        }

        .price-value {
            color: #0fccce;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .copy-price {
            background: rgba(15, 204, 206, 0.2);
            color: #0fccce;
            border: 1px solid #0fccce;
            border-radius: 4px;
            padding: 5px 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.8rem;
        }

        .copy-price:hover {
            background: #0fccce;
            color: #16213e;
        }

        .qr-section {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }

        .qr-title {
            color: #0fccce;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .qr-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input {
            position: absolute;
            left: -9999px;
        }

        .file-input-label {
            display: block;
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            color: #b0b0b0;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-input-label:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: #0fccce;
        }

        .submit-order {
            width: 100%;
            background: linear-gradient(135deg, #0fccce, #00a2ff);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .submit-order:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(15, 204, 206, 0.4);
        }

        /* Processing Modal */
        .processing-modal {
            background: #16213e;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            max-width: 400px;
            width: 90%;
        }

        .processing-icon {
            font-size: 3rem;
            color: #0fccce;
            margin-bottom: 20px;
        }

        .processing-text {
            font-size: 1.2rem;
            color: #fff;
            margin-bottom: 10px;
        }

        .processing-subtext {
            color: #b0b0b0;
            font-size: 0.9rem;
        }

        /* Status Check Modal */
        .status-result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
        }

        .status-item {
            padding: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .status-item:last-child {
            border-bottom: none;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .status-pending {
            background: #f39c12;
            color: #fff;
        }

        .status-proses {
            background: #3498db;
            color: #fff;
        }

        .status-sukses {
            background: #27ae60;
            color: #fff;
        }

        /* Responsive Styles */
        @media screen and (max-width: 768px) {
            .burger {
                display: flex;
            }

            .nav-menu {
                position: fixed;
                top: 70px;
                right: -100%;
                flex-direction: column;
                background-color: #16213e;
                width: 70%;
                height: calc(100vh - 70px);
                text-align: center;
                transition: all 0.5s ease;
                box-shadow: -5px 0 10px rgba(0, 0, 0, 0.2);
                padding-top: 30px;
            }

            .nav-menu.active {
                right: 0;
            }

            .nav-item {
                margin: 15px 0;
            }

            .nav-link {
                font-size: 20px;
                display: block;
                padding: 15px;
            }

            .logo-image {
                height: 40px;
            }

            .section-title {
                font-size: 2rem;
            }

            .games-container {
                gap: 15px;
                max-width: 90%;
            }

            .game-card {
                width: 150px;
            }

            .game-info {
                padding: 12px;
            }

            .game-title {
                font-size: 0.9rem;
            }

            .game-description {
                font-size: 0.75rem;
            }

            .testimonials-slider {
                max-width: 280px;
            }

            .testimonial-image {
                max-width: 180px;
            }

            .testimonial-nav-btn {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }

            .burger.active .burger-line:nth-child(1) {
                transform: rotate(45deg) translate(5px, 5px);
            }

            .burger.active .burger-line:nth-child(2) {
                opacity: 0;
            }

            .burger.active .burger-line:nth-child(3) {
                transform: rotate(-45deg) translate(7px, -6px);
            }

            .order-modal {
                width: 95%;
                margin: 10px;
            }
        }

        @media screen and (max-width: 480px) {
            .games-container {
                gap: 12px;
                max-width: 100%;
            }

            .game-card {
                width: 140px;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .testimonials-slider {
                max-width: 240px;
            }

            .testimonial-image {
                max-width: 160px;
            }

            .game-info {
                padding: 10px;
            }

            .game-title {
                font-size: 0.85rem;
            }

            .testimonial-controls {
                gap: 15px;
            }

            .testimonial-nav-btn {
                width: 35px;
                height: 35px;
                font-size: 14px;
            }

            .testimonial-indicator {
                font-size: 0.8rem;
                min-width: 70px;
            }

            .logo-image {
                height: 35px;
            }

            .modal-title {
                font-size: 1.3rem;
            }
        }

        @media screen and (max-width: 360px) {
            .games-container {
                gap: 10px;
            }
            
            .game-card {
                width: 130px;
            }
            
            .testimonials-slider {
                max-width: 200px;
            }
            
            .testimonial-image {
                max-width: 140px;
            }

            .logo-image {
                height: 32px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="#" class="logo">
                <?php
                $logo = getSetting('store_logo');
                if ($logo && file_exists($logo)): ?>
                    <img src="<?= $logo ?>" alt="Vladastore Logo" class="logo-image">
                <?php else: ?>
                    <div class="logo-fallback">
                        <div class="logo-icon">V</div>
                        <div class="logo-fallback-text">Vladastore</div>
                    </div>
                <?php endif; ?>
            </a>

            <nav>
                <ul class="nav-menu">
                    <li class="nav-item"><a href="#" class="nav-link">Beranda</a></li>
                    <li class="nav-item"><a href="#gamesSection" class="nav-link">Game</a></li>
                    <li class="nav-item"><a href="#testimonialsSection" class="nav-link">Testimoni</a></li>
                    <li class="nav-item"><a href="#" class="nav-link" id="checkStatusBtn">Cek Status</a></li>
                </ul>
            </nav>

            <div class="burger">
                <div class="burger-line"></div>
                <div class="burger-line"></div>
                <div class="burger-line"></div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <!-- Games Section -->
        <section class="games-section" id="gamesSection">
            <h2 class="section-title">Pilih Game untuk Top Up</h2>
            <div class="games-container">
                <?php foreach ($games as $game): ?>
                    <div class="game-card" data-game="<?= $game['id'] ?>">
                        <div class="game-image">
                            <img src="<?= $game['image_url'] ?: 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80' ?>" 
                                 alt="<?= htmlspecialchars($game['name']) ?>">
                        </div>
                        <div class="game-info">
                            <h3 class="game-title"><?= htmlspecialchars($game['name']) ?></h3>
                            <p class="game-description"><?= htmlspecialchars($game['platform']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="testimonials-section" id="testimonialsSection">
            <h2 class="section-title">Testimoni Pelanggan</h2>
            <div class="testimonials-slider">
                <div class="testimonials-track" id="testimonialsTrack">
                    <?php foreach ($testimonials as $testimonial): ?>
                        <div class="testimonial-slide">
                            <div class="testimonial-image">
                                <img src="<?= $testimonial['image_url'] ?: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80' ?>" 
                                     alt="<?= htmlspecialchars($testimonial['name']) ?>">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="testimonial-controls">
                <button class="testimonial-nav-btn" id="testimonialPrevBtn" disabled>&#10094;</button>
                <div class="testimonial-indicator" id="testimonialIndicator">1 / <?= count($testimonials) ?></div>
                <button class="testimonial-nav-btn" id="testimonialNextBtn">&#10095;</button>
            </div>
        </section>
    </main>

    <!-- Order Modal -->
    <div class="modal-overlay" id="orderModal">
        <div class="order-modal">
            <div class="modal-header">
                <h2 class="modal-title">Form Pemesanan</h2>
                <button class="close-modal" id="closeModal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="order-steps">
                    <h3>Cara Order:</h3>
                    <ol>
                        <li>Pastikan produk yang dipilih benar.</li>
                        <li>Isi data game yang diminta.</li>
                        <li>Isi nomor WhatsApp aktif untuk menerima status order.</li>
                        <li>Lakukan pembayaran ke QR ALL PAYMENT yang sudah disiapkan.</li>
                        <li>Selanjutnya jika pembayaran berhasil, masukkan bukti pembayaran.</li>
                        <li>Tekan tombol order.</li>
                        <li>Tunggu 1-5 menit sampai ada konfirmasi dari WhatsApp.</li>
                    </ol>
                </div>

                <form id="orderForm">
                    <input type="hidden" id="orderGameId">
                    <input type="hidden" id="orderProductId">
                    
                    <div class="form-group">
                        <label class="form-label" for="product">Produk:</label>
                        <input type="text" id="product" class="form-input" readonly>
                    </div>

                    <div id="customFormInputs">
                        <!-- Dynamic form inputs will be loaded here -->
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="whatsapp">Nomor WhatsApp:</label>
                        <input type="tel" id="whatsapp" class="form-input" placeholder="Contoh: 081234567890" required>
                    </div>

                    <div class="price-display">
                        <span class="price-text">Harga Total:</span>
                        <span class="price-value" id="totalPrice">Rp 0</span>
                        <button type="button" class="copy-price" id="copyPrice">Salin</button>
                    </div>

                    <div class="qr-section">
                        <h3 class="qr-title">QR ALL PAYMENT</h3>
                        <img src="https://g.top4top.io/p_3591vl47o0.jpg" alt="QR Code Payment" class="qr-image">
                        <p>Scan QR di atas untuk melakukan pembayaran</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kirim file bukti pembayaran:</label>
                        <div class="file-input-wrapper">
                            <input type="file" id="paymentProof" class="file-input" accept="image/*,.pdf" required>
                            <label for="paymentProof" class="file-input-label" id="fileInputLabel">Pilih File Bukti Pembayaran</label>
                        </div>
                    </div>

                    <button type="submit" class="submit-order">ORDER SEKARANG</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Processing Modal -->
    <div class="modal-overlay" id="processingModal">
        <div class="processing-modal">
            <div class="processing-icon">⏳</div>
            <div class="processing-text">Orderan Sedang Diproses</div>
            <div class="processing-subtext">Silakan tunggu, orderan Anda sedang kami proses...</div>
        </div>
    </div>

    <!-- Status Check Modal -->
    <div class="modal-overlay" id="statusModal">
        <div class="order-modal">
            <div class="modal-header">
                <h2 class="modal-title">Cek Status Order</h2>
                <button class="close-modal" id="closeStatusModal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    <div class="form-group">
                        <label class="form-label" for="statusWhatsapp">Nomor WhatsApp:</label>
                        <input type="tel" id="statusWhatsapp" class="form-input" placeholder="Masukkan nomor WhatsApp yang digunakan saat order" required>
                    </div>
                    <button type="submit" class="submit-order">Cek Status</button>
                </form>
                <div id="statusResult" class="status-result"></div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3 class="footer-title">Tentang Vladastore</h3>
                <div class="footer-about">
                    <p>Vladastore adalah platform terpercaya untuk top up game online. Kami menyediakan berbagai produk digital dengan proses cepat dan aman.</p>
                    <p>Dengan pengalaman lebih dari 5 tahun, kami telah melayani ribuan pelanggan dengan kepuasan terjamin.</p>
                </div>
            </div>
            <div class="footer-section">
                <h3 class="footer-title">Link Cepat</h3>
                <ul class="footer-links">
                    <li><a href="#">Beranda</a></li>
                    <li><a href="#gamesSection">Game</a></li>
                    <li><a href="#testimonialsSection">Testimoni</a></li>
                    <li><a href="#" id="footerStatusBtn">Cek Status</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3 class="footer-title">Kontak Kami</h3>
                <ul class="contact-info">
                    <li>
                        <i>✉</i>
                        <span>Email: <?= getSetting('contact_email') ?></span>
                    </li>
                    <li>
                        <i>📞</i>
                        <span>Telepon: <?= getSetting('contact_phone') ?></span>
                    </li>
                    <li>
                        <i>💬</i>
                        <span>WhatsApp: <?= getSetting('contact_whatsapp') ?></span>
                    </li>
                    <li>
                        <i>🕒</i>
                        <span>Jam Operasional: 08.00 - 22.00 WIB</span>
                    </li>
                </ul>
            </div>
            <div class="footer-section">
                <h3 class="footer-title">Metode Pembayaran</h3>
                <div class="payment-methods">
                    <div class="payment-method">
                        <img src="assets/images/payment-bca.png" alt="BCA" class="payment-logo" onerror="this.style.display='none'">
                    </div>
                    <div class="payment-method">
                        <img src="assets/images/payment-bni.png" alt="BNI" class="payment-logo" onerror="this.style.display='none'">
                    </div>
                    <div class="payment-method">
                        <img src="assets/images/payment-bri.png" alt="BRI" class="payment-logo" onerror="this.style.display='none'">
                    </div>
                    <div class="payment-method">
                        <img src="assets/images/payment-mandiri.png" alt="Mandiri" class="payment-logo" onerror="this.style.display='none'">
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <div class="copyright">
                    &copy; 2023 Vladastore. Semua Hak Dilindungi.
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Game Data from PHP
        const gameData = <?= json_encode($games) ?>;

        // DOM Elements
        const orderModal = document.getElementById('orderModal');
        const processingModal = document.getElementById('processingModal');
        const statusModal = document.getElementById('statusModal');
        const closeModal = document.getElementById('closeModal');
        const closeStatusModal = document.getElementById('closeStatusModal');
        const orderForm = document.getElementById('orderForm');
        const statusForm = document.getElementById('statusForm');
        const productInput = document.getElementById('product');
        const totalPriceElement = document.getElementById('totalPrice');
        const copyPriceButton = document.getElementById('copyPrice');
        const paymentProofInput = document.getElementById('paymentProof');
        const fileInputLabel = document.getElementById('fileInputLabel');
        const customFormInputs = document.getElementById('customFormInputs');
        const statusResult = document.getElementById('statusResult');
        const checkStatusBtn = document.getElementById('checkStatusBtn');
        const footerStatusBtn = document.getElementById('footerStatusBtn');

        // Current selected product
        let currentProduct = null;

        // Game Cards Event Listeners
        document.querySelectorAll('.game-card').forEach(card => {
            card.addEventListener('click', () => {
                const gameId = card.getAttribute('data-game');
                loadGameProducts(gameId);
            });
        });

        // Load game products and form
        function loadGameProducts(gameId) {
            fetch(`api/get_game_products.php?game_id=${gameId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showProductSelection(data.game, data.products, data.formFields);
                    } else {
                        alert('Error loading game data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading game data');
                });
        }

        // Show product selection
        function showProductSelection(game, products, formFields) {
            // Create product selection modal
            const productModal = document.createElement('div');
            productModal.className = 'modal-overlay active';
            productModal.innerHTML = `
                <div class="order-modal">
                    <div class="modal-header">
                        <h2 class="modal-title">Pilih Produk ${game.name}</h2>
                        <button class="close-modal" onclick="this.closest('.modal-overlay').remove()">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="topup-items">
                            ${products.map(product => `
                                <div class="topup-item">
                                    <div class="topup-details">
                                        <div class="topup-name">${product.name}</div>
                                        <div class="topup-description">${product.description}</div>
                                    </div>
                                    <div class="topup-price">${product.price}</div>
                                    <button class="topup-action" onclick="openOrderModal(${game.id}, ${product.id}, '${game.name}', '${product.name}', '${product.price}', ${JSON.stringify(formFields).replace(/'/g, "&#39;")})">Beli</button>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(productModal);
        }

        // Open Order Modal
        function openOrderModal(gameId, productId, gameName, productName, price, formFields) {
            // Remove any existing product modals
            document.querySelectorAll('.modal-overlay').forEach(modal => {
                if (modal !== orderModal && modal !== processingModal && modal !== statusModal) {
                    modal.remove();
                }
            });

            currentProduct = {
                gameId: gameId,
                productId: productId,
                game: gameName,
                item: productName,
                price: price
            };

            // Fill form data
            productInput.value = `${gameName} - ${productName}`;
            totalPriceElement.textContent = price;

            // Generate custom form inputs
            customFormInputs.innerHTML = '';
            formFields.forEach(field => {
                const inputGroup = document.createElement('div');
                inputGroup.className = 'form-group';
                inputGroup.innerHTML = `
                    <label class="form-label" for="${field.field_label}">${field.field_label}</label>
                    <input type="${field.field_type}" 
                           id="${field.field_label}" 
                           name="${field.field_label}" 
                           class="form-input" 
                           placeholder="${field.placeholder || `Masukkan ${field.field_label}`}"
                           ${field.is_required ? 'required' : ''}>
                `;
                customFormInputs.appendChild(inputGroup);
            });

            // Show modal
            orderModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        // Close Order Modal
        function closeOrderModal() {
            orderModal.classList.remove('active');
            document.body.style.overflow = 'auto';
            orderForm.reset();
            fileInputLabel.textContent = 'Pilih File Bukti Pembayaran';
            currentProduct = null;
        }

        // Copy Price to Clipboard
        copyPriceButton.addEventListener('click', function() {
            const price = totalPriceElement.textContent;
            navigator.clipboard.writeText(price).then(() => {
                const originalText = copyPriceButton.textContent;
                copyPriceButton.textContent = 'Tersalin!';
                setTimeout(() => {
                    copyPriceButton.textContent = originalText;
                }, 2000);
            });
        });

        // Handle File Input Change
        paymentProofInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                fileInputLabel.textContent = file.name;
            } else {
                fileInputLabel.textContent = 'Pilih File Bukti Pembayaran';
            }
        });

        // Handle Order Form Submission
        orderForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            if (!currentProduct) {
                alert('Produk tidak valid');
                return;
            }

            // Collect form data
            const formData = new FormData();
            formData.append('game_id', currentProduct.gameId);
            formData.append('product_id', currentProduct.productId);
            formData.append('whatsapp', document.getElementById('whatsapp').value);
            
            // Collect custom form data
            const customData = {};
            const customInputs = customFormInputs.querySelectorAll('input, select, textarea');
            customInputs.forEach(input => {
                customData[input.name] = input.value;
            });
            formData.append('custom_data', JSON.stringify(customData));
            
            // Add payment proof file
            const paymentProof = document.getElementById('paymentProof').files[0];
            if (paymentProof) {
                formData.append('payment_proof', paymentProof);
            }

            // Show processing modal
            orderModal.classList.remove('active');
            processingModal.classList.add('active');

            // Send order data
            fetch('api/process_order.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                processingModal.classList.remove('active');
                if (data.success) {
                    alert('Order berhasil dikirim! Kami akan menghubungi Anda via WhatsApp.');
                    closeOrderModal();
                } else {
                    alert('Error: ' + data.message);
                    orderModal.classList.add('active');
                }
            })
            .catch(error => {
                processingModal.classList.remove('active');
                alert('Error mengirim order: ' + error.message);
                orderModal.classList.add('active');
            });
        });

        // Status Check functionality
        function openStatusModal() {
            statusModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeStatusModal() {
            statusModal.classList.remove('active');
            document.body.style.overflow = 'auto';
            statusForm.reset();
            statusResult.innerHTML = '';
        }

        checkStatusBtn.addEventListener('click', openStatusModal);
        footerStatusBtn.addEventListener('click', openStatusModal);

        statusForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const whatsapp = document.getElementById('statusWhatsapp').value;
            
            fetch(`api/get_order_status.php?whatsapp=${encodeURIComponent(whatsapp)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.orders.length > 0) {
                        let html = '<h4>Riwayat Order:</h4>';
                        data.orders.forEach(order => {
                            const statusClass = `status-${order.status}`;
                            html += `
                                <div class="status-item">
                                    <strong>${order.game_name} - ${order.product_name}</strong><br>
                                    <span class="status-badge ${statusClass}">${order.status.toUpperCase()}</span><br>
                                    <small>Harga: ${order.price}</small><br>
                                    <small>Tanggal: ${new Date(order.created_at).toLocaleString()}</small>
                                </div>
                            `;
                        });
                        statusResult.innerHTML = html;
                    } else {
                        statusResult.innerHTML = '<p>Tidak ada order ditemukan untuk nomor WhatsApp tersebut.</p>';
                    }
                })
                .catch(error => {
                    statusResult.innerHTML = '<p>Error memuat status order.</p>';
                });
        });

        // Modal Event Listeners
        closeModal.addEventListener('click', closeOrderModal);
        closeStatusModal.addEventListener('click', closeStatusModal);

        orderModal.addEventListener('click', (e) => {
            if (e.target === orderModal) closeOrderModal();
        });

        statusModal.addEventListener('click', (e) => {
            if (e.target === statusModal) closeStatusModal();
        });

        processingModal.addEventListener('click', (e) => {
            if (e.target === processingModal) {
                processingModal.classList.remove('active');
                orderModal.classList.add('active');
            }
        });

        // Mobile Navigation Toggle
        const burger = document.querySelector('.burger');
        const navMenu = document.querySelector('.nav-menu');

        burger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            burger.classList.toggle('active');
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                burger.classList.remove('active');
            });
        });

        // Testimonials Slider
        const testimonialsTrack = document.getElementById('testimonialsTrack');
        const testimonialPrevBtn = document.getElementById('testimonialPrevBtn');
        const testimonialNextBtn = document.getElementById('testimonialNextBtn');
        const testimonialIndicator = document.getElementById('testimonialIndicator');
        let currentTestimonial = 0;
        const totalTestimonials = document.querySelectorAll('.testimonial-slide').length;

        function updateTestimonialSlider() {
            testimonialsTrack.style.transform = `translateX(-${currentTestimonial * 100}%)`;
            testimonialIndicator.textContent = `${currentTestimonial + 1} / ${totalTestimonials}`;
            
            testimonialPrevBtn.disabled = currentTestimonial === 0;
            testimonialNextBtn.disabled = currentTestimonial === totalTestimonials - 1;
        }

        testimonialPrevBtn.addEventListener('click', () => {
            if (currentTestimonial > 0) {
                currentTestimonial--;
                updateTestimonialSlider();
            }
        });

        testimonialNextBtn.addEventListener('click', () => {
            if (currentTestimonial < totalTestimonials - 1) {
                currentTestimonial++;
                updateTestimonialSlider();
            }
        });

        // Initialize testimonial slider
        updateTestimonialSlider();
    </script>
</body>
</html>