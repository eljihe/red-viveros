<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    private function __construct()
    {
    }

    public static function render(string $view, array $data = [], ?string $layout = 'layout'): string
    {
        $content = self::renderTemplate("views/{$view}.php", $data);

        if ($layout === null) {
            return $content;
        }

        $data['content'] = $content;

        return self::renderTemplate("views/layouts/{$layout}.php", $data);
    }

    private static function renderTemplate(string $relativePath, array $data): string
    {
        $file = dirname(__DIR__) . '/' . $relativePath;

        if (!is_file($file)) {
            throw new AppException("No se encontró la vista: {$relativePath}");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $file;

        return (string) ob_get_clean();
    }
}
