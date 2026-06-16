<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <!-- Основные метатеги -->
    <title>Редактор изображений онлайн — Конвертер фото бесплатно | ImageTool</title>
    <meta name="description"
          content="Бесплатный онлайн редактор изображений. Изменяйте размер, формат, обрезайте фото, удаляйте метаданные. Конвертируйте JPEG, PNG, WebP, GIF за секунды. Без регистрации.">
    <meta name="keywords"
          content="редактор изображений онлайн, конвертер фото, изменить размер фото, обрезка фото, удалить метаданные, конвертировать jpeg в png, webp конвертер, бесплатный редактор фото">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / VK / Одноклассники -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Редактор изображений онлайн — Конвертер фото бесплатно | ImageTool">
    <meta property="og:description"
          content="Бесплатный онлайн редактор изображений. Изменяйте размер, формат, обрезайте фото, удаляйте метаданные. Конвертируйте JPEG, PNG, WebP, GIF за секунды.">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="ImageTool">
    <meta property="og:locale" content="ru_RU">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Редактор изображений онлайн — Конвертер фото бесплатно">
    <meta name="twitter:description"
          content="Бесплатный онлайн редактор изображений. Изменяйте размер, формат, обрезайте фото, удаляйте метаданные.">
    <meta name="twitter:image" content="{{ asset('images/twitter-image.jpg') }}">

    <!-- Дополнительные метатеги -->
    <meta name="author" content="ImageTool">
    <meta name="application-name" content="ImageTool">
    <meta name="theme-color" content="#22c55e">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <style>
        [x-cloak] { display: none; }

        body.dark {
            background-color: #0f0f1a !important;
        }

        .dark .bg-white { background-color: #16213e !important; }
        .dark .bg-gray-50 { background-color: #16213e !important; }
        .dark .bg-gray-100 { background-color: #1a1a2e !important; }
        .dark .bg-gray-200 { background-color: #16213e !important; }
        .dark .bg-gray-800 { background-color: #0f3460 !important; }
        .dark .bg-gray-900 { background-color: #16213e !important; }

        .dark input,
        .dark select,
        .dark textarea {
            background-color: #0f0f1a !important;
            border-color: #2c3e66 !important;
            color: #e0e0e0 !important;
        }

        .dark input:focus,
        .dark select:focus,
        .dark textarea:focus {
            border-color: #22c55e !important;
            outline: none !important;
        }

        .dark input::placeholder,
        .dark select::placeholder,
        .dark textarea::placeholder {
            color: #666 !important;
        }

        .dark .border-gray-300 { border-color: #2c3e66 !important; }
        .dark .border-gray-200 { border-color: #2c3e66 !important; }

        .dark .text-gray-700 { color: #c0c0c0 !important; }
        .dark .text-gray-600 { color: #aaa !important; }
        .dark .text-gray-500 { color: #888 !important; }
        .dark .text-gray-400 { color: #777 !important; }

        .dark .drop-zone {
            background-color: #16213e !important;
            border-color: #2c3e66 !important;
        }
        .dark .drop-zone.drag-over {
            background-color: #1a3a5c !important;
            border-color: #22c55e !important;
        }

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

        input[type="range"] {
            -webkit-appearance: none;
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
        }
        .dark input[type="range"] {
            background: #2c3e66;
        }
        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            background: #22c55e;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s;
        }
        input[type="range"]::-webkit-slider-thumb:hover {
            transform: scale(1.2);
            background: #16a34a;
        }

        .crop-container {
            position: relative;
            display: inline-block;
            user-select: none;
        }
        .crop-overlay {
            position: absolute;
            border: 2px solid #22c55e;
            background: rgba(34, 197, 94, 0.15);
            cursor: move;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.45);
            transition: none;
        }
        .crop-handle {
            position: absolute;
            width: 12px;
            height: 12px;
            background: white;
            border: 2px solid #22c55e;
            border-radius: 3px;
            z-index: 10;
            transition: transform 0.1s;
        }
        .crop-handle:hover {
            transform: scale(1.2);
            background: #22c55e;
        }
        .crop-handle.nw { top: -6px; left: -6px; cursor: nw-resize; }
        .crop-handle.n { top: -6px; left: 50%; transform: translateX(-50%); cursor: n-resize; }
        .crop-handle.ne { top: -6px; right: -6px; cursor: ne-resize; }
        .crop-handle.w { top: 50%; left: -6px; transform: translateY(-50%); cursor: w-resize; }
        .crop-handle.e { top: 50%; right: -6px; transform: translateY(-50%); cursor: e-resize; }
        .crop-handle.sw { bottom: -6px; left: -6px; cursor: sw-resize; }
        .crop-handle.s { bottom: -6px; left: 50%; transform: translateX(-50%); cursor: s-resize; }
        .crop-handle.se { bottom: -6px; right: -6px; cursor: se-resize; }

        .crop-instruction {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.65);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            pointer-events: none;
            white-space: nowrap;
            z-index: 15;
        }
        img {
            pointer-events: auto;
        }

        body, body * {
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }

        .dark input[type="checkbox"] {
            background-color: #0f0f1a !important;
            border-color: #2c3e66 !important;
        }
        .dark input[type="checkbox"]:checked {
            background-color: #22c55e !important;
            border-color: #22c55e !important;
        }

        .dark .bg-gray-200 {
            background-color: #2c3e66 !important;
        }
        .dark .bg-gray-200:hover {
            background-color: #3a5a8a !important;
        }
        .dark .text-gray-700 {
            color: #e0e0e0 !important;
        }

        .batch-file-item {
            transition: all 0.2s;
        }
        .batch-file-item:hover {
            background: #f0fdf4;
        }
        .dark .batch-file-item:hover {
            background: #1a3a5c;
        }
    </style>
</head>
<body class="bg-gray-100" x-data="converter()" x-init="init()">

<!-- Переключатель темы -->
<div @click="toggleTheme" class="theme-switch">
    <svg x-show="!isDarkMode" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
    </svg>
    <svg x-show="isDarkMode" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
    </svg>
</div>

<div x-cloak class="min-h-screen py-8 px-4 transition-colors duration-300">
    @yield('content')

    <!-- Модальное окно Premium -->
    <div x-show="showPremiumModal" x-transition class="fixed inset-0 bg-black bg-opacity-80 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-yellow-500 to-orange-500 bg-clip-text text-transparent">⭐ Premium подписка</h2>
                    <button @click="showPremiumModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="border rounded-xl p-4">
                        <h3 class="text-lg font-bold mb-2">Бесплатно</h3>
                        <p class="text-2xl font-bold text-gray-600">0 ₽</p>
                        <ul class="mt-4 space-y-2 text-sm">
                            <li class="flex items-center gap-2">✅ Все форматы (JPEG, PNG, WebP, GIF)</li>
                            <li class="flex items-center gap-2">✅ До 5 МБ</li>
                            <li class="flex items-center gap-2">✅ Базовые фильтры</li>
                            <li class="flex items-center gap-2 text-gray-400">❌ Удаление метаданных</li>
                            <li class="flex items-center gap-2 text-gray-400">❌ Пакетная обработка</li>
                            <li class="flex items-center gap-2 text-gray-400">❌ AI улучшение</li>
                            <li class="flex items-center gap-2 text-gray-400">❌ Экспорт в PDF</li>
                        </ul>
                    </div>
                    <div class="border-2 border-yellow-500 rounded-xl p-4 bg-gradient-to-br from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20">
                        <h3 class="text-lg font-bold text-yellow-600">⭐ Premium</h3>
                        <p class="text-2xl font-bold">299 ₽<span class="text-sm font-normal">/мес</span></p>
                        <p class="text-sm text-gray-500">или 1990 ₽/год (экономия 45%)</p>
                        <ul class="mt-4 space-y-2 text-sm">
                            <li class="flex items-center gap-2">✅ Все форматы</li>
                            <li class="flex items-center gap-2">✅ До 50 МБ</li>
                            <li class="flex items-center gap-2">✅ Удаление метаданных (EXIF)</li>
                            <li class="flex items-center gap-2">✅ Пакетная обработка (до 20 фото)</li>
                            <li class="flex items-center gap-2">✅ AI улучшение качества</li>
                            <li class="flex items-center gap-2">✅ Экспорт в PDF</li>
                        </ul>
                        <form action="{{ route('checkout') }}" method="POST" class="w-full mt-4">
                            @csrf
                            <input type="hidden" name="plan" value="premium">
                            <input type="hidden" name="redirect" value="{{ url()->current() }}">
                            <button type="submit" class="w-full bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white py-2 rounded-lg font-semibold transition">
                                ⭐ Активировать Premium
                            </button>
                        </form>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-4 text-center">Оплата через ЮKassa (банковская карта, СБП). Подписка автоматически продлевается.</p>
            </div>
        </div>
    </div>

    <div class="text-center mt-8 pt-4 border-t border-gray-200 dark:border-gray-700">
        <p class="text-xs text-gray-500 dark:text-gray-400">
            Популярные запросы:
            <a href="/converter?format=jpeg" class="text-green-600 hover:text-green-700">конвертировать в JPEG</a> •
            <a href="/converter?format=png" class="text-green-600 hover:text-green-700">конвертировать в PNG</a> •
            <a href="/converter?format=webp" class="text-green-600 hover:text-green-700">конвертировать в WebP</a> •
            <a href="/converter?crop=1" class="text-green-600 hover:text-green-700">обрезка фото</a> •
            <a href="/converter?remove_metadata=1" class="text-green-600 hover:text-green-700">удалить метаданные</a>
        </p>
    </div>
</div>

<script>
    function converter() {
        return {
            // Состояние
            previewUrl: null,
            resultUrl: null,
            originalFile: null,
            originalSize: '',
            originalType: '',
            originalWidth: 0,
            originalHeight: 0,
            resultWidth: 0,
            resultHeight: 0,
            resultSize: '',
            processing: false,
            dragOver: false,
            autoUpdate: false,
            applyTimeout: null,
            isDarkMode: false,
            checkPaymentInterval: null,

            // Премиум
            isPremium: false,
            showPremiumModal: false,

            // Пакетный режим
            batchMode: false,
            batchFiles: [],
            batchProcessing: false,
            batchProcessed: 0,
            batchResults: [],
            batchDragOver: false,

            // Обрезка
            cropRect: { x: 0, y: 0, width: 0, height: 0 },
            isDrawing: false,
            isMoving: false,
            isResizing: false,
            resizeDir: null,
            drawStart: { x: 0, y: 0 },
            moveStart: { x: 0, y: 0, rectX: 0, rectY: 0 },
            resizeStart: { x: 0, y: 0, rectX: 0, rectY: 0, rectW: 0, rectH: 0 },
            scale: 1,

            settings: {
                format: 'jpeg',
                quality: 85,
                width: null,
                height: null,
                crop: false,
                keepProportions: true,
                aspectRatio: '',
                filter: '',
                rotate: 0,
                flip: '',
                brightness: 0,
                contrast: 0,
                saturation: 0,
                removeMetadata: false,
                aiEnhance: false,
                exportPDF: false
            },

            init() {
                this.initTheme();
                this.checkPremium();
                // Запускаем проверку статуса платежа при загрузке
                this.startPaymentCheck();
            },

            initTheme() {
                const savedTheme = localStorage.getItem('darkMode');
                if (savedTheme === 'true') {
                    this.isDarkMode = true;
                    document.documentElement.classList.add('dark');
                    document.body.classList.add('dark');
                } else {
                    this.isDarkMode = false;
                    document.documentElement.classList.remove('dark');
                    document.body.classList.remove('dark');
                }
            },

            async checkPremium() {
                try {
                    const response = await fetch('/converter/check-premium');
                    const data = await response.json();
                    this.isPremium = data.is_premium || false;

                    if (this.isPremium && this.checkPaymentInterval) {
                        clearInterval(this.checkPaymentInterval);
                        this.checkPaymentInterval = null;
                    }
                } catch (error) {
                    this.isPremium = false;
                }
            },

            toggleTheme() {
                this.isDarkMode = !this.isDarkMode;
                localStorage.setItem('darkMode', this.isDarkMode);
                if (this.isDarkMode) {
                    document.documentElement.classList.add('dark');
                    document.body.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    document.body.classList.remove('dark');
                }
            },

            startPaymentCheck() {
                if (this.checkPaymentInterval) {
                    clearInterval(this.checkPaymentInterval);
                }

                // Немедленная проверка
                this.checkPaymentStatus();

                // Периодическая проверка каждые 5 секунд (только если нет премиума)
                this.checkPaymentInterval = setInterval(() => {
                    // Проверяем только если премиум еще не активен
                    if (!this.isPremium) {
                        this.checkPaymentStatus();
                    } else {
                        // Если премиум уже активен, останавливаем проверку
                        clearInterval(this.checkPaymentInterval);
                        this.checkPaymentInterval = null;
                    }
                }, 5000);
            },

            async checkPaymentStatus() {
                // Не проверяем, если уже есть премиум
                if (this.isPremium) {
                    if (this.checkPaymentInterval) {
                        clearInterval(this.checkPaymentInterval);
                        this.checkPaymentInterval = null;
                    }
                    return;
                }

                try {
                    const response = await fetch('/check-payment-status', {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();

                    console.log('Payment status check:', data);

                    if (data.is_premium && !this.isPremium) {
                        // Останавливаем интервал перед перезагрузкой
                        if (this.checkPaymentInterval) {
                            clearInterval(this.checkPaymentInterval);
                            this.checkPaymentInterval = null;
                        }
                        this.isPremium = true;
                        // Перезагружаем страницу один раз
                        location.reload();
                    }
                } catch (error) {
                    console.error('Check payment status error:', error);
                }
            },
            setBatchMode(mode) {
                this.batchMode = mode;
                if (!mode) this.clearBatchFiles();
            },

            toggleBatchMode() {
                if (!this.isPremium && !this.batchMode) {
                    this.showPremiumModal = true;
                    return;
                }
                this.batchMode = !this.batchMode;
                if (!this.batchMode) this.clearBatchFiles();
            },

            handleBatchDrop(event) {
                this.batchDragOver = false;
                const files = Array.from(event.dataTransfer.files);
                this.addBatchFiles(files);
            },

            handleBatchFiles(event) {
                const files = Array.from(event.target.files);
                this.addBatchFiles(files);
            },

            addBatchFiles(files) {
                if (!this.isPremium && files.length > 1) {
                    this.showPremiumModal = true;
                    return;
                }
                const maxSize = this.isPremium ? 50 : 5;
                const newFiles = files.filter(f => f.type.startsWith('image/') && f.size <= maxSize * 1024 * 1024).slice(0, 20 - this.batchFiles.length);
                newFiles.forEach(file => {
                    this.batchFiles.push({
                        file: file,
                        name: file.name,
                        size: file.size,
                        status: 'pending',
                        resultUrl: null
                    });
                });
                document.getElementById('batchInput').value = '';
            },

            clearBatchFiles() {
                this.batchFiles = [];
                this.batchResults = [];
                this.batchProcessed = 0;
            },

            removeBatchFile(index) {
                this.batchFiles.splice(index, 1);
            },

            async processBatchFiles() {
                if (!this.isPremium) {
                    this.showPremiumModal = true;
                    return;
                }
                this.batchProcessing = true;
                this.batchProcessed = 0;
                this.batchResults = [];

                for (let i = 0; i < this.batchFiles.length; i++) {
                    const item = this.batchFiles[i];
                    item.status = 'processing';

                    const formData = new FormData();
                    formData.append('image', item.file);
                    formData.append('format', this.settings.format);
                    formData.append('quality', this.settings.quality);
                    formData.append('filter', this.settings.filter);
                    formData.append('rotate', this.settings.rotate);
                    formData.append('flip', this.settings.flip);
                    formData.append('brightness', this.settings.brightness);
                    formData.append('contrast', this.settings.contrast);
                    formData.append('saturation', this.settings.saturation);

                    if (this.settings.removeMetadata) formData.append('remove_metadata', '1');
                    if (this.settings.aiEnhance) formData.append('ai_enhance', '1');

                    try {
                        const response = await fetch('/converter/process', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: formData
                        });
                        const data = await response.json();
                        if (data.success) {
                            item.status = 'done';
                            item.resultUrl = data.base64;
                            this.batchResults.push({ name: item.name, url: data.base64 });
                        } else {
                            item.status = 'error';
                        }
                    } catch (error) {
                        item.status = 'error';
                    }
                    this.batchProcessed++;
                }
                this.batchProcessing = false;
            },

            downloadBatchFile(item) {
                if (item.resultUrl) {
                    const a = document.createElement('a');
                    a.href = item.resultUrl;
                    a.download = 'converted_' + item.name.replace(/\.[^/.]+$/, '') + '.' + this.settings.format;
                    a.click();
                }
            },

            downloadAllBatch() {
                const zip = new JSZip();
                this.batchResults.forEach(result => {
                    const base64Data = result.url.split(',')[1];
                    zip.file(result.name.replace(/\.[^/.]+$/, '') + '.' + this.settings.format, base64Data, { base64: true });
                });
                zip.generateAsync({ type: 'blob' }).then(content => {
                    const a = document.createElement('a');
                    a.href = URL.createObjectURL(content);
                    a.download = 'converted_images.zip';
                    a.click();
                    URL.revokeObjectURL(a.href);
                });
            },

            async applyPDFExport() {
                if (!this.isPremium) {
                    this.showPremiumModal = true;
                    return;
                }
                if (!this.resultUrl) return;
                const img = new Image();
                img.src = this.resultUrl;
                img.onload = () => {
                    const { jsPDF } = window.jspdf;
                    const pdf = new jsPDF({
                        orientation: img.width > img.height ? 'landscape' : 'portrait',
                        unit: 'px',
                        format: [img.width, img.height]
                    });
                    pdf.addImage(this.resultUrl, 'JPEG', 0, 0, img.width, img.height);
                    pdf.save('converted.pdf');
                };
            },

            initCrop() {
                if (this.$refs.originalImage) {
                    this.scale = this.$refs.originalImage.clientWidth / this.originalWidth;
                    if (this.settings.crop && (!this.cropRect.width || this.cropRect.width === 0)) {
                        this.resetCrop();
                    }
                }
            },

            toggleCrop() {
                if (this.settings.crop) {
                    this.resetCrop();
                } else {
                    this.cropRect = { x: 0, y: 0, width: 0, height: 0 };
                    this.autoApply();
                }
            },

            resetCrop() {
                if (this.$refs.originalImage) {
                    this.cropRect = {
                        x: 0,
                        y: 0,
                        width: this.$refs.originalImage.clientWidth,
                        height: this.$refs.originalImage.clientHeight
                    };
                    this.updateCropSettings();
                }
            },

            updateCropSettings() {
                if (this.cropRect.width > 0 && this.cropRect.height > 0) {
                    this.settings.cropWidth = Math.max(10, Math.round(this.cropRect.width / this.scale));
                    this.settings.cropHeight = Math.max(10, Math.round(this.cropRect.height / this.scale));
                    this.settings.cropX = Math.max(0, Math.round(this.cropRect.x / this.scale));
                    this.settings.cropY = Math.max(0, Math.round(this.cropRect.y / this.scale));
                    this.autoApply();
                }
            },

            startDrawCrop(e) {
                if (!this.settings.crop) return;
                this.isDrawing = true;
                const rect = this.$refs.originalImage.getBoundingClientRect();
                this.drawStart = { x: e.clientX - rect.left, y: e.clientY - rect.top };
                this.cropRect = { x: this.drawStart.x, y: this.drawStart.y, width: 0, height: 0 };
                e.preventDefault();
                window.addEventListener('mousemove', this.onDrawCrop.bind(this));
                window.addEventListener('mouseup', this.stopDrawCrop.bind(this));
            },

            onDrawCrop(e) {
                if (!this.isDrawing) return;
                const rect = this.$refs.originalImage.getBoundingClientRect();
                let currentX = e.clientX - rect.left;
                let currentY = e.clientY - rect.top;
                let newX = Math.min(this.drawStart.x, currentX);
                let newY = Math.min(this.drawStart.y, currentY);
                let newWidth = Math.abs(currentX - this.drawStart.x);
                let newHeight = Math.abs(currentY - this.drawStart.y);
                if (newWidth > 5 && newHeight > 5) {
                    this.cropRect = { x: newX, y: newY, width: newWidth, height: newHeight };
                    this.updateCropSettings();
                }
            },

            stopDrawCrop() {
                this.isDrawing = false;
                window.removeEventListener('mousemove', this.onDrawCrop);
                window.removeEventListener('mouseup', this.stopDrawCrop);
            },

            startMoveCrop(e) {
                this.isMoving = true;
                const rect = this.$refs.originalImage.getBoundingClientRect();
                this.moveStart = { x: e.clientX - rect.left, y: e.clientY - rect.top, rectX: this.cropRect.x, rectY: this.cropRect.y };
                e.preventDefault();
                window.addEventListener('mousemove', this.onMoveCrop.bind(this));
                window.addEventListener('mouseup', this.stopMoveCrop.bind(this));
            },

            onMoveCrop(e) {
                if (!this.isMoving) return;
                const rect = this.$refs.originalImage.getBoundingClientRect();
                let currentX = e.clientX - rect.left;
                let currentY = e.clientY - rect.top;
                let newX = this.moveStart.rectX + (currentX - this.moveStart.x);
                let newY = this.moveStart.rectY + (currentY - this.moveStart.y);
                newX = Math.max(0, Math.min(newX, rect.width - this.cropRect.width));
                newY = Math.max(0, Math.min(newY, rect.height - this.cropRect.height));
                this.cropRect.x = newX;
                this.cropRect.y = newY;
                this.updateCropSettings();
            },

            stopMoveCrop() {
                this.isMoving = false;
                window.removeEventListener('mousemove', this.onMoveCrop);
                window.removeEventListener('mouseup', this.stopMoveCrop);
            },

            startResizeCrop(dir, e) {
                this.isResizing = true;
                this.resizeDir = dir;
                const rect = this.$refs.originalImage.getBoundingClientRect();
                this.resizeStart = { x: e.clientX - rect.left, y: e.clientY - rect.top, rectX: this.cropRect.x, rectY: this.cropRect.y, rectW: this.cropRect.width, rectH: this.cropRect.height };
                e.preventDefault();
                window.addEventListener('mousemove', this.onResizeCrop.bind(this));
                window.addEventListener('mouseup', this.stopResizeCrop.bind(this));
            },

            onResizeCrop(e) {
                if (!this.isResizing) return;
                const rect = this.$refs.originalImage.getBoundingClientRect();
                let currentX = e.clientX - rect.left;
                let currentY = e.clientY - rect.top;
                let newX = this.resizeStart.rectX;
                let newY = this.resizeStart.rectY;
                let newW = this.resizeStart.rectW;
                let newH = this.resizeStart.rectH;
                switch(this.resizeDir) {
                    case 'nw': newW = this.resizeStart.rectW - (currentX - this.resizeStart.x); newH = this.resizeStart.rectH - (currentY - this.resizeStart.y); newX = this.resizeStart.rectX + (currentX - this.resizeStart.x); newY = this.resizeStart.rectY + (currentY - this.resizeStart.y); break;
                    case 'n': newH = this.resizeStart.rectH - (currentY - this.resizeStart.y); newY = this.resizeStart.rectY + (currentY - this.resizeStart.y); break;
                    case 'ne': newW = this.resizeStart.rectW + (currentX - this.resizeStart.x); newH = this.resizeStart.rectH - (currentY - this.resizeStart.y); newY = this.resizeStart.rectY + (currentY - this.resizeStart.y); break;
                    case 'w': newW = this.resizeStart.rectW - (currentX - this.resizeStart.x); newX = this.resizeStart.rectX + (currentX - this.resizeStart.x); break;
                    case 'e': newW = this.resizeStart.rectW + (currentX - this.resizeStart.x); break;
                    case 'sw': newW = this.resizeStart.rectW - (currentX - this.resizeStart.x); newH = this.resizeStart.rectH + (currentY - this.resizeStart.y); newX = this.resizeStart.rectX + (currentX - this.resizeStart.x); break;
                    case 's': newH = this.resizeStart.rectH + (currentY - this.resizeStart.y); break;
                    case 'se': newW = this.resizeStart.rectW + (currentX - this.resizeStart.x); newH = this.resizeStart.rectH + (currentY - this.resizeStart.y); break;
                }
                newW = Math.max(30, newW);
                newH = Math.max(30, newH);
                newX = Math.max(0, Math.min(newX, rect.width - newW));
                newY = Math.max(0, Math.min(newY, rect.height - newH));
                this.cropRect = { x: newX, y: newY, width: newW, height: newH };
                this.updateCropSettings();
            },

            stopResizeCrop() {
                this.isResizing = false;
                window.removeEventListener('mousemove', this.onResizeCrop);
                window.removeEventListener('mouseup', this.stopResizeCrop);
            },

            applyAspectRatio() {
                if (!this.settings.aspectRatio || !this.originalWidth) return;
                const [ratioW, ratioH] = this.settings.aspectRatio.split(':').map(Number);
                let newHeight = Math.round(this.originalWidth * ratioH / ratioW);
                this.settings.width = this.originalWidth;
                this.settings.height = newHeight;
                if (this.settings.crop) {
                    this.settings.crop = false;
                    this.cropRect = { x: 0, y: 0, width: 0, height: 0 };
                }
                this.autoApply();
            },

            handleFile(event) {
                const file = event.target.files[0];
                if (file) this.loadFile(file);
            },

            handleDrop(event) {
                this.dragOver = false;
                const file = event.dataTransfer.files[0];
                if (file) this.loadFile(file);
            },

            loadFile(file) {
                const maxSize = this.isPremium ? 50 : 5;
                if (!file.type.startsWith('image/')) {
                    alert('❌ Выберите изображение');
                    return;
                }
                if (file.size > maxSize * 1024 * 1024) {
                    if (!this.isPremium) {
                        this.showPremiumModal = true;
                        return;
                    }
                    alert(`❌ Файл больше ${maxSize}MB`);
                    return;
                }
                this.originalFile = file;
                this.originalSize = this.formatBytes(file.size);
                this.originalType = file.type.split('/')[1].toUpperCase();
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previewUrl = e.target.result;
                    const img = new Image();
                    img.onload = () => {
                        this.originalWidth = img.width;
                        this.originalHeight = img.height;
                        setTimeout(() => this.initCrop(), 100);
                        this.autoApply();
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
                this.resultUrl = null;
                this.resultSize = '';
            },

            formatBytes(bytes) {
                if (bytes === 0) return '0 B';
                const k = 1024;
                const sizes = ['B', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            },

            applyPreset(preset) {
                const presets = {
                    social: { width: 1080, height: 1080, crop: true },
                    youtube: { width: 1280, height: 720, crop: false },
                    avatar: { width: 400, height: 400, crop: true },
                    vk: { width: 1200, height: 630, crop: false }
                };
                if (presets[preset]) {
                    this.settings.width = presets[preset].width;
                    this.settings.height = presets[preset].height;
                    this.settings.crop = presets[preset].crop;
                    if (this.settings.crop && this.$refs.originalImage) {
                        setTimeout(() => this.resetCrop(), 50);
                    }
                    this.autoApply();
                }
            },

            autoApply() {
                if (!this.originalFile) return;
                if (this.applyTimeout) clearTimeout(this.applyTimeout);
                this.autoUpdate = true;
                this.applyTimeout = setTimeout(() => this.applySettings(), 300);
            },

            async applySettings() {
                if (!this.originalFile) return;
                this.processing = true;
                const formData = new FormData();
                formData.append('image', this.originalFile);
                formData.append('format', this.settings.format);
                formData.append('quality', this.settings.quality);
                formData.append('filter', this.settings.filter);
                formData.append('rotate', this.settings.rotate);
                formData.append('flip', this.settings.flip);
                formData.append('crop', this.settings.crop ? '1' : '0');
                formData.append('keep_proportions', this.settings.keepProportions ? '1' : '0');
                formData.append('brightness', this.settings.brightness);
                formData.append('contrast', this.settings.contrast);
                formData.append('saturation', this.settings.saturation);
                if (this.settings.width && this.settings.width > 0) formData.append('width', this.settings.width);
                if (this.settings.height && this.settings.height > 0) formData.append('height', this.settings.height);
                if (this.settings.crop && this.settings.cropWidth && this.settings.cropHeight) {
                    formData.append('crop_width', this.settings.cropWidth);
                    formData.append('crop_height', this.settings.cropHeight);
                    formData.append('crop_x', this.settings.cropX || 0);
                    formData.append('crop_y', this.settings.cropY || 0);
                }
                if (this.settings.removeMetadata && this.isPremium) formData.append('remove_metadata', '1');
                if (this.settings.aiEnhance && this.isPremium) formData.append('ai_enhance', '1');
                try {
                    const response = await fetch('/converter/process', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: formData
                    });
                    const data = await response.json();
                    if (data.success) {
                        this.resultUrl = data.base64;
                        this.resultWidth = data.width;
                        this.resultHeight = data.height;
                        const sizeInBytes = Math.round((data.base64.length * 3) / 4);
                        this.resultSize = this.formatBytes(sizeInBytes);
                    }
                } catch (error) {
                    console.error('Request error:', error);
                } finally {
                    this.processing = false;
                    this.autoUpdate = false;
                }
            },

            downloadImage() {
                if (this.resultUrl) {
                    const a = document.createElement('a');
                    a.href = this.resultUrl;
                    a.download = `converted_${Date.now()}.${this.settings.format === 'jpeg' ? 'jpg' : this.settings.format}`;
                    a.click();
                }
            },

            resetForm() {
                this.settings = {
                    format: 'jpeg',
                    quality: 85,
                    width: null,
                    height: null,
                    crop: false,
                    keepProportions: true,
                    aspectRatio: '',
                    filter: '',
                    rotate: 0,
                    flip: '',
                    brightness: 0,
                    contrast: 0,
                    saturation: 0,
                    removeMetadata: false,
                    aiEnhance: false,
                    exportPDF: false
                };
                this.resultSize = '';
                if (this.$refs.originalImage) {
                    setTimeout(() => this.resetCrop(), 50);
                }
                this.autoApply();
            }
        }
    }
</script>

</body>
</html>
