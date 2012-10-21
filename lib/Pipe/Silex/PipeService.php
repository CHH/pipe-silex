<?php

namespace Pipe\Silex;

use Pipe\Environment;
use Pipe\Config;
use Silex\Application;
use Jazz;
use CHH\Silex\CacheServiceProvider\CacheNamespace;

class PipeService
{
    public $environment;

    protected $app;

    function __construct(Application $app)
    {
        $this->app = $app;

        $config = new Config;
        $config->debug = isset($app['pipe.debug']) ? $app['pipe.debug'] : false;

        if (isset($app['caches'])) {
            $app['caches']['pipe'] = $app->share(function($app) {
                return new CacheNamespace('pipe', $app['caches']['default']);
            });
        }

        if (isset($app['pipe.css_compressor'])) {
            $config->cssCompressor = $app['pipe.css_compressor'];
        }

        if (isset($app['pipe.js_compressor'])) {
            $config->jsCompressor = $app['pipe.js_compressor'];
        }

        $this->environment = $config->createEnvironment();

        foreach ($app["pipe.load_path"] as $path) {
            $this->environment->appendPath($path);
        }
    }

    function assetLink($logicalPath)
    {
        if (!isset($this->app["pipe.use_precompiled"]) or false == $this->app["pipe.use_precompiled"]) {
            return $this->app["url_generator"]->generate(
                PipeServiceProvider::ROUTE_ASSET, array("logicalPath" => $logicalPath)
            );
        }

        $manifest = $this->manifest();

        if (isset($manifest->$logicalPath)) {
            return "{$app["pipe.prefix"]}/{$manifest->$logicalPath}";
        }
    }

    function assetLinkTag($logicalPath)
    {
        $asset = $this->environment->find($logicalPath);
        $html = '';

        $contentType = $asset->getContentType();

        switch ($contentType) {
            case "application/javascript":
                $html = Jazz::render(array(
                    '#script', array('src' => $this->assetLink($logicalPath), 'type' => $contentType)
                ));
                break;
            case "text/css":
                $html = Jazz::render(array(
                    '#link', array('rel' => 'stylesheet', 'href' => $this->assetLink($logicalPath), 'type' => $contentType)
                ));
                break;
            default:
                if (stripos($contentType, 'image/') === 0) {
                    $html = Jazz::render(array(
                        '#img', array('src' => $this->assetLink($logicalPath), 'alt' => '')
                    ));
                }
                break;
        }

        return $html;
    }

    protected function manifest()
    {
        if (null === $this->manifest) {
            if (isset($this->app['caches']['pipe'])) {
                $cache = $this->app['caches']['pipe'];

                if ($cache->contains('manifest')) {
                    $this->manifest = $cache->fetch('manifest');
                } else {
                    $this->manifest = $this->fetchManifest();
                    $cache->save('manifest', $this->manifest);
                }
            } else {
                $this->manifest = $this->fetchManifest();
            }
        }

        return $this->manifest;
    }

    private function fetchManifest()
    {
        return json_decode(@file_get_contents($this->app["pipe.manifest"]));
    }
}
