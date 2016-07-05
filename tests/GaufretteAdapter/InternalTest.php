<?php

namespace Sokil\FileStorageBundle\GaufretteAdapter;

use Knp\Bundle\GaufretteBundle\DependencyInjection\KnpGaufretteExtension;
use Sokil\FileStorageBundle\DependencyInjection\FileStorageExtension;
use Sokil\FileStorageBundle\Entity\File;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class InternalTest extends \PHPUnit_Framework_TestCase
{
    private function setPrivateProperty($object, $propertyName, $value)
    {
        $reflectionClass = new \ReflectionClass($object);
        $property = $reflectionClass->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    /**
     * @return ContainerBuilder
     */
    public function createContainer()
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

        // init entity
        $file = new File();
        $this->setPrivateProperty($file, 'id', 42);

        // init repository
        $repositoryMock = $this
            ->getMockBuilder('\Sokil\FileStorageBundle\Repository\FileRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock
            ->expects($this->any())
            ->method('find')
            ->will($this->returnValue($file));

        // init entity manager
        $entityManagerMock = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $entityManagerMock
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repositoryMock));

        // add mocks of dependencies
        $container->set(
            'doctrine.orm.default_entity_manager',
            $entityManagerMock
        );

        // file storage bundle
        $fileStorageExtension = new FileStorageExtension();
        $fileStorageExtension->load([], $container);

        // knp gaufrette bundle
        $knpGaufretteExtension = new KnpGaufretteExtension();
        $knpGaufretteExtension->load($config, $container);


        // compile container
        $container->compile();

        return $container;
    }

    public function testInitAdapter()
    {
        $container = $this->createContainer();

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

    public function testGetPathByKey()
    {
        $adapter = $this->createContainer()
            ->get('knp_gaufrette.filesystem_map')
            ->get('acme_internal')
            ->getAdapter();

        $reflectedAdapter = new \ReflectionClass($adapter);
        $reflectedMethod = $reflectedAdapter->getMethod('getPathById');
        $reflectedMethod->setAccessible(true);
        $path = $reflectedMethod->invoke($adapter, 42);

        $this->assertSame('/tmp/042/000/42', $path);
    }
}
