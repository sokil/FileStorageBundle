<?php

namespace Sokil\FileStorageBundle\Entity;

use Sokil\FileStorageBundle\PathStrategy\ChunkPathStrategy;

class ChunkPathStrategyTest extends \PHPUnit_Framework_TestCase
{
    private function setPrivateProperty($object, $propertyName, $value)
    {
        $reflectionClass = new \ReflectionClass($object);
        $property = $reflectionClass->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage File must be persisted
     */
    public function testGetSystemDir_EntityNotPersisted()
    {
        $file = new File();
        $file->setName('example.txt');

        $pathStrategy = new ChunkPathStrategy(2, 3);

        $path = $pathStrategy->getPath($file);

        $this->fail('Not persisted entity must throw exception when get path');
    }

    public function testGetSystemDir_EntityPersisted()
    {
        $file = new File();
        $file->setName('example.txt');

        $pathStrategy = new ChunkPathStrategy(2, 3);

        $this->setPrivateProperty($file, 'id', 1); // 000001
        $this->assertEquals('/001/000/1', $pathStrategy->getPath($file));

        $this->setPrivateProperty($file, 'id', 12); // 000012
        $this->assertEquals('/012/000/12', $pathStrategy->getPath($file));

        $this->setPrivateProperty($file, 'id', 123); // 000123
        $this->assertEquals('/123/000/123', $pathStrategy->getPath($file));

        $this->setPrivateProperty($file, 'id', 1234); // 001234
        $this->assertEquals('/234/001/1234', $pathStrategy->getPath($file));

        $this->setPrivateProperty($file, 'id', 12345); // 012345
        $this->assertEquals('/345/012/12345', $pathStrategy->getPath($file));

        $this->setPrivateProperty($file, 'id', 123456); // 123456
        $this->assertEquals('/456/123/123456', $pathStrategy->getPath($file));

        $this->setPrivateProperty($file, 'id', 1234567); // 1234567
        $this->assertEquals('/567/234/1234567', $pathStrategy->getPath($file));
    }

    public function testGetSystemPath()
    {

    }
}