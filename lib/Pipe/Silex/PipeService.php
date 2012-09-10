<?php

namespace Pipe\Silex;

use Pipe\Environment;
use Silex\Application;
use Jazz;

class PipeService
{
    public $environment;

    protected $app;

    function __construct(Application $app)
    {
        $this->environment = new Environment;
        $this->app = $app;

        if (isset($app["pipe.root"]) and !isset($app["pipe.load_path"])) {
            $root = $app["pipe.root"];

            $app["pipe.load_path"] = array(
                "$root/vendor/stylesheets",
                "$root/stylesheets",
                "$root/vendor/javascripts",
                "$root/javascripts",
            );
        }

        foreach ($app["pipe.load_path"] as $path) {
            $this->environment->appendPath($path);
        }
    }

    function assetLink($logicalPath)
    {
        if (!isset($this->app["pipe.use_precompiled"]) or false == $this->app["pipe.use_precompiled"]) {
            return $this->app["url_generator"]->generate(
                "pipe.assets", array("logicalPath" => $logicalPath)
            );
        }

        $manifest = json_decode(@file_get_contents($app["pipe.manifest"]));

        if (isset($manifest->$logicalPath)) {
            return "{$app["pipe.prefix"]}/{$manifest->$logicalPath}";
        }
    }

    function assetLinkTag($logicalPath)
    {
        $asset = $this->environment->find($logicalPath);
        $html = '';

        switch ($asset->getContentType()) {
            case "application/javascript":
                $html = Jazz::render(array(
                    '#script', array('src' => $this->assetLink($logicalPath), 'type' => $asset->getContentType())
                ));
                break;
            case "text/css":
                $html = Jazz::render(array(
                    '#link', array('rel' => 'stylesheet', 'href' => $this->assetLink($logicalPath), 'type' => $asset->getContentType())
                ));
                break;
        }

        return $html;
    }
}
