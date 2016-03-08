<?php

namespace Sokil\FileStorageBundle\Entity;

use Sokil\FileStorageBundle\Entity\File;

class FileTest extends \PHPUnit_Framework_TestCase
{
    private function setPrivateProperty($object, $propertyName, $value)
    {
        $reflectionClass = new \ReflectionClass($object);
        $property = $reflectionClass->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    public function testGetExtension()
    {
        $file = new File();
        $file->setName('example.TXT');

        $this->assertEquals('txt', $file->getExtension());
    }
}