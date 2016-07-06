<?php

namespace Sokil\FileStorageBundle\GaufretteAdapter;

interface LocalFileInterface
{
    public function getPath($key);
}