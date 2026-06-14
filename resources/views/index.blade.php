@extends('layouts.main')

@section('content')
    <div x-data="imageConverter()" x-cloak class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Заголовок -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center p-2 bg-gradient-to-r from-green-500 to-blue-500 rounded-2xl shadow-lg mb-4">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent">
                    Редактор изображений
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-3 text-lg">
                    Профессиональная обработка изображений онлайн
                </p>
            </div>

            <!-- Зона загрузки -->
            <div x-show="!previewUrl"
                 class="relative group">
                <div @dragover.prevent @drop.prevent="handleDrop($event)"
                     class="border-3 border-dashed rounded-2xl p-12 text-center transition-all duration-300 cursor-pointer
                        border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                        hover:border-green-500 dark:hover:border-green-500 hover:bg-green-50 dark:hover:bg-gray-700">
                    <input type="file" id="imageInput" accept="image/*" @change="handleFileSelect" class="hidden">
                    <label for="imageInput" class="cursor-pointer block">
                        <div class="flex flex-col items-center justify-center space-y-4">
                            <div class="w-24 h-24 bg-gradient-to-r from-green-500 to-blue-500 rounded-full flex items-center justify-center shadow-lg transform transition-transform group-hover:scale-110">
                                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xl font-semibold text-gray-700 dark:text-gray-300">Выберите изображение</p>
                                <p class="text-gray-500 dark:text-gray-400 mt-2">или перетащите его сюда</p>
                            </div>
                            <div class="flex flex-wrap justify-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full">JPEG</span>
                                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full">PNG</span>
                                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full">WebP</span>
                                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full">GIF</span>
                            </div>
                            <p class="text-xs text-gray-400">Максимальный размер: 20MB</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Основной контент после загрузки -->
            <div x-show="previewUrl" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">

                <!-- Сравнение оригинал/результат -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Оригинал -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-3">
                            <h3 class="text-white font-semibold flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Оригинал
                            </h3>
                        </div>
                        <div class="p-4">
                            <img :src="previewUrl" class="rounded-lg w-full h-auto object-contain" style="max-height: 400px;">
                            <div class="mt-3 flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                <span x-text="'Размер: ' + originalSize"></span>
                                <span x-text="'Разрешение: ' + originalWidth + 'x' + originalHeight"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Результат -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                        <div class="bg-gradient-to-r from-green-600 to-blue-600 px-6 py-3">
                            <h3 class="text-white font-semibold flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Результат
                            </h3>
                        </div>
                        <div class="p-4">
                            <div x-show="!resultUrl && !processing" class="flex items-center justify-center h-64 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                <div class="text-center">
                                    <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    <p class="mt-2 text-gray-500">Настройте параметры и нажмите "Применить"</p>
                                </div>
                            </div>
                            <div x-show="processing" class="flex items-center justify-center h-64 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                <div class="text-center">
                                    <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-green-500 mx-auto"></div>
                                    <p class="mt-3 text-gray-600">Обработка изображения...</p>
                                </div>
                            </div>
                            <img x-show="resultUrl && !processing" :src="resultUrl" class="rounded-lg w-full h-auto object-contain" style="max-height: 400px;">
                        </div>
                    </div>
                </div>

                <!-- Панель инструментов -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                    <!-- Вкладки -->
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <div class="flex flex-wrap">
                            <button @click="activeTab = 'basic'" :class="{'border-green-500 text-green-600 dark:text-green-400': activeTab === 'basic', 'border-transparent text-gray-500 dark:text-gray-400': activeTab !== 'basic'}"
                                    class="px-6 py-3 text-sm font-medium border-b-2 transition-colors">
                                Основные
                            </button>
                            <button @click="activeTab = 'resize'" :class="{'border-green-500 text-green-600 dark:text-green-400': activeTab === 'resize', 'border-transparent text-gray-500 dark:text-gray-400': activeTab !== 'resize'}"
                                    class="px-6 py-3 text-sm font-medium border-b-2 transition-colors">
                                Размер
                            </button>
                            <button @click="activeTab = 'effects'" :class="{'border-green-500 text-green-600 dark:text-green-400': activeTab === 'effects', 'border-transparent text-gray-500 dark:text-gray-400': activeTab !== 'effects'}"
                                    class="px-6 py-3 text-sm font-medium border-b-2 transition-colors">
                                Эффекты
                            </button>
                            <button @click="activeTab = 'advanced'" :class="{'border-green-500 text-green-600 dark:text-green-400': activeTab === 'advanced', 'border-transparent text-gray-500 dark:text-gray-400': activeTab !== 'advanced'}"
                                    class="px-6 py-3 text-sm font-medium border-b-2 transition-colors">
                                Дополнительно
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Основные настройки -->
                        <div x-show="activeTab === 'basic'" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Формат изображения
                                    </label>
                                    <div class="grid grid-cols-4 gap-2">
                                        <button type="button" @click="settings.format = 'jpeg'"
                                                :class="{'bg-green-600 text-white': settings.format === 'jpeg', 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200': settings.format !== 'jpeg'}"
                                                class="px-4 py-2 rounded-lg font-medium transition-all">
                                            JPEG
                                        </button>
                                        <button type="button" @click="settings.format = 'png'"
                                                :class="{'bg-green-600 text-white': settings.format === 'png', 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200': settings.format !== 'png'}"
                                                class="px-4 py-2 rounded-lg font-medium transition-all">
                                            PNG
                                        </button>
                                        <button type="button" @click="settings.format = 'webp'"
                                                :class="{'bg-green-600 text-white': settings.format === 'webp', 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200': settings.format !== 'webp'}"
                                                class="px-4 py-2 rounded-lg font-medium transition-all">
                                            WebP
                                        </button>
                                        <button type="button" @click="settings.format = 'gif'"
                                                :class="{'bg-green-600 text-white': settings.format === 'gif', 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200': settings.format !== 'gif'}"
                                                class="px-4 py-2 rounded-lg font-medium transition-all">
                                            GIF
                                        </button>
                                    </div>
                                </div>

                                <div x-show="settings.format === 'jpeg' || settings.format === 'webp'">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Качество: <span x-text="settings.quality" class="font-bold text-green-600"></span>%
                                    </label>
                                    <div class="relative">
                                        <input type="range" x-model="settings.quality" min="1" max="100" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700">
                                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                                            <span>Низкое</span>
                                            <span>Среднее</span>
                                            <span>Высокое</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Настройки размера -->
                        <div x-show="activeTab === 'resize'" class="space-y-6">
                            <div class="flex items-center justify-between mb-4">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" x-model="resizeEnabled" class="w-5 h-5 text-green-600 rounded focus:ring-green-500">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Изменить размер</span>
                                </label>
                                <button @click="preserveRatio = !preserveRatio" x-show="resizeEnabled"
                                        class="text-sm text-green-600 hover:text-green-700">
                                    <span x-text="preserveRatio ? '🔒 Сохранять пропорции' : '🔓 Свободное изменение'"></span>
                                </button>
                            </div>

                            <div x-show="resizeEnabled" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ширина (px)</label>
                                    <input type="number" x-model="settings.width" placeholder="Авто"
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Высота (px)</label>
                                    <input type="number" x-model="settings.height" placeholder="Авто"
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" x-model="settings.crop" class="w-5 h-5 text-green-600 rounded focus:ring-green-500">
                                        <span class="text-gray-700 dark:text-gray-300">Обрезать для точного соответствия</span>
                                    </label>
                                </div>
                            </div>

                            <div x-show="!resizeEnabled" class="text-center py-8 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                </svg>
                                <p>Включите опцию "Изменить размер" чтобы настроить параметры</p>
                            </div>
                        </div>

                        <!-- Эффекты -->
                        <div x-show="activeTab === 'effects'" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <button @click="settings.filter = ''"
                                    :class="{'ring-2 ring-green-500 bg-green-50 dark:bg-green-900/20': settings.filter === ''}"
                                    class="p-4 bg-gray-100 dark:bg-gray-700 rounded-xl text-center hover:bg-gray-200 transition-all">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm font-medium">Оригинал</span>
                            </button>
                            <button @click="settings.filter = 'grayscale'"
                                    :class="{'ring-2 ring-green-500 bg-green-50 dark:bg-green-900/20': settings.filter === 'grayscale'}"
                                    class="p-4 bg-gray-100 dark:bg-gray-700 rounded-xl text-center hover:bg-gray-200 transition-all">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                                <span class="text-sm font-medium">Ч/Б</span>
                            </button>
                            <button @click="settings.filter = 'sepia'"
                                    :class="{'ring-2 ring-green-500 bg-green-50 dark:bg-green-900/20': settings.filter === 'sepia'}"
                                    class="p-4 bg-gray-100 dark:bg-gray-700 rounded-xl text-center hover:bg-gray-200 transition-all">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                </svg>
                                <span class="text-sm font-medium">Сепия</span>
                            </button>
                            <button @click="settings.filter = 'blur'"
                                    :class="{'ring-2 ring-green-500 bg-green-50 dark:bg-green-900/20': settings.filter === 'blur'}"
                                    class="p-4 bg-gray-100 dark:bg-gray-700 rounded-xl text-center hover:bg-gray-200 transition-all">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15.536a5 5 0 010-7.072m-2.828 9.9a9 9 0 010-12.728"></path>
                                </svg>
                                <span class="text-sm font-medium">Размытие</span>
                            </button>
                            <button @click="settings.filter = 'sharpen'"
                                    :class="{'ring-2 ring-green-500 bg-green-50 dark:bg-green-900/20': settings.filter === 'sharpen'}"
                                    class="p-4 bg-gray-100 dark:bg-gray-700 rounded-xl text-center hover:bg-gray-200 transition-all">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 12a8 8 0 11-16 0 8 8 0 0116 0z"></path>
                                </svg>
                                <span class="text-sm font-medium">Резкость</span>
                            </button>
                        </div>

                        <!-- Дополнительные настройки -->
                        <div x-show="activeTab === 'advanced'" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Поворот
                                    </label>
                                    <div class="grid grid-cols-4 gap-2">
                                        <button @click="settings.rotate = 0"
                                                :class="{'bg-green-600 text-white': settings.rotate === 0}"
                                                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 transition-all">
                                            0°
                                        </button>
                                        <button @click="settings.rotate = 90"
                                                :class="{'bg-green-600 text-white': settings.rotate === 90}"
                                                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 transition-all">
                                            90°
                                        </button>
                                        <button @click="settings.rotate = 180"
                                                :class="{'bg-green-600 text-white': settings.rotate === 180}"
                                                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 transition-all">
                                            180°
                                        </button>
                                        <button @click="settings.rotate = 270"
                                                :class="{'bg-green-600 text-white': settings.rotate === 270}"
                                                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 transition-all">
                                            270°
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Яркость
                                    </label>
                                    <input type="range" x-model="settings.brightness" min="-100" max="100" class="w-full">
                                    <div class="text-center text-sm mt-1" x-text="settings.brightness + '%'"></div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Контраст
                                    </label>
                                    <input type="range" x-model="settings.contrast" min="-100" max="100" class="w-full">
                                    <div class="text-center text-sm mt-1" x-text="settings.contrast + '%'"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Кнопки действий -->
                        <div class="flex flex-wrap gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button @click="applySettings" :disabled="processing"
                                    class="flex-1 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 disabled:from-green-400 disabled:to-green-500 text-white px-6 py-3 rounded-xl font-semibold transition-all transform hover:scale-105 disabled:transform-none">
                                <span x-show="!processing">✨ Применить эффекты</span>
                                <span x-show="processing" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Обработка...
                            </span>
                            </button>
                            <button @click="downloadImage" :disabled="!resultUrl || processing"
                                    class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 disabled:from-blue-400 disabled:to-blue-500 text-white px-6 py-3 rounded-xl font-semibold transition-all transform hover:scale-105 disabled:transform-none">
                                💾 Скачать результат
                            </button>
                            <button @click="resetForm"
                                    class="px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-semibold transition-all">
                                🔄 Новое изображение
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function imageConverter() {
            return {
                previewUrl: null,
                resultUrl: null,
                originalFile: null,
                originalSize: '',
                originalWidth: 0,
                originalHeight: 0,
                processing: false,
                resizeEnabled: false,
                preserveRatio: true,
                activeTab: 'basic',

                settings: {
                    format: 'jpeg',
                    quality: 85,
                    width: null,
                    height: null,
                    keep_proportions: true,
                    crop: false,
                    filter: '',
                    rotate: 0,
                    brightness: 0,
                    contrast: 0
                },

                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (file) this.processFile(file);
                },

                handleDrop(event) {
                    const file = event.dataTransfer.files[0];
                    if (file) this.processFile(file);
                },

                processFile(file) {
                    if (!file.type.startsWith('image/')) {
                        this.showNotification('Пожалуйста, выберите изображение', 'error');
                        return;
                    }

                    if (file.size > 20 * 1024 * 1024) {
                        this.showNotification('Файл слишком большой. Максимум 20MB', 'error');
                        return;
                    }

                    this.originalFile = file;
                    this.originalSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';

                    // Получаем размеры изображения
                    const img = new Image();
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.previewUrl = e.target.result;
                        img.onload = () => {
                            this.originalWidth = img.width;
                            this.originalHeight = img.height;
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);

                    this.resultUrl = null;
                    this.settings.width = null;
                    this.settings.height = null;
                },

                async applySettings() {
                    if (!this.originalFile) return;

                    this.processing = true;
                    this.resultUrl = null;

                    const formData = new FormData();
                    formData.append('image', this.originalFile);
                    formData.append('format', this.settings.format);
                    formData.append('quality', this.settings.quality);
                    formData.append('filter', this.settings.filter);
                    formData.append('rotate', this.settings.rotate);
                    formData.append('brightness', this.settings.brightness);
                    formData.append('contrast', this.settings.contrast);

                    if (this.resizeEnabled) {
                        if (this.settings.width) formData.append('width', this.settings.width);
                        if (this.settings.height) formData.append('height', this.settings.height);
                        formData.append('keep_proportions', this.preserveRatio);
                        formData.append('crop', this.settings.crop);
                    }

                    try {
                        const response = await fetch('/converter/process', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || ''
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.resultUrl = data.url + '?t=' + Date.now();
                            this.showNotification('Изображение успешно обработано!', 'success');
                        } else {
                            this.showNotification(data.message || 'Ошибка обработки', 'error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.showNotification('Ошибка при обработке изображения', 'error');
                    } finally {
                        this.processing = false;
                    }
                },

                downloadImage() {
                    if (this.resultUrl) {
                        const a = document.createElement('a');
                        a.href = this.resultUrl;
                        a.download = `converted_${Date.now()}.${this.settings.format === 'jpeg' ? 'jpg' : this.settings.format}`;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        this.showNotification('Скачивание начато!', 'success');
                    }
                },

                resetForm() {
                    this.previewUrl = null;
                    this.resultUrl = null;
                    this.originalFile = null;
                    this.resizeEnabled = false;
                    this.activeTab = 'basic';
                    this.settings = {
                        format: 'jpeg',
                        quality: 85,
                        width: null,
                        height: null,
                        keep_proportions: true,
                        crop: false,
                        filter: '',
                        rotate: 0,
                        brightness: 0,
                        contrast: 0
                    };
                    document.getElementById('imageInput').value = '';
                },

                showNotification(message, type) {
                    // Простая реализация уведомления
                    alert(message);
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }

        input[type="range"] {
            -webkit-appearance: none;
            appearance: none;
            background: #e5e7eb;
            border-radius: 9999px;
            height: 8px;
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            background: #22c55e;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }

        input[type="range"]::-webkit-slider-thumb:hover {
            background: #16a34a;
            transform: scale(1.1);
        }

        .dark input[type="range"] {
            background: #374151;
        }
    </style>
@endsection
