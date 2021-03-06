<?php

namespace Sokil\FileStorageBundle;

use Knp\Bundle\GaufretteBundle\FilesystemMap;
use Doctrine\ORM\EntityManagerInterface;
use Sokil\FileStorageBundle\Entity\File;
use Sokil\FileStorageBundle\Exception\FileAlreadyExistsException;
use Sokil\FileStorageBundle\Exception\FileNotFoundException;

class FileStorage
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var FilesystemMap
     */
    protected $filesystemMap;

    public function __construct(
        EntityManagerInterface $entityManager,
        FilesystemMap $filesystemMap
    ) {
        $this->entityManager = $entityManager;
        $this->filesystemMap = $filesystemMap;
    }

    /**
     * @param string $filesystemName
     * @return File
     */
    public function write(
        File $file,
        $filesystemName,
        $content
    ) {
        if ($file->getId()) {
            throw new FileAlreadyExistsException('File already persisted to database with id ' . $file->getId());
        }

        // get target filesystem
        $filesystem = $this->filesystemMap->get($filesystemName);

        // create file
        $file->setFilesystem($filesystemName);

        // check if hash already exists
        $persistedFile = $this->entityManager
            ->getRepository('FileStorageBundle:File')
            ->findOneByHash($file->getHash());

        if ($persistedFile && $persistedFile->getSize() === $file->getSize()) {
            $file = $persistedFile;
        } else {
            // register uploaded file
            $this->entityManager->persist($file);
            $this->entityManager->flush();
        
            // send file to filesystem    
            $filesystem->write(
                $file->getId(),
                $content,
                true
            );
        }
        
        return $file;
    }

    /**
     * @param $key
     * @return File
     * @throws \Exception
     */
    public function getMetadata($key)
    {
        $persistedFile = $this->entityManager
            ->getRepository('FileStorageBundle:File')
            ->find($key);

        if (!$persistedFile) {
            throw new FileNotFoundException('File not found');
        }

        return $persistedFile;
    }

    public function getContent($key)
    {
        // get file
        $file = $this->getMetadata($key);

        // get filesystem
        $filesystem = $this->filesystemMap->get($file->getFilesystem());

        // get content
        return $filesystem->read($key);
    }
}
