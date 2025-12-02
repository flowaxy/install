const translations = {
    uk: {
        'logo.alt': 'Flowaxy CMS',
        'header.title': 'Flowaxy CMS',
        'header.subtitle': 'Майстер установки',
        'welcome.title': 'Ласкаво просимо до Flowaxy CMS!',
        'welcome.text1': 'Flowaxy CMS — це сучасна та потужна система управління контентом, створена для розробників та бізнесу, які потребують гнучкості, продуктивності та повного контролю над своїми веб-проектами.',
        'welcome.text2': 'Система поєднує в собі простоту використання з потужними можливостями кастомізації, забезпечуючи швидку установку, зручне управління контентом та високу продуктивність навіть при великих навантаженнях.',
        'features.title': 'Основні можливості:',
        'feature1': '<strong>Модульна архітектура</strong> — розширюйте функціональність за допомогою плагінів та модулів без зміни ядра системи',
        'feature2': '<strong>Система тем та шаблонів</strong> — створюйте унікальний дизайн з повною підтримкою кастомізації та адаптивності',
        'feature3': '<strong>Безпека та оптимізація</strong> — вбудовані механізми захисту, кешування та оптимізації запитів до бази даних',
        'feature4': '<strong>Зручна адмін-панель</strong> — інтуїтивний інтерфейс для управління контентом, користувачами та налаштуваннями',
        'feature5': '<strong>Багатомовність</strong> — повна підтримка інтернаціоналізації та локалізації для глобальних проектів',
        'feature6': '<strong>API та інтеграції</strong> — RESTful API для інтеграції з зовнішніми сервісами та мобільними додатками',
        'feature7': '<strong>Рольова модель доступу</strong> — гнучка система ролей та дозволів для точного контролю доступу',
        'promo.title': 'Потрібна допомога з розробкою?',
        'promo.text': 'Flowaxy — це не лише CMS, але й професійна веб-студія, яка надає повний спектр послуг: розробка сайтів та веб-додатків, інтеграція зі сторонніми сервісами, технічна підтримка та консультування. Зверніться до нас для отримання професійної допомоги та реалізації ваших ідей!',
        'promo.link': 'Відвідати Flowaxy.com →',
        'button.start': 'Почати установку',
        'label.host': 'Хост',
        'label.port': 'Порт',
        'label.database': 'База даних',
        'label.username': 'Користувач',
        'label.password': 'Пароль',
        'label.password_confirm': 'Підтвердження пароля',
        'label.email': 'Email',
        'label.mysql_version': 'Версія MySQL',
        'label.charset': 'Кодування (Charset)',
        'label.charset_hint': 'Рекомендується utf8mb4 для підтримки emoji та всіх Unicode символів',
        'label.version': 'Версія:',
        'label.count': 'Кількість:',
        'button.test': 'Тестувати підключення',
        'button.save': 'Зберегти та продовжити',
        'button.continue': 'Продовжити',
        'button.retry': 'Повторити',
        'button.retry_check': 'Повторити перевірку',
        'button.create': 'Створити та завершити',
        'test.success': 'Підключення успішне!',
        'test.error': 'Помилка підключення: ',
        'test.testing': 'Перевірка підключення...',
        'progress.text': 'Підготовка...',
        'progress.retrying': 'Повторюється...',
        'table.creating': 'Створення',
        'table.success': 'Створено',
        'table.error': 'Помилка',
        'error.text': 'Помилка створення користувача',
        'system-check.title': 'Перевірка системи',
        'system-check.text': 'Перевіряємо наявність необхідних компонентів для встановлення системи.',
        'system-check.status-title': 'Статус перевірки',
        'system-check.status-success': 'Всі перевірки пройдені успішно',
        'system-check.status-errors': 'Знайдено помилок: ',
        'system-check.status-ok-count': 'Успішно:',
        'system-check.status-warning-count': 'Попереджень:',
        'system-check.status-error-count': 'Помилок:',
        'system-check.not-run': 'Перевірка не виконана',
        'system-check.errors-title': 'Помилки:',
        'system-check.warnings-title': 'Попередження:',
        'button.back': 'Назад',
        'system-check.desc.BaseModule': 'Базовий клас модулів системи',
        'system-check.desc.InstallerManager': 'Менеджер установки системи',
        'system-check.desc.DatabaseHelper': 'Допоміжний клас для роботи з базою даних',
        'system-check.desc.PHP': 'Мова програмування PHP',
        'system-check.desc.PHP_Ext_pdo': 'Розширення PDO для роботи з базами даних',
        'system-check.desc.PHP_Ext_pdo_mysql': 'Драйвер PDO для MySQL',
        'system-check.desc.PHP_Ext_mbstring': 'Розширення для роботи з багатобайтовими рядками',
        'system-check.desc.PHP_Ext_json': 'Розширення для роботи з JSON',
        'system-check.desc.PHP_Ext_openssl': 'Розширення для шифрування та безпеки',
        'system-check.desc.ConfigDir': 'Директорія для зберігання конфігурації CMS (database.ini тощо)',
        'system-check.desc.CacheDir': 'Директорія для зберігання кешу',
        'system-check.desc.SessionsDir': 'Директорія для зберігання сесій',
        'system-check.desc.LogsDir': 'Директорія для зберігання логів',
        'system-check.desc.UploadsDir': 'Директорія для завантажених файлів',
        'system-check.desc.PluginsDir': 'Директорія для плагінів',
        'system-check.desc.ThemesDir': 'Директорія для тем оформлення',
        'system-check.desc.TempDir': 'Тимчасова директорія для завантаження файлів',
        'system-check.desc.RootHtaccess': 'Головний файл .htaccess в корені сайту',
        'system-check.desc.SessionSupport': 'Підтримка сесій PHP',
        'system-check.desc.JsonSupport': 'Підтримка роботи з JSON',
        'system-check.desc.FileFunctions': 'Функції для роботи з файлами',
        'system-check.desc.TableDefinitions': 'Визначення таблиць бази даних',
        'system-check.path': 'Шлях:',
        'system-check.created_auto': '✓ Створено автоматично',
        'sidebar.title': 'Кроки установки',
        'step.welcome.title': 'Привітання',
        'step.welcome.sub': 'Опис системи',
        'step.system-check.title': 'Перевірка системи',
        'step.system-check.sub': 'PHP, розширення, директорії',
        'step.database.title': 'База даних',
        'step.database.sub': 'Параметри підключення',
        'step.tables.title': 'Створення таблиць',
        'step.tables.sub': 'Підготовка БД',
        'step.user.title': 'Адміністратор',
        'step.user.sub': 'Обліковий запис',
        'step.success.title': 'Готово',
        'step.success.sub': 'Завершення',
        'success.title': 'Установка завершена!',
        'success.text': 'Flowaxy CMS успішно встановлена. Ви можете увійти в адмін-панель, використовуючи дані нижче.',
        'success.account_created': 'Обліковий запис адміністратора створено',
        'success.login': 'Логін:',
        'success.email': 'Email:',
        'success.password_hint': 'Пароль (збережіть його зараз, повторно він показаний не буде):',
        'success.go_to_admin': 'Перейти в адмінку'
    },
    ru: {
        'logo.alt': 'Flowaxy CMS',
        'header.title': 'Flowaxy CMS',
        'header.subtitle': 'Мастер установки',
        'welcome.title': 'Добро пожаловать в Flowaxy CMS!',
        'welcome.text1': 'Flowaxy CMS — это современная и мощная система управления контентом, созданная для разработчиков и бизнеса, которые требуют гибкости, производительности и полного контроля над своими веб-проектами.',
        'welcome.text2': 'Система сочетает в себе простоту использования с мощными возможностями кастомизации, обеспечивая быструю установку, удобное управление контентом и высокую производительность даже при больших нагрузках.',
        'features.title': 'Основные возможности:',
        'feature1': '<strong>Модульная архитектура</strong> — расширяйте функциональность с помощью плагинов и модулей без изменения ядра системы',
        'feature2': '<strong>Система тем и шаблонов</strong> — создавайте уникальный дизайн с полной поддержкой кастомизации и адаптивности',
        'feature3': '<strong>Безопасность и оптимизация</strong> — встроенные механизмы защиты, кеширования и оптимизации запросов к базе данных',
        'feature4': '<strong>Удобная админ-панель</strong> — интуитивный интерфейс для управления контентом, пользователями и настройками',
        'feature5': '<strong>Многоязычность</strong> — полная поддержка интернационализации и локализации для глобальных проектов',
        'feature6': '<strong>API и интеграции</strong> — RESTful API для интеграции с внешними сервисами и мобильными приложениями',
        'feature7': '<strong>Ролевая модель доступа</strong> — гибкая система ролей и разрешений для точного контроля доступа',
        'promo.title': 'Нужна помощь с разработкой?',
        'promo.text': 'Flowaxy — это не только CMS, но и профессиональная веб-студия, которая предоставляет полный спектр услуг: разработка сайтов и веб-приложений, интеграция со сторонними сервисами, техническая поддержка и консультирование. Обратитесь к нам за профессиональной помощью и реализацией ваших идей!',
        'promo.link': 'Посетить Flowaxy.com →',
        'button.start': 'Начать установку',
        'label.host': 'Хост',
        'label.port': 'Порт',
        'label.database': 'База данных',
        'label.username': 'Пользователь',
        'label.password': 'Пароль',
        'label.password_confirm': 'Подтверждение пароля',
        'label.email': 'Email',
        'label.mysql_version': 'Версия MySQL',
        'label.charset': 'Кодировка (Charset)',
        'label.charset_hint': 'Рекомендуется utf8mb4 для поддержки emoji и всех Unicode символов',
        'label.version': 'Версия:',
        'label.count': 'Количество:',
        'button.test': 'Тестировать подключение',
        'button.save': 'Сохранить и продолжить',
        'button.continue': 'Продолжить',
        'button.retry': 'Повторить',
        'button.retry_check': 'Повторить проверку',
        'button.create': 'Создать и завершить',
        'test.success': 'Подключение успешно!',
        'test.error': 'Ошибка подключения: ',
        'test.testing': 'Проверка подключения...',
        'progress.text': 'Подготовка...',
        'progress.retrying': 'Повторяется...',
        'table.creating': 'Создание',
        'table.success': 'Создано',
        'table.error': 'Ошибка',
        'error.text': 'Ошибка создания пользователя',
        'system-check.title': 'Проверка системы',
        'system-check.text': 'Проверяем наличие необходимых компонентов для установки системы.',
        'system-check.status-title': 'Статус проверки',
        'system-check.status-success': 'Все проверки пройдены успешно',
        'system-check.status-errors': 'Найдено ошибок: ',
        'system-check.status-ok-count': 'Успешно:',
        'system-check.status-warning-count': 'Предупреждений:',
        'system-check.status-error-count': 'Ошибок:',
        'system-check.not-run': 'Проверка не выполнена',
        'system-check.errors-title': 'Ошибки:',
        'system-check.warnings-title': 'Предупреждения:',
        'button.back': 'Назад',
        'system-check.desc.BaseModule': 'Базовый класс модулей системы',
        'system-check.desc.InstallerManager': 'Менеджер установки системы',
        'system-check.desc.DatabaseHelper': 'Вспомогательный класс для работы с базой данных',
        'system-check.desc.PHP': 'Язык программирования PHP',
        'system-check.desc.PHP_Ext_pdo': 'Расширение PDO для работы с базами данных',
        'system-check.desc.PHP_Ext_pdo_mysql': 'Драйвер PDO для MySQL',
        'system-check.desc.PHP_Ext_mbstring': 'Расширение для работы с многобайтовыми строками',
        'system-check.desc.PHP_Ext_json': 'Расширение для работы с JSON',
        'system-check.desc.PHP_Ext_openssl': 'Расширение для шифрования и безопасности',
        'system-check.desc.ConfigDir': 'Директория для хранения конфигурации CMS (database.ini и др.)',
        'system-check.desc.CacheDir': 'Директория для хранения кеша',
        'system-check.desc.SessionsDir': 'Директория для хранения сессий',
        'system-check.desc.LogsDir': 'Директория для хранения логов',
        'system-check.desc.UploadsDir': 'Директория для загруженных файлов',
        'system-check.desc.PluginsDir': 'Директория для плагинов',
        'system-check.desc.ThemesDir': 'Директория для тем оформления',
        'system-check.desc.TempDir': 'Временная директория для загрузки файлов',
        'system-check.desc.RootHtaccess': 'Главный файл .htaccess в корне сайта',
        'system-check.desc.SessionSupport': 'Поддержка сессий PHP',
        'system-check.desc.JsonSupport': 'Поддержка работы с JSON',
        'system-check.desc.FileFunctions': 'Функции для работы с файлами',
        'system-check.desc.TableDefinitions': 'Определения таблиц базы данных',
        'system-check.path': 'Путь:',
        'system-check.created_auto': '✓ Создано автоматически',
        'sidebar.title': 'Шаги установки',
        'step.welcome.title': 'Приветствие',
        'step.welcome.sub': 'Описание системы',
        'step.system-check.title': 'Проверка системы',
        'step.system-check.sub': 'PHP, расширения, директории',
        'step.database.title': 'База данных',
        'step.database.sub': 'Параметры подключения',
        'step.tables.title': 'Создание таблиц',
        'step.tables.sub': 'Подготовка БД',
        'step.user.title': 'Администратор',
        'step.user.sub': 'Учетная запись',
        'step.success.title': 'Готово',
        'step.success.sub': 'Завершение',
        'success.title': 'Установка завершена!',
        'success.text': 'Flowaxy CMS успешно установлена. Вы можете войти в админ-панель, используя данные ниже.',
        'success.account_created': 'Учетная запись администратора создана',
        'success.login': 'Логин:',
        'success.email': 'Email:',
        'success.password_hint': 'Пароль (сохраните его сейчас, повторно он показан не будет):',
        'success.go_to_admin': 'Перейти в админку'
    },
    en: {
        'logo.alt': 'Flowaxy CMS',
        'header.title': 'Flowaxy CMS',
        'header.subtitle': 'Installation Wizard',
        'welcome.title': 'Welcome to Flowaxy CMS!',
        'welcome.text1': 'Flowaxy CMS is a modern and powerful content management system created for developers and businesses that require flexibility, performance, and full control over their web projects.',
        'welcome.text2': 'The system combines ease of use with powerful customization capabilities, providing quick installation, convenient content management, and high performance even under heavy loads.',
        'features.title': 'Key Features:',
        'feature1': '<strong>Modular Architecture</strong> — extend functionality with plugins and modules without changing the core system',
        'feature2': '<strong>Theme & Template System</strong> — create unique designs with full customization and responsiveness support',
        'feature3': '<strong>Security & Optimization</strong> — built-in protection mechanisms, caching, and database query optimization',
        'feature4': '<strong>Convenient Admin Panel</strong> — intuitive interface for managing content, users, and settings',
        'feature5': '<strong>Multilingual Support</strong> — full internationalization and localization support for global projects',
        'feature6': '<strong>API & Integrations</strong> — RESTful API for integration with external services and mobile applications',
        'feature7': '<strong>Role-Based Access Control</strong> — flexible role and permission system for precise access control',
        'promo.title': 'Need development help?',
        'promo.text': 'Flowaxy is also a web studio that provides website development, integration and support services. Contact us for professional help!',
        'promo.link': 'Visit Flowaxy.com →',
        'button.start': 'Start Installation',
        'label.host': 'Host',
        'label.port': 'Port',
        'label.database': 'Database',
        'label.username': 'Username',
        'label.password': 'Password',
        'label.password_confirm': 'Confirm Password',
        'label.email': 'Email',
        'label.mysql_version': 'MySQL Version',
        'label.charset': 'Charset',
        'label.charset_hint': 'utf8mb4 is recommended for emoji and full Unicode support',
        'label.version': 'Version:',
        'label.count': 'Count:',
        'button.test': 'Test Connection',
        'button.save': 'Save & Continue',
        'button.continue': 'Continue',
        'button.retry': 'Retry',
        'button.retry_check': 'Retry Check',
        'button.create': 'Create & Finish',
        'test.success': 'Connection successful!',
        'test.error': 'Connection error: ',
        'test.testing': 'Testing connection...',
        'progress.text': 'Preparing...',
        'progress.retrying': 'Retrying...',
        'table.creating': 'Creating',
        'table.success': 'Created',
        'table.error': 'Error',
        'error.text': 'Error creating user',
        'system-check.title': 'System Check',
        'system-check.text': 'We are verifying that all required components for installing the system are available.',
        'system-check.status-title': 'Check Status',
        'system-check.status-success': 'All checks passed successfully',
        'system-check.status-errors': 'Errors found: ',
        'system-check.status-ok-count': 'OK:',
        'system-check.status-warning-count': 'Warnings:',
        'system-check.status-error-count': 'Errors:',
        'system-check.not-run': 'System check was not run',
        'system-check.errors-title': 'Errors:',
        'system-check.warnings-title': 'Warnings:',
        'button.back': 'Back',
        'system-check.desc.BaseModule': 'Base module class of the system',
        'system-check.desc.InstallerManager': 'Installer module that manages system installation',
        'system-check.desc.DatabaseHelper': 'Helper class for working with the database',
        'system-check.desc.PHP': 'PHP programming language',
        'system-check.desc.PHP_Ext_pdo': 'PDO extension for database access',
        'system-check.desc.PHP_Ext_pdo_mysql': 'PDO driver for MySQL',
        'system-check.desc.PHP_Ext_mbstring': 'Extension for working with multibyte strings',
        'system-check.desc.PHP_Ext_json': 'Extension for working with JSON',
        'system-check.desc.PHP_Ext_openssl': 'Extension for encryption and security',
        'system-check.desc.ConfigDir': 'Directory for storing CMS configuration (database.ini, etc.)',
        'system-check.desc.CacheDir': 'Directory for storing cache',
        'system-check.desc.SessionsDir': 'Directory for storing PHP sessions',
        'system-check.desc.LogsDir': 'Directory for storing log files',
        'system-check.desc.UploadsDir': 'Directory for uploaded files',
        'system-check.desc.PluginsDir': 'Directory for plugins',
        'system-check.desc.ThemesDir': 'Directory for themes',
        'system-check.desc.TempDir': 'Temporary directory for file uploads',
        'system-check.desc.RootHtaccess': 'Main .htaccess file in the site root',
        'system-check.desc.SessionSupport': 'PHP session support',
        'system-check.desc.JsonSupport': 'Support for JSON functions',
        'system-check.desc.FileFunctions': 'Functions for working with files',
        'system-check.desc.TableDefinitions': 'Definitions of database tables used by CMS',
        'system-check.path': 'Path:',
        'system-check.created_auto': '✓ Created automatically',
        'sidebar.title': 'Installation Steps',
        'step.welcome.title': 'Welcome',
        'step.welcome.sub': 'System Description',
        'step.system-check.title': 'System Check',
        'step.system-check.sub': 'PHP, extensions, directories',
        'step.database.title': 'Database',
        'step.database.sub': 'Connection Settings',
        'step.tables.title': 'Tables',
        'step.tables.sub': 'Create Database Tables',
        'step.user.title': 'Admin User',
        'step.user.sub': 'Create Admin Account',
        'step.success.title': 'Complete',
        'step.success.sub': 'Installation Finished',
        'success.title': 'Installation Complete!',
        'success.text': 'Flowaxy CMS has been successfully installed. You can log in to the admin panel using the information below.',
        'success.account_created': 'Administrator account created',
        'success.login': 'Username:',
        'success.email': 'Email:',
        'success.password_hint': 'Password (save it now, it will not be shown again):',
        'success.go_to_admin': 'Go to Admin Panel'
    }
};

// Определение языка браузера (по умолчанию английский)
function getBrowserLang() {
    const lang = navigator.language || navigator.userLanguage;
    const code = lang.split('-')[0].toLowerCase();
    return ['uk', 'ru', 'en'].includes(code) ? code : 'en';
}

// Получение сохраненного языка или языка браузера
function getSavedLang() {
    const saved = localStorage.getItem('installer_lang');
    if (saved && ['uk', 'ru', 'en'].includes(saved)) {
        return saved;
    }
    return getBrowserLang();
}

// Сохранение выбранного языка
function saveLang(lang) {
    localStorage.setItem('installer_lang', lang);
}

// Применение переводов
function applyTranslations(lang) {
    const trans = translations[lang] || translations.en;
    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (trans[key]) {
            if (el.tagName === 'INPUT' && el.type === 'submit') {
                el.value = trans[key];
            } else if (el.tagName === 'IMG' && el.hasAttribute('alt')) {
                el.alt = trans[key];
            } else if (el.tagName === 'BUTTON' || el.tagName === 'A') {
                // Для кнопок и ссылок проверяем, есть ли HTML-теги
                if (trans[key].includes('<')) {
                    el.innerHTML = trans[key];
                } else {
                    el.textContent = trans[key];
                }
            } else {
                // Для остальных элементов проверяем, есть ли HTML-теги
                if (trans[key].includes('<')) {
                    el.innerHTML = trans[key];
                } else {
                    el.textContent = trans[key];
                }
            }
        }
    });
    document.title = 'Flowaxy CMS Installation';
    document.documentElement.lang = lang;
    
    // Обновляем активную кнопку языка
    document.querySelectorAll('.lang-btn').forEach(btn => {
        if (btn.getAttribute('data-lang') === lang) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
}

// Переключение языка
function switchLanguage(newLang) {
    if (!['uk', 'ru', 'en'].includes(newLang)) {
        return;
    }
    saveLang(newLang);
    applyTranslations(newLang);
}

// Инициализация
let lang = getSavedLang();
applyTranslations(lang);

// Обработчики для кнопок переключения языков
document.querySelectorAll('.lang-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const newLang = this.getAttribute('data-lang');
        switchLanguage(newLang);
    });
});

// Тестирование подключения (шаг database)
const testBtn = document.getElementById('testBtn');
if (testBtn) {
    testBtn.addEventListener('click', async function() {
        const form = document.getElementById('databaseForm');
        // Явно формируем тело запроса, чтобы гарантировать корректную
        // передачу всех полей (host, port, name, user, pass, version, charset)
        const params = new URLSearchParams();
        params.append('db_host', (form.elements['db_host']?.value || '').trim());
        params.append('db_port', (form.elements['db_port']?.value || '').trim());
        params.append('db_name', (form.elements['db_name']?.value || '').trim());
        params.append('db_user', (form.elements['db_user']?.value || '').trim());
        params.append('db_pass', form.elements['db_pass']?.value || '');
        params.append('db_version', (form.elements['db_version']?.value || '').trim());
        params.append('db_charset', (form.elements['db_charset']?.value || '').trim() || 'utf8mb4');
        const resultDiv = document.getElementById('testResult');
        const saveBtn = document.getElementById('saveBtn');
        
        resultDiv.className = 'test-result testing';
        resultDiv.textContent = translations[lang]['test.testing'];
        testBtn.disabled = true;
        saveBtn.disabled = true;
        
        try {
            const url = '/install?action=test_db&' + params.toString();
            const response = await fetch(url, {
                method: 'GET',
                credentials: 'same-origin'
            });

            // Читаем ответ как текст и пробуем распарсить JSON вручную,
            // чтобы избежать ошибок вида "Unexpected token '<'" и
            // показать реальное сообщение от сервера
            const rawText = await response.text();
            let data;
            try {
                data = JSON.parse(rawText);
            } catch (parseError) {
                resultDiv.className = 'test-result error';
                resultDiv.textContent =
                    translations[lang]['test.error'] +
                    ' ' +
                    (rawText || parseError.message).substring(0, 200);
                saveBtn.disabled = true;
                return;
            }
            
            if (data.success) {
                // Проверяем соответствие версий и кодировки
                let warnings = [];
                let errors = [];
                
                if (data.version_warning && !data.version_match) {
                    // Автоматически обновляем селект на правильную версию
                    const versionSelect = document.getElementById('dbVersion');
                    if (versionSelect && data.detected_version) {
                        versionSelect.value = data.detected_version;
                    }
                    warnings.push(data.version_warning);
                }
                
                // Проверяем ошибки кодировки (критично)
                if (data.charset_error) {
                    errors.push(data.charset_error);
                    saveBtn.disabled = true;
                } else if (data.charset_warning) {
                    warnings.push(data.charset_warning);
                }
                
                if (errors.length > 0) {
                    resultDiv.className = 'test-result error';
                    resultDiv.innerHTML = '<strong>Помилка підключення!</strong><br>' + 
                        errors.map(e => '<small style="color: #c53030; margin-top: 4px; display: block; font-weight: 600;">' + e + '</small>').join('');
                    saveBtn.disabled = true;
                } else if (warnings.length > 0) {
                    resultDiv.className = 'test-result warning';
                    resultDiv.innerHTML = '<strong>' + translations[lang]['test.success'] + '</strong><br>' + 
                        warnings.map(w => '<small style="color: #d69e2e; margin-top: 4px; display: block;">' + w + '</small>').join('');
                    saveBtn.disabled = false;
                } else {
                    resultDiv.className = 'test-result success';
                    let message = translations[lang]['test.success'];
                    if (data.mysql_version) {
                        message += ' (MySQL ' + data.mysql_version;
                        if (data.db_charset) {
                            message += ', ' + data.db_charset;
                            if (data.db_collation) {
                                message += ' / ' + data.db_collation;
                            }
                        }
                        message += ')';
                    }
                    resultDiv.textContent = message;
                    saveBtn.disabled = false;
                }
            } else {
                resultDiv.className = 'test-result error';
                resultDiv.textContent = translations[lang]['test.error'] + (data.message || 'Unknown error');
                saveBtn.disabled = true;
            }
        } catch (error) {
            resultDiv.className = 'test-result error';
            resultDiv.textContent = translations[lang]['test.error'] + error.message;
        } finally {
            testBtn.disabled = false;
        }
    });
}

