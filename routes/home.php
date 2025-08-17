<?php

use Slim\App;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (App $app, Twig $twig) {
    $app->get(DIRECTORY_SEPARATOR, function (Request $request, Response $response) use ($twig) {
        global $templateNames;

        return $twig->render($response, $templateNames->home);
    });
};
