@extends('layouts.main')

@section('content')
    <div class="max-w-6xl mx-auto">

        <!-- Шапка -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent">
                🎨 Редактор изображений
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Изменяйте параметры — результат обновляется автоматически</p>
            <div x-show="!isPremium" class="mt-2 inline-flex items-center gap-2 text-xs bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 px-3 py-1 rounded-full">
                <span>⭐</span>
                <span>Бесплатно до 5 МБ | Премиум до 50 МБ + AI улучшение + удаление метаданных + пакетная обработка + PDF</span>
                <button @click="showPremiumModal = true" class="font-bold underline hover:no-underline">Активировать</button>
            </div>
            <div x-show="isPremium" class="mt-2 inline-flex items-center gap-2 text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-3 py-1 rounded-full">
                <span>⭐</span>
                <span>Премиум активен</span>
            </div>
        </div>

        <!-- Режим: Одиночный / Пакетный -->
        <div class="flex justify-center gap-3 mb-6">
            <button @click="setBatchMode(false)" :class="{'bg-green-600 text-white': !batchMode, 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300': batchMode}" class="px-5 py-2 rounded-lg font-medium transition">
                📷 Одиночный режим
            </button>
            <button @click="toggleBatchMode()" :disabled="!isPremium && !batchMode" :class="{'bg-green-600 text-white': batchMode, 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300': !batchMode, 'opacity-50': !isPremium && !batchMode}" class="px-5 py-2 rounded-lg font-medium transition">
                📦 Пакетный режим <span x-show="!isPremium" class="text-xs ml-1">(Premium)</span>
            </button>
        </div>

        <!-- Пакетный режим -->
        <div x-show="batchMode" x-transition>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
                <h3 class="font-semibold mb-3 text-gray-700 dark:text-gray-300">📦 Пакетная загрузка (до 20 файлов)</h3>
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center cursor-pointer hover:border-green-400 transition"
                     @dragover.prevent="batchDragOver = true" @dragleave.prevent="batchDragOver = false" @drop.prevent="handleBatchDrop($event)">
                    <input type="file" id="batchInput" accept="image/*" multiple @change="handleBatchFiles" class="hidden">
                    <label for="batchInput" class="cursor-pointer block">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        </svg>
                        <p class="text-gray-600 dark:text-gray-400">Выберите до 20 изображений или перетащите их сюда</p>
                    </label>
                </div>

                <div x-show="batchFiles.length > 0" class="mt-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Файлы (<span x-text="batchFiles.length"></span>/20)</span>
                        <button @click="clearBatchFiles" class="text-xs text-red-500 hover:text-red-700">Очистить все</button>
                    </div>
                    <div class="max-h-60 overflow-y-auto space-y-2">
                        <template x-for="(file, idx) in batchFiles" :key="idx">
                            <div class="batch-file-item flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm" x-text="file.name"></span>
                                    <span class="text-xs text-gray-500" x-text="formatBytes(file.size)"></span>
                                    <span x-show="file.status === 'processing'" class="text-xs text-blue-500">⏳ обработка...</span>
                                    <span x-show="file.status === 'done'" class="text-xs text-green-500">✅ готово</span>
                                    <span x-show="file.status === 'error'" class="text-xs text-red-500">❌ ошибка</span>
                                </div>
                                <div class="flex gap-2">
                                    <button x-show="file.resultUrl" @click="downloadBatchFile(file)" class="text-xs bg-blue-500 text-white px-2 py-0.5 rounded">Скачать</button>
                                    <button @click="removeBatchFile(idx)" class="text-xs text-red-500">✕</button>
                                </div>
                            </div>
                        </template>
                    </div>
                    <button @click="processBatchFiles" :disabled="batchProcessing || batchFiles.length === 0" class="w-full mt-4 bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white py-2 rounded-lg font-medium transition">
                        <span x-show="!batchProcessing">🔄 Обработать все (<span x-text="batchFiles.length"></span> файлов)</span>
                        <span x-show="batchProcessing">⏳ Обработка <span x-text="batchProcessed"></span>/<span x-text="batchFiles.length"></span>...</span>
                    </button>
                    <button x-show="batchResults.length > 0" @click="downloadAllBatch" class="w-full mt-2 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium transition">
                        📦 Скачать все (ZIP)
                    </button>
                </div>
            </div>
        </div>

        <!-- Одиночный режим -->
        <div x-show="!batchMode" x-transition>
            <!-- Зона загрузки -->
            <div x-show="!previewUrl"
                 @dragover.prevent="dragOver = true" @dragleave.prevent="dragOver = false" @drop.prevent="handleDrop($event)"
                 :class="{'drag-over border-green-500 bg-green-50 dark:bg-green-900/20': dragOver}"
                 class="drop-zone border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-10 text-center cursor-pointer bg-white dark:bg-gray-800 hover:border-green-400 dark:hover:border-green-500 transition">
                <input type="file" id="fileInput" accept="image/*" @change="handleFile" class="hidden">
                <label for="fileInput" class="cursor-pointer block">
                    <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="text-lg font-medium text-gray-700 dark:text-gray-300">Выберите изображение</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">или перетащите его сюда</p>
                    <div class="flex justify-center gap-2 mt-4">
                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-xs text-gray-600 dark:text-gray-400">JPEG</span>
                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-xs text-gray-600 dark:text-gray-400">PNG</span>
                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-xs text-gray-600 dark:text-gray-400">WebP</span>
                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-xs text-gray-600 dark:text-gray-400">GIF</span>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-3">
                        Максимум <span x-show="!isPremium">5 МБ</span><span x-show="isPremium">50 МБ</span>
                    </p>
                </label>
            </div>

            <!-- Редактор -->
            <div x-show="previewUrl" x-transition>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden transition-colors">
                        <div class="bg-gray-800 dark:bg-gray-900 px-4 py-2 flex justify-between items-center">
                            <span class="text-white text-sm font-medium">📷 Оригинал</span>
                            <div>
                                <span class="text-gray-400 text-xs" x-text="originalWidth + 'x' + originalHeight"></span>
                                <span class="text-gray-400 text-xs ml-2" x-text="originalSize"></span>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-center" style="min-height: 350px;">
                            <div class="crop-container" x-ref="cropContainer">
                                <img :src="previewUrl" class="rounded-lg max-w-full object-contain" style="max-height: 320px; cursor: crosshair;"
                                     x-ref="originalImage"
                                     @load="initCrop"
                                     @mousedown="startDrawCrop($event)">
                                <template x-if="settings.crop && cropRect.width > 0">
                                    <div class="crop-overlay rounded-lg"
                                         :style="{ width: cropRect.width + 'px', height: cropRect.height + 'px', left: cropRect.x + 'px', top: cropRect.y + 'px' }"
                                         @mousedown.stop="startMoveCrop($event)">
                                        <div class="crop-handle nw" @mousedown.stop="startResizeCrop('nw', $event)"></div>
                                        <div class="crop-handle n" @mousedown.stop="startResizeCrop('n', $event)"></div>
                                        <div class="crop-handle ne" @mousedown.stop="startResizeCrop('ne', $event)"></div>
                                        <div class="crop-handle w" @mousedown.stop="startResizeCrop('w', $event)"></div>
                                        <div class="crop-handle e" @mousedown.stop="startResizeCrop('e', $event)"></div>
                                        <div class="crop-handle sw" @mousedown.stop="startResizeCrop('sw', $event)"></div>
                                        <div class="crop-handle s" @mousedown.stop="startResizeCrop('s', $event)"></div>
                                        <div class="crop-handle se" @mousedown.stop="startResizeCrop('se', $event)"></div>
                                    </div>
                                </template>
                                <div x-show="settings.crop && cropRect.width > 0" class="crop-instruction">🖱️ Тяни за область или за уголки</div>
                                <div x-show="!settings.crop" class="crop-instruction" style="background: rgba(0,0,0,0.5);">✂️ Включите "Обрезку" чтобы выделить область</div>
                            </div>
                        </div>
                        <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700 text-xs text-gray-500 dark:text-gray-400 flex justify-between">
                            <span>🎨 <span x-text="originalType"></span></span>
                            <span>📏 <span x-text="originalSize"></span></span>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden transition-colors">
                        <div class="bg-gradient-to-r from-green-600 to-blue-600 px-4 py-2 flex justify-between items-center">
                            <span class="text-white text-sm font-medium">✨ Результат</span>
                            <div>
                                <span x-show="resultWidth" class="text-green-100 text-xs" x-text="resultWidth + 'x' + resultHeight"></span>
                                <span x-show="resultSize" class="text-green-100 text-xs ml-2 font-bold" x-text="resultSize"></span>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-center" style="min-height: 350px;">
                            <div x-show="processing" class="text-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500 mx-auto"></div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">Обработка...</p>
                            </div>
                            <img x-show="resultUrl && !processing" :src="resultUrl" class="rounded-lg max-w-full object-contain" style="max-height: 320px;">
                            <div x-show="!resultUrl && !processing" class="text-center text-gray-400 dark:text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <p class="text-sm">Настройте параметры</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Панель настроек -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-5 transition-colors">
                    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Формат</label>
                            <select x-model="settings.format" @change="autoApply()" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-1.5 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200">
                                <option value="jpeg">JPEG</option>
                                <option value="png">PNG</option>
                                <option value="webp">WebP</option>
                                <option value="gif">GIF</option>
                            </select>
                        </div>

                        <div x-show="settings.format === 'jpeg' || settings.format === 'webp'" class="col-span-2">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                🗜️ Сжатие (качество) — <span x-text="settings.quality" class="text-green-600 font-bold"></span>%
                                <span x-show="resultSize" class="text-gray-500 dark:text-gray-400 ml-2" x-text="'→ ' + resultSize"></span>
                            </label>
                            <input type="range" x-model="settings.quality" @input="autoApply()" min="1" max="100" class="w-full">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Ширина</label>
                            <input type="number" x-model="settings.width" @input="autoApply()" placeholder="Авто" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-1.5 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Высота</label>
                            <input type="number" x-model="settings.height" @input="autoApply()" placeholder="Авто" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-1.5 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">🎨 Фильтр</label>
                            <select x-model="settings.filter" @change="autoApply()" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-1.5 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200">
                                <option value="">Нет</option>
                                <option value="grayscale">Ч/Б</option>
                                <option value="sepia">Сепия</option>
                                <option value="blur">Размытие</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">🔄 Поворот</label>
                            <select x-model="settings.rotate" @change="autoApply()" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-1.5 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200">
                                <option value="0">0°</option>
                                <option value="90">90°</option>
                                <option value="180">180°</option>
                                <option value="270">270°</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">🪞 Отражение</label>
                            <select x-model="settings.flip" @change="autoApply()" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-1.5 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200">
                                <option value="">Нет</option>
                                <option value="horizontal">↔️ Горизонт.</option>
                                <option value="vertical">↕️ Вертик.</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">📐 Соотношение</label>
                            <select x-model="settings.aspectRatio" @change="applyAspectRatio()" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-1.5 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200">
                                <option value="">Свободное</option>
                                <option value="1:1">1:1 (Квадрат)</option>
                                <option value="4:3">4:3 (Классическое)</option>
                                <option value="16:9">16:9 (Кино)</option>
                                <option value="3:2">3:2 (Фото)</option>
                                <option value="2:3">2:3 (Портрет)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">☀️ Яркость <span x-text="settings.brightness"></span></label>
                            <input type="range" x-model="settings.brightness" @input="autoApply()" min="-100" max="100" class="w-full">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">🌓 Контраст <span x-text="settings.contrast"></span></label>
                            <input type="range" x-model="settings.contrast" @input="autoApply()" min="-100" max="100" class="w-full">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">🎨 Насыщенность <span x-text="settings.saturation"></span></label>
                            <input type="range" x-model="settings.saturation" @input="autoApply()" min="-100" max="100" class="w-full">
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">⭐ Премиум функции</span>
                            <span x-show="!isPremium" class="text-xs bg-gradient-to-r from-yellow-500 to-orange-500 text-white px-2 py-0.5 rounded-full">Доступно в Premium</span>
                            <span x-show="isPremium" class="text-xs bg-green-500 text-white px-2 py-0.5 rounded-full">Активно</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <label class="flex items-center gap-2 cursor-pointer" :class="{'opacity-50': !isPremium}">
                                <input type="checkbox" x-model="settings.removeMetadata" @change="autoApply()" :disabled="!isPremium" class="w-4 h-4 text-green-600 rounded">
                                <span class="text-sm text-gray-700 dark:text-gray-300">🛡️ Удалить метаданные (EXIF)</span>
                                <span x-show="!isPremium" class="text-xs text-yellow-500">Premium</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer" :class="{'opacity-50': !isPremium}">
                                <input type="checkbox" x-model="settings.aiEnhance" @change="autoApply()" :disabled="!isPremium" class="w-4 h-4 text-green-600 rounded">
                                <span class="text-sm text-gray-700 dark:text-gray-300">✨ AI улучшение качества</span>
                                <span x-show="!isPremium" class="text-xs text-yellow-500">Premium</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer" :class="{'opacity-50': !isPremium}">
                                <input type="checkbox" x-model="settings.exportPDF" @change="applyPDFExport()" :disabled="!isPremium" class="w-4 h-4 text-green-600 rounded">
                                <span class="text-sm text-gray-700 dark:text-gray-300">📄 Экспорт в PDF</span>
                                <span x-show="!isPremium" class="text-xs text-yellow-500">Premium</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex flex-wrap items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="settings.crop" @change="toggleCrop()" class="w-4 h-4 text-green-600 rounded">
                                <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">✂️ Режим обрезки</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="settings.keepProportions" @change="autoApply()" class="w-4 h-4 text-green-600 rounded">
                                <span class="text-sm text-gray-700 dark:text-gray-300">🔒 Сохранять пропорции</span>
                            </label>
                            <button @click="resetCrop" x-show="settings.crop" class="text-xs text-red-500 hover:text-red-700">Сбросить обрезку</button>
                        </div>
                        <div class="flex gap-2">
                            <button @click="downloadImage" :disabled="!resultUrl" class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-5 py-1.5 rounded-lg text-sm font-medium transition">💾 Скачать</button>
                            <button @click="resetForm" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-5 py-1.5 rounded-lg text-sm font-medium transition">🔄 Сбросить</button>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mt-4 pt-3 border-t border-gray-200 dark:border-gray-700">
                        <span class="text-xs text-gray-500 dark:text-gray-400 mr-1">⚡ Пресеты:</span>
                        <button @click="applyPreset('social')" class="text-xs bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 px-3 py-1 rounded-full text-gray-700 dark:text-gray-300">📱 Соцсети 1080x1080</button>
                        <button @click="applyPreset('youtube')" class="text-xs bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 px-3 py-1 rounded-full text-gray-700 dark:text-gray-300">▶️ YouTube 1280x720</button>
                        <button @click="applyPreset('avatar')" class="text-xs bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 px-3 py-1 rounded-full text-gray-700 dark:text-gray-300">👤 Аватар 400x400</button>
                        <button @click="applyPreset('vk')" class="text-xs bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 px-3 py-1 rounded-full text-gray-700 dark:text-gray-300">📘 ВКонтакте 1200x630</button>
                    </div>

                    <div x-show="autoUpdate" class="text-center mt-3">
                    <span class="inline-flex items-center gap-1 text-xs text-green-600">
                        <svg class="w-3 h-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Применяем...
                    </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Футер -->
        <div class="text-center mt-8 pt-4 border-t border-gray-200 dark:border-gray-700">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Используя сервис, вы соглашаетесь с
                <a href="/privacy-policy" class="text-green-600 hover:text-green-700">Политикой конфиденциальности</a>
            </p>
        </div>
    </div>
@endsection
