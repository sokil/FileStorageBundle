<?php

namespace Sokil\FileStorageBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sokil\Upload\HandlerFactory;
use Sokil\Upload\Handler;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    private static $containerBuilder;

    public static function setUpBeforeClass()
    {
        self::$containerBuilder = new ContainerBuilder();
        $extension = new FileStorageExtension();

        self::$containerBuilder->registerExtension($extension);
        self::$containerBuilder->loadFromExtension($extension->getAlias());
        self::$containerBuilder->compile();
    }

    public static function tearDownAfterClass()
    {
        self::$containerBuilder = null;
    }

    public function testGetHandlerFactory()
    {
        $handlerFactory = self::$containerBuilder->get('file_storage.handler_factory');
        $this->assertInstanceOf(HandlerFactory::class, $handlerFactory);
    }

    public function testGetHandler()
    {
        $handler = self::$containerBuilder->get('file_storage.handler_factory')->createUploadHandler();
        $this->assertInstanceOf(Handler::class, $handler);
    }
}