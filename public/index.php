<?php

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

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$environment = data_wrap($_ENV);

$twig = Twig::create(root_path('templates'));
$twig->getEnvironment()
    ->addGlobal('default', $environment->default_template);

$app = AppFactory::create();
$app->add(TwigMiddleware::create($app, $twig));

(require_once root_path('src/index.php'))($app, $twig);

$app->run();
