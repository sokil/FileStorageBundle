<?php

namespace Sokil\FileStorageBundle;

use Knp\Bundle\GaufretteBundle\FilesystemMap;
use Sokil\FileStorageBundle\FileBuilder\AbstractFileBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Sokil\FileStorageBundle\Entity\File;

class FileWriter
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
    public function write(AbstractFileBuilder $fileBuilder, $filesystemName)
    {
        // get target filesystem
        $filesystem = $this->filesystemMap->get($filesystemName);

        // create file
        $file = $fileBuilder->getFile();
        $file->setFilesystem($filesystemName);

        // register uploaded file
        $this->entityManager->persist($file);
        $this->entityManager->flush();

        // get target filename
        $targetPath = $file->getSystemDir() . '/' . $file->getId();
        $extension = $file->getExtension();
        if ($extension) {
            $targetPath .= '.' . $extension;
        }

        // move file to target storage
        $content = $fileBuilder->getContent();

        $filesystem->write(
            $targetPath,
            $content,
            true
        );

        return $file;
    }
}