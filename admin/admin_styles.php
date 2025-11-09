<?php
// File ini berisi CSS yang sama seperti di admin index.php
// Dipisahkan untuk kemudahan maintenance
?>
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

.admin-main {
    max-width: 1400px;
    margin: 0 auto;
    padding: 30px 20px;
}

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

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    color: var(--light);
    font-weight: 600;
}

.form-input {
    width: 100%;
    padding: 12px 15px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    color: var(--light);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-input:focus {
    outline: none;
    border-color: var(--primary);
    background: rgba(15, 204, 206, 0.1);
}

.submit-btn {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(15, 204, 206, 0.4);
}

@media (max-width: 768px) {
    .admin-nav {
        flex-direction: column;
        gap: 10px;
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