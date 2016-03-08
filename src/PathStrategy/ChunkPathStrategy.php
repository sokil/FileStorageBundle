<?php

namespace Sokil\FileStorageBundle\PathStrategy;

use Sokil\FileStorageBundle\Entity\File;

class ChunkPathStrategy implements PathStrategyInterface
{
    private $chunksNumber;

    private $chunkSize;

    private $preserveExtension;

    public function __construct(
        $chunksNumber = 2,
        $chunkSize = 3,
        $preserveExtension = false
    ) {
        $this->chunksNumber = $chunksNumber;
        $this->chunkSize = $chunkSize;
        $this->preserveExtension = $preserveExtension;
    }

    private function getDir(File $file)
    {
        $idChunks = str_split(
            str_pad(
                substr($file->getId(), -6),
                6,
                '0',
                STR_PAD_LEFT
            ),
            3
        );

        return '/' . $idChunks[1] . '/' . $idChunks[0];
    }

    public function getPath(File $file)
    {
        if (!$file->getId()) {
            throw new \RuntimeException('File must be persisted');
        }

        $basename = $file->getId();

        if ($this->preserveExtension) {
            $extension = $file->getExtension();
            if ($extension) {
                $basename .= '.' . $extension;
            }
        }

        return $this->getDir($file) . '/' . $basename;
    }
}