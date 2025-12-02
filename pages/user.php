<?php declare(strict_types=1); ?>

<div class="step-content <?= $step === 'user' ? 'active' : '' ?>" id="step-user">
    <?php if ($error): ?>
        <div class="alert error" data-i18n="error.text"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="" id="userForm">
        <div class="form-group">
            <label data-i18n="label.username">Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label data-i18n="label.email">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label data-i18n="label.password">Password</label>
            <input type="password" name="password" required minlength="8">
        </div>

        <div class="form-group">
            <label data-i18n="label.password_confirm">Confirm Password</label>
            <input type="password" name="password_confirm" required minlength="8">
        </div>

        <button type="submit" class="btn btn-primary btn-full" data-i18n="button.create">Create & Finish</button>
    </form>
</div>


