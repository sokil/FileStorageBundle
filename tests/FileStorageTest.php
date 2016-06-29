<?php

namespace Sokil\FileStorageBundle;

use Knp\Bundle\GaufretteBundle\DependencyInjection\KnpGaufretteExtension;
use Knp\Bundle\GaufretteBundle\FilesystemMap;
use Sokil\FileStorageBundle\DependencyInjection\FileStorageExtension;
use Sokil\FileStorageBundle\Entity\File;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FileStorageTest extends \PHPUnit_Framework_TestCase
{
    private function createEntityManagerMock()
    {
        // create file repository mock
        $fileRepositoryMock = $this
            ->getMockBuilder('\Sokil\FileStorageBundle\Repository\FileRepository')
            ->disableOriginalConstructor()
            ->getMock();

        // create entity manager mock
        $entityManagerMock = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $entityManagerMock
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($fileRepositoryMock));

        return $entityManagerMock;
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

    public function testWrite()
    {
        // get file writer
        $fileStorage = new FileStorage(
            $this->createEntityManagerMock(),
            $this->createFilesystemMap()
        );

        // get file
        $file = new File();

        // write uploaded
        $fileStorage->write(
            $file,
            'someFilesystemName',
            'someContent'
        );

        // tests
        $this->assertEquals('someFilesystemName', $file->getFilesystem());
    }
}