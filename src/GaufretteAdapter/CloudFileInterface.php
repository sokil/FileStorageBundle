<?php

namespace Sokil\FileStorageBundle\GaufretteAdapter;

interface CloudFileInterface
{
    public function getUrl($key);
}