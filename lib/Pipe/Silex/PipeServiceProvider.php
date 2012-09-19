<?php

namespace Pipe\Silex;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Symfony\Component\HttpFoundation\Response;

class PipeServiceProvider implements ServiceProviderInterface
{
    function register(Application $app)
    {
        $app->register(new \Silex\Provider\UrlGeneratorServiceProvider());

        $app["pipe"] = $app->share(function($app) {
            return new PipeService($app);
        });

        $app->get("/_pipe/assets/{logicalPath}", function($logicalPath) use ($app) {
            $asset = $app["pipe"]->environment->find($logicalPath);

            if (!$asset) {
                return $app->abort(404, "Asset '$logicalPath' not found");
            }

            $res = new Response($asset->getBody(), 200);
            $res->headers->set("Content-Type", $asset->getContentType());
            $res->headers->set("Content-Length", strlen($asset->getBody()));

            if (isset($app["logger"]) and $app["logger"] !== null) {
                $app["logger"]->log("Pipe: Serving '$logicalPath'");
            }

            return $res;
        })
        ->assert("logicalPath", ".+")
        ->bind("pipe.assets");
    }

    function boot(Application $app)
    {
        if (isset($app["twig"])) {
            $app["twig"]->addExtension(new PipeTwigExtension);
        }
    }
}
