<?php

namespace Sokil\FileStorageBundle\FileBuilder;

use Sokil\FileStorageBundle\Entity\File;

abstract class AbstractFileBuilder
{
    /**
     * @return File
     */
    abstract public function buildFileEntity();

    /**
     * @return mixed
     */
    abstract public function getContent();
}