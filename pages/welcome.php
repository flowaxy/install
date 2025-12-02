<?php declare(strict_types=1); ?>

<div class="step-content <?= $step === 'welcome' ? 'active' : '' ?>" id="step-welcome">
    <div class="welcome-section">
        <h2 data-i18n="welcome.title">Ласкаво просимо до Flowaxy CMS!</h2>
        <p data-i18n="welcome.text1">Flowaxy CMS — це сучасна та потужна система управління контентом, створена для розробників та бізнесу, які потребують гнучкості, продуктивності та повного контролю над своїми веб-проектами.</p>
        <p data-i18n="welcome.text2">Система поєднує в собі простоту використання з потужними можливостями кастомізації, забезпечуючи швидку установку, зручне управління контентом та високу продуктивність навіть при великих навантаженнях.</p>

        <h3 class="features-title" data-i18n="features.title">Основні можливості:</h3>
        <ul class="features-list">
            <li data-i18n="feature1">
                <strong>Модульна архітектура</strong> — розширюйте функціональність за допомогою плагінів та модулів без зміни ядра системи
            </li>
            <li data-i18n="feature2">
                <strong>Система тем та шаблонів</strong> — створюйте унікальний дизайн з повною підтримкою кастомізації та адаптивності
            </li>
            <li data-i18n="feature3">
                <strong>Безпека та оптимізація</strong> — вбудовані механізми захисту, кешування та оптимізації запитів до бази даних
            </li>
            <li data-i18n="feature4">
                <strong>Зручна адмін-панель</strong> — інтуїтивний інтерфейс для управління контентом, користувачами та налаштуваннями
            </li>
            <li data-i18n="feature5">
                <strong>Багатомовність</strong> — повна підтримка інтернаціоналізації та локалізації для глобальних проектів
            </li>
            <li data-i18n="feature6">
                <strong>API та інтеграції</strong> — RESTful API для інтеграції з зовнішніми сервісами та мобільними додатками
            </li>
            <li data-i18n="feature7">
                <strong>Рольова модель доступу</strong> — гнучка система ролей та дозволів для точного контролю доступу
            </li>
        </ul>
    </div>

    <div class="flowaxy-promo">
        <h3 data-i18n="promo.title">Потрібна допомога з розробкою?</h3>
        <p data-i18n="promo.text">Flowaxy — це не лише CMS, але й професійна веб-студія, яка надає повний спектр послуг: розробка сайтів та веб-додатків, інтеграція зі сторонніми сервісами, технічна підтримка та консультування. Зверніться до нас для отримання професійної допомоги та реалізації ваших ідей!</p>
        <a href="https://flowaxy.com" target="_blank" data-i18n="promo.link">Відвідати Flowaxy.com →</a>
    </div>

    <div class="installer-actions">
        <a href="/install?step=system-check" class="btn btn-primary btn-full" data-i18n="button.start">Start Installation</a>
    </div>
</div>


