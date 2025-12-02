<?php declare(strict_types=1); ?>

<div class="step-content <?= $step === 'database' ? 'active' : '' ?>" id="step-database">
    <!-- Після тесту підключення зберігаємо конфіг на цю ж URL, без редиректів сервера -->
    <form id="databaseForm" method="POST" action="">
        <input type="hidden" name="step" value="database">
        <input type="hidden" name="action" value="save_db">

        <div class="form-group">
            <label data-i18n="label.host">Host</label>
            <input type="text" name="db_host" value="<?= htmlspecialchars($_POST['db_host'] ?? $_GET['db_host'] ?? '127.0.0.1') ?>" required>
        </div>

        <div class="form-group">
            <label data-i18n="label.port">Port</label>
            <input type="number" name="db_port" value="<?= htmlspecialchars($_POST['db_port'] ?? $_GET['db_port'] ?? '3306') ?>" required>
        </div>

        <div class="form-group">
            <label data-i18n="label.database">Database</label>
            <input type="text" name="db_name" value="<?= htmlspecialchars($_POST['db_name'] ?? $_GET['db_name'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label data-i18n="label.username">Username</label>
            <input type="text" name="db_user" value="<?= htmlspecialchars($_POST['db_user'] ?? $_GET['db_user'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label data-i18n="label.password">Password</label>
            <input type="password" name="db_pass" value="<?= htmlspecialchars($_POST['db_pass'] ?? $_GET['db_pass'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label data-i18n="label.mysql_version">MySQL Version</label>
            <select name="db_version" id="dbVersion" required>
                <?php
                $versionValue = $_POST['db_version'] ?? $_GET['db_version'] ?? '5.7';
                ?>
                <option value="8.4" <?= $versionValue === '8.4' ? 'selected' : '' ?>>MySQL 8.4</option>
                <option value="5.7" <?= $versionValue === '5.7' ? 'selected' : '' ?>>MySQL 5.7</option>
            </select>
        </div>

        <div class="form-group">
            <label data-i18n="label.charset">Кодування (Charset)</label>
            <?php
            $charsetValue = $_POST['db_charset'] ?? $_GET['db_charset'] ?? 'utf8mb4';
            ?>
            <select name="db_charset" id="dbCharset" required>
                <option value="utf8mb4" <?= $charsetValue === 'utf8mb4' ? 'selected' : '' ?>>utf8mb4</option>
            </select>
            <small style="display: block; margin-top: 4px; color: #718096; font-size: 12px;" data-i18n="label.charset_hint">utf8mb4 підтримує всі Unicode символи, включаючи emoji</small>
        </div>

        <div class="test-result" id="testResult"></div>

        <div class="installer-actions">
            <button type="button" class="btn btn-secondary" id="testBtn" data-i18n="button.test">Test Connection</button>
            <button type="submit" class="btn btn-primary" id="saveBtn" data-i18n="button.save" disabled>Save & Continue</button>
        </div>
    </form>
</div>


