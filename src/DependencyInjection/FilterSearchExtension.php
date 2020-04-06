<?php

namespace FilterSearch\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class FilterSearchExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . "/../../config"));
        $loader->load("services.xml");

        $this->loadTwigTheme($container);
    }

    private function loadTwigTheme(ContainerBuilder $container)
    {
        if(!$container->hasParameter('twig.form.resources')) {
            return;
        }

        $container->setParameter('twig.form.resources', array_merge([
            '@FilterSearch/filter_search.html.twig',
            '@FilterSearch/search_modules.html.twig',
        ], $container->getParameter('twig.form.resources')));
    }
}
