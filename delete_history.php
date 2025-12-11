<?php
// delete_history.php - Hapus semua riwayat pengujian
require_once __DIR__ . '/koneksi.php';

$message = '';
$type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] === 'yes') {
        // Hapus semua data dari tabel hasil_running
        if ($conn->query('TRUNCATE TABLE hasil_running')) {
            $message = '✅ Semua riwayat pengujian berhasil dihapus!';
            $type = 'success';
        } else {
            $message = '❌ Gagal menghapus riwayat: ' . $conn->error;
            $type = 'error';
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hapus Riwayat Pengujian</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container { 
            max-width: 600px; 
            background: white; 
            border-radius: 10px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        header h1 { font-size: 1.8em; margin-bottom: 5px; }
        
        .content { padding: 40px; }
        
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .warning-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }

        .warning-box h3 {
            color: #856404;
            margin-bottom: 15px;
            font-size: 1.3em;
        }

        .warning-box p {
            color: #856404;
            margin-bottom: 10px;
            font-size: 1.05em;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .button-group button,
        .button-group a {
            flex: 1;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
        }

        .btn-cancel:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
        }

        .info-text {
            background: #e7f3fe;
            border-left: 5px solid #2196F3;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            color: #1976D2;
        }

        footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🗑️ Hapus Riwayat Pengujian</h1>
            <p>Bersihkan semua data hasil pengujian</p>
        </header>

        <div class="content">
            <?php if ($message): ?>
                <div class="message <?= $type ?>">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <?php if (!$message || $type === 'error'): ?>
                <div class="warning-box">
                    <h3>⚠️ Perhatian!</h3>
                    <p>Anda akan menghapus <strong>SEMUA</strong> data riwayat pengujian yang telah disimpan.</p>
                    <p>Tindakan ini tidak dapat dibatalkan!</p>
                </div>

                <div class="info-text">
                    <strong>💡 Informasi:</strong> Data nilai mahasiswa (dataset_nilai) tidak akan dihapus, hanya riwayat hasil pengujian (hasil_running) yang akan dihapus.
                </div>

                <form method="post">
                    <div class="button-group">
                        <button type="submit" class="btn-danger" onclick="return confirm('Anda yakin ingin menghapus SEMUA riwayat pengujian? Tindakan ini tidak dapat dibatalkan!');" name="confirm_delete" value="yes">
                            🗑️ Hapus Semua Riwayat
                        </button>
                        <a href="index.php" class="btn-cancel">❌ Batal</a>
                    </div>
                </form>
            <?php else: ?>
                <div style="text-align: center; margin-top: 30px;">
                    <p style="color: #666; margin-bottom: 20px;">Riwayat pengujian telah berhasil dibersihkan.</p>
                    <a href="index.php" class="btn-cancel" style="display: inline-block;">
                        🏠 Kembali ke Home
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <footer>
            <p>Analisis Efisiensi Algoritma Pengurutan | Desember 2025</p>
        </footer>
    </div>
</body>
</html>
