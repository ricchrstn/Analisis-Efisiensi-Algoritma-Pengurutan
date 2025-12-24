<?php
/**
 * error_handler.php - Error handling utilities
 * 
 * Provides user-friendly error pages and error handling functions.
 */

/**
 * Display error page with user-friendly message
 * 
 * @param string $title Error title
 * @param string $message Error message
 * @return void
 */
function show_error($title, $message) {
    ?>
    <!doctype html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Error - <?= htmlspecialchars($title) ?></title>
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
            .error-container { 
                max-width: 600px; 
                background: white; 
                border-radius: 10px; 
                box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                overflow: hidden;
            }
            .error-header {
                background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
                color: white;
                padding: 30px;
                text-align: center;
            }
            .error-header h1 { font-size: 2em; margin-bottom: 10px; }
            .error-content { 
                padding: 40px; 
                text-align: center;
            }
            .error-icon {
                font-size: 4em;
                margin-bottom: 20px;
            }
            .error-message {
                background: #f8d7da;
                border: 2px solid #f5c6cb;
                border-radius: 8px;
                padding: 20px;
                margin: 20px 0;
                color: #721c24;
                line-height: 1.6;
            }
            .error-actions {
                margin-top: 30px;
            }
            .btn {
                display: inline-block;
                padding: 12px 24px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                text-decoration: none;
                border-radius: 5px;
                font-weight: bold;
                transition: all 0.3s;
                margin: 5px;
            }
            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-header">
                <h1>⚠️ <?= htmlspecialchars($title) ?></h1>
            </div>
            <div class="error-content">
                <div class="error-icon">❌</div>
                <div class="error-message">
                    <?= nl2br(htmlspecialchars($message)) ?>
                </div>
                <div class="error-actions">
                    <a href="index.php" class="btn">🏠 Kembali ke Home</a>
                    <a href="javascript:history.back()" class="btn">← Kembali</a>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?>

