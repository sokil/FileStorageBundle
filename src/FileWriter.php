<?php

namespace Sokil\FileStorageBundle;

use Knp\Bundle\GaufretteBundle\FilesystemMap;
use Sokil\FileStorageBundle\FileBuilder\AbstractFileBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Sokil\FileStorageBundle\Entity\File;
use Sokil\FileStorageBundle\PathStrategy\PathStrategyInterface;

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

    protected $pathStrategy;

    public function __construct(
        EntityManagerInterface $entityManager,
        FilesystemMap $filesystemMap,
        PathStrategyInterface $pathStrategy = null
    ) {
        $this->entityManager = $entityManager;
        $this->filesystemMap = $filesystemMap;
        $this->pathStrategy = $pathStrategy;
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
        $file = $fileBuilder->buildFileEntity();
        $file->setFilesystem($filesystemName);

        // check if hash already exists
        $persistedFile = $this->entityManager
            ->getRepository('FileStorageBundle:File')
            ->findOneByHsh($file->getHash());

        if ($persistedFile && $persistedFile->getSize() === $file->getSize()) {
            $file = $persistedFile;
        } else {
            // register uploaded file
            $this->entityManager->persist($file);
            $this->entityManager->flush();
        }

        // get target filename
        if ($this->pathStrategy) {
            $targetPath = $this->pathStrategy->getPath($file);
        } else {
            $targetPath = $file->getId();
            $extension = $file->getExtension();
            if ($extension) {
                $targetPath .= '.' . $extension;
            }
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