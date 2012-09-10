<?php

$app = require_once(__DIR__ . "/config/application.php");

$app->get("/", function() use ($app) {
    return $app['twig']->render("index.html");
});

