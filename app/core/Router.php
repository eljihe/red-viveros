<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<string, array<string, array{0: class-string, 1: string}>> */
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = $this->currentPath();

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            throw new HttpNotFoundException("Ruta no encontrada: {$method} /{$path}");
        }

        [$controllerClass, $action] = $handler;
        $controller = new $controllerClass();

        if (!method_exists($controller, $action)) {
            throw new HttpNotFoundException("Acción no encontrada: {$controllerClass}::{$action}");
        }

        $controller->$action();
    }

    private function currentPath(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = (string) parse_url($uri, PHP_URL_PATH);

        $baseUrl = rtrim((string) Config::get('app.base_url', ''), '/');

        if ($baseUrl !== '' && str_starts_with($path, $baseUrl)) {
            $path = substr($path, strlen($baseUrl));
        }

        return trim($path, '/');
    }

    private function normalize(string $path): string
    {
        return trim($path, '/');
    }
}
