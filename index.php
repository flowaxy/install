<?php
/**
 * Flowaxy CMS Installer
 * Окремий інсталятор, який може бути повністю видалений після установки.
 *
 * Взаємодіє з ядром через ENGINE_DIR, не вимагає завантаження всього рушія.
 */

declare(strict_types=1);

// Визначаємо корінь проєкту та директорію движка, якщо ще не визначені
if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', dirname(__DIR__));
}
if (!defined('ENGINE_DIR')) {
    define('ENGINE_DIR', ROOT_DIR . '/engine');
}

// Проста перевірка: якщо папка install не існує — система вже встановлена
// НО: дозволяємо доступ до action=finish_install, оскільки це фінальний крок, який видалить інсталятор
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$installDir = ROOT_DIR . '/install';
$installedFlagFile = ROOT_DIR . '/storage/config/installed.flag';
// Якщо папка install не існує або є файл-маркер встановлення - система встановлена
if ((!is_dir($installDir) || file_exists($installedFlagFile)) && $action !== 'finish_install') {
    http_response_code(403);
    ?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flowaxy CMS вже встановлена</title>
    <link rel="icon" type="image/png" href="/install/assets/images/brand/favicon.png">
    <link rel="stylesheet" href="/install/assets/styles/installer.css?v=2">
    <style>
        .already-installed-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .already-installed-box {
            background: #ffffff;
            border-radius: 14px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 12px 40px rgba(15, 23, 42, 0.18);
            overflow: hidden;
        }
        
        .already-installed-header {
            background: #020617;
            padding: 20px 24px;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(15, 23, 42, 0.8);
        }
        
        .already-installed-header .logo-container {
            margin: 0;
            display: flex;
            align-items: center;
        }
        
        .already-installed-header .logo-container img {
            height: 32px;
            width: auto;
        }
        
        .already-installed-content {
            padding: 40px;
            text-align: center;
        }
        
        .already-installed-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }
        
        .already-installed-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 16px;
            letter-spacing: -0.01em;
        }
        
        .already-installed-message {
            font-size: 14px;
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        
        .already-installed-message code {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            padding: 2px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #2d3748;
        }
        
        .already-installed-warning {
            background: #fffaf0;
            border: 1px solid #feebc8;
            border-left: 4px solid #ed8936;
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            text-align: left;
        }
        
        .already-installed-warning-title {
            font-size: 14px;
            font-weight: 600;
            color: #c05621;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .already-installed-warning-text {
            font-size: 13px;
            color: #744210;
            line-height: 1.5;
        }
        
        .already-installed-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .already-installed-actions .btn {
            min-width: 140px;
        }
        
        @media (max-width: 640px) {
            .already-installed-content {
                padding: 30px 20px;
            }
            
            .already-installed-title {
                font-size: 20px;
            }
            
            .already-installed-actions {
                flex-direction: column;
            }
            
            .already-installed-actions .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="already-installed-container">
        <div class="already-installed-box">
            <div class="already-installed-header">
                <div class="logo-container">
                    <img src="/install/assets/images/brand/logo-white.png" alt="Flowaxy CMS">
                </div>
            </div>
            <div class="already-installed-content">
                <div class="already-installed-icon">⚠️</div>
                <h1 class="already-installed-title">Flowaxy CMS вже встановлена</h1>
                <p class="already-installed-message">
                    Система виявлена, що Flowaxy CMS вже встановлена на цьому сервері.
                </p>
                <div class="already-installed-warning">
                    <div class="already-installed-warning-title">
                        <span>⚠️</span>
                        <span>Для повторної інсталяції:</span>
                    </div>
                    <div class="already-installed-warning-text">
                        Видаліть файл <code>storage/config/installed.flag</code> та всі відповідні таблиці з бази даних перед початком нової інсталяції.
                    </div>
                </div>
                <div class="already-installed-actions">
                    <a href="/" class="btn btn-primary">Перейти на сайт</a>
                    <a href="/admin" class="btn btn-secondary">Панель адміністратора</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
    <?php
    exit;
}

// Підключаємо контролер інсталятора з ядра install
$installerHandler = __DIR__ . '/core/InstallerController.php';
if (!is_file($installerHandler)) {
    http_response_code(500);
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Installer error</title></head><body>';
    echo '<h1>Installer handler not found</h1>';
    echo '<p>Очікувався файл <code>install/core/InstallerController.php</code>, але він відсутній.</p>';
    echo '</body></html>';
    exit;
}

require_once $installerHandler;
