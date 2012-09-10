<?php

require_once(__DIR__ . "/../../vendor/autoload.php");

$app = new Silex\Application;

$env = @$_SERVER["APP_ENVIRONMENT"] ?: "development";

if (file_exists(__DIR__ . "/environments/$env.php")) {
    require_once(__DIR__ . "/environments/$env.php");
}

$app->register(new Silex\Provider\TwigServiceProvider, array(
    "twig.path" => __DIR__ . "/../views/"
));

$app->register(new Pipe\Silex\PipeServiceProvider, array(
    "pipe.root" => __DIR__ . "/../assets/",
));

return $app;
