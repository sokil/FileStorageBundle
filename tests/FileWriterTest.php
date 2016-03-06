<?php

namespace Sokil\FileStorageBundle;

use Knp\Bundle\GaufretteBundle\DependencyInjection\KnpGaufretteExtension;
use Sokil\FileStorageBundle\DependencyInjection\FileStorageExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sokil\Upload\HandlerFactory;
use Sokil\Upload\Handler;

class FileWriterTest extends \PHPUnit_Framework_TestCase
{
    private $containerBuilder;

    public function setUp()
    {
        $this->containerBuilder = new ContainerBuilder();

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

    public function testWrite()
    {
        $fileWriter = $this->containerBuilder->get('file_storage.default_file_writer');
    }
}