<?php

namespace Sokil\FileStorageBundle\GaufretteAdapter;

use Knp\Bundle\GaufretteBundle\DependencyInjection\KnpGaufretteExtension;
use Sokil\FileStorageBundle\DependencyInjection\FileStorageExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class InternalTest extends \PHPUnit_Framework_TestCase
{
    public function testInitAdapter()
    {
        // knp gaufrette config
        $config = [
            0 => [
                'factories' => [
                    __DIR__ . '/../../src/Resources/config/adapter_factories.xml',
                ],
                'adapters' => [
                    'acme_internal' => [
                        'internal' => [
                            'pathStrategy' => [
                                'name' => 'chunkPath',
                                'options' => [
                                    'chunksNumber' => 2,
                                    'chunkSize' => 3,
                                    'preserveExtension' => false,
                                    'baseDir' => '/tmp',
                                ],
                            ],
                        ],
                    ],
                ],
                'filesystems' => [
                    'acme_internal' => [
                        'adapter' => 'acme_internal',
                    ],
                ],
            ],
        ];

        $container = new ContainerBuilder();

        // add mocks of dependencies
        $container->set(
            'doctrine.orm.default_entity_manager',
            $this->getMock(
                '\Doctrine\ORM\EntityManager',
                [],
                [],
                '',
                false
            )
        );

        // knp gaufrette bundle
        $knpGaufretteExtension = new KnpGaufretteExtension();
        $knpGaufretteExtension->load($config, $container);

        // file storage bundle
        $fileStorageExtension = new FileStorageExtension();
        $fileStorageExtension->load([], $container);

        // compile container
        $container->compile();

        // get filesystem
        /* @var $filesystem \Gaufrette\Filesystem */
        $filesystemMap = $container->get('knp_gaufrette.filesystem_map');
        $filesystem = $filesystemMap->get('acme_internal');
        $adapter = $filesystem->getAdapter();

        $this->assertInstanceOf(
            '\Sokil\FileStorageBundle\GaufretteAdapter\Internal',
            $adapter
        );
    }
}
