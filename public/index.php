<?php

use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

function root_path(string $path): string {
    return dirname(__DIR__) . DIRECTORY_SEPARATOR . $path;
}

function data_wrap(array $data = []): object {
    return new class($data) {
        public array $attributes = [];

        public function __construct(array $data) {
            $this->attributes = array_change_key_case($data);
        }

        public function __get(string $key) {
            return $this->attributes[$key] ?? null;
        }

        public function __set(string $key, $value): void {
            $this->attributes[$key] = $value;
        }
        public function all(): array {
            return $this->attributes;
        }
    };
}

require root_path('vendor/autoload.php');

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
$environment = data_wrap($_ENV);

$templates = root_path('templates'); // All views

$twig = Twig::create($templates);

// The same base template is used for all views

$twig->getEnvironment()->addGlobal('base', $environment->base_template);

$templateNames = data_wrap();
foreach (scandir($templates) as $template) {
    if (strlen($template) <= 2) {
        continue;
    }
    $templateNames->{substr($template, 0, -10)} = $template;
}

$app = AppFactory::create();
$app->add(TwigMiddleware::create($app, $twig));

$routes = root_path('routes'); // I can define all the routes I need

foreach (scandir($routes) as $route) {
    if (strlen($route) <= 2) {
        continue;
    }
    $path = basename($routes) . DIRECTORY_SEPARATOR . $route;

    (require_once root_path($path))($app, $twig);
}
$app->run();
