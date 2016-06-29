<?php

namespace Sokil\FileStorageBundle\DependencyInjection\GaufretteAdapterFactory;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Knp\Bundle\GaufretteBundle\DependencyInjection\Factory\AdapterFactoryInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Local adapter factory
 *
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
class InternalAdapterFactory implements AdapterFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create(ContainerBuilder $container, $id, array $config)
    {
        // define path strategy
        $parentPathStrategyServiceName = 'file_storage.gaufrette.adapter.internal.pathStrategy.' . $config['pathStrategy']['name'];
        $pathStrategyServiceName = $parentPathStrategyServiceName . '.' . $id;

        $container
            ->setDefinition(
                $pathStrategyServiceName,
                new DefinitionDecorator($parentPathStrategyServiceName)
            )
            ->replaceArgument(0, $config['pathStrategy']['options']);

        // define internal adapter
        $container->setDefinition($id, new DefinitionDecorator('file_storage.gaufrette.adapter.internal'))
            ->replaceArgument(0, new Reference($pathStrategyServiceName));
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()
    {
        return 'internal';
    }

    /**
     * {@inheritDoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('pathStrategy')
                    ->children()
                        ->scalarNode('name')->end()
                        ->arrayNode('options') ->end()
            ->end();
    }
}
