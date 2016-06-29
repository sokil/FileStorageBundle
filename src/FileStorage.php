<?php

namespace Sokil\FileStorageBundle;

use Knp\Bundle\GaufretteBundle\FilesystemMap;
use Doctrine\ORM\EntityManagerInterface;
use Sokil\FileStorageBundle\Entity\File;

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

    protected $pathStrategy;

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
        }

        $filesystem->write(
            $file->getId(),
            $content,
            true
        );
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
            ->findOne($key);

        if (!$persistedFile) {
            throw new \Exception('File not found');
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