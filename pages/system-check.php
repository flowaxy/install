<?php declare(strict_types=1); ?>

<div class="step-content <?= $step === 'system-check' ? 'active' : '' ?>" id="step-system-check">
    <div class="system-check-section">
        <h2 data-i18n="system-check.title">System Check</h2>
        <p data-i18n="system-check.text">We are verifying that all required components for installing the system are available.</p>

        <?php if (!empty($systemChecks)): ?>
            <?php
            $okCount = 0;
            $errorCount = 0;
            $warningCount = 0;
            foreach ($systemChecks as $check) {
                $status = $check['status'] ?? 'unknown';
                if ($status === 'ok') $okCount++;
                elseif ($status === 'error') $errorCount++;
                elseif ($status === 'warning') $warningCount++;
            }
            $totalCount = count($systemChecks);
            ?>
            <div style="margin: 20px 0; padding: 16px; background: #f7fafc; border: 1px solid #e2e8f0; display: flex; gap: 20px; align-items: center;">
                <div style="flex: 1;">
                    <div style="font-size: 12px; color: #718096; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;" data-i18n="system-check.status-title">Check Status</div>
                    <div style="font-size: 18px; font-weight: 600; color: #2d3748;">
                        <?php if ($errorCount === 0): ?>
                            <span style="color: #48bb78;" data-i18n="system-check.status-success">✓ All checks passed successfully</span>
                        <?php else: ?>
                            <span style="color: #f56565;">
                                ✗ <span data-i18n="system-check.status-errors">Errors found: </span><?= $errorCount ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div style="text-align: right; padding-left: 20px; border-left: 1px solid #e2e8f0;">
                    <div style="font-size: 12px; color: #718096; margin-bottom: 4px;">
                        <span data-i18n="system-check.status-ok-count">OK:</span>
                        <strong style="color: #48bb78;"><?= $okCount ?></strong>
                    </div>
                    <?php if ($warningCount > 0): ?>
                        <div style="font-size: 12px; color: #718096; margin-bottom: 4px;">
                            <span data-i18n="system-check.status-warning-count">Warnings:</span>
                            <strong style="color: #ed8936;"><?= $warningCount ?></strong>
                        </div>
                    <?php endif; ?>
                    <?php if ($errorCount > 0): ?>
                        <div style="font-size: 12px; color: #718096;">
                            <span data-i18n="system-check.status-error-count">Errors:</span>
                            <strong style="color: #f56565;"><?= $errorCount ?></strong>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="system-checks-list">
            <?php if (!empty($systemChecks)): ?>
                <?php foreach ($systemChecks as $checkName => $check): ?>
                    <div class="system-check-item <?= $check['status'] ?? 'unknown' ?>">
                        <div class="check-icon">
                            <?php if (($check['status'] ?? '') === 'ok'): ?>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" fill="currentColor"/>
                                </svg>
                            <?php elseif (($check['status'] ?? '') === 'error'): ?>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="currentColor"/>
                                </svg>
                            <?php elseif (($check['status'] ?? '') === 'warning'): ?>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" fill="currentColor"/>
                                </svg>
                            <?php else: ?>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11 18h2v-2h-2v2zm1-16C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-2.21 0-4 1.79-4 4h2c0-1.1.9-2 2-2s2 .9 2 2c0 2-3 1.75-3 5h2c0-2.25 3-2.5 3-5 0-2.21-1.79-4-4-4z" fill="currentColor"/>
                                </svg>
                            <?php endif; ?>
                        </div>
                        <div class="check-info">
                            <div class="check-name"><?= htmlspecialchars($checkName) ?></div>
                            <?php
                                $descKey = 'system-check.desc.' . $checkName;
                            ?>
                            <div class="check-details" style="color: #4a5568; font-style: italic; margin-bottom: 6px;" data-i18n="<?= htmlspecialchars($descKey) ?>">
                                <?= isset($check['description']) ? htmlspecialchars($check['description']) : '' ?>
                            </div>
                            <?php if (isset($check['version'])): ?>
                                <div class="check-details"><strong data-i18n="label.version">Version:</strong> <?= htmlspecialchars($check['version']) ?></div>
                            <?php endif; ?>
                            <?php if (isset($check['count']) && $check['count'] !== null && $check['count'] !== ''): ?>
                                <div class="check-details">
                                    <strong data-i18n="label.count">Count:</strong> <?= htmlspecialchars((string)$check['count']) ?>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($check['info'])): ?>
                                <div class="check-details"><?= htmlspecialchars($check['info']) ?></div>
                            <?php endif; ?>
                            <?php if (isset($check['display_path'])): ?>
                                <div class="check-path-text"><span data-i18n="system-check.path">Шлях:</span> <?= htmlspecialchars($check['display_path']) ?></div>
                            <?php elseif (isset($check['path'])): ?>
                                <div class="check-path-text"><span data-i18n="system-check.path">Шлях:</span> <?= htmlspecialchars($check['path']) ?></div>
                            <?php endif; ?>
                            <?php if (isset($check['error'])): ?>
                                <div class="check-error-text"><?= htmlspecialchars($check['error']) ?></div>
                            <?php endif; ?>
                            <?php if (isset($check['warning'])): ?>
                                <div class="check-warning-text"><?= htmlspecialchars($check['warning']) ?></div>
                            <?php endif; ?>
                            <?php if (isset($check['created']) && $check['created']): ?>
                                <div class="check-details" style="color: #38a169;" data-i18n="system-check.created_auto">✓ Створено автоматично</div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p data-i18n="system-check.not-run">System check was not run</p>
            <?php endif; ?>
        </div>

        <?php if (!empty($systemErrors)): ?>
            <div class="system-errors">
                <h3 data-i18n="system-check.errors-title">Errors:</h3>
                <ul>
                    <?php foreach ($systemErrors as $errorItem): ?>
                        <li><?= htmlspecialchars($errorItem) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($systemWarnings)): ?>
            <div class="system-warnings">
                <h3 data-i18n="system-check.warnings-title">Warnings:</h3>
                <ul>
                    <?php foreach ($systemWarnings as $warning): ?>
                        <li><?= htmlspecialchars($warning) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="installer-actions" style="margin-top: 24px; display: flex !important; gap: 10px; visibility: visible !important; opacity: 1 !important;">
            <a href="/install?step=welcome" class="btn btn-secondary" data-i18n="button.back" style="flex: 1 1 0; min-width: 0; display: inline-block !important; visibility: visible !important; opacity: 1 !important;">Back</a>
            <?php
            $systemErrors = $systemErrors ?? [];
            $hasErrors = is_array($systemErrors) && count($systemErrors) > 0;
            if (!$hasErrors): ?>
                <a href="/install?step=database" class="btn btn-primary" data-i18n="button.continue" style="flex: 1 1 0; min-width: 0; display: inline-block !important; visibility: visible !important; opacity: 1 !important;">Продовжити</a>
            <?php else: ?>
                <a href="/install?step=system-check" class="btn btn-primary" data-i18n="button.retry_check" onclick="window.location.reload(); return false;" style="flex: 1 1 0; min-width: 0; display: inline-block !important; visibility: visible !important; opacity: 1 !important;">Повторити перевірку</a>
            <?php endif; ?>
        </div>
    </div>
</div>


