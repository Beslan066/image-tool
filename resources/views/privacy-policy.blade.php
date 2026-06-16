<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Политика конфиденциальности | ImageTool</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body.dark {
            background-color: #0f0f1a !important;
        }
        .dark .bg-white { background-color: #16213e !important; }
        .dark .bg-gray-50 { background-color: #16213e !important; }
        .dark .bg-gray-100 { background-color: #1a1a2e !important; }
        .dark .text-gray-600 { color: #aaa !important; }
        .dark .text-gray-700 { color: #c0c0c0 !important; }
        .dark .text-gray-800 { color: #ddd !important; }
        .dark .border-gray-200 { border-color: #2c3e66 !important; }
        .dark .border-gray-300 { border-color: #2c3e66 !important; }

        .theme-switch {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 100;
            background: #22c55e;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        .theme-switch:hover {
            transform: scale(1.1);
        }
        .theme-switch svg {
            width: 28px;
            height: 28px;
            stroke: white;
        }
        body, body * {
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }
    </style>
</head>
<body class="bg-gray-50" x-data="{ isDarkMode: localStorage.getItem('darkMode') === 'true' || false }" :class="{'dark': isDarkMode}">

<!-- Переключатель темы -->
<div @click="isDarkMode = !isDarkMode; localStorage.setItem('darkMode', isDarkMode)" class="theme-switch">
    <svg x-show="!isDarkMode" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
    </svg>
    <svg x-show="isDarkMode" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
    </svg>
</div>

<div class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-blue-600 px-6 py-8">
            <h1 class="text-3xl font-bold text-white">Политика конфиденциальности</h1>
            <p class="text-green-100 mt-2">Последнее обновление: {{ date('d.m.Y') }}</p>
        </div>

        <div class="p-6 sm:p-8 space-y-6 text-gray-700 dark:text-gray-300">

            <!-- 1. Общие положения -->
            <section>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">1. Общие положения</h2>
                <p class="mb-3">Настоящая Политика конфиденциальности (далее — «Политика») регулирует порядок обработки и защиты информации о пользователях сервиса «ImageTool — Редактор изображений» (далее — «Сервис», «Мы»).</p>
                <p class="mb-3">Используя Сервис, Вы даете согласие на сбор и использование информации в соответствии с настоящей Политикой.</p>
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 p-4 rounded">
                    <p class="text-sm text-yellow-700 dark:text-yellow-400">⚠️ <strong>Важно:</strong> Сервис предназначен для лиц старше 18 лет. Использование Сервиса несовершеннолетними допускается только с согласия родителей или законных представителей.</p>
                </div>
            </section>

            <!-- 2. Какие данные мы собираем -->
            <section>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">2. Какие данные мы собираем</h2>

                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mt-4 mb-2">2.1. Техническая информация (собирается автоматически)</h3>
                <ul class="list-disc pl-6 space-y-1">
                    <li>IP-адрес</li>
                    <li>Тип браузера и версия</li>
                    <li>Операционная система</li>
                    <li>Дата и время посещения</li>
                    <li>Страница, с которой Вы перешли на наш Сервис (Referer URL)</li>
                    <li>Действия на сайте (клики, время на странице)</li>
                </ul>

                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mt-4 mb-2">2.2. Информация, которую Вы предоставляете добровольно</h3>
                <ul class="list-disc pl-6 space-y-1">
                    <li><strong>Загружаемые изображения</strong> — только на время обработки (см. раздел 4)</li>
                    <li>Данные, которые Вы указываете при регистрации (email, имя)</li>
                    <li>Данные платежных карт при оплате Premium (обрабатываются через ЮKassa, мы не храним)</li>
                    <li>Информация, которую Вы указываете при обращении в поддержку</li>
                </ul>

                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mt-4 mb-2">2.3. Мы НЕ собираем</h3>
                <ul class="list-disc pl-6 space-y-1 text-green-600 dark:text-green-400">
                    <li>Паспортные данные и другие документы</li>
                    <li>Биометрические данные без Вашего явного согласия</li>
                    <li>Данные о геолокации (если Вы не разрешили это в браузере)</li>
                    <li>Историю переписки в мессенджерах</li>
                </ul>
            </section>

            <!-- 3. Цели обработки -->
            <section>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">3. Цели обработки данных</h2>
                <ul class="list-disc pl-6 space-y-2">
                    <li><strong>Обеспечение работы Сервиса</strong> — обработка загруженных изображений</li>
                    <li><strong>Улучшение качества</strong> — анализ статистики посещений и поведения пользователей</li>
                    <li><strong>Безопасность</strong> — предотвращение злоупотреблений, DDoS-атак и технических сбоев</li>
                    <li><strong>Поддержка пользователей</strong> — ответы на обращения</li>
                    <li><strong>Юридические обязательства</strong> — выполнение требований законодательства РФ</li>
                    <li><strong>Маркетинг</strong> — информирование о новых функциях (только с Вашего согласия)</li>
                </ul>
            </section>

            <!-- 4. Обработка изображений -->
            <section class="bg-red-50 dark:bg-red-900/10 border-l-4 border-red-500 p-4 rounded">
                <h2 class="text-2xl font-bold text-red-700 dark:text-red-400 mb-4">⚠️ 4. Обработка изображений</h2>

                <div class="space-y-3">
                    <p><strong class="text-red-700 dark:text-red-400">Критически важно!</strong> Мы НЕ ХРАНИМ загруженные Вами изображения.</p>

                    <ul class="list-disc pl-6 space-y-2">
                        <li>Процесс обработки изображений происходит <strong>исключительно в оперативной памяти сервера</strong></li>
                        <li>После обработки и скачивания результата:
                            <ul class="list-disc pl-6 mt-1">
                                <li>✅ Оригинал Вашего изображения <strong>немедленно удаляется</strong> с сервера</li>
                                <li>✅ Обработанный результат <strong>сохраняется временно</strong> (не более 5 минут) и автоматически удаляется</li>
                            </ul>
                        </li>
                        <li>Мы НЕ используем Ваши изображения для:
                            <ul class="list-disc pl-6 mt-1">
                                <li>❌ Обучения нейросетей</li>
                                <li>❌ Публикации где-либо</li>
                                <li>❌ Передачи третьим лицам</li>
                                <li>❌ Анализа содержания (кроме технической необходимости для обработки)</li>
                            </ul>
                        </li>
                    </ul>

                    <div class="bg-red-100 dark:bg-red-900/30 p-3 rounded mt-3">
                        <p class="text-sm text-red-700 dark:text-red-300">🔴 <strong>Предупреждение:</strong> Не загружайте изображения, содержащие конфиденциальную информацию, паспортные данные, банковские карты или другие личные данные.</p>
                    </div>
                </div>
            </section>

            <!-- 5. Риски и меры безопасности -->
            <section>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">5. Риски и меры безопасности</h2>

                <div class="space-y-4">
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">5.1. Технические риски</h3>
                        <ul class="list-disc pl-6 space-y-1">
                            <li><strong>Сбой сервера:</strong> В случае технического сбоя обработка изображения может быть прервана. Рекомендуем сохранять оригиналы.</li>
                            <li><strong>Атаки злоумышленников:</strong> Мы используем современные методы защиты, но 100% гарантии безопасности в интернете не существует.</li>
                            <li><strong>Перехват данных:</strong> Все данные передаются по защищенному протоколу HTTPS, но риск перехвата при использовании общественных Wi-Fi существует.</li>
                        </ul>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">5.2. Правовые риски</h3>
                        <ul class="list-disc pl-6 space-y-1">
                            <li><strong>Загрузка запрещенного контента:</strong> Вы несете полную ответственность за загружаемые изображения. Запрещены: порнография, насилие, экстремизм, нарушение авторских прав.</li>
                            <li><strong>Изображения людей:</strong> Загружая фото людей, Вы обязаны получить их согласие на обработку.</li>
                            <li><strong>Коммерческое использование:</strong> Сервис предназначен для личного использования. Коммерческое использование регулируется отдельным соглашением.</li>
                        </ul>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">5.3. Меры безопасности</h3>
                        <ul class="list-disc pl-6 space-y-1">
                            <li>✅ Шифрование при передаче данных (HTTPS/TLS 1.3)</li>
                            <li>✅ Немедленное удаление изображений после обработки</li>
                            <li>✅ Регулярное обновление программного обеспечения</li>
                            <li>✅ Ограниченный доступ к серверам (только авторизованный персонал)</li>
                            <li>✅ Двухфакторная аутентификация для администраторов</li>
                            <li>✅ Регулярное резервное копирование (только технических данных, не изображений)</li>
                            <li>✅ Система обнаружения вторжений (IDS)</li>
                            <li>✅ Защита от DDoS-атак</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- 6. Передача данных третьим лицам -->
            <section>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">6. Передача данных третьим лицам</h2>
                <ul class="list-disc pl-6 space-y-2">
                    <li><strong>ЮKassa</strong> — для обработки платежей при оформлении Premium подписки. Передаются: email, сумма платежа, идентификатор пользователя. Данные карт обрабатываются напрямую ЮKassa, мы их не видим.</li>
                    <li><strong>Правоохранительные органы</strong> — только по официальному запросу в соответствии с законодательством РФ.</li>
                    <li><strong>Аналитические сервисы</strong> — анонимная статистика через Yandex.Metrica.</li>
                    <li>Мы НЕ продаем, НЕ обмениваем и НЕ публикуем Ваши данные или изображения.</li>
                    <li>Мы НЕ передаем данные в страны, не обеспечивающие адекватную защиту данных.</li>
                </ul>
            </section>

            <!-- 7. Хранение данных -->
            <section>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">7. Хранение данных</h2>
                <ul class="list-disc pl-6 space-y-2">
                    <li><strong>Изображения:</strong> не хранятся (удаляются сразу после обработки)</li>
                    <li><strong>Технические логи:</strong> до 30 дней (IP-адрес, время запроса)</li>
                    <li><strong>Данные пользователей:</strong> до момента удаления аккаунта</li>
                    <li><strong>История платежей:</strong> 5 лет (для налоговой отчетности)</li>
                    <li><strong>Cookies:</strong> до 2 лет (с возможностью отключения в браузере)</li>
                </ul>
            </section>

            <!-- 8. Ваши права -->
            <section>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">8. Ваши права</h2>
                <p class="mb-3">В соответствии с Федеральным законом от 27.07.2006 № 152-ФЗ «О персональных данных», Вы имеете право:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li><strong>Не загружать изображения</strong> — использование Сервиса полностью добровольно</li>
                    <li><strong>Удалить изображение</strong> до обработки (просто закройте страницу)</li>
                    <li><strong>Запросить информацию</strong> о том, какие данные о Вас хранятся (свяжитесь с нами)</li>
                    <li><strong>Потребовать удаления</strong> Ваших данных из логов (хранятся не более 30 дней)</li>
                    <li><strong>Отозвать согласие</strong> на обработку данных в любой момент</li>
                    <li><strong>Обжаловать действия</strong> в Роскомнадзоре</li>
                    <li><strong>Подать иск</strong> в суд в случае нарушения Ваших прав</li>
                </ul>
            </section>

            <!-- 9. Запрещенный контент -->
            <section class="bg-red-50 dark:bg-red-900/10 border-l-4 border-red-500 p-4 rounded">
                <h2 class="text-2xl font-bold text-red-700 dark:text-red-400 mb-4">🚫 9. Запрещенный контент</h2>
                <p class="mb-3 font-bold text-red-700 dark:text-red-400">ЗАПРЕЩАЕТСЯ загружать изображения, содержащие:</p>
                <ul class="list-disc pl-6 space-y-1 text-red-700 dark:text-red-300">
                    <li>🔞 Порнографию и контент для взрослых (18+)</li>
                    <li>💀 Пропаганду насилия, экстремизма, терроризма</li>
                    <li>😡 Оскорбление человеческого достоинства и национальной розни</li>
                    <li>👤 Изображения без согласия изображенных лиц</li>
                    <li>©️ Материалы, нарушающие авторские и смежные права</li>
                    <li>🔒 Изображения, содержащие государственную тайну</li>
                    <li>💰 Изображения банковских карт, паспортов и других документов</li>
                    <li>💊 Фотографии наркотиков и пропаганду наркотических средств</li>
                </ul>
                <div class="bg-red-100 dark:bg-red-900/30 p-3 rounded mt-3">
                    <p class="text-sm text-red-700 dark:text-red-300">⚠️ Мы оставляем за собой право блокировать обработку такого контента и передавать информацию о нарушителях в правоохранительные органы.</p>
                </div>
            </section>

            <!-- 10. Ответственность -->
            <section>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">10. Ограничение ответственности</h2>
                <div class="space-y-3">
                    <p><strong>10.1.</strong> Сервис предназначен для обработки изображений, которые Вы имеете право обрабатывать.</p>
                    <p><strong>10.2.</strong> <strong class="text-red-600 dark:text-red-400">Загружая изображение, Вы подтверждаете</strong>, что:</p>
                    <ul class="list-disc pl-6 space-y-1">
                        <li>✅ Имеете право на обработку загружаемого изображения</li>
                        <li>✅ Получили согласие изображенных на них лиц (если на фото есть люди)</li>
                        <li>✅ Не нарушаете законодательство РФ и права третьих лиц</li>
                    </ul>
                    <p><strong>10.3.</strong> Мы НЕ несем ответственности за:</p>
                    <ul class="list-disc pl-6 space-y-1">
                        <li>❌ Неправомерное использование Сервиса третьими лицами</li>
                        <li>❌ Содержание загружаемых Вами изображений</li>
                        <li>❌ Ущерб, вызванный использованием обработанных изображений</li>
                        <li>❌ Убытки, связанные с техническими сбоями</li>
                        <li>❌ Потерю данных, если Вы не сохранили оригинал</li>
                    </ul>
                    <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded mt-3">
                        <p class="text-sm text-blue-700 dark:text-blue-400">💡 <strong>Рекомендация:</strong> Всегда сохраняйте оригиналы изображений до получения результата. Сервис не гарантирует сохранность файлов.</p>
                    </div>
                </div>
            </section>

            <!-- 11. Cookies -->
            <section>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">11. Cookies и технологии отслеживания</h2>
                <p class="mb-3">Сервис может использовать cookies для:</p>
                <ul class="list-disc pl-6 space-y-1">
                    <li>Обеспечения работы функций Сервиса (авторизация, настройки)</li>
                    <li>Сбора анонимной статистики посещений (Yandex.Metrica)</li>
                    <li>Запоминания выбранной темы (светлая/темная)</li>
                </ul>
                <p class="mt-3">Вы можете отключить cookies в настройках браузера, но это может повлиять на работу некоторых функций.</p>
                <div class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded mt-2">
                    <p class="text-sm text-gray-600 dark:text-gray-400">🛠️ <strong>Как отключить cookies:</strong> Настройки браузера → Конфиденциальность → Блокировка cookies.</p>
                </div>
            </section>

            <!-- 12. Изменения -->
            <section>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">12. Изменения Политики конфиденциальности</h2>
                <p class="mb-3">Мы можем периодически обновлять настоящую Политику. О существенных изменениях мы уведомим Вас:</p>
                <ul class="list-disc pl-6 space-y-1">
                    <li>Через уведомление в Сервисе</li>
                    <li>По электронной почте (если Вы зарегистрированы)</li>
                </ul>
                <p class="mt-3">Продолжая использовать Сервис после изменений, Вы соглашаетесь с обновленной Политикой.</p>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Дата последнего обновления: <strong>{{ date('d.m.Y') }}</strong></p>
            </section>

            <!-- 13. Контакты -->
            <section class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded">
                <h2 class="text-2xl font-bold text-green-700 dark:text-green-400 mb-4">📧 13. Контактная информация</h2>
                <p class="mb-3">По всем вопросам, связанным с настоящей Политикой или обработкой Ваших данных, Вы можете связаться с нами:</p>
                <div class="space-y-2">
                    <p><strong>📧 Email:</strong> <a href="mailto:privacy@imagetool.ru" class="text-green-600 dark:text-green-400 hover:underline">privacy@imagetool.ru</a></p>
                    <p><strong>📞 Телефон:</strong> <a href="tel:+74951234567" class="text-green-600 dark:text-green-400 hover:underline">+7 (495) 123-45-67</a></p>
                    <p><strong>📍 Адрес:</strong> 127006, г. Москва, ул. Тверская, д. 1</p>
                    <p><strong>🕐 Время работы поддержки:</strong> Пн-Пт с 10:00 до 20:00 (МСК)</p>
                </div>
                <div class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                    <p>Ответ на запрос направляется в течение <strong>24 часов</strong> в рабочие дни.</p>
                </div>
            </section>

            <!-- Кнопка возврата -->
            <div class="mt-8 pt-4 border-t border-gray-200 dark:border-gray-700 text-center">
                <a href="{{ route('converter') }}" class="inline-block bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white px-6 py-3 rounded-lg font-medium transition">
                    ← Вернуться в редактор
                </a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
