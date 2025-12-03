<?php
/**
 * Обробник установщика
 * 
 * @package Engine\Includes
 */

declare(strict_types=1);

// Проста функція logger() для інсталятора (визначається ДО завантаження будь-яких класів ядра)
// Це необхідно, оскільки деякі класи ядра можуть викликати logger() під час створення таблиць
if (!function_exists('logger')) {
    // Підключаємо інтерфейс LoggerInterface, якщо він доступний
    if (defined('ENGINE_DIR')) {
        $loggerInterfaceFile = ENGINE_DIR . '/core/contracts/LoggerInterface.php';
        if (file_exists($loggerInterfaceFile)) {
            require_once $loggerInterfaceFile;
        }
    }
    
    // Створюємо простий клас-заглушку, який реалізує LoggerInterface
    if (interface_exists('LoggerInterface')) {
        class InstallerLoggerStub implements LoggerInterface
        {
            public function log(int $level, string $message, array $context = []): void
            {
                $levelName = match($level) {
                    LoggerInterface::LEVEL_DEBUG => 'DEBUG',
                    LoggerInterface::LEVEL_INFO => 'INFO',
                    LoggerInterface::LEVEL_WARNING => 'WARNING',
                    LoggerInterface::LEVEL_ERROR => 'ERROR',
                    LoggerInterface::LEVEL_CRITICAL => 'CRITICAL',
                    default => 'UNKNOWN'
                };
                error_log("[Installer {$levelName}] {$message}" . (empty($context) ? '' : ' ' . json_encode($context, JSON_UNESCAPED_UNICODE)));
            }
            
            public function logDebug(string $message, array $context = []): void
            {
                // В режимі інсталяції debug не логуємо
            }
            
            public function logInfo(string $message, array $context = []): void
            {
                error_log('[Installer Info] ' . $message . (empty($context) ? '' : ' ' . json_encode($context, JSON_UNESCAPED_UNICODE)));
            }
            
            public function logWarning(string $message, array $context = []): void
            {
                error_log('[Installer Warning] ' . $message . (empty($context) ? '' : ' ' . json_encode($context, JSON_UNESCAPED_UNICODE)));
            }
            
            public function logError(string $message, array $context = []): void
            {
                error_log('[Installer Error] ' . $message . (empty($context) ? '' : ' ' . json_encode($context, JSON_UNESCAPED_UNICODE)));
            }
            
            public function logCritical(string $message, array $context = []): void
            {
                error_log('[Installer Critical] ' . $message . (empty($context) ? '' : ' ' . json_encode($context, JSON_UNESCAPED_UNICODE)));
            }
            
            public function logException(\Throwable $exception, array $context = []): void
            {
                error_log('[Installer Exception] ' . $exception->getMessage() . ' in ' . $exception->getFile() . ':' . $exception->getLine() . (empty($context) ? '' : ' ' . json_encode($context, JSON_UNESCAPED_UNICODE)));
            }
            
            public function getRecentLogs(int $lines = 100): array
            {
                return [];
            }
            
            public function clearLogs(): bool
            {
                return true;
            }
            
            public function getStats(): array
            {
                return [];
            }
            
            public function reloadSettings(): void
            {
                // Нічого не робимо
            }
            
            public function getSetting(string $key, string $default = ''): string
            {
                return $default;
            }
            
            public function setSetting(string $key, string $value): void
            {
                // Нічого не робимо
            }
        }
    }
    
    function logger(): LoggerInterface {
        // Завжди повертаємо заглушку, яка реалізує LoggerInterface
        static $stub = null;
        if ($stub === null) {
            $stub = new InstallerLoggerStub();
        }
        return $stub;
    }
}

// Підключаємо необхідні класи ядра для роботи з БД (установщик працює окремо від autoloader'а ядра)
if (defined('ENGINE_DIR')) {
    $databaseHelperFile = ENGINE_DIR . '/core/support/helpers/DatabaseHelper.php';
    if (file_exists($databaseHelperFile)) {
        require_once $databaseHelperFile;
    }
}

// Ініціалізація сесії для установщика (важливо для Linux)
if (session_status() === PHP_SESSION_NONE) {
    // Переконуємося, що директорія для сесій існує та доступна для запису
    $sessionSavePath = session_save_path();
    if (empty($sessionSavePath) || !is_writable($sessionSavePath)) {
        // Намагаємося використати директорію storage/sessions
        $customSessionPath = __DIR__ . '/../../storage/sessions';
        if (!is_dir($customSessionPath)) {
            @mkdir($customSessionPath, 0755, true);
        }
        if (is_dir($customSessionPath) && is_writable($customSessionPath)) {
            session_save_path($customSessionPath);
        }
    }
    
    // Ініціалізуємо сесію
    if (!headers_sent()) {
        session_start();
    }
}

// Отримуємо змінні з запиту
// Використовуємо REQUEST, щоб підтримати як GET, так і POST
$step = $_REQUEST['step'] ?? 'welcome';
$action = $_REQUEST['action'] ?? '';
$databaseIniFile = ROOT_DIR . '/storage/config/database.ini';

// Ініціалізуємо змінні для шаблону
$systemChecks = [];
$systemErrors = [];
$systemWarnings = [];
$error = null;

// Блокування доступу до установщика, якщо система вже встановлена
// Виняток: AJAX запити для тестування БД (action=test_db, create_table)
// Це потрібно для перевірки підключення до БД під час установки
$isAjaxAction = ($action === 'test_db' || $action === 'create_table') && $_SERVER['REQUEST_METHOD'] === 'POST';

// Перевіряємо, чи йде процес установки (є налаштування БД в сесії)
        // Використовуємо тільки нативну сесію, інсталер не залежить від sessionManager з ядра
        $isInstallationInProgress = isset($_SESSION['install_db_config']) && is_array($_SESSION['install_db_config']);

// Блокуємо доступ тільки якщо файл створено І процес установки не йде
if (!$isAjaxAction && file_exists($databaseIniFile) && !$isInstallationInProgress) {
    // Система вже встановлена - блокуємо доступ до установщика
    http_response_code(403);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Доступ заборонено - Flowaxy CMS</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 500px;
            text-align: center;
        }
        h1 {
            color: #333;
            margin: 0 0 20px 0;
            font-size: 28px;
        }
        p {
            color: #666;
            margin: 0 0 30px 0;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>⚠️ Доступ заборонено</h1>
        <p>Система вже встановлена. Доступ до сторінки установки блокується з метою безпеки.</p>
        <a href="/" class="btn">Перейти на головну</a>
        <a href="/admin" class="btn" style="margin-left: 10px; background: #764ba2;">Перейти в адмінку</a>
    </div>
</body>
</html>';
    exit;
}

// AJAX: тест БД
// Обробляємо за параметром action=test_db незалежно від методу,
// щоб гарантувати повернення JSON (важливо для fetch/FormData).
if ($action === 'test_db') {
    header('Content-Type: application/json');
    try {
        // Використовуємо $_REQUEST, щоб підтримати і POST, і GET (важливо для fetch)
        $host = $_REQUEST['db_host'] ?? '127.0.0.1';
        $port = (int)($_REQUEST['db_port'] ?? 3306);
        $name = $_REQUEST['db_name'] ?? '';
        $user = $_REQUEST['db_user'] ?? 'root';
        $pass = $_REQUEST['db_pass'] ?? '';
        $version = $_REQUEST['db_version'] ?? '5.7';
        
        // Налаштування підключення залежно від версії MySQL
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 3
        ];
        
        $selectedCharset = $_REQUEST['db_charset'] ?? 'utf8mb4';
        
        // Для MySQL 5.7 використовуємо старий спосіб підключення
        if ($version === '5.7') {
            $dsn = "mysql:host={$host};port={$port};charset={$selectedCharset}";
        } else {
            // Для MySQL 8.4 використовуємо новий спосіб
            $dsn = "mysql:host={$host};port={$port};charset={$selectedCharset}";
        }
        
        $pdo = new PDO($dsn, $user, $pass, $options);
        
        // Перевіряємо версію MySQL
        $versionStmt = $pdo->query("SELECT VERSION()");
        $mysqlVersion = $versionStmt->fetchColumn();
        
        // Перевіряємо кодування бази даних
        $charsetInfo = null;
        $dbCharset = null;
        $dbCollation = null;
        try {
            if (!empty($name)) {
                $charsetStmt = $pdo->prepare("SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?");
                $charsetStmt->execute([$name]);
                $charsetInfo = $charsetStmt->fetch(PDO::FETCH_ASSOC);
                if ($charsetInfo) {
                    $dbCharset = $charsetInfo['DEFAULT_CHARACTER_SET_NAME'] ?? null;
                    $dbCollation = $charsetInfo['DEFAULT_COLLATION_NAME'] ?? null;
                }
            }
        } catch (Exception $e) {
            // Ігноруємо помилку перевірки кодування
        }
        
        // Визначаємо мажорну версію MySQL
        $versionParts = explode('.', $mysqlVersion);
        $majorVersion = (int)($versionParts[0] ?? 0);
        $minorVersion = (int)($versionParts[1] ?? 0);
        
        // Визначаємо, яка версія встановлена (для селекта)
        $detectedVersion = '8.4';
        if ($majorVersion === 5) {
            $detectedVersion = '5.7'; // Для всіх версій 5.x (5.5, 5.6, 5.7) вважаємо як 5.7
        } elseif ($majorVersion >= 8) {
            $detectedVersion = '8.4'; // Для всіх версій 8.x (8.0, 8.1, 8.2, 8.3, 8.4) вважаємо як 8.4
        }
        
        // Перевіряємо відповідність вибраної версії реальній
        $versionMatch = ($version === $detectedVersion);
        $versionWarning = '';
        if (!$versionMatch) {
            // Визначаємо мову для повідомлення (за замовчуванням українська)
            $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'uk';
            $isUkrainian = str_contains($lang, 'uk') || str_contains($lang, 'ru');
            
            if ($isUkrainian) {
                $versionWarning = "Увага: Вибрана версія MySQL {$version}, але на сервері встановлена версія {$mysqlVersion} (визначено як {$detectedVersion}). Версія автоматично оновлена.";
            } else {
                $versionWarning = "Warning: Selected MySQL version {$version}, but server has version {$mysqlVersion} (detected as {$detectedVersion}). Version automatically updated.";
            }
        }
        
        $stmt = $pdo->prepare("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?");
        $stmt->execute([$name]);
        $databaseExists = $stmt->fetch() !== false;
        
        $charsetWarning = '';
        $charsetError = '';
        $charsetMatch = true;
        $connectionSuccess = true;
        
        // Якщо база даних існує, але кодування не було отримано, намагаємося отримати ще раз
        if ($databaseExists && !$dbCharset) {
            try {
                $charsetStmt = $pdo->prepare("SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?");
                $charsetStmt->execute([$name]);
                $charsetInfo = $charsetStmt->fetch(PDO::FETCH_ASSOC);
                if ($charsetInfo) {
                    $dbCharset = $charsetInfo['DEFAULT_CHARACTER_SET_NAME'] ?? null;
                    $dbCollation = $charsetInfo['DEFAULT_COLLATION_NAME'] ?? null;
                }
            } catch (Exception $e) {
                // Ігноруємо помилку
            }
        }
        
        // Якщо база даних не існує, отримуємо кодування сервера за замовчуванням
        if (!$databaseExists && !$dbCharset) {
            try {
                $defaultCharsetStmt = $pdo->query("SELECT @@character_set_server, @@collation_server");
                $defaultCharset = $defaultCharsetStmt->fetch(PDO::FETCH_NUM);
                if ($defaultCharset) {
                    $dbCharset = $defaultCharset[0] ?? null;
                    $dbCollation = $defaultCharset[1] ?? null;
                }
            } catch (Exception $e) {
                // Ігноруємо помилку
            }
        }
        
        // Перевіряємо відповідність вибраного кодування кодуванню БД або сервера
        if ($dbCharset) {
            // Нормалізуємо кодування для порівняння - витягуємо базове кодування
            // Наприклад: utf8mb4_0900_ai_ci -> utf8mb4, utf8mb4_unicode_ci -> utf8mb4
            $normalizedDbCharset = strtolower(preg_replace('/[^a-z0-9]/', '', explode('_', $dbCharset)[0]));
            $normalizedSelectedCharset = strtolower($selectedCharset);
            
            // Перевіряємо збіг базових кодувань
            if ($normalizedDbCharset !== $normalizedSelectedCharset) {
                $charsetMatch = false;
                $connectionSuccess = false; // Блокуємо продовження при невідповідності
                $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'uk';
                $isUkrainian = str_contains($lang, 'uk') || str_contains($lang, 'ru');
                
                if ($databaseExists) {
                    // База існує - це помилка
                    if ($isUkrainian) {
                        $charsetError = "Помилка: Вибрана кодування {$selectedCharset}, але база даних має кодування {$dbCharset} (collation: {$dbCollation}). Кодування повинні співпадати! Змініть кодування бази даних або виберіть правильну кодування.";
                    } else {
                        $charsetError = "Error: Selected charset {$selectedCharset}, but database has charset {$dbCharset} (collation: {$dbCollation}). Charsets must match! Change database charset or select correct charset.";
                    }
                } else {
                    // База не існує - попередження про кодування сервера
                    if ($isUkrainian) {
                        $charsetWarning = "Увага: Сервер MySQL має кодування за замовчуванням {$dbCharset} (collation: {$dbCollation}). При створенні бази даних буде використано кодування сервера. Рекомендується створити базу з кодуванням utf8mb4: CREATE DATABASE `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
                    } else {
                        $charsetWarning = "Warning: MySQL server default charset is {$dbCharset} (collation: {$dbCollation}). Database will be created with server charset. Recommended to create database with utf8mb4: CREATE DATABASE `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
                    }
                    // Для неіснуючої бази не блокуємо, тільки попереджаємо
                    $connectionSuccess = true;
                }
            } else {
                // Кодування співпадають, але можуть відрізнятися collation - показуємо інформацію
                if ($dbCollation && !str_contains(strtolower($dbCollation), strtolower($selectedCharset))) {
                    $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'uk';
                    $isUkrainian = str_contains($lang, 'uk') || str_contains($lang, 'ru');
                    
                    if ($isUkrainian) {
                        $charsetWarning = "Кодування співпадає ({$dbCharset}), але collation відрізняється: {$dbCollation}. Рекомендується utf8mb4_unicode_ci.";
                    } else {
                        $charsetWarning = "Charset matches ({$dbCharset}), but collation differs: {$dbCollation}. utf8mb4_unicode_ci is recommended.";
                    }
                }
            }
        } elseif (!$dbCharset && $selectedCharset !== 'utf8mb4') {
            $charsetWarning = "Рекомендується використовувати utf8mb4 для підтримки emoji та всіх Unicode символів.";
        }
        
        echo json_encode([
            'success' => $connectionSuccess, 
            'database_exists' => $databaseExists,
            'mysql_version' => $mysqlVersion,
            'detected_version' => $detectedVersion,
            'selected_version' => $version,
            'version_match' => $versionMatch,
            'version_warning' => $versionWarning,
            'db_charset' => $dbCharset,
            'db_collation' => $dbCollation,
            'selected_charset' => $selectedCharset,
            'charset_match' => $charsetMatch,
            'charset_warning' => $charsetWarning,
            'charset_error' => $charsetError,
            'message' => $charsetError ?: ($charsetWarning ?: 'Підключення успішне!')
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// AJAX: створення таблиці
// Обробляємо по параметру action=create_table незалежно від методу,
// щоб гарантувати JSON-відповідь навіть у нестандартних оточеннях.
if ($action === 'create_table') {
    // Очищаємо буфер виводу перед відправкою JSON
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    // Встановлюємо заголовки
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=UTF-8');
    }
    
    // Вимікаємо вивід помилок на екран (але логуємо їх)
    $oldErrorReporting = error_reporting(E_ALL);
    $oldDisplayErrors = ini_get('display_errors');
    ini_set('display_errors', '0');
    
    // Реєструємо обробник помилок для гарантії JSON відповіді
    register_shutdown_function(function() {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            // Якщо була фатальна помилка, відправляємо JSON з помилкою
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            if (!headers_sent()) {
                header('Content-Type: application/json; charset=UTF-8');
            }
            echo json_encode([
                'success' => false,
                'message' => 'Критична помилка PHP: ' . $error['message'],
                'file' => $error['file'],
                'line' => $error['line']
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    });
    
    $debugInfo = [];
    
    try {
        // Перевірка системи перед створенням таблиці
        $checkErrors = [];
        
        // 1. Завантаження BaseModule з ядра
        if (!class_exists('BaseModule')) {
            $baseModuleFile = ENGINE_DIR . '/core/support/base/BaseModule.php';
            $debugInfo['baseModuleFile'] = $baseModuleFile;
            $debugInfo['baseModuleExists'] = file_exists($baseModuleFile);
            if (file_exists($baseModuleFile)) {
                require_once $baseModuleFile;
                $debugInfo['baseModuleLoaded'] = class_exists('BaseModule');
            } else {
                $checkErrors[] = 'BaseModule не знайдено: ' . $baseModuleFile;
            }
        } else {
            $debugInfo['baseModuleExists'] = true;
            $debugInfo['baseModuleLoaded'] = true;
        }
        
        // 2. Завантаження InstallerManager
        if (!class_exists('InstallerManager')) {
            $installerFile = __DIR__ . '/InstallerManager.php';
            $debugInfo['installerFile'] = $installerFile;
            $debugInfo['installerFileExists'] = file_exists($installerFile);
            $debugInfo['installerFileReadable'] = file_exists($installerFile) ? is_readable($installerFile) : false;
            
            if (file_exists($installerFile)) {
                require_once $installerFile;
                $debugInfo['installerLoaded'] = class_exists('InstallerManager');
                
                if (!class_exists('InstallerManager')) {
                    $checkErrors[] = 'InstallerManager не завантажився після require_once: ' . $installerFile;
                }
            } else {
                $checkErrors[] = 'InstallerManager не знайдено: ' . $installerFile;
            }
        } else {
            $debugInfo['installerFileExists'] = true;
            $debugInfo['installerLoaded'] = true;
        }
        
        if (!empty($checkErrors)) {
            // Логуємо тільки критичні помилки
            if (class_exists('Logger')) {
                Logger::getInstance()->logError('InstallerManager System Check Errors', ['errors' => $checkErrors, 'debug' => $debugInfo]);
            }
            
            // Переконуємося, що буфер чистий перед відправкою
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            
            $response = [
                'success' => false, 
                'message' => 'Помилка перевірки системи: ' . implode('; ', $checkErrors),
                'errors' => $checkErrors,
                'debug' => $debugInfo
            ];
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Завантаження конфігурації БД з сесії
        loadDatabaseConfigFromSession();
        
        $data = json_decode(file_get_contents('php://input'), true);
        $table = $data['table'] ?? '';
        
        $debugInfo['table'] = $table;
        $debugInfo['sessionStatus'] = session_status();
        $debugInfo['sessionId'] = session_id();
        $debugInfo['sessionKeys'] = array_keys($_SESSION ?? []);
        $storedConfig = getInstallerDbConfig(false);
        $debugInfo['dbConfigFound'] = is_array($storedConfig);
        if (is_array($storedConfig)) {
            $debugInfo['dbConfigKeys'] = array_keys($storedConfig);
        }
        $debugInfo['dbHost'] = defined('DB_HOST') ? DB_HOST : 'not defined';
        $debugInfo['dbName'] = defined('DB_NAME') ? DB_NAME : 'not defined';
        $debugInfo['dbUser'] = defined('DB_USER') ? DB_USER : 'not defined';
        $debugInfo['dbPass'] = defined('DB_PASS') ? (empty(DB_PASS) ? 'empty' : '***') : 'not defined';
        
        // Перевірка наявності конфігурації БД
        $databaseIniFile = dirname(__DIR__, 2) . '/storage/config/database.ini';
        $debugInfo['databaseIniFile'] = $databaseIniFile;
        $debugInfo['databaseIniExists'] = file_exists($databaseIniFile);
        $debugInfo['databaseIniReadable'] = file_exists($databaseIniFile) ? is_readable($databaseIniFile) : false;
        
        // Завантажуємо конфігурацію БД
        $dbConfig = getInstallerDbConfig();
        $debugInfo['dbConfigRetrieved'] = is_array($dbConfig);
        
        if (is_array($dbConfig) && !empty($dbConfig)) {
            $debugInfo['dbConfigKeys'] = array_keys($dbConfig);
            $debugInfo['dbConfigHost'] = $dbConfig['host'] ?? 'not set';
            $debugInfo['dbConfigName'] = $dbConfig['name'] ?? 'not set';
            $debugInfo['dbConfigHasPass'] = isset($dbConfig['pass']) && $dbConfig['pass'] !== '';
            
            // Отримуємо значення з конфігурації
            $host = $dbConfig['host'] ?? '127.0.0.1';
            $port = $dbConfig['port'] ?? 3306;
            $name = $dbConfig['name'] ?? '';
            $user = $dbConfig['user'] ?? 'root';
            $pass = $dbConfig['pass'] ?? '';
            $charset = $dbConfig['charset'] ?? 'utf8mb4';
            
            // Спрощена логіка: використовуємо ТІЛЬКИ GLOBALS для установщика
            $GLOBALS['_INSTALLER_DB_HOST'] = $host . ':' . $port;
            $GLOBALS['_INSTALLER_DB_NAME'] = $name;
            $GLOBALS['_INSTALLER_DB_USER'] = $user;
            $GLOBALS['_INSTALLER_DB_PASS'] = $pass;
            $GLOBALS['_INSTALLER_DB_CHARSET'] = $charset;
        } else {
            $debugInfo['dbConfigError'] = 'Failed to retrieve dbConfig from any source';
        }
        
        // Перевіряємо наявність валідної конфігурації (з GLOBALS)
        $dbHost = $GLOBALS['_INSTALLER_DB_HOST'] ?? '';
        $dbName = $GLOBALS['_INSTALLER_DB_NAME'] ?? '';
        $hasValidConfig = !empty($dbHost) && !empty($dbName);
        
        if (!$hasValidConfig) {
            // Database configuration not loaded
            
            // Переконуємося, що буфер чистий перед відправкою
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            
            $response = [
                'success' => false, 
                'message' => 'Конфігурація бази даних не завантажена. Перевірте налаштування підключення на попередньому кроці.',
                'debug' => $debugInfo
            ];
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Оновлюємо debug info з фінальними значеннями
        $debugInfo['finalDbHost'] = defined('DB_HOST') ? DB_HOST : 'not defined';
        $debugInfo['finalDbName'] = defined('DB_NAME') ? DB_NAME : 'not defined';
        $debugInfo['finalDbUser'] = defined('DB_USER') ? DB_USER : 'not defined';
        $debugInfo['finalDbCharset'] = defined('DB_CHARSET') ? DB_CHARSET : 'not defined';
        
        // Перевірка наявності DatabaseHelper (беремо з ядра через ENGINE_DIR)
        if (!class_exists('DatabaseHelper')) {
            $databaseHelperFile = defined('ENGINE_DIR')
                ? ENGINE_DIR . '/core/support/helpers/DatabaseHelper.php'
                : null;

            if ($databaseHelperFile && file_exists($databaseHelperFile)) {
                require_once $databaseHelperFile;
            } else {
                // DatabaseHelper not found
                while (ob_get_level() > 0) {
                    ob_end_clean();
                }

                $response = [
                    'success' => false,
                    'message' => 'DatabaseHelper не знайдено у ядрі CMS',
                    'debug' => $debugInfo
                ];

                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
        
        if (!class_exists('InstallerManager')) {
            // InstallerManager class not found after loading
            
            // Переконуємося, що буфер чистий перед відправкою
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            
            $response = [
                'success' => false, 
                'message' => 'InstallerManager not available after loading',
                'debug' => $debugInfo
            ];
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        $installer = InstallerManager::getInstance();
        if (!$installer) {
            // Failed to get InstallerManager instance
            
            // Переконуємося, що буфер чистий перед відправкою
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            
            $response = [
                'success' => false, 
                'message' => 'Failed to get InstallerManager instance',
                'debug' => $debugInfo
            ];
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Отримуємо кодування з конфігурації
        $charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';
        $collation = 'utf8mb4_unicode_ci';
        if ($charset === 'utf8') {
            $collation = 'utf8_unicode_ci';
        } elseif ($charset === 'latin1') {
            $collation = 'latin1_swedish_ci';
        }
        
        // Намагаємося отримати з конфігурації установщика
        $dbConfig = getInstallerDbConfig();
        if (is_array($dbConfig) && isset($dbConfig['charset'])) {
            $charset = $dbConfig['charset'];
            // Визначаємо collation на основі charset
            if ($charset === 'utf8mb4') {
                $collation = 'utf8mb4_unicode_ci';
            } elseif ($charset === 'utf8') {
                $collation = 'utf8_unicode_ci';
            } elseif ($charset === 'latin1') {
                $collation = 'latin1_swedish_ci';
            }
        }
        
        $tables = $installer->getTableDefinitions($charset, $collation);
        $debugInfo['tablesCount'] = count($tables);
        $debugInfo['availableTables'] = array_keys($tables);

        // Спроба підключення до БД
        try {
            $conn = DatabaseHelper::getConnection(false); // Не показуємо сторінку помилки
            if (!$conn) {
                $lastError = error_get_last();
                
                // Спрощена логіка: використовуємо ТІЛЬКИ GLOBALS для установщика
                $dbHost = $GLOBALS['_INSTALLER_DB_HOST'] ?? '127.0.0.1';
                $dbName = $GLOBALS['_INSTALLER_DB_NAME'] ?? '';
                $dbUser = $GLOBALS['_INSTALLER_DB_USER'] ?? 'root';
                $dbPass = $GLOBALS['_INSTALLER_DB_PASS'] ?? '';
                $dbCharset = $GLOBALS['_INSTALLER_DB_CHARSET'] ?? 'utf8mb4';
                
                // Логуємо для діагностики (без пароля)
                // Direct connection attempt
                
                // Перевіряємо, що у нас є мінімальна конфігурація
                if (empty($dbHost) || empty($dbName)) {
                    // Переконуємося, що буфер чистий перед відправкою
                    while (ob_get_level() > 0) {
                        ob_end_clean();
                    }
                    
                    $response = [
                        'success' => false,
                        'message' => 'Конфігурація бази даних не завантажена. Перевірте налаштування підключення на попередньому кроці.',
                        'debug' => array_merge($debugInfo, [
                            'dbHost' => $dbHost,
                            'dbName' => $dbName,
                            'dbUser' => $dbUser,
                            'dbCharset' => $dbCharset,
                            'globals' => [
                                'host' => $GLOBALS['_INSTALLER_DB_HOST'] ?? 'not set',
                                'name' => $GLOBALS['_INSTALLER_DB_NAME'] ?? 'not set',
                                'user' => $GLOBALS['_INSTALLER_DB_USER'] ?? 'not set',
                                'charset' => $GLOBALS['_INSTALLER_DB_CHARSET'] ?? 'not set'
                            ]
                        ])
                    ];
                    
                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                    exit;
                }
                
                // Спроба прямого підключення для діагностики
                try {
                    $hostParts = explode(':', $dbHost);
                    $host = $hostParts[0] ?? '127.0.0.1';
                    $port = isset($hostParts[1]) ? (int)$hostParts[1] : 3306;
                    
                    $testConn = new PDO(
                        "mysql:host={$host};port={$port};charset={$dbCharset}",
                        $dbUser,
                        $dbPass,
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_TIMEOUT => 5
                        ]
                    );
                    
                    // Перевірка існування бази даних
                    $stmt = $testConn->prepare("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?");
                    $stmt->execute([$dbName]);
                    $dbExists = $stmt->fetch() !== false;
                    
                    $debugInfo['directConnection'] = 'success';
                    $debugInfo['databaseExists'] = $dbExists;
                    
                    if (!$dbExists) {
                        // Database does not exist
                        
                        // Переконуємося, що буфер чистий перед відправкою
                        while (ob_get_level() > 0) {
                            ob_end_clean();
                        }
                        
                        $response = [
                            'success' => false, 
                            'message' => 'База даних "' . $dbName . '" не існує. Створіть її в панелі управління хостингом.',
                            'debug' => $debugInfo,
                            'lastError' => $lastError
                        ];
                        
                        echo json_encode($response, JSON_UNESCAPED_UNICODE);
                        exit;
                    }
                    
                    // Якщо база існує, намагаємося підключитися до неї
                    $testConn = new PDO(
                        "mysql:host={$host};port={$port};dbname={$dbName};charset={$dbCharset}",
                        $dbUser,
                        $dbPass,
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_TIMEOUT => 5
                        ]
                    );
                    
                    $debugInfo['directConnectionToDb'] = 'success';
                    $conn = $testConn;
                } catch (PDOException $e) {
                    $debugInfo['directConnectionError'] = $e->getMessage();
                    $debugInfo['directConnectionCode'] = $e->getCode();
                    
                    // Database connection failed - логуємо через Logger, якщо доступний
                    if (class_exists('Logger')) {
                        Logger::getInstance()->logError('Database connection failed', [
                            'error' => $e->getMessage(),
                            'debug' => $debugInfo,
                            'last_error' => $lastError
                        ]);
                    }
                    
                    // Переконуємося, що буфер чистий перед відправкою
                    while (ob_get_level() > 0) {
                        ob_end_clean();
                    }
                    
                    $response = [
                        'success' => false, 
                        'message' => 'Ошибка подключения к базе данных: ' . $e->getMessage(),
                        'debug' => $debugInfo,
                        'lastError' => $lastError,
                        'pdoCode' => $e->getCode(),
                        'pdoMessage' => $e->getMessage()
                    ];
                    
                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }
        } catch (Exception $e) {
            // Exception during database connection
            
            // Переконуємося, що буфер чистий перед відправкою
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            
            $response = [
                'success' => false, 
                'message' => 'Исключение при подключении к БД: ' . $e->getMessage(),
                'debug' => $debugInfo
            ];
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Створюємо ВСІ таблиці за визначенням інсталятора, незалежно від того,
        // яку саме таблицю запитав фронтенд. Це робить установку надійною,
        // навіть якщо список на фронті відрізняється.
        try {
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            foreach ($tables as $tableName => $sql) {
                $debugInfoCurrent = $debugInfo;
                $debugInfoCurrent['currentTable'] = $tableName;
                $debugInfoCurrent['sqlLength'] = strlen($sql);
                $debugInfoCurrent['sqlPreview'] = substr($sql, 0, 200) . '...';

                // Перевіряємо, чи існує таблиця
                $tableExists = false;
                try {
                    $dbName = $GLOBALS['_INSTALLER_DB_NAME'] ?? '';
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?");
                    $stmt->execute([$dbName, $tableName]);
                    $tableExists = $stmt->fetchColumn() > 0;
                    $debugInfoCurrent['tableExists'] = $tableExists;
                } catch (Exception $e) {
                    // Помилка перевірки існування таблиці – не критична, просто продовжуємо
                }

                if ($tableExists) {
                    continue;
                }

                try {
                    $conn->exec($sql);
                } catch (PDOException $e) {
                    $errorInfo = $e->errorInfo ?? [];
                    if (class_exists('Logger')) {
                        Logger::getInstance()->logError('PDO Error creating table', [
                            'table' => $tableName,
                            'error' => $e->getMessage(),
                            'code' => $e->getCode(),
                            'error_info' => $errorInfo,
                            'sql' => substr($sql, 0, 500)
                        ]);
                    }
                }

                // Після створення roles запускаємо SQL для заповнення ролей/прав
                if ($tableName === 'roles') {
                    try {
                        $rolesSqlFile = __DIR__ . '/../db/roles_permissions.sql';
                        if (file_exists($rolesSqlFile)) {
                            $rolesSql = file_get_contents($rolesSqlFile);
                            if (!empty($rolesSql)) {
                                $statements = array_filter(
                                    array_map('trim', explode(';', $rolesSql)),
                                    fn($stmt) => !empty($stmt) &&
                                        !preg_match('/^--/', $stmt) &&
                                        !preg_match('/^\/\*/', $stmt) &&
                                        stripos($stmt, 'CREATE TABLE') === false
                                );

                                foreach ($statements as $statement) {
                                    $statement = preg_replace('/--.*$/m', '', $statement);
                                    $statement = preg_replace('/\/\*.*?\*\//s', '', $statement);
                                    $statement = trim($statement);

                                    if (!empty($statement)) {
                                        try {
                                            $conn->exec($statement);
                                        } catch (Exception $e) {
                                            if (stripos($e->getMessage(), 'Duplicate') === false &&
                                                stripos($e->getMessage(), 'already exists') === false) {
                                                if (class_exists('Logger')) {
                                                    Logger::getInstance()->logError('Roles SQL error', ['error' => $e->getMessage()]);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } catch (Exception $e) {
                        // Ігноруємо помилки заповнення ролей – не критично для завершення установки
                    }
                }
            }

            // Всі визначення таблиць оброблені
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            $response = [
                'success' => true,
                // Повертаємо таблицю, яку запитував фронт, для сумісності,
                // але фактично вже створені всі таблиці.
                'table' => $table,
                'debug' => $debugInfo
            ];

            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } catch (PDOException $e) {
            $errorInfo = $e->errorInfo ?? [];
            if (class_exists('Logger')) {
                Logger::getInstance()->logError('PDO Error creating tables batch', [
                    'error' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'error_info' => $errorInfo
                ]);
            }

            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            $response = [
                'success' => false,
                'message' => 'Ошибка при создании таблиц: ' . $e->getMessage(),
                'pdoCode' => $e->getCode(),
                'pdoErrorInfo' => $errorInfo,
                'table' => $table,
                'debug' => $debugInfo
            ];

            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    } catch (Exception $e) {
        error_log('Exception creating table: ' . $e->getMessage());
        error_log('File: ' . $e->getFile() . ':' . $e->getLine());
        error_log('Trace: ' . $e->getTraceAsString());
        error_log('Debug: ' . json_encode($debugInfo, JSON_UNESCAPED_UNICODE));
        
        // Переконуємося, що буфер чистий перед відправкою
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        $response = [
            'success' => false, 
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'debug' => $debugInfo
        ];
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    } catch (Throwable $e) {
        // Логуємо тільки критичні помилки через Logger (якщо доступний)
        if (class_exists('Logger')) {
            Logger::getInstance()->logException($e, ['debug' => $debugInfo]);
        }
        
        // Переконуємося, що буфер чистий перед відправкою
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        $response = [
            'success' => false, 
            'message' => 'Критическая ошибка: ' . $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'debug' => $debugInfo
        ];
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/**
 * Завантаження налаштувань БД з сесії (для використання під час установки)
 */
function loadDatabaseConfigFromSession(): void {
    // Переконуємося, що сесія ініціалізована
    if (session_status() === PHP_SESSION_NONE) {
        if (!headers_sent()) {
            session_start();
        }
    }
    
    $dbConfig = getInstallerDbConfig();
    
    if (is_array($dbConfig) && !empty($dbConfig)) {
        // Спрощена логіка: використовуємо ТІЛЬКИ GLOBALS для інсталятора
        // Не намагаємося визначати константи, оскільки вони можуть бути вже визначені як порожні
        $host = $dbConfig['host'] ?? '127.0.0.1';
        $port = $dbConfig['port'] ?? 3306;
        $name = $dbConfig['name'] ?? '';
        $user = $dbConfig['user'] ?? 'root';
        $pass = $dbConfig['pass'] ?? '';
        $charset = $dbConfig['charset'] ?? 'utf8mb4';
        
        // Встановлюємо GLOBALS - це єдине джерело конфігурації для інсталятора
        $GLOBALS['_INSTALLER_DB_HOST'] = $host . ':' . $port;
        $GLOBALS['_INSTALLER_DB_NAME'] = $name;
        $GLOBALS['_INSTALLER_DB_USER'] = $user;
        $GLOBALS['_INSTALLER_DB_PASS'] = $pass;
        $GLOBALS['_INSTALLER_DB_CHARSET'] = $charset;
        
        // GLOBALS set for database config
    } else {
        // Логуємо для відладки
        $sessionKeys = (isset($_SESSION) && is_array($_SESSION)) ? array_keys($_SESSION) : [];
        error_log('loadDatabaseConfigFromSession: dbConfig is empty. Session keys: ' . implode(', ', $sessionKeys));
    }
}

/**
 * Отримання налаштувань БД з доступних джерел (database.ini, сесія, cookies)
 */
function getInstallerDbConfig(bool $useSession = true): ?array {
    $dbConfig = null;

    // 1. Пробуємо завантажити з storage/config/database.ini (основний конфіг)
    $databaseIniFile = dirname(__DIR__, 2) . '/storage/config/database.ini';
    if (file_exists($databaseIniFile) && is_readable($databaseIniFile)) {
        try {
            // Завантажуємо клас Ini з ядра, якщо він ще не завантажений
            if (!class_exists('Ini') && defined('ENGINE_DIR')) {
                $iniFile = ENGINE_DIR . '/infrastructure/filesystem/Ini.php';
                if (file_exists($iniFile)) {
                    require_once $iniFile;
                }
            }
            
            if (class_exists('Ini')) {
                $ini = new Ini($databaseIniFile);
                $dbSection = $ini->getSection('database');
                if (is_array($dbSection) && !empty($dbSection)) {
                    // Парсимо host:port якщо потрібно
                    $host = $dbSection['host'] ?? '127.0.0.1';
                    $port = 3306;
                    if (str_contains($host, ':')) {
                        [$host, $port] = explode(':', $host, 2);
                        $port = (int)$port;
                    } else {
                        $port = (int)($dbSection['port'] ?? 3306);
                    }
                    
                    $dbConfig = [
                        'host' => $host,
                        'port' => $port,
                        'name' => $dbSection['name'] ?? '',
                        'user' => $dbSection['user'] ?? 'root',
                        'pass' => $dbSection['pass'] ?? '',
                        'charset' => $dbSection['charset'] ?? 'utf8mb4'
                    ];
                    // Якщо знайшли в ini, повертаємо одразу
                    if (!empty($dbConfig['host']) && !empty($dbConfig['name'])) {
                        return $dbConfig;
                    }
                }
            }
        } catch (Exception $e) {
            error_log('Error reading database.ini: ' . $e->getMessage());
        }
    }
    
    // 3. Пробуємо завантажити з сесії (перевіряємо всі можливі ключі)
    if ($useSession) {
        // Пріоритет 1: Прямий доступ до сесії (без префікса)
        if (isset($_SESSION['install_db_config']) && is_array($_SESSION['install_db_config'])) {
            $dbConfig = $_SESSION['install_db_config'];
            error_log('getInstallerDbConfig: Found in $_SESSION[install_db_config]');
        } elseif (isset($_SESSION['db_config']) && is_array($_SESSION['db_config'])) {
            $dbConfig = $_SESSION['db_config'];
            error_log('getInstallerDbConfig: Found in $_SESSION[db_config]');
        } elseif (isset($_SESSION['installer.db_config']) && is_array($_SESSION['installer.db_config'])) {
            $dbConfig = $_SESSION['installer.db_config'];
            error_log('getInstallerDbConfig: Found in $_SESSION[installer.db_config]');
        }
        
        // Пріоритет 2: Через SessionManager з префіксом
        if ((!is_array($dbConfig) || empty($dbConfig)) && function_exists('sessionManager')) {
            try {
                $session = sessionManager('installer');
                $dbConfig = $session->get('db_config');
                if (is_array($dbConfig) && !empty($dbConfig)) {
                    error_log('getInstallerDbConfig: Found via sessionManager(installer)');
                }
            } catch (Exception $e) {
                error_log('Error getting session via sessionManager: ' . $e->getMessage());
            }
        }
        
        // Пріоритет 3: Пошук в будь-якому місці сесії
        if ((!is_array($dbConfig) || empty($dbConfig)) && isset($_SESSION) && is_array($_SESSION)) {
            foreach ($_SESSION as $key => $value) {
                if (is_array($value) && isset($value['host']) && isset($value['name'])) {
                    $dbConfig = $value;
                    error_log('getInstallerDbConfig: Found in $_SESSION[' . $key . ']');
                    break;
                }
            }
        }
    }
    
    // 3. Fallback на cookie (якщо сесія не працює)
    if ((!is_array($dbConfig) || empty($dbConfig)) && isset($_COOKIE['installer_db_config'])) {
        try {
            $cookieData = json_decode($_COOKIE['installer_db_config'], true);
            if (is_array($cookieData) && !empty($cookieData['host']) && !empty($cookieData['name'])) {
                $dbConfig = $cookieData;
                $hasPassword = isset($cookieData['pass']) && $cookieData['pass'] !== '';
                error_log('getInstallerDbConfig: Found in cookie' . ($hasPassword ? ' (with password)' : ' (password may be empty)'));
            }
        } catch (Exception $e) {
            error_log('Error reading cookie: ' . $e->getMessage());
        }
    }
    
    if (is_array($dbConfig) && !empty($dbConfig)) {
        error_log('getInstallerDbConfig: Successfully loaded config with keys: ' . implode(', ', array_keys($dbConfig)));
    } else {
        error_log('getInstallerDbConfig: No config found in any source');
        error_log('getInstallerDbConfig: Session keys: ' . implode(', ', array_keys($_SESSION ?? [])));
        error_log('getInstallerDbConfig: Cookie keys: ' . implode(', ', array_keys($_COOKIE ?? [])));
    }
    
    return is_array($dbConfig) && !empty($dbConfig) ? $dbConfig : null;
}

/**
 * Збереження налаштувань БД в сесію та cookies (fallback)
 */
function storeInstallerDbConfig(array $dbConfig): void {
    // Переконуємося, що сесія активна
    if (session_status() === PHP_SESSION_NONE) {
        if (!headers_sent()) {
            session_start();
        }
    }
    
    if (!isset($_SESSION) || !is_array($_SESSION)) {
        $_SESSION = [];
    }
    
    // Зберігаємо безпосередньо в сесію (пріоритет) - БЕЗ префікса
    $_SESSION['install_db_config'] = $dbConfig;
    $_SESSION['db_config'] = $dbConfig;
    
    // Також зберігаємо з префіксом installer для SessionManager
    $_SESSION['installer.db_config'] = $dbConfig;
    
    // Також зберігаємо через SessionManager, якщо доступний
    if (function_exists('sessionManager')) {
        try {
            $session = sessionManager('installer');
            $session->set('db_config', $dbConfig);
        } catch (Exception $e) {
            error_log('Error saving to sessionManager: ' . $e->getMessage());
        }
    }
    
    // Зберігаємо в cookie як fallback (включаючи пароль, оскільки це потрібно для установки)
    // В production це не використовується, тільки під час установки
    $cookieData = [
        'host' => $dbConfig['host'] ?? '',
        'port' => $dbConfig['port'] ?? 3306,
        'name' => $dbConfig['name'] ?? '',
        'user' => $dbConfig['user'] ?? '',
        'pass' => $dbConfig['pass'] ?? '', // Зберігаємо пароль для установки
        'version' => $dbConfig['version'] ?? '8.4',
        'charset' => $dbConfig['charset'] ?? 'utf8mb4'
    ];
    setcookie('installer_db_config', json_encode($cookieData), time() + 3600, '/', '', false, true);

    // Одразу створюємо файл storage/config/database.ini
    try {
        $configDir = dirname(__DIR__, 2) . '/storage/config';
        if (!is_dir($configDir)) {
            @mkdir($configDir, 0755, true);
        }
        $databaseIniFile = $configDir . '/database.ini';

        $content = "[database]\n";
        foreach ($cookieData as $k => $v) {
            if (is_string($v) && (str_contains($v, ' ') || str_contains($v, '='))) {
                $v = '"' . addslashes($v) . '"';
            }
            $content .= "{$k} = {$v}\n";
        }

        @file_put_contents($databaseIniFile, $content);
    } catch (Exception $e) {
        error_log('storeInstallerDbConfig: failed to write database.ini: ' . $e->getMessage());
    }
    
    // Логуємо для відладки
    error_log('storeInstallerDbConfig: Config saved. Session ID: ' . session_id());
    error_log('storeInstallerDbConfig: Session save path: ' . session_save_path());
    error_log('storeInstallerDbConfig: Config keys: ' . implode(', ', array_keys($dbConfig)));
    error_log('storeInstallerDbConfig: Session keys after save: ' . implode(', ', array_keys($_SESSION ?? [])));
}

/**
 * Очищення всіх даних інсталятора з сесії та cookies
 */
function clearInstallerDbConfig(): void {
    // Очищаємо сесію інсталятора
    if (function_exists('sessionManager')) {
        try {
            $session = sessionManager('installer');
            $session->remove('db_config');
            $session->clear(); // Очищаємо всю сесію інсталятора
        } catch (Exception $e) {
            error_log('Error clearing sessionManager: ' . $e->getMessage());
        }
    }
    
    // Очищаємо всі ключі інсталятора з $_SESSION
    $installerKeys = ['install_db_config', 'db_config', 'installer_step', 'installer_data'];
    foreach ($installerKeys as $key) {
        unset($_SESSION[$key]);
    }
    
    // Очищаємо cookies інсталятора
    $cookieKeys = ['installer_db_config', 'installer_step'];
    foreach ($cookieKeys as $key) {
        if (isset($_COOKIE[$key])) {
            setcookie($key, '', time() - 3600, '/');
            unset($_COOKIE[$key]);
        }
    }
    
    // Очищаємо GLOBALS інсталятора
    $globalsKeys = ['_INSTALLER_DB_HOST', '_INSTALLER_DB_NAME', '_INSTALLER_DB_USER', '_INSTALLER_DB_PASS', '_INSTALLER_DB_CHARSET'];
    foreach ($globalsKeys as $key) {
        unset($GLOBALS[$key]);
    }


    
    // Для інсталятора використовуємо тільки нативну PHP-сесію, без залежності від класу Session ядра
    if (session_status() === PHP_SESSION_ACTIVE) {
        // Очищаємо всі дані сесії
        $_SESSION = [];
        // Знищуємо сесію та cookie PHPSESSID
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    error_log('Installer session and data cleared (native PHP session)');
}

/**
 * Створення файлу database.ini з налаштувань у сесії
 */
function saveDatabaseIniFile(): bool {
    $dbConfig = getInstallerDbConfig();
    
    if (!is_array($dbConfig) || empty($dbConfig)) {
        return false;
    }
    
    $databaseIniFile = dirname(__DIR__, 2) . '/storage/config/database.ini';
    
    try {
        // Використовуємо клас Ini з engine/classes
        if (class_exists('Ini')) {
            $ini = new Ini();
            $ini->setSection('database', $dbConfig);
            $ini->save($databaseIniFile);
        } else {
            // Fallback на ручне створення
            $content = "[database]\n";
            foreach ($dbConfig as $k => $v) {
                // Екранюємо значення, якщо вони містять спеціальні символи
                if (is_string($v) && (str_contains($v, ' ') || str_contains($v, '='))) {
                    $v = '"' . addslashes($v) . '"';
                }
                $content .= "{$k} = {$v}\n";
            }
            @file_put_contents($databaseIniFile, $content);
        }
        
        return file_exists($databaseIniFile);
    } catch (Exception $e) {
        error_log("Error saving database.ini: " . $e->getMessage());
        return false;
    }
}

// Обробка збереження налаштувань БД (кнопка "Зберегти та продовжити")
// Основний тригер — action=save_db. Умова НЕ зав'язана жорстко на step,
// щоб спрацювати навіть якщо параметр step загубився або був перевизначений.
if (
    $action === 'save_db'
    || (!empty($_REQUEST['db_host']) && !empty($_REQUEST['db_name']))
) {
    try {
        $src = $_REQUEST;
        $dbConfig = [
            'host' => $src['db_host'] ?? '127.0.0.1',
            'port' => (int)($src['db_port'] ?? 3306),
            'name' => $src['db_name'] ?? '',
            'user' => $src['db_user'] ?? 'root',
            'pass' => $src['db_pass'] ?? '',
            'version' => $src['db_version'] ?? '5.7',
            'charset' => $src['db_charset'] ?? 'utf8mb4'
        ];
        
        // Переконуємося, що сесія ініціалізована
        if (session_status() === PHP_SESSION_NONE) {
            if (!headers_sent()) {
                session_start();
            }
        }
        
        // Зберігаємо конфігурацію в сесію та cookies
        storeInstallerDbConfig($dbConfig);
        
        // Логуємо для відладки
        error_log('=== DATABASE CONFIG SAVE ===');
        error_log('Session ID: ' . session_id());
        error_log('Session status: ' . session_status());
        error_log('Session save path: ' . session_save_path());
        error_log('Session keys after save: ' . implode(', ', array_keys($_SESSION ?? [])));
        
        // Перевіряємо, що дані дійсно збережені
        $savedConfig = $_SESSION['install_db_config'] ?? $_SESSION['db_config'] ?? $_SESSION['installer.db_config'] ?? null;
        if (is_array($savedConfig)) {
            error_log('Config verified in session. Keys: ' . implode(', ', array_keys($savedConfig)));
        } else {
            error_log('WARNING: Config NOT found in session after save!');
        }
        
        // Явно зберігаємо сесію
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
            error_log('Session written and closed');
        }
        
        error_log('=== END DATABASE CONFIG SAVE ===');
        
        // Після успішного збереження просто переключаємо крок на "tables"
        // та відрисовуємо поточний шаблон без редиректу.
        $step = 'tables';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Крок перевірки системи (перед налаштуванням БД)
if ($step === 'system-check') {
    // Перевірка системи перед налаштуванням БД
    // Виконуємо перевірки без підключення до БД
    $systemChecks = [];
    $systemErrors = [];
    $systemWarnings = [];
    
    // Опис компонентів для користувачів
    $componentDescriptions = [
        'BaseModule' => 'Базовий клас модулів системи',
        'InstallerManager' => 'Менеджер установки системи',
        'DatabaseHelper' => 'Допоміжний клас для роботи з базою даних',
        'PHP' => 'Мова програмування PHP',
        'PHP_Ext_pdo' => 'Розширення PDO для роботи з базами даних',
        'PHP_Ext_pdo_mysql' => 'Драйвер PDO для MySQL',
        'PHP_Ext_mbstring' => 'Розширення для роботи з багатобайтовими рядками',
        'PHP_Ext_json' => 'Розширення для роботи з JSON',
        'PHP_Ext_openssl' => 'Розширення для шифрування та безпеки',
        'DataDir' => 'Директорія для зберігання конфігурації',
        'CacheDir' => 'Директорія для зберігання кешу',
        'SessionsDir' => 'Директорія для зберігання сесій',
        'LogsDir' => 'Директорія для зберігання логів',
        'UploadsDir' => 'Директорія для завантажених файлів',
        'PluginsDir' => 'Директорія для плагінів',
        'ThemesDir' => 'Директорія для тем оформлення',
        'TempDir' => 'Тимчасова директорія для завантаження файлів',
        'RootHtaccess' => 'Головний файл .htaccess в корені сайту',
        'SessionSupport' => 'Підтримка сесій PHP',
        'JsonSupport' => 'Підтримка роботи з JSON',
        'FileFunctions' => 'Функції для роботи з файлами',
        'TableDefinitions' => 'Визначення таблиць бази даних'
    ];
    
    // Функція для отримання відносного шляху (локальна для system-check)
    function installer_getRelativePath($absolutePath) {
        $rootDir = dirname(__DIR__, 2); // Корінь проекту
        if (str_starts_with($absolutePath, $rootDir)) {
            return str_replace($rootDir, '', $absolutePath);
        }
        return $absolutePath;
    }
    
    // Функция для проверки прав на запись с реальной попыткой записи и создания .htaccess
    function installer_checkDirectoryWritable($dir, $name, $createHtaccess = false): array {
        $relativePath = installer_getRelativePath($dir);
        $result = ['status' => 'ok', 'path' => $dir, 'display_path' => $relativePath];
        
        // Проверяем существование директории
        if (!is_dir($dir)) {
            // Пытаемся создать
            if (@mkdir($dir, 0755, true)) {
                $result['status'] = 'ok';
                $result['created'] = true;
            } else {
                $result['status'] = 'error';
                $result['error'] = "Директория не существует и не может быть создана";
                return $result;
            }
        }
        
        // Створюємо .htaccess для cache та logs
        if ($createHtaccess) {
            $htaccessFile = $dir . '/.htaccess';
            if (!file_exists($htaccessFile)) {
                @file_put_contents($htaccessFile, "Deny from all\n");
            }
        }
        
        // Проверяем права на чтение
        if (!is_readable($dir)) {
            $result['status'] = 'error';
            $result['error'] = "Нет прав на чтение";
            return $result;
        }
        
        // Реальна перевірка запису - намагаємося створити тестовий файл
        $testFile = $dir . '/.test_write_' . time() . '.tmp';
        $testWrite = @file_put_contents($testFile, 'test');
        if ($testWrite === false) {
            $result['status'] = 'error';
            $result['error'] = "Нет прав на запись (не удалось создать тестовый файл)";
            return $result;
        }
        
        // Видаляємо тестовий файл
        @unlink($testFile);
        
        // Перевіряємо права на видалення
        if (file_exists($testFile)) {
            $result['status'] = 'warning';
            $result['warning'] = "Не удалось удалить тестовый файл (возможны проблемы с правами)";
        }
        
        return $result;
    }
    
    // 1. Перевірка наявності BaseModule
    if (!class_exists('BaseModule')) {
        $baseModuleFile = ENGINE_DIR . '/core/support/base/BaseModule.php';
        if (file_exists($baseModuleFile)) {
            require_once $baseModuleFile;
            if (class_exists('BaseModule')) {
                $systemChecks['BaseModule'] = [
                    'status' => 'ok', 
                    'file' => $baseModuleFile,
                    'description' => $componentDescriptions['BaseModule'] ?? '',
                    'display_path' => installer_getRelativePath($baseModuleFile)
                ];
            } else {
                $systemErrors[] = 'BaseModule не загрузился после require_once';
                $systemChecks['BaseModule'] = [
                    'status' => 'error', 
                    'error' => 'Класс не загрузился после require_once',
                    'description' => $componentDescriptions['BaseModule'] ?? ''
                ];
            }
        } else {
            $systemErrors[] = 'BaseModule не найден: ' . $baseModuleFile;
            $systemChecks['BaseModule'] = [
                'status' => 'error', 
                'error' => 'Файл не найден',
                'description' => $componentDescriptions['BaseModule'] ?? ''
            ];
        }
    } else {
        $systemChecks['BaseModule'] = [
            'status' => 'ok',
            'description' => $componentDescriptions['BaseModule'] ?? ''
        ];
    }
    
    // 2. Перевірка наявності InstallerManager
    if (!class_exists('InstallerManager')) {
        $installerFile = __DIR__ . '/InstallerManager.php';
        if (file_exists($installerFile)) {
            require_once $installerFile;
            if (class_exists('InstallerManager')) {
                $systemChecks['InstallerManager'] = [
                    'status' => 'ok', 
                    'file' => $installerFile,
                    'description' => $componentDescriptions['InstallerManager'] ?? '',
                    'display_path' => installer_getRelativePath($installerFile)
                ];
            } else {
                $systemErrors[] = 'InstallerManager не загрузился после require_once';
                $systemChecks['InstallerManager'] = [
                    'status' => 'error', 
                    'error' => 'Класс не загрузился после require_once',
                    'description' => $componentDescriptions['InstallerManager'] ?? ''
                ];
            }
        } else {
            $systemErrors[] = 'InstallerManager не найден: ' . $installerFile;
            $systemChecks['InstallerManager'] = [
                'status' => 'error', 
                'error' => 'Файл не найден',
                'description' => $componentDescriptions['InstallerManager'] ?? ''
            ];
        }
    } else {
        $systemChecks['InstallerManager'] = [
            'status' => 'ok',
            'description' => $componentDescriptions['InstallerManager'] ?? ''
        ];
    }
    
    // 3. Перевірка наявності DatabaseHelper (беремо з ядра через ENGINE_DIR)
    $databaseHelperFile = defined('ENGINE_DIR')
        ? ENGINE_DIR . '/core/support/helpers/DatabaseHelper.php'
        : null;
    if ($databaseHelperFile && file_exists($databaseHelperFile)) {
        if (!class_exists('DatabaseHelper')) {
            require_once $databaseHelperFile;
        }
        if (class_exists('DatabaseHelper')) {
            $systemChecks['DatabaseHelper'] = [
                'status' => 'ok',
                'file' => $databaseHelperFile,
                'description' => $componentDescriptions['DatabaseHelper'] ?? '',
                'display_path' => installer_getRelativePath($databaseHelperFile)
            ];
        } else {
            $systemErrors[] = 'DatabaseHelper не загрузился после require_once';
            $systemChecks['DatabaseHelper'] = [
                'status' => 'error',
                'error' => 'Класс не загрузился',
                'description' => $componentDescriptions['DatabaseHelper'] ?? ''
            ];
        }
    } else {
        $systemErrors[] = 'DatabaseHelper не найден у ядра CMS';
        $systemChecks['DatabaseHelper'] = [
            'status' => 'error',
            'error' => 'Файл не найден',
            'description' => $componentDescriptions['DatabaseHelper'] ?? ''
        ];
    }
    
    // 4. Перевірка версії PHP
    $phpVersion = PHP_VERSION;
    $phpVersionOk = version_compare($phpVersion, '8.4.0', '>=');
    if ($phpVersionOk) {
        $systemChecks['PHP'] = [
            'status' => 'ok', 
            'version' => $phpVersion,
            'description' => $componentDescriptions['PHP'] ?? ''
        ];
    } else {
        $systemErrors[] = "PHP версия {$phpVersion} ниже требуемой (8.4.0+)";
        $systemChecks['PHP'] = [
            'status' => 'error', 
            'version' => $phpVersion, 
            'error' => 'Требуется версия 8.4.0+',
            'description' => $componentDescriptions['PHP'] ?? ''
        ];
    }
    
    // 5. Перевірка розширень PHP
    $requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'openssl'];
    foreach ($requiredExtensions as $ext) {
        $extKey = 'PHP_Ext_' . $ext;
        if (extension_loaded($ext)) {
            $systemChecks[$extKey] = [
                'status' => 'ok', 
                'extension' => $ext,
                'description' => $componentDescriptions[$extKey] ?? ''
            ];
        } else {
            $systemErrors[] = "Расширение PHP {$ext} не установлено";
            $systemChecks[$extKey] = [
                'status' => 'error', 
                'extension' => $ext, 
                'error' => 'Расширение не установлено',
                'description' => $componentDescriptions[$extKey] ?? ''
            ];
        }
    }
    
    // 6. Перевірка (та створення) директорії storage/config з .htaccess (основна конфігурація движка)
    $configDir = dirname(__DIR__, 2) . '/storage/config';
    $configCheck = installer_checkDirectoryWritable($configDir, 'ConfigDir', true);
    $configCheck['description'] = $componentDescriptions['DataDir'] ?? 'Директорія для конфігурації CMS (database.ini, services.ini і т.д.)';
    $systemChecks['ConfigDir'] = $configCheck;
    if ($configCheck['status'] === 'error') {
        $systemErrors[] = "Директория storage/config недоступна для записи: " . ($configCheck['error'] ?? 'Неизвестная ошибка');
    }
    
    // 7. Перевірка доступності директорії для кешу (створюємо з .htaccess)
    $cacheDir = __DIR__ . '/../../storage/cache';
    $cacheCheck = installer_checkDirectoryWritable($cacheDir, 'CacheDir', true);
    $cacheCheck['description'] = $componentDescriptions['CacheDir'] ?? '';
    $systemChecks['CacheDir'] = $cacheCheck;
    if ($cacheCheck['status'] === 'error') {
        $systemErrors[] = "Директория кеша недоступна для записи: " . ($cacheCheck['error'] ?? 'Неизвестная ошибка');
    } elseif ($cacheCheck['status'] === 'warning') {
        $systemWarnings[] = "Директория кеша: " . ($cacheCheck['warning'] ?? 'Предупреждение');
    }
    
    // 8. Перевірка директорії storage/sessions (створюємо автоматично з .htaccess)
    $sessionsDir = __DIR__ . '/../../storage/sessions';
    $sessionsCheck = installer_checkDirectoryWritable($sessionsDir, 'SessionsDir', true);
    $sessionsCheck['description'] = $componentDescriptions['SessionsDir'] ?? '';
    $systemChecks['SessionsDir'] = $sessionsCheck;
    if ($sessionsCheck['status'] === 'error') {
        $systemErrors[] = "Директория sessions недоступна для записи: " . ($sessionsCheck['error'] ?? 'Неизвестная ошибка');
    }
    
    // 9. Перевірка директорії storage/logs (створюємо з .htaccess)
    $logsDir = __DIR__ . '/../../storage/logs';
    $logsCheck = installer_checkDirectoryWritable($logsDir, 'LogsDir', true);
    $logsCheck['description'] = $componentDescriptions['LogsDir'] ?? '';
    $systemChecks['LogsDir'] = $logsCheck;
    if ($logsCheck['status'] === 'error') {
        $systemErrors[] = "Директория logs недоступна для записи: " . ($logsCheck['error'] ?? 'Неизвестная ошибка');
    }
    
    // 10. Перевірка директорії uploads (створюємо автоматично з .htaccess)
    $uploadsDir = __DIR__ . '/../../uploads';
    $uploadsCheck = installer_checkDirectoryWritable($uploadsDir, 'UploadsDir', true);
    $uploadsCheck['description'] = $componentDescriptions['UploadsDir'] ?? '';
    $systemChecks['UploadsDir'] = $uploadsCheck;
    if ($uploadsCheck['status'] === 'error') {
        $systemErrors[] = "Директория uploads недоступна для записи: " . ($uploadsCheck['error'] ?? 'Неизвестная ошибка');
    }
    
    // 11. Перевірка директорії plugins (створюємо автоматично з .htaccess)
    $pluginsDir = __DIR__ . '/../../plugins';
    $pluginsCheck = installer_checkDirectoryWritable($pluginsDir, 'PluginsDir', true);
    $pluginsCheck['description'] = $componentDescriptions['PluginsDir'] ?? '';
    $systemChecks['PluginsDir'] = $pluginsCheck;
    if ($pluginsCheck['status'] === 'error') {
        $systemErrors[] = "Директория plugins недоступна для записи: " . ($pluginsCheck['error'] ?? 'Неизвестная ошибка');
    }
    
    // 12. Перевірка директорії themes (створюємо автоматично з .htaccess)
    $themesDir = __DIR__ . '/../../themes';
    $themesCheck = installer_checkDirectoryWritable($themesDir, 'ThemesDir', true);
    $themesCheck['description'] = $componentDescriptions['ThemesDir'] ?? '';
    $systemChecks['ThemesDir'] = $themesCheck;
    if ($themesCheck['status'] === 'error') {
        $systemErrors[] = "Директория themes недоступна для записи: " . ($themesCheck['error'] ?? 'Неизвестная ошибка');
    }
    
    // 13. Перевірка директорії storage/temp (створюємо автоматично з .htaccess)
    $tempDir = __DIR__ . '/../../storage/temp';
    $tempCheck = installer_checkDirectoryWritable($tempDir, 'TempDir', true);
    $tempCheck['description'] = $componentDescriptions['TempDir'] ?? '';
    $systemChecks['TempDir'] = $tempCheck;
    if ($tempCheck['status'] === 'error') {
        $systemErrors[] = "Директория temp недоступна для записи: " . ($tempCheck['error'] ?? 'Неизвестная ошибка');
    }

    // 14. Перевірка та авто-створення .htaccess в корені проєкту
    $rootDir = dirname(__DIR__, 2);
    $rootHtaccess = $rootDir . '/.htaccess';
    $rootHtaccessStatus = [
        'status' => 'ok',
        'path' => $rootHtaccess,
        'display_path' => installer_getRelativePath($rootHtaccess),
    ];

    if (!file_exists($rootHtaccess)) {
        $htaccessContent = <<<HTACCESS
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Отдаём существующие файлы и директории как есть
    RewriteCond %{REQUEST_FILENAME} -f [OR]
    RewriteCond %{REQUEST_FILENAME} -d
    RewriteRule ^ - [L]

    # Статика админки (styles, scripts, images)
    RewriteCond %{REQUEST_URI} ^/admin/assets/(styles|scripts|images)/(.*)$
    RewriteCond %{DOCUMENT_ROOT}/engine/interface/admin-ui/assets/%1/%2 -f
    RewriteRule ^admin/assets/(styles|scripts|images)/(.*)$ /engine/interface/admin-ui/assets/%1/%2 [L]

    # Font Awesome та інші статичні ресурси адмінки (для сторінок помилок)
    RewriteCond %{REQUEST_URI} ^/engine/interface/admin-ui/assets/(styles|scripts|images|fonts|webfonts)/(.*)$
    RewriteCond %{DOCUMENT_ROOT}/engine/interface/admin-ui/assets/%1/%2 -f
    RewriteRule ^engine/interface/admin-ui/assets/(styles|scripts|images|fonts|webfonts)/(.*)$ /engine/interface/admin-ui/assets/%1/%2 [L]

    # Все остальные запросы отправляем в index.php
    RewriteRule ^ index.php [L]
</IfModule>

<IfModule mod_autoindex.c>
    Options -Indexes
</IfModule>

# Кастомні сторінки помилок
ErrorDocument 400 /error-handler.php?code=400
ErrorDocument 401 /error-handler.php?code=401
ErrorDocument 403 /error-handler.php?code=403
ErrorDocument 404 /error-handler.php?code=404
ErrorDocument 405 /error-handler.php?code=405
ErrorDocument 408 /error-handler.php?code=408
ErrorDocument 409 /error-handler.php?code=409
ErrorDocument 410 /error-handler.php?code=410
ErrorDocument 413 /error-handler.php?code=413
ErrorDocument 414 /error-handler.php?code=414
ErrorDocument 415 /error-handler.php?code=415
ErrorDocument 429 /error-handler.php?code=429
ErrorDocument 500 /error-handler.php?code=500
ErrorDocument 501 /error-handler.php?code=501
ErrorDocument 502 /error-handler.php?code=502
ErrorDocument 503 /error-handler.php?code=503
ErrorDocument 504 /error-handler.php?code=504
ErrorDocument 505 /error-handler.php?code=505
ErrorDocument 507 /error-handler.php?code=507
ErrorDocument 508 /error-handler.php?code=508
ErrorDocument 510 /error-handler.php?code=510
ErrorDocument 511 /error-handler.php?code=511
HTACCESS;

        $writeResult = @file_put_contents($rootHtaccess, $htaccessContent);
        if ($writeResult === false) {
            $rootHtaccessStatus['status'] = 'error';
            $rootHtaccessStatus['error'] = 'Не удалось создать файл .htaccess в корне сайта';
            $systemErrors[] = $rootHtaccessStatus['error'];
        } else {
            $rootHtaccessStatus['created'] = true;
        }
    }

    $rootHtaccessStatus['description'] = $componentDescriptions['RootHtaccess'] ?? 'Головний файл .htaccess в корені сайту';
    $systemChecks['RootHtaccess'] = $rootHtaccessStatus;
    
    // 14.1. Перевірка та авто-створення .htaccess для plugins/
    $pluginsHtaccess = $rootDir . '/plugins/.htaccess';
    if (!file_exists($pluginsHtaccess)) {
        $pluginsHtaccessContent = <<<HTACCESS
# Дозволити доступ до статичних ресурсів плагінів
<DirectoryMatch "^.*/assets/">
    <IfModule mod_authz_core.c>
        Require all granted
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order Allow,Deny
        Allow from all
    </IfModule>
</DirectoryMatch>

# Дозволити доступ до статичних файлів (CSS, JS, зображення, шрифти)
<FilesMatch "\.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot|otf|webp|json)$">
    <IfModule mod_authz_core.c>
        Require all granted
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order Allow,Deny
        Allow from all
    </IfModule>
</FilesMatch>

# Заборонити доступ до конфігураційних директорій
<DirectoryMatch "(src|db|tests|config)/">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order Allow,Deny
        Deny from all
    </IfModule>
</DirectoryMatch>

# Захист системних файлів плагінів
<FilesMatch "\.(htaccess|htpasswd|ini|log|sh|sql|php)$">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order Allow,Deny
        Deny from all
    </IfModule>
</FilesMatch>

# Заборонити доступ до init.php (виконується через систему)
<Files "init.php">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order Allow,Deny
        Deny from all
    </IfModule>
</Files>
HTACCESS;
        @file_put_contents($pluginsHtaccess, $pluginsHtaccessContent);
    }
    
    // 14.2. Перевірка та авто-створення .htaccess для themes/
    $themesHtaccess = $rootDir . '/themes/.htaccess';
    if (!file_exists($themesHtaccess)) {
        $themesHtaccessContent = <<<HTACCESS
# Заборона прямого доступу до PHP файлів тем
<FilesMatch "\.php$">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order deny,allow
        Deny from all
    </IfModule>
</FilesMatch>

# Дозволяємо доступ до статичних файлів
<FilesMatch "\.(css|js|jpg|jpeg|png|gif|svg|ico|woff|woff2|ttf|eot|webp|map)$">
    <IfModule mod_authz_core.c>
        Require all granted
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order allow,deny
        Allow from all
    </IfModule>
</FilesMatch>
HTACCESS;
        @file_put_contents($themesHtaccess, $themesHtaccessContent);
    }
    
    // 14.3. Перевірка та авто-створення .htaccess для storage/cache/
    $storageCacheDir = $rootDir . '/storage/cache';
    $storageCacheHtaccess = $storageCacheDir . '/.htaccess';
    if (!is_dir($storageCacheDir)) {
        @mkdir($storageCacheDir, 0755, true);
    }
    if (!file_exists($storageCacheHtaccess)) {
        $storageCacheHtaccessContent = <<<HTACCESS
# Заборона прямого доступу до файлів кешу
<IfModule mod_authz_core.c>
    Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
    Order deny,allow
    Deny from all
</IfModule>
HTACCESS;
        @file_put_contents($storageCacheHtaccess, $storageCacheHtaccessContent);
    }
    
    // 14.4. Перевірка та авто-створення .htaccess для storage/config/
    $storageConfigDir = $rootDir . '/storage/config';
    $storageConfigHtaccess = $storageConfigDir . '/.htaccess';
    if (!is_dir($storageConfigDir)) {
        @mkdir($storageConfigDir, 0755, true);
    }
    if (!file_exists($storageConfigHtaccess)) {
        $storageConfigHtaccessContent = <<<HTACCESS
# Заборона прямого доступу до конфігураційних файлів
<IfModule mod_authz_core.c>
    Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
    Order deny,allow
    Deny from all
</IfModule>
HTACCESS;
        @file_put_contents($storageConfigHtaccess, $storageConfigHtaccessContent);
    }
    
    // 14.5. Перевірка та авто-створення .htaccess для storage/logs/
    $storageLogsDir = $rootDir . '/storage/logs';
    $storageLogsHtaccess = $storageLogsDir . '/.htaccess';
    if (!is_dir($storageLogsDir)) {
        @mkdir($storageLogsDir, 0755, true);
    }
    if (!file_exists($storageLogsHtaccess)) {
        $storageLogsHtaccessContent = <<<HTACCESS
# Заборона прямого доступу до файлів логів
<IfModule mod_authz_core.c>
    Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
    Order deny,allow
    Deny from all
</IfModule>
HTACCESS;
        @file_put_contents($storageLogsHtaccess, $storageLogsHtaccessContent);
    }
    
    // 14.6. Перевірка та авто-створення .htaccess для storage/temp/
    $storageTempDir = $rootDir . '/storage/temp';
    $storageTempHtaccess = $storageTempDir . '/.htaccess';
    if (!is_dir($storageTempDir)) {
        @mkdir($storageTempDir, 0755, true);
    }
    if (!file_exists($storageTempHtaccess)) {
        $storageTempHtaccessContent = <<<HTACCESS
# Заборона прямого доступу до тимчасових файлів
<IfModule mod_authz_core.c>
    Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
    Order deny,allow
    Deny from all
</IfModule>
HTACCESS;
        @file_put_contents($storageTempHtaccess, $storageTempHtaccessContent);
    }
    
    // 15. Перевірка доступності функції session_start
    if (function_exists('session_start')) {
        $systemChecks['SessionSupport'] = [
            'status' => 'ok',
            'description' => $componentDescriptions['SessionSupport'] ?? ''
        ];
    } else {
        $systemErrors[] = "Функция session_start недоступна";
        $systemChecks['SessionSupport'] = [
            'status' => 'error', 
            'error' => 'Функция session_start недоступна',
            'description' => $componentDescriptions['SessionSupport'] ?? ''
        ];
    }
    
    // 14. Перевірка доступності функцій json_encode/json_decode
    if (function_exists('json_encode') && function_exists('json_decode')) {
        $systemChecks['JsonSupport'] = [
            'status' => 'ok',
            'description' => $componentDescriptions['JsonSupport'] ?? ''
        ];
    } else {
        $systemErrors[] = "Функции JSON недоступны";
        $systemChecks['JsonSupport'] = [
            'status' => 'error', 
            'error' => 'Функции JSON недоступны',
            'description' => $componentDescriptions['JsonSupport'] ?? ''
        ];
    }
    
    // 15. Перевірка доступності функцій file_get_contents/file_put_contents
    if (function_exists('file_get_contents') && function_exists('file_put_contents')) {
        $systemChecks['FileFunctions'] = [
            'status' => 'ok',
            'description' => $componentDescriptions['FileFunctions'] ?? ''
        ];
    } else {
        $systemErrors[] = "Функции работы с файлами недоступны";
        $systemChecks['FileFunctions'] = [
            'status' => 'error', 
            'error' => 'Функции работы с файлами недоступны',
            'description' => $componentDescriptions['FileFunctions'] ?? ''
        ];
    }
    
    // 16. Перевірка методу getTableDefinitions у InstallerManager (після завантаження)
    if (class_exists('InstallerManager')) {
        try {
            $installer = InstallerManager::getInstance();
            if ($installer && method_exists($installer, 'getTableDefinitions')) {
                // Получаем кодировку из конфигурации
        $charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';
        $collation = 'utf8mb4_unicode_ci';
        if ($charset === 'utf8') {
            $collation = 'utf8_unicode_ci';
        } elseif ($charset === 'latin1') {
            $collation = 'latin1_swedish_ci';
        }
        
        // Пробуем получить из конфигурации установщика
        $dbConfig = getInstallerDbConfig();
        if (is_array($dbConfig) && isset($dbConfig['charset'])) {
            $charset = $dbConfig['charset'];
            // Определяем collation на основе charset
            if ($charset === 'utf8mb4') {
                $collation = 'utf8mb4_unicode_ci';
            } elseif ($charset === 'utf8') {
                $collation = 'utf8_unicode_ci';
            } elseif ($charset === 'latin1') {
                $collation = 'latin1_swedish_ci';
            }
        }
        
        $tables = $installer->getTableDefinitions($charset, $collation);
                $tablesCount = is_array($tables) ? count($tables) : 0;
                if ($tablesCount > 0) {
                    $systemChecks['TableDefinitions'] = [
                        'status' => 'ok', 
                        'count' => (int)$tablesCount, 
                        'info' => "Доступно таблиць: {$tablesCount}",
                        'description' => $componentDescriptions['TableDefinitions'] ?? 'Визначення таблиць бази даних'
                    ];
                } else {
                    $systemErrors[] = 'Не удалось получить определения таблиц';
                    $systemChecks['TableDefinitions'] = [
                        'status' => 'error', 
                        'count' => 0,
                        'error' => 'Не удалось получить определения таблиц',
                        'description' => $componentDescriptions['TableDefinitions'] ?? 'Визначення таблиць бази даних'
                    ];
                }
            } else {
                $systemErrors[] = 'Метод getTableDefinitions не найден в InstallerManager';
                $systemChecks['TableDefinitions'] = ['status' => 'error', 'error' => 'Метод не существует'];
            }
        } catch (Exception $e) {
            $systemErrors[] = 'Ошибка при проверке InstallerManager: ' . $e->getMessage();
            $systemChecks['TableDefinitions'] = ['status' => 'error', 'error' => $e->getMessage()];
        }
    }
    
    // Логуємо перевірки
    error_log('Installer System Checks: ' . json_encode($systemChecks, JSON_UNESCAPED_UNICODE));
    if (!empty($systemErrors)) {
        error_log('Installer System Errors: ' . implode('; ', $systemErrors));
    }
}

if ($step === 'user') {
    // Завантажуємо налаштування БД з сесії
    loadDatabaseConfigFromSession();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';
            
            if ($password !== $passwordConfirm) {
                $error = 'Паролі не співпадають';
            } elseif (strlen($password) < 8) {
                $error = 'Пароль повинен містити мінімум 8 символів';
            } else {
                $db = DatabaseHelper::getConnection();
                
                // Перевіряємо, що ролі та дозволи створені (вони повинні бути створені після створення таблиці roles)
                $stmt = $db->query("SELECT COUNT(*) FROM roles");
                $rolesCount = (int)$stmt->fetchColumn();
                
                if ($rolesCount === 0) {
                    // Якщо ролей немає, створюємо їх (на випадок якщо SQL не виконався)
                    ensureRolesAndPermissions($db);
                }
                
                // Створюємо користувача
                $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
                $userId = (int)$db->lastInsertId();
                
                // Призначаємо роль розробника першому користувачу
                try {
                    // Отримуємо ID ролі developer
                    $stmt = $db->prepare("SELECT id FROM roles WHERE slug = 'developer' LIMIT 1");
                    $stmt->execute();
                    $role = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($role) {
                        $roleId = (int)$role['id'];
                        // Призначаємо роль розробника
                        // Призначаємо роль користувачу (role_ids в users)
                        $stmt = $db->prepare("UPDATE users SET role_ids = ? WHERE id = ?");
                        $stmt->execute([json_encode([$roleId]), $userId]);
                    } else {
                        // Якщо роль не знайдена, це критична помилка
                        error_log("Critical: Role 'developer' not found. Creating it manually...");
                        // Створюємо роль developer вручну
                        $stmt = $db->prepare("INSERT INTO roles (name, slug, description, is_system) VALUES (?, ?, ?, ?)");
                        $stmt->execute(['Розробник', 'developer', 'Повний доступ до всіх функцій системи. Роль створюється тільки при установці движка і не може бути видалена.', 1]);
                        $roleId = (int)$db->lastInsertId();
                        
                        // Призначаємо всі дозволи ролі developer
                        $stmt = $db->query("SELECT id FROM permissions");
                        $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
                        $permissionsJson = json_encode($permissions);
                        $stmt = $db->prepare("UPDATE roles SET permissions = ? WHERE id = ?");
                        $stmt->execute([$permissionsJson, $roleId]);
                        
                        // Призначаємо роль користувачу
                        // Призначаємо роль користувачу (role_ids в users)
                        $stmt = $db->prepare("UPDATE users SET role_ids = ? WHERE id = ?");
                        $stmt->execute([json_encode([$roleId]), $userId]);
                    }
                } catch (Exception $e) {
                    error_log("Error assigning developer role: " . $e->getMessage());
                    // Не перериваємо установку, але логуємо помилку
                }
                
                // Установка завершена - переконуємося, що файл database.ini існує (він уже створений в storeInstallerDbConfig)
                if (!saveDatabaseIniFile()) {
                    $error = 'Помилка при збереженні конфігурації бази даних. Перевірте права доступу до директорії storage/config/';
                } else {
                    // Передаємо дані про створеного користувача в шаблон завершення
                    $createdUser = [
                        'username' => $username,
                        'email' => $email,
                        // Пароль показуємо лише один раз на екрані завершення
                        'password' => $password
                    ];
                    // Переходимо на крок успішного завершення
                    $step = 'success';
                }
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Обработка завершения установки (кнопка "Видалити інсталятор і перейти в адмінку" на шаге success)
if ($action === 'finish_install') {
    try {
        $rootDir = dirname(__DIR__, 2);
        $configDir = $rootDir . '/storage/config';
        $installDir = $rootDir . '/install';
        
        // 1. Переконуємося, що database.ini існує
        $databaseIniFile = $configDir . '/database.ini';
        if (!file_exists($databaseIniFile)) {
            // Якщо файл не існує, спробуємо створити з сесії
            $dbConfig = getInstallerDbConfig();
            if (is_array($dbConfig) && !empty($dbConfig)) {
                if (!is_dir($configDir)) {
                    @mkdir($configDir, 0755, true);
                }
                
                $content = "[database]\n";
                foreach ($dbConfig as $k => $v) {
                    if (is_string($v) && (str_contains($v, ' ') || str_contains($v, '='))) {
                        $v = '"' . addslashes($v) . '"';
                    }
                    $content .= "{$k} = {$v}\n";
                }
                @file_put_contents($databaseIniFile, $content);
            }
        }
        
        // 2. Створюємо файл-маркер, що система встановлена
        $installedFlagFile = $configDir . '/installed.flag';
        if (!is_dir($configDir)) {
            @mkdir($configDir, 0755, true);
        }
        @file_put_contents($installedFlagFile, date('Y-m-d H:i:s') . "\n");
        
        // 3. Видаляємо папку install (рекурсивно)
        if (is_dir($installDir)) {
            // Функція для рекурсивного видалення директорії
            $deleteDirectory = function($dir) use (&$deleteDirectory) {
                if (!is_dir($dir)) {
                    return false;
                }
                $files = array_diff(scandir($dir), ['.', '..']);
                foreach ($files as $file) {
                    $path = $dir . DIRECTORY_SEPARATOR . $file;
                    if (is_dir($path)) {
                        $deleteDirectory($path);
                    } else {
                        @unlink($path);
                    }
                }
                return @rmdir($dir);
            };
            
            $deleteDirectory($installDir);
        }
        
        // 4. Редирект на адмінку
        header('Location: /admin/login');
        exit;
    } catch (Exception $e) {
        error_log("Error finishing installation: " . $e->getMessage());
        // В случае ошибки всё равно редиректим на админку
        header('Location: /admin/login');
        exit;
    }
}

/**
 * Переконуємося, що ролі та дозволи створені
 */
function ensureRolesAndPermissions(PDO $db): void {
    try {
        // Проверяем, есть ли уже роли
        $stmt = $db->query("SELECT COUNT(*) FROM roles");
        $rolesCount = (int)$stmt->fetchColumn();
        
        if ($rolesCount === 0) {
            // Створюємо базові ролі (тільки системні: Guest, user, developer)
            $roles = [
                ['Разработчик', 'developer', 'Полный доступ ко всем функциям системы. Роль создается только при установке движка и не может быть удалена.', 1],
                ['Пользователь', 'user', 'Обычный пользователь с базовыми правами', 1],
                ['Гость', 'guest', 'Базовая роль для неавторизованных пользователей', 1]
            ];
            
            foreach ($roles as $role) {
                $stmt = $db->prepare("INSERT IGNORE INTO roles (name, slug, description, is_system) VALUES (?, ?, ?, ?)");
                $stmt->execute($role);
            }
        }
        
        // Проверяем, есть ли уже разрешения
        $stmt = $db->query("SELECT COUNT(*) FROM permissions");
        $permissionsCount = (int)$stmt->fetchColumn();
        
        if ($permissionsCount === 0) {
            // Створюємо всі дозволи для адмін-панелі
            // Формат: [name, slug, description, category]
            $permissions = [
                // Тимчасово для розробки - одне право доступу
                ['Доступ к админ-панели', 'admin.access', 'Доступ к административной панели', 'admin'],
            ];
            
            // Создаем все разрешения
            foreach ($permissions as $permission) {
                $stmt = $db->prepare("INSERT IGNORE INTO permissions (name, slug, description, category) VALUES (?, ?, ?, ?)");
                $stmt->execute($permission);
            }
            
            // Призначаємо всі дозволи ролі developer
            $stmt = $db->prepare("SELECT id FROM roles WHERE slug = 'developer' LIMIT 1");
            $stmt->execute();
            $developerRole = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($developerRole) {
                $roleId = (int)$developerRole['id'];
                $stmt = $db->query("SELECT id FROM permissions");
                $permissionIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $permissionsJson = json_encode($permissionIds);
                $stmt = $db->prepare("UPDATE roles SET permissions = ? WHERE id = ?");
                $stmt->execute([$permissionsJson, $roleId]);
            }
            
            // Призначаємо базові дозволи ролі user
            $stmt = $db->prepare("SELECT id FROM roles WHERE slug = 'user' LIMIT 1");
            $stmt->execute();
            $userRole = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Разрешения для роли user удалены (разрешения кабинета - это плагин)
        }
    } catch (Exception $e) {
        error_log("Error ensuring roles and permissions: " . $e->getMessage());
    }
}

// Загружаем настройки БД из сессии для шагов tables и user
if ($step === 'tables' || $step === 'user') {
    // Переконуємося, що сесія ініціалізована
    if (session_status() === PHP_SESSION_NONE) {
        if (!headers_sent()) {
            session_start();
        }
    }
    
    // Логуємо для відладки
    error_log('=== LOADING DB CONFIG FOR STEP: ' . $step . ' ===');
    error_log('Session ID: ' . session_id());
    error_log('Session status: ' . session_status());
    error_log('Session save path: ' . session_save_path());
    error_log('Session keys: ' . implode(', ', array_keys($_SESSION ?? [])));
    error_log('Cookie keys: ' . implode(', ', array_keys($_COOKIE ?? [])));
    
    // Пробуем загрузить конфигурацию
    $dbConfig = getInstallerDbConfig(true);
    
    if (is_array($dbConfig) && !empty($dbConfig)) {
        // Загружаем конфигурацию в константы
        loadDatabaseConfigFromSession();
        error_log('DB config loaded successfully for step: ' . $step);
        error_log('DB_HOST: ' . (defined('DB_HOST') ? DB_HOST : 'not defined'));
        error_log('DB_NAME: ' . (defined('DB_NAME') ? DB_NAME : 'not defined'));
    } else {
        error_log('ERROR: No db_config found in any source for step: ' . $step);
        error_log('Session dump: ' . json_encode($_SESSION ?? [], JSON_UNESCAPED_UNICODE));
        error_log('Cookie dump: ' . json_encode($_COOKIE ?? [], JSON_UNESCAPED_UNICODE));
    }
    
    error_log('=== END LOADING DB CONFIG ===');
}

// Передаем результаты проверок системы в шаблон
$systemChecks = is_array($systemChecks) ? $systemChecks : [];
$systemErrors = is_array($systemErrors) ? $systemErrors : [];
$systemWarnings = is_array($systemWarnings) ? $systemWarnings : [];

// Подключаем единый шаблон установщика
$template = __DIR__ . '/../templates/installer.php';
if (file_exists($template)) {
    include $template;
} else {
    echo '<h1>Flowaxy CMS Installation</h1><p>Installer template not found</p>';
    if (!empty($systemErrors)) {
        echo '<h2>Ошибки системы:</h2><ul>';
        foreach ($systemErrors as $error) {
            echo '<li>' . htmlspecialchars($error) . '</li>';
        }
        echo '</ul>';
    }
    if (!empty($systemChecks)) {
        echo '<h2>Проверки системы:</h2><pre>' . print_r($systemChecks, true) . '</pre>';
    }
}
exit;

