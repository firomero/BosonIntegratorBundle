<?php

namespace UCI\Boson\IntegratorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Esta es la clase que valida y mezcla las configuraciones de los ficheros app/config con las del sistema.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('integrator');
        $rootNode->children()
                    ->arrayNode('server')
                        ->children()
                            ->booleanNode('is_server')->defaultValue('true')->end()
                            ->arrayNode('app_list')
                                ->prototype('scalar')->end()
                            ->end()
//                            ->scalarNode('sensitive')->defaultValue('false')->end()
                        ->end()
                    ->end()
                    ->arrayNode('client')
                        ->children()
                            ->scalarNode('server')->defaultValue('http://localhost/api/rest')->end()
                        ->end()
                    ->end()
                ->end();

        return $treeBuilder;
    }
}
