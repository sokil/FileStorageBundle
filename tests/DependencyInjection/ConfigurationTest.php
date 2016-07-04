<?php

namespace Sokil\FileStorageBundle\DependencyInjection;

use Knp\Bundle\GaufretteBundle\DependencyInjection\KnpGaufretteExtension;
use Sokil\FileStorageBundle\FileBuilder\UploadedFileBuilder;
use Sokil\FileStorageBundle\FileWriter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sokil\Upload\HandlerFactory;
use Sokil\Upload\Handler;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    private $containerBuilder;

    public function setUp()
    {
        $this->containerBuilder = new ContainerBuilder();

        //$this->containerBuilder->setParameter('kernel.debug', true);

        // add mocks of dependencies
        $this->containerBuilder->set(
            'doctrine.orm.default_entity_manager',
            $this->getMock(
                '\Doctrine\ORM\EntityManager',
                [],
                [],
                '',
                false
            )
        );

        // init gaufrette bundle
        $gaufretteExtension = new KnpGaufretteExtension();
        $this->containerBuilder->registerExtension($gaufretteExtension);
        $this->containerBuilder->loadFromExtension($gaufretteExtension->getAlias());

        // init file storage bundle
        $fileStorageExtension = new FileStorageExtension();
        $this->containerBuilder->registerExtension($fileStorageExtension);
        $this->containerBuilder->loadFromExtension($fileStorageExtension->getAlias());

        // compile configuration
        $this->containerBuilder->compile();
    }

    public function tearDown()
    {
        $this->containerBuilder = null;
    }

    public function testGetFileStorage()
    {
        $fileStorage = $this->containerBuilder->get('file_storage');
        $this->assertInstanceOf('\Sokil\FileStorageBundle\FileStorage', $fileStorage);
    }

    public function testGetInternalAdapterPathStrategyChunkPath()
    {
        $stretegy = $this->containerBuilder->get('file_storage.adapter.internal.pathstrategy.chunkpath');
        $this->assertInstanceOf('\Sokil\FileStorageBundle\GaufretteAdapter\Internal\PathStrategy\ChunkPathStrategy', $stretegy);
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function testGetInternalAdapter()
    {
        $this->containerBuilder->get('file_storage.gaufrette.adapter.internal');
    }
}
