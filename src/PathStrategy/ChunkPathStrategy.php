<?php

namespace Sokil\FileStorageBundle\PathStrategy;

use Sokil\FileStorageBundle\Entity\File;

class ChunkPathStrategy implements PathStrategyInterface
{
    private $chunksNumber;

    private $chunkSize;

    public function __construct(
        $chunksNumber = 2,
        $chunkSize = 3
    ) {
        $this->chunksNumber = $chunksNumber;
        $this->chunkSize = $chunkSize;
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

        $systemBasename = $file->getId();

        $extension = $file->getExtension();
        if ($extension) {
            $systemBasename += '.' . $extension;
        }

        return $this->getDir($file) . '/' . $systemBasename;
    }
}