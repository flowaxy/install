<?php declare(strict_types=1); ?>

<div class="step-content <?= $step === 'tables' ? 'active' : '' ?>" id="step-tables">
    <div class="progress-container">
        <div class="progress-bar">
            <div class="progress-fill" id="progressFill"></div>
        </div>
        <div class="progress-text" id="progressText" data-i18n="progress.text">Preparing...</div>
    </div>

    <ul class="tables-list" id="tablesList"></ul>

    <div id="tablesActions" style="display: none; margin-top: 20px;">
        <button class="btn btn-secondary btn-full" id="retryBtn" data-i18n="button.retry" onclick="retryFailedTables()">Повторити</button>
        <button class="btn btn-primary btn-full" id="continueBtn" style="display: none; margin-top: 10px;" data-i18n="button.continue" onclick="window.location.href='/install?step=user'">Продовжити</button>
    </div>
</div>


