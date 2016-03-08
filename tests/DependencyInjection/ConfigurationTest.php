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

    public function testGetHandlerFactory()
    {
        $handlerFactory = $this->containerBuilder->get('file_storage.upload_handler_factory');
        $this->assertInstanceOf(HandlerFactory::class, $handlerFactory);
    }

    public function testGetHandler()
    {
        $handler = $this->containerBuilder->get('file_storage.upload_handler_factory')->createUploadHandler();
        $this->assertInstanceOf(Handler::class, $handler);
    }

    public function testGetDefultFileWriter()
    {
        $fileWriter = $this->containerBuilder->get('file_storage.default_file_writer');
        $this->assertInstanceOf(FileWriter::class, $fileWriter);
    }

    public function testGetDefaultUploadedFileBuilder()
    {
        $fileBuilder = $this->containerBuilder->get('file_storage.default_uploaded_file_builder');
        $this->assertInstanceOf(UploadedFileBuilder::class, $fileBuilder);
    }
}