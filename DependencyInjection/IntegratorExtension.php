<?php

namespace UCI\Boson\IntegratorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use UCI\Boson\ExcepcionesBundle\DependencyInjection\ExcepcionesExtension;
use UCI\Boson\AspectBundle\DependencyInjection\AspectExtension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class IntegratorExtension extends ExcepcionesExtension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $extExcepciones = new ExcepcionesExtension();
        $extExcepciones->loadFileExcepciones($container);
        $AspectExt = new AspectExtension();
        $AspectExt->loadFileAspects($container);
        foreach($config as $configKey => $configVal) {
            $container->setParameter($configKey, $configVal);
        }
        $server =  $config['server'] ;
        $client =  $config['client'] ;
        $sensitive =  $config['server']['sensitive'] ;
        $container->setParameter('server', $server);
        $container->setParameter('client', $client);
        $container->setParameter('server.sensitive', $sensitive);


    }
}
