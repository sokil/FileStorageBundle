<?php

namespace Sokil\FileStorageBundle\FileBuilder;

use Sokil\FileStorageBundle\Entity\File;
use Sokil\Upload\Handler;

class UploadedFileBuilder extends AbstractFileBuilder
{
    /**
     * @var Handler
     */
    private $uploadHandler;

    public function __construct(Handler $uploadHandler)
    {
        $this->uploadHandler = $uploadHandler;

        return $this;
    }

    public function getFile()
    {
        $sourceFile = $this->uploadHandler->getFile();
        
        // build file
        $file = new File();
        $file
            ->setName($sourceFile->getOriginalBasename())
            ->setSize($sourceFile->getSize())
            ->setCreatedAt(new \DateTime())
            ->setMime($sourceFile->getType())
            ->setHash($sourceFile->getMd5Sum());

        return $file;
    }

    public function getContent()
    {
        $sourceFile = $this->uploadHandler->getFile();

        return stream_get_contents($sourceFile->getStream());
    }
}