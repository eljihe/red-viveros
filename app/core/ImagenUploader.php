<?php

declare(strict_types=1);

namespace App\Core;

final class ImagenUploader
{
    private function __construct()
    {
    }

    /**
     * Redimensiona a 1200px de lado mayor y guarda como JPEG con nombre aleatorio.
     * Si GD no está disponible o falla, guarda el archivo tal cual (fallback).
     *
     * @return string|null Nombre del archivo generado, o null si falló.
     */
    public static function guardarRedimensionada(string $tmpFile, string $carpetaAbsoluta): ?string
    {
        try {
            if (!is_dir($carpetaAbsoluta)) {
                mkdir($carpetaAbsoluta, 0755, true);
            }

            if (!is_writable($carpetaAbsoluta)) {
                Logger::error('Carpeta de uploads no es escribible', ['directorio' => $carpetaAbsoluta]);
                return null;
            }

            $filename = bin2hex(random_bytes(16)) . '.jpg';
            $path = $carpetaAbsoluta . '/' . $filename;

            if (!extension_loaded('gd')) {
                Logger::info('GD no disponible, guardando foto sin redimensionar', ['archivo_temporal' => basename($tmpFile)]);
                return self::copiarSinRedimensionar($tmpFile, $path, $filename);
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $tmpFile);
            finfo_close($finfo);

            switch ($mime) {
                case 'image/png':
                    $source = imagecreatefrompng($tmpFile);
                    break;
                case 'image/webp':
                    if (function_exists('imagecreatefromwebp')) {
                        $source = imagecreatefromwebp($tmpFile);
                    } else {
                        Logger::info('WebP recibido pero GD no lo redimensiona, se guarda como está');
                        return self::copiarSinRedimensionar($tmpFile, $path, $filename);
                    }
                    break;
                case 'image/jpeg':
                default:
                    $source = imagecreatefromjpeg($tmpFile);
                    break;
            }

            if ($source === false) {
                Logger::info('GD disponible pero no pudo procesar imagen, se guarda sin redimensionar');
                return self::copiarSinRedimensionar($tmpFile, $path, $filename);
            }

            $width = imagesx($source);
            $height = imagesy($source);

            if ($width > 1200 || $height > 1200) {
                $maxDim = max($width, $height);
                $ratio = 1200 / $maxDim;
                $newWidth = (int) ($width * $ratio);
                $newHeight = (int) ($height * $ratio);
                $resized = imagecreatetruecolor($newWidth, $newHeight);
                if ($resized !== false) {
                    imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    imagedestroy($source);
                    $source = $resized;
                }
            }

            $result = imagejpeg($source, $path, 82);
            imagedestroy($source);

            if ($result && file_exists($path)) {
                Logger::info('Foto redimensionada y guardada', ['archivo' => $filename]);
                return $filename;
            }

            Logger::error('imagejpeg() falló', ['ruta' => $path]);
            return null;
        } catch (\Throwable $e) {
            Logger::error('Excepción en guardarRedimensionada: ' . $e->getMessage(), ['excepcion' => get_class($e), 'linea' => $e->getLine()]);
            return null;
        }
    }

    public static function eliminar(string $carpetaAbsoluta, string $archivo): void
    {
        $path = $carpetaAbsoluta . '/' . basename($archivo);
        if (file_exists($path)) {
            unlink($path);
        }
    }

    private static function copiarSinRedimensionar(string $tmpFile, string $path, string $filename): ?string
    {
        if (copy($tmpFile, $path) && file_exists($path)) {
            Logger::info('Foto guardada sin redimensionar', ['archivo' => $filename]);
            return $filename;
        }

        Logger::error('copy() falló al guardar foto', ['origen' => $tmpFile, 'destino' => $path]);
        return null;
    }
}
