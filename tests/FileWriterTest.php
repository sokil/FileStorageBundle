<?php

namespace Sokil\FileStorageBundle;

use Knp\Bundle\GaufretteBundle\DependencyInjection\KnpGaufretteExtension;
use Knp\Bundle\GaufretteBundle\FilesystemMap;
use Sokil\FileStorageBundle\DependencyInjection\FileStorageExtension;
use Sokil\FileStorageBundle\Entity\File;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FileWriterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    private function createEntityManagerMock()
    {
        $entityManager = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        return $entityManager;
    }

    private function createFilesystemMock()
    {
        $filesystem = $this
            ->getMockBuilder('Gaufrette\Filesystem')
            ->disableOriginalConstructor()
            ->getMock();

        return $filesystem;
    }

    private function createFilesystemMap()
    {
        $filesystemMap = new FilesystemMap([
            'someFilesystemName' => $this->createFilesystemMock(),
        ]);

        return $filesystemMap;
    }

    private function createFileBuilderMock()
    {
        // prepare entity
        $fileEntity = new File();
        $fileEntity
            ->setName('someFileName.txt')
            ->setSize('42')
            ->setHash('0123456789abcdef0123456789abcdef')
            ->setCreatedAt(new \DateTime())
            ->setMime('text/plain');

        // set id to make file already saved
        $fileEntityReflectionClass = new \ReflectionClass($fileEntity);
        $fileEntityIdProperty = $fileEntityReflectionClass->getProperty('id');
        $fileEntityIdProperty->setAccessible(true);
        $fileEntityIdProperty->setValue($fileEntity, 987654321);

        $fileBuilder = $this
            ->getMockBuilder('Sokil\FileStorageBundle\FileBuilder\AbstractFileBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['buildFileEntity', 'getContent'])
            ->getMock();

        $fileBuilder
            ->expects($this->once())
            ->method('buildFileEntity')
            ->will($this->returnValue($fileEntity));

        $fileBuilder
            ->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue(str_repeat('f', 42)));

        return $fileBuilder;
    }

    public function testWrite()
    {
        // get file writer
        $fileWriter = new FileWriter(
            $this->createEntityManagerMock(),
            $this->createFilesystemMap()
        );

        // get file builder
        $fileBuilder = $this->createFileBuilderMock();

        // write uploaded
        $file = $fileWriter->write(
            $fileBuilder,
            'someFilesystemName'
        );

        // tests
        $this->assertEquals('someFilesystemName', $file->getFilesystem());
    }
}