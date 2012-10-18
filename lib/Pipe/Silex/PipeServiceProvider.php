<?php

namespace Pipe\Silex;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Symfony\Component\HttpFoundation\Response;

class PipeServiceProvider implements ServiceProviderInterface
{
    const ROUTE_ASSET = 'pipe.asset';

    function register(Application $app)
    {
        $app->register(new \Silex\Provider\UrlGeneratorServiceProvider());

        $app['pipe.precompile'] = new \ArrayObject(array(
            'application.js',
            'application.css'
        ));

        $app['pipe.load_path'] = $app->share(function() use ($app) {
            $loadPath = new \SplDoublyLinkedList;

            if (isset($app['pipe.root'])) {
                $root = $app['pipe.root'];

                foreach (array(
                    "$root/images",
                    "$root/javascripts",
                    "$root/vendor/javascripts",
                    "$root/stylesheets",
                    "$root/vendor/stylesheets",
                ) as $path) {
                    $loadPath->push($path);
                }
            }

            return $loadPath;
        });

        $app["pipe"] = $app->share(function($app) {
            return new PipeService($app);
        });

        $app->get("/_pipe/asset/{logicalPath}", function($logicalPath) use ($app) {
            $asset = $app["pipe"]->environment->find($logicalPath, array('bundled' => true));

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
        ->bind(self::ROUTE_ASSET);
    }

    function boot(Application $app)
    {
        if (isset($app["twig"])) {
            $app["twig"]->addExtension(new PipeTwigExtension);
        }
    }
}
