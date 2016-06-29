<?php

namespace Sokil\FileStorageBundle\GaufretteAdapter\Internal\PathStrategy;

use Sokil\FileStorageBundle\Entity\File;

class ChunkPathStrategy implements PathStrategyInterface
{
    private $baseDir;

    private $chunksNumber = 2;

    private $chunkSize = 3;

    private $preserveExtension = false;

    public function __construct(array $options = [])
    {
        if (!empty($options['baseDir'])) {
            $this->baseDir = rtrim($options['baseDir'], '/');
        } else {
            $this->baseDir = sys_get_temp_dir();
        }


        if (!empty($options['chunksNumber']) && is_numeric($options['chunksNumber'])) {
            $this->chunksNumber = $options['chunksNumber'];
        }

        if (!empty($options['chunkSize']) && is_numeric($options['chunkSize'])) {
            $this->chunkSize = $options['chunkSize'];
        }

        if (!empty($options['preserveExtension'])) {
            $this->preserveExtension = (bool) $options['preserveExtension'];
        }
    }

    private function getDir(File $file)
    {
        $chunksLength = $this->chunksNumber * $this->chunkSize;

        $chunks = str_split(
            str_pad(
                substr($file->getId(), -$chunksLength),
                $chunksLength,
                '0',
                STR_PAD_LEFT
            ),
            $this->chunkSize
        );

        $chunks[] = $this->baseDir;

        return implode('/', array_reverse($chunks));
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