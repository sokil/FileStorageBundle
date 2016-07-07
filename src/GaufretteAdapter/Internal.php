<?php

namespace Sokil\FileStorageBundle\GaufretteAdapter;

use Sokil\FileStorageBundle\Repository\FileRepository;
use Gaufrette\Util;
use Gaufrette\Adapter;
use Gaufrette\Adapter\ChecksumCalculator;
use Gaufrette\Adapter\SizeCalculator;
use Gaufrette\Adapter\MimeTypeProvider;
use Gaufrette\Adapter\StreamFactory;
use Gaufrette\Stream;
use Gaufrette\Exception;
use Sokil\FileStorageBundle\GaufretteAdapter\Internal\PathStrategy\PathStrategyInterface;

class Internal implements
    Adapter,
    StreamFactory,
    ChecksumCalculator,
    SizeCalculator,
    MimeTypeProvider,
    LocalFileInterface
{
    /**
     * @var FileRepository
     */
    private $repository;

    private $pathStrategy;

    public function __construct(
        FileRepository $repository,
        PathStrategyInterface $pathStrategy
    ) {
        $this->repository = $repository;
        $this->pathStrategy = $pathStrategy;
    }

    public function getPath($id, $absolute = true)
    {
        $file = $this->repository->find($id);
        $path = $this->pathStrategy->getPath($file, $absolute);

        return $path;
    }

    /**
     * {@inheritDoc}
     */
    public function read($id)
    {
        $path = $this->getPath($id);
        return file_get_contents($path);
    }

    /**
     * {@inheritDoc}
     */
    public function write($id, $content)
    {
        $path = $this->getPath($id);
        return file_put_contents($path, $content);
    }

    /**
     * Rename not allowed because path depends from key in db
     */
    public function rename($sourceKey, $targetKey)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function exists($id)
    {
        $path = $this->getPath($id);
        return file_exists($path);
    }

    public function keys()
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function mtime($id)
    {
        return filemtime($this->getPath($id));
    }

    /**
     * {@inheritDoc}
     */
    public function delete($id)
    {
        $path = $this->getPath($id);
        unlink ($path);
    }

    /**
     * @param  string  $id
     * @return boolean
     */
    public function isDirectory($id)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function createStream($id)
    {
        return new Stream\Local($this->getPath($id));
    }

    /**
     * {@inheritdoc}
     */
    public function checksum($id)
    {
        $file = $this->repository->find($id);
        return $file->getHash();
    }

    /**
     * {@inheritdoc}
     */
    public function size($id)
    {
        $file = $this->repository->find($id);
        return $file->getSize();
    }

    /**
     * {@inheritdoc}
     */
    public function mimeType($id)
    {
        $file = $this->repository->find($id);
        return $file->getMime();
    }
}
