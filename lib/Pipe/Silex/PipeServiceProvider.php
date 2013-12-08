<?php

namespace Pipe\Silex;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Pipe\Environment;

use CHH\Silex\CacheServiceProvider\CacheNamespace;
use Symfony\Component\HttpFoundation\Response;

class PipeServiceProvider implements ServiceProviderInterface
{
    const ROUTE_ASSET = 'pipe.asset';

    function register(Application $app)
    {
        $app->register(new \Silex\Provider\UrlGeneratorServiceProvider());

        $app['pipe.use_precompiled_gzip'] = true;

        if (isset($app['caches'])) {
            $app['caches'] = $app->share($app->extend('caches', function($caches) use ($app) {
                $caches['pipe'] = $app->share($app['cache.namespace']('pipe', $caches['default']));
                return $caches;
            }));
        }

        $app['pipe.precompile'] = function() {
            return array(
                'application.js',
                'application.css'
            );
        };

        $app['pipe.load_path'] = $app->share(function() use ($app) {
            if (isset($app['pipe.root'])) {
                $root = $app['pipe.root'];

                return array(
                    "$root/images",
                    "$root/javascripts",
                    "$root/vendor/javascripts",
                    "$root/stylesheets",
                    "$root/vendor/stylesheets",
                );
            }

            return array();
        });

        $app["pipe.manifest"] = $app->share(function() use ($app) {
            if (isset($app['pipe.precompile_directory'])) {
                return $app['pipe.precompile_directory'] . "/manifest.json";
            }

            return "manifest.json";
        });

        $app["pipe"] = $app->share(function($app) {
            return new PipeService($app);
        });

        $app['pipe.environment'] = $app->share(function() use ($app) {
            $environment = new Environment;

            if (isset($app['pipe.css_compressor'])) {
                $environment->setCssCompressor($app['pipe.css_compressor']);
            }

            if (isset($app['pipe.js_compressor'])) {
                $environment->setJsCompressor($app['pipe.js_compressor']);
            }

            foreach ($app["pipe.load_path"] as $path) {
                $environment->appendPath($path);
            }

            return $environment;
        });

        if (isset($app["twig"])) {
            $app['twig'] = $app->share($app->extend('twig', function($twig) {
                $twig->addExtension(new PipeTwigExtension);

                return $twig;
            }));
        }

        $app->get("/_pipe/asset/{logicalPath}", function($logicalPath) use ($app) {
            $asset = $app["pipe.environment"]->find($logicalPath, array('bundled' => true));

            if (!$asset) {
                return $app->abort(404, "Asset '$logicalPath' not found");
            }

            $lastModified = new \DateTime;
            $lastModified->setTimestamp($asset->getLastModified());

            $res = new Response();
            $res->setPublic();
            $res->setLastModified($lastModified);

            if ($res->isNotModified($app['request'])) {
                return $res;
            }

            $time = microtime(true);

            $res->headers->set("Content-Type", $asset->getContentType());
            $res->headers->set("Content-Length", strlen($asset->getBody()));
            $res->setContent($asset->getBody());

            if (isset($app["monolog"]) and $app["monolog"] !== null) {
                $d = microtime(true) - $time;

                $app["monolog"]->addInfo(
                    sprintf('pipe: Generated "%s" in %f seconds', $logicalPath, $d), array(
                        'time' => $d, 'path' => $logicalPath, 'realpath' => $asset->path
                    )
                );
            }

            return $res;
        })
        ->assert("logicalPath", ".+")
        ->bind(self::ROUTE_ASSET);
    }

    function boot(Application $app)
    {
    }
}
