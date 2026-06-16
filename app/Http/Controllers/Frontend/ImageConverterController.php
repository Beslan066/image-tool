<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\ImageProcessingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageConverterController extends Controller
{
    protected $imageProcessor;

    public function __construct(ImageProcessingService $imageProcessor)
    {
        $this->imageProcessor = $imageProcessor;
    }

    public function index()
    {
        return view('converter.index');
    }

    public function process(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:51200',
            'format' => 'nullable|in:jpeg,png,webp,gif',
            'quality' => 'nullable|integer|min:1|max:100',
            'width' => 'nullable|integer|min:1|max:4000',
            'height' => 'nullable|integer|min:1|max:4000',
            'crop' => 'nullable|boolean',
            'crop_x' => 'nullable|integer',
            'crop_y' => 'nullable|integer',
            'crop_width' => 'nullable|integer',
            'crop_height' => 'nullable|integer',
            'keep_proportions' => 'nullable|boolean',
            'rotate' => 'nullable|integer|in:0,90,180,270',
            'filter' => 'nullable|in:grayscale,sepia,blur,sharpen',
            'flip' => 'nullable|in:horizontal,vertical',
            'brightness' => 'nullable|integer|min:-100|max:100',
            'contrast' => 'nullable|integer|min:-100|max:100',
            'saturation' => 'nullable|integer|min:-100|max:100',
            'opacity' => 'nullable|integer|min:0|max:100',
            'remove_metadata' => 'nullable|boolean',
            'ai_enhance' => 'nullable|boolean',
        ]);

        $user = $request->user();
        $isPremium = $user && $user->isPremiumActive();

        $file = $request->file('image');

        // Проверка на размер файла
        $fileSize = $file->getSize();
        $maxSize = $isPremium ? 50 * 1024 * 1024 : 5 * 1024 * 1024;

        if ($fileSize > $maxSize) {
            return response()->json([
                'success' => false,
                'message' => $isPremium ? 'Файл слишком большой. Максимум 50MB' : 'Бесплатно до 5MB. Оформите Premium для загрузки файлов до 50MB',
                'requires_premium' => !$isPremium
            ], 403);
        }

        // Проверка премиум функций
        if ($request->input('remove_metadata', false) && !$isPremium) {
            return response()->json([
                'success' => false,
                'message' => 'Удаление метаданных доступно только в Premium подписке',
                'requires_premium' => true
            ], 403);
        }

        if ($request->input('ai_enhance', false) && !$isPremium) {
            return response()->json([
                'success' => false,
                'message' => 'AI улучшение качества доступно только в Premium подписке',
                'requires_premium' => true
            ], 403);
        }

        try {
            $this->validateImageSecurity($file);
            $gdResource = $this->createGdResource($file);

            // Применяем обрезку (если есть)
            if ($request->input('crop', false) && $request->has('crop_width') && $request->has('crop_height')) {
                $cropX = (int)$request->input('crop_x', 0);
                $cropY = (int)$request->input('crop_y', 0);
                $cropW = (int)$request->input('crop_width');
                $cropH = (int)$request->input('crop_height');

                $originalWidth = imagesx($gdResource);
                $originalHeight = imagesy($gdResource);

                $cropX = max(0, min($cropX, $originalWidth - 1));
                $cropY = max(0, min($cropY, $originalHeight - 1));
                $cropW = min($cropW, $originalWidth - $cropX);
                $cropH = min($cropH, $originalHeight - $cropY);

                if ($cropW > 0 && $cropH > 0) {
                    $cropped = imagecreatetruecolor($cropW, $cropH);
                    $this->preserveTransparency($cropped);
                    imagecopyresampled($cropped, $gdResource, 0, 0, $cropX, $cropY, $cropW, $cropH, $cropW, $cropH);
                    imagedestroy($gdResource);
                    $gdResource = $cropped;
                }
            }

            // Изменение размера
            $targetWidth = $request->input('width');
            $targetHeight = $request->input('height');
            $keepProportions = $request->input('keep_proportions', true);

            if (($targetWidth && $targetWidth > 0) || ($targetHeight && $targetHeight > 0)) {
                $currentWidth = imagesx($gdResource);
                $currentHeight = imagesy($gdResource);

                if ($targetWidth && !$targetHeight) {
                    $ratio = $currentWidth / $currentHeight;
                    $targetHeight = (int)($targetWidth / $ratio);
                } elseif (!$targetWidth && $targetHeight) {
                    $ratio = $currentWidth / $currentHeight;
                    $targetWidth = (int)($targetHeight * $ratio);
                }

                if (!$targetWidth) $targetWidth = $currentWidth;
                if (!$targetHeight) $targetHeight = $currentHeight;

                if ($keepProportions) {
                    $ratio = min($targetWidth / $currentWidth, $targetHeight / $currentHeight);
                    $newWidth = max(1, (int)($currentWidth * $ratio));
                    $newHeight = max(1, (int)($currentHeight * $ratio));
                } else {
                    $newWidth = $targetWidth;
                    $newHeight = $targetHeight;
                }

                $resized = imagecreatetruecolor($newWidth, $newHeight);
                $this->preserveTransparency($resized);
                imagecopyresampled($resized, $gdResource, 0, 0, 0, 0, $newWidth, $newHeight, $currentWidth, $currentHeight);
                imagedestroy($gdResource);
                $gdResource = $resized;
            }

            // Поворот
            $rotate = $request->input('rotate', 0);
            if ($rotate != 0) {
                $gdResource = imagerotate($gdResource, $rotate, 0);
            }

            // Отражение
            $flip = $request->input('flip');
            if ($flip) {
                $width = imagesx($gdResource);
                $height = imagesy($gdResource);
                $flipped = imagecreatetruecolor($width, $height);
                $this->preserveTransparency($flipped);

                if ($flip === 'horizontal') {
                    imagecopyresampled($flipped, $gdResource, 0, 0, ($width - 1), 0, $width, $height, -$width, $height);
                } elseif ($flip === 'vertical') {
                    imagecopyresampled($flipped, $gdResource, 0, 0, 0, ($height - 1), $width, $height, $width, -$height);
                }
                imagedestroy($gdResource);
                $gdResource = $flipped;
            }

            // Фильтры
            $filter = $request->input('filter');
            if ($filter) {
                switch ($filter) {
                    case 'grayscale':
                        imagefilter($gdResource, IMG_FILTER_GRAYSCALE);
                        break;
                    case 'sepia':
                        imagefilter($gdResource, IMG_FILTER_GRAYSCALE);
                        imagefilter($gdResource, IMG_FILTER_COLORIZE, 90, 60, 30);
                        break;
                    case 'blur':
                        imagefilter($gdResource, IMG_FILTER_GAUSSIAN_BLUR);
                        break;
                    case 'sharpen':
                        imagefilter($gdResource, IMG_FILTER_EDGEDETECT);
                        break;
                }
            }

            // Яркость/Контраст/Насыщенность/Прозрачность
            if ($request->input('brightness', 0) != 0) {
                imagefilter($gdResource, IMG_FILTER_BRIGHTNESS, $request->brightness);
            }
            if ($request->input('contrast', 0) != 0) {
                imagefilter($gdResource, IMG_FILTER_CONTRAST, $request->contrast);
            }
            if ($request->input('saturation', 0) != 0) {
                imagefilter($gdResource, IMG_FILTER_SATURATE, $request->saturation);
            }

            $format = $request->input('format', 'jpeg');
            $quality = $request->input('quality', 85);

            $resultWidth = imagesx($gdResource);
            $resultHeight = imagesy($gdResource);
            $blob = $this->getImageBlob($gdResource, $format, $quality, $request->input('remove_metadata', false));


            imagedestroy($gdResource);

            return response()->json([
                'success' => true,
                'message' => 'Изображение обработано',
                'base64' => 'data:image/' . $format . ';base64,' . base64_encode($blob),
                'width' => $resultWidth,
                'height' => $resultHeight
            ]);

        } catch (\Exception $e) {
            \Log::error('Image conversion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка обработки: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkPremium(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'is_premium' => $user ? $user->isPremiumActive() : false,
            'premium_until' => $user && $user->premium_until ? $user->premium_until->format('d.m.Y') : null
        ]);
    }

    private function createGdResource($file)
    {
        $path = $file->getRealPath();
        $mime = $file->getMimeType();

        switch ($mime) {
            case 'image/jpeg':
                return imagecreatefromjpeg($path);
            case 'image/png':
                $resource = imagecreatefrompng($path);
                imagealphablending($resource, false);
                imagesavealpha($resource, true);
                return $resource;
            case 'image/gif':
                return imagecreatefromgif($path);
            case 'image/webp':
                return imagecreatefromwebp($path);
            default:
                throw new \Exception('Неподдерживаемый формат');
        }
    }

    private function preserveTransparency($image)
    {
        imagealphablending($image, false);
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefilledrectangle($image, 0, 0, imagesx($image), imagesy($image), $transparent);
    }

    private function getImageBlob($gdResource, $format, $quality = 85, $removeMetadata = false)
    {
        $quality = max(1, min(100, $quality));

        ob_start();
        switch ($format) {
            case 'jpeg':
                // Для JPEG используем imagejpeg, который не добавляет комментарии
                imagejpeg($gdResource, null, $quality);
                break;
            case 'png':
                $compression = 9 - (int)($quality / 11.11);
                $compression = max(0, min(9, $compression));
                imagepng($gdResource, null, $compression);
                break;
            case 'webp':
                imagewebp($gdResource, null, $quality);
                break;
            case 'gif':
                imagegif($gdResource);
                break;
            default:
                imagejpeg($gdResource, null, $quality);
        }
        $blob = ob_get_clean();

        // Если нужно удалить метаданные
        if ($removeMetadata) {
            $blob = $this->stripMetadata($blob, $format);
        }

        return $blob;
    }

    /**
     * Полное удаление метаданных из изображения
     */
    private function stripMetadata($blob, $format)
    {
        if ($format === 'jpeg') {
            // Для JPEG удаляем все маркеры метаданных
            // Сохраняем только SOI (FF D8) и EOI (FF D9)
            $markers = [
                "\xFF\xE0", // APP0 (JFIF)
                "\xFF\xE1", // APP1 (EXIF)
                "\xFF\xE2", // APP2
                "\xFF\xE3", // APP3
                "\xFF\xE4", // APP4
                "\xFF\xE5", // APP5
                "\xFF\xE6", // APP6
                "\xFF\xE7", // APP7
                "\xFF\xE8", // APP8
                "\xFF\xE9", // APP9
                "\xFF\xEA", // APP10
                "\xFF\xEB", // APP11
                "\xFF\xEC", // APP12
                "\xFF\xED", // APP13
                "\xFF\xEE", // APP14
                "\xFF\xEF", // APP15
                "\xFF\xFE", // COM (Comment)
            ];

            $result = "\xFF\xD8"; // SOI маркер
            $pos = 2;
            $length = strlen($blob);

            while ($pos < $length) {
                if ($pos + 1 >= $length) break;

                $marker = substr($blob, $pos, 2);
                $isMetadataMarker = false;

                foreach ($markers as $m) {
                    if ($marker === $m) {
                        $isMetadataMarker = true;
                        break;
                    }
                }

                if ($isMetadataMarker) {
                    // Пропускаем маркер и данные
                    if ($pos + 3 >= $length) break;
                    $segmentLength = unpack('n', substr($blob, $pos + 2, 2))[1];
                    $pos += 2 + $segmentLength;
                } else {
                    // Копируем данные до следующего маркера
                    $nextPos = $pos + 1;
                    while ($nextPos < $length && substr($blob, $nextPos, 1) !== "\xFF") {
                        $nextPos++;
                    }
                    $result .= substr($blob, $pos, $nextPos - $pos);
                    $pos = $nextPos;
                }
            }

            return $result;
        }

        // Для PNG можно пересохранить без метаданных
        if ($format === 'png') {
            // PNG уже не содержит метаданных при создании через GD
            return $blob;
        }

        return $blob;
    }

    private function validateImageSecurity($file)
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \Exception('Неподдерживаемый формат файла');
        }

        $handle = fopen($file->getRealPath(), 'rb');
        $content = fread($handle, 1024);
        fclose($handle);

        $dangerousPatterns = ['/<\?php/i', '/<script/i', '/javascript:/i', '/eval/i', '/base64_decode/i'];
        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                throw new \Exception('Обнаружен подозрительный код в изображении');
            }
        }

        if (!@getimagesize($file->getRealPath())) {
            throw new \Exception('Некорректное изображение');
        }
    }

    public function privacyPolicy()
    {
        return view('privacy-policy');
    }
}
