<?php

namespace Sokil\FileStorageBundle\GaufretteAdapter\Internal\PathStrategy;

use Sokil\FileStorageBundle\Entity\File;

interface PathStrategyInterface
{
    public function __construct(array $options = []);

    public function getPath(File $file);
}