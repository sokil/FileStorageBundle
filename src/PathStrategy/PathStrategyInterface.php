<?php

namespace Sokil\FileStorageBundle\PathStrategy;

use Sokil\FileStorageBundle\Entity\File;

interface PathStrategyInterface
{
    public function getPath(File $file);
}