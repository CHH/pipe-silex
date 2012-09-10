<?php

require_once(__DIR__ . "/../vendor/autoload.php");

$app = new Silex\Application;

switch (@$_SERVER["APP_ENVIRONMENT"]) {
    case "development":
        $app["debug"] = true;
        $app["pipe.use_precompiled"] = false;
        $app["pipe.debug"] = true;
        break;
    case "production":
    default:
        break;
}

$app->register(new Silex\Provider\TwigServiceProvider, array(
    "twig.path" => __DIR__ . "/views"
));

$app->register(new Pipe\Silex\PipeServiceProvider, array(
    "pipe.root" => __DIR__ . "/assets/",
));

$app->get("/", function() use ($app) {
    return $app['twig']->render("index.html");
});
