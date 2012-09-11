<?php

require_once(__DIR__ . "/../../vendor/autoload.php");

$app = new Silex\Application;

$app->register(new Silex\Provider\TwigServiceProvider, array(
    "twig.path" => __DIR__ . "/../views/"
));

$app->register(new Pipe\Silex\PipeServiceProvider, array(
    # Setup the Pipe root and use the default load path setup:
    #
    # - $root/vendor/stylesheets
    # - $root/stylesheets/
    # - $root/vendor/javascripts
    # - $root/javascripts
    #
    "pipe.root" => __DIR__ . "/../assets/",
));

$env = @$_SERVER["APP_ENVIRONMENT"] ?: "development";

# Load a environment specific configuration file
if (file_exists(__DIR__ . "/environments/$env.php")) {
    require_once(__DIR__ . "/environments/$env.php");
}

return $app;
