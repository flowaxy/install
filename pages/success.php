<?php declare(strict_types=1); ?>

<?php
// Дані про створеного користувача (може бути не задано при помилці)
$createdUser = $createdUser ?? null;
?>

<div class="step-content <?= $step === 'success' ? 'active' : '' ?>" id="step-success">
    <div class="welcome-section">
        <h2 data-i18n="success.title">Установка завершена!</h2>
        <p data-i18n="success.text">Flowaxy CMS успішно встановлена. Ви можете увійти в адмін-панель, використовуючи дані нижче.</p>

        <?php if (is_array($createdUser)): ?>
            <div class="system-checks-list" style="margin-top: 16px;">
                <div class="system-check-item ok">
                    <div class="check-icon">
                        ✓
                    </div>
                    <div class="check-info">
                        <div class="check-name" data-i18n="success.account_created">Обліковий запис адміністратора створено</div>
                        <div class="check-details">
                            <strong data-i18n="success.login">Логін:</strong> <?= htmlspecialchars($createdUser['username'] ?? '') ?>
                        </div>
                        <?php if (!empty($createdUser['email'])): ?>
                            <div class="check-details">
                                <strong data-i18n="success.email">Email:</strong> <?= htmlspecialchars($createdUser['email'] ?? '') ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($createdUser['password'])): ?>
                            <div class="check-details" style="margin-top: 8px; color: #c05621;">
                                <strong data-i18n="success.password_hint">Пароль (збережіть його зараз, повторно він показаний не буде):</strong><br>
                                <code><?= htmlspecialchars($createdUser['password']) ?></code>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="installer-actions" style="margin-top: 24px;">
            <a href="/install?action=finish_install" class="btn btn-primary btn-full" data-i18n="success.go_to_admin">Видалити інсталятор і перейти в адмінку</a>
        </div>
    </div>
</div>


