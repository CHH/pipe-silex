<?php

namespace Pipe\Silex;

class PipeTwigExtension extends \Twig_Extension
{
    function getName()
    {
        return "pipe";
    }

    function getFunctions()
    {
        return array(
            "pipe_link_tag" => new \Twig_Function_Method($this, "assetLinkTag", array(
                "needs_environment" => true, "is_safe" => array("html")
            )),
            "pipe_link" => new \Twig_Function_Method($this, "assetLink", array(
                "needs_environment" => true, "is_safe" => array("html")
            ))
        );
    }

    function assetLinkTag(\Twig_Environment $twig, $path)
    {
        $globals = $twig->getGlobals();
        return $globals["app"]["pipe"]->assetLinkTag($path);
    }

    function assetLink(\Twig_Environment $twig, $path)
    {
        $globals = $twig->getGlobals();
        return $globals["app"]["pipe"]->assetLink($path);
    }
}