// Создание таблиц (шаг tables) - только если мы на шаге tables
const tablesList = document.getElementById('tablesList');
// Определяем текущий шаг по активному блоку разметки (без PHP в JS)
const activeStepElement = document.querySelector('.step-content.active');
const currentStep = activeStepElement ? activeStepElement.id.replace('step-', '') : 'welcome';

if (tablesList && currentStep === 'tables') {
    // Список таблиц для создания (включаючи таблиці ролей)
    // Порядок важен: сначала базовые таблицы, затем roles и permissions, затем зависимые таблицы
    const tables = ['users', 'site_settings', 'plugins', 'plugin_settings', 'theme_settings', 'roles', 'permissions'];
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');
    const continueBtn = document.getElementById('continueBtn');
    
    tables.forEach(table => {
        const li = document.createElement('li');
        li.id = `table-${table}`;
        li.innerHTML = `
            <div class="table-icon creating"></div>
            <span>${table}</span>
        `;
        tablesList.appendChild(li);
    });
    
    let failedTables = [];
    let successCount = 0;
    
    async function createTable(table, retry = false) {
        const li = document.getElementById(`table-${table}`);
        const icon = li.querySelector('.table-icon');
        
        // Если это повторная попытка, сбрасываем состояние
        if (retry) {
            icon.className = 'table-icon creating';
            li.classList.remove('error');
            li.classList.add('creating');
            const errorMsg = li.querySelector('.table-error');
            if (errorMsg) {
                errorMsg.remove();
            }
        }
        
        await new Promise(resolve => setTimeout(resolve, 300));
        
        try {
            const response = await fetch('/install?action=create_table', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ table: table }),
                credentials: 'same-origin'
            });

            const rawText = await response.text();

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${rawText.substring(0, 200)}`);
            }

            let data;
            try {
                if (!rawText || rawText.trim() === '') {
                    throw new Error('Пустой ответ от сервера');
                }
                data = JSON.parse(rawText);
            } catch (parseError) {
                throw new Error('Ошибка парсинга JSON: ' + parseError.message + '. Ответ: ' + rawText.substring(0, 300));
            }
            
            if (data.success) {
                // Проверяем, существует ли таблица уже
                if (data.exists) {
                    // Таблица уже существует - показываем желтым
                    icon.className = 'table-icon exists';
                    li.classList.remove('creating', 'error', 'success');
                    li.classList.add('exists');
                    
                    // Добавляем подсказку, что таблица уже существует
                    let existsMsg = li.querySelector('.table-exists');
                    if (!existsMsg) {
                        existsMsg = document.createElement('div');
                        existsMsg.className = 'table-exists';
                        existsMsg.style.cssText = 'font-size: 12px; color: #d69e2e; margin-top: 4px; padding: 4px 8px; background: #fffaf0; border-radius: 4px;';
                        existsMsg.textContent = data.message || 'Таблиця вже існує';
                        li.appendChild(existsMsg);
                    }
                } else {
                    // Таблица создана успешно - показываем зеленым
                    icon.className = 'table-icon success';
                    li.classList.remove('creating', 'error', 'exists');
                    li.classList.add('success');
                    
                    // Удаляем сообщение о существовании, если было
                    const existsMsg = li.querySelector('.table-exists');
                    if (existsMsg) {
                        existsMsg.remove();
                    }
                }
                
                // Удаляем сообщение об ошибке, если было
                const errorMsg = li.querySelector('.table-error');
                if (errorMsg) {
                    errorMsg.remove();
                }
                
                successCount++;
                // Удаляем из списка неуспешных, если была там
                failedTables = failedTables.filter(t => t !== table);
                return true;
            } else {
                icon.className = 'table-icon error';
                li.classList.remove('creating');
                li.classList.add('error');
                
                // Отображаем детальную информацию об ошибке
                let errorMsg = li.querySelector('.table-error');
                if (!errorMsg) {
                    errorMsg = document.createElement('div');
                    errorMsg.className = 'table-error';
                    errorMsg.style.cssText = 'font-size: 12px; color: #c53030; margin-top: 4px; padding: 4px 8px; background: #fff5f5; border-radius: 4px; word-break: break-word;';
                    li.appendChild(errorMsg);
                }
                
                let errorText = data.message || 'Помилка створення таблиці';
                
                // Добавляем детальную информацию, если доступна
                if (data.pdoCode || data.pdoErrorInfo) {
                    errorText += '<br><small>';
                    if (data.pdoCode) {
                        errorText += 'Код помилки: ' + data.pdoCode + '<br>';
                    }
                    if (data.pdoErrorInfo && Array.isArray(data.pdoErrorInfo) && data.pdoErrorInfo.length > 2) {
                        errorText += 'SQL State: ' + data.pdoErrorInfo[0] + '<br>';
                        errorText += 'Driver Error: ' + data.pdoErrorInfo[1] + '<br>';
                        errorText += 'Driver Message: ' + data.pdoErrorInfo[2];
                    }
                    errorText += '</small>';
                }
                
                // Добавляем отладочную информацию в режиме разработки
                if (data.debug && (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1')) {
                    errorText += '<br><small style="color: #718096;">Debug: ' + JSON.stringify(data.debug) + '</small>';
                }
                
                errorMsg.innerHTML = errorText;
                
                // Добавляем в список неуспешных
                if (!failedTables.includes(table)) {
                    failedTables.push(table);
                }
                
                // Логируем ошибку в консоль
                console.error('Ошибка создания таблицы', table, data);
                return false;
            }
        } catch (error) {
            icon.className = 'table-icon error';
            li.classList.remove('creating');
            li.classList.add('error');
            
            // Отображаем ошибку сети
            let errorMsg = li.querySelector('.table-error');
            if (!errorMsg) {
                errorMsg = document.createElement('div');
                errorMsg.className = 'table-error';
                errorMsg.style.cssText = 'font-size: 12px; color: #c53030; margin-top: 4px; padding: 4px 8px; background: #fff5f5; border-radius: 4px; word-break: break-word;';
                li.appendChild(errorMsg);
            }
            errorMsg.textContent = 'Ошибка сети: ' + (error.message || 'Неизвестная ошибка');
            
            // Добавляем в список неуспешных
            if (!failedTables.includes(table)) {
                failedTables.push(table);
            }
            
            console.error('Ошибка сети при создании таблицы', table, error);
            return false;
        }
    }
    
    async function createTables() {
        successCount = 0;
        failedTables = [];
        
        for (let i = 0; i < tables.length; i++) {
            const table = tables[i];
            await createTable(table);
            
            const progress = Math.round(((i + 1) / tables.length) * 100);
            progressFill.style.width = progress + '%';
            progressText.textContent = translations[lang]['progress.text'].replace('...', `: ${i + 1}/${tables.length}`);
        }
        
        // Показываем кнопки действий
        const tablesActions = document.getElementById('tablesActions');
        tablesActions.style.display = 'block';
        
        // Если все таблицы успешно созданы
        if (failedTables.length === 0) {
            continueBtn.style.display = 'block';
            document.getElementById('retryBtn').style.display = 'none';
            progressText.textContent = translations[lang]['progress.text'].replace('...', ': Завершено!');
        } else {
            // Есть ошибки - показываем кнопку повтора
            continueBtn.style.display = 'none';
            document.getElementById('retryBtn').style.display = 'block';
            progressText.textContent = translations[lang]['progress.text'].replace('...', `: ${successCount}/${tables.length} успішно, ${failedTables.length} помилок`);
        }
    }
    
    // Функция для повторной попытки создания неуспешных таблиц
    window.retryFailedTables = async function() {
        if (failedTables.length === 0) {
            return;
        }
        
        const retryBtn = document.getElementById('retryBtn');
        retryBtn.disabled = true;
        retryBtn.textContent = translations[lang]['progress.retrying'] || 'Повторюється...';
        
        progressText.textContent = translations[lang]['progress.text'].replace('...', ': ' + (translations[lang]['progress.retrying'] || 'Повторна спроба...'));
        
        // Повторяем только неуспешные таблицы
        for (const table of [...failedTables]) {
            await createTable(table, true);
        }
        
        // Обновляем прогресс
        const totalSuccess = tables.length - failedTables.length;
        progressFill.style.width = Math.round((totalSuccess / tables.length) * 100) + '%';
        
        // Проверяем результат
        if (failedTables.length === 0) {
            continueBtn.style.display = 'block';
            retryBtn.style.display = 'none';
            progressText.textContent = translations[lang]['progress.text'].replace('...', ': Завершено!');
        } else {
            retryBtn.disabled = false;
            retryBtn.textContent = translations[lang]['button.retry'] || 'Повторити';
            progressText.textContent = translations[lang]['progress.text'].replace('...', `: ${totalSuccess}/${tables.length} успішно, ${failedTables.length} помилок`);
        }
    };
    
    createTables();
}

// Валидация паролей (шаг user)
const userForm = document.getElementById('userForm');
if (userForm) {
    userForm.addEventListener('submit', function(e) {
        const password = document.querySelector('input[name="password"]').value;
        const passwordConfirm = document.querySelector('input[name="password_confirm"]').value;
        
        if (password !== passwordConfirm) {
            e.preventDefault();
            alert(translations[lang]['error.text'] || 'Passwords do not match');
            return false;
        }
        
        if (password.length < 8) {
            e.preventDefault();
            alert(translations[lang]['error.text'] || 'Password must be at least 8 characters');
            return false;
        }
    });
}
