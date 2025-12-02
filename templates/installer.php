<?php
declare(strict_types=1);

// Якщо контролер вже змінив $step (наприклад, після збереження БД),
// використовуємо його. Інакше беремо з REQUEST.
if (!isset($step) || $step === '') {
    $step = $_REQUEST['step'] ?? 'welcome';
}
$error = $error ?? null;
?>
<!DOCTYPE html>
<html lang="en" id="installer-page">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flowaxy CMS Installation</title>
    <link rel="icon" type="image/png" href="/install/assets/images/brand/favicon.png">
    <link rel="stylesheet" href="/install/assets/styles/installer.css?v=2">
</head>
<body>
    <div class="installer-container">
        <div class="installer-header">
            <div class="logo-container">
                <img src="/install/assets/images/brand/logo-white.png" alt="Flowaxy CMS" data-i18n="logo.alt">
            </div>
            <div class="installer-header-right">
                <div class="installer-header-text">
                    <p data-i18n="header.subtitle">Майстер установки</p>
                </div>
                <div class="language-switcher">
                    <button class="lang-btn" data-lang="uk" title="Українська">UA</button>
                    <button class="lang-btn" data-lang="ru" title="Русский">RU</button>
                    <button class="lang-btn" data-lang="en" title="English">EN</button>
                </div>
            </div>
        </div>

        <div class="installer-main">
            <aside class="installer-sidebar">
                <div class="sidebar-title" data-i18n="sidebar.title">Кроки установки</div>
                <ol class="sidebar-steps">
                    <li class="sidebar-step <?= $step === 'welcome' ? 'active' : ($step !== 'welcome' ? 'completed' : '') ?>">
                        <span class="sidebar-step-index">1</span>
                        <div class="sidebar-step-body">
                            <div class="sidebar-step-title" data-i18n="step.welcome.title">Привітання</div>
                            <div class="sidebar-step-sub" data-i18n="step.welcome.sub">Опис системи</div>
                        </div>
                    </li>
                    <li class="sidebar-step <?= $step === 'system-check' ? 'active' : ($step === 'database' || $step === 'tables' || $step === 'user' || $step === 'success' ? 'completed' : '') ?>">
                        <span class="sidebar-step-index">2</span>
                        <div class="sidebar-step-body">
                            <div class="sidebar-step-title" data-i18n="step.system-check.title">Перевірка системи</div>
                            <div class="sidebar-step-sub" data-i18n="step.system-check.sub">PHP, розширення, директорії</div>
                        </div>
                    </li>
                    <li class="sidebar-step <?= $step === 'database' ? 'active' : ($step === 'tables' || $step === 'user' || $step === 'success' ? 'completed' : '') ?>">
                        <span class="sidebar-step-index">3</span>
                        <div class="sidebar-step-body">
                            <div class="sidebar-step-title" data-i18n="step.database.title">База даних</div>
                            <div class="sidebar-step-sub" data-i18n="step.database.sub">Параметри підключення</div>
                        </div>
                    </li>
                    <li class="sidebar-step <?= $step === 'tables' ? 'active' : ($step === 'user' || $step === 'success' ? 'completed' : '') ?>">
                        <span class="sidebar-step-index">4</span>
                        <div class="sidebar-step-body">
                            <div class="sidebar-step-title" data-i18n="step.tables.title">Створення таблиць</div>
                            <div class="sidebar-step-sub" data-i18n="step.tables.sub">Підготовка БД</div>
                        </div>
                    </li>
                    <li class="sidebar-step <?= $step === 'user' ? 'active' : ($step === 'success' ? 'completed' : '') ?>">
                        <span class="sidebar-step-index">5</span>
                        <div class="sidebar-step-body">
                            <div class="sidebar-step-title" data-i18n="step.user.title">Адміністратор</div>
                            <div class="sidebar-step-sub" data-i18n="step.user.sub">Обліковий запис</div>
                        </div>
                    </li>
                    <li class="sidebar-step <?= $step === 'success' ? 'active' : '' ?>">
                        <span class="sidebar-step-index">6</span>
                        <div class="sidebar-step-body">
                            <div class="sidebar-step-title" data-i18n="step.success.title">Готово</div>
                            <div class="sidebar-step-sub" data-i18n="step.success.sub">Завершення</div>
                        </div>
                    </li>
                </ol>
            </aside>

            <main class="installer-content">
                <?php
                // Подключаем отдельный файл для каждого шагa из директории pages
                $stepFile = __DIR__ . '/../pages/' . $step . '.php';
                if (!is_file($stepFile)) {
                    $stepFile = __DIR__ . '/../pages/welcome.php';
                }
                require $stepFile;
                ?>
            </main>
        </div>

        <footer class="installer-footer">
            <span>Flowaxy CMS Installer</span>
            <span>v1.0.0 Dev</span>
        </footer>
    </div>

    <script src="/install/assets/scripts/installer.js?v=3"></script>
</body>
</html>
