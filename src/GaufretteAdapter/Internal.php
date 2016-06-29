<?php

namespace Sokil\FileStorageBundle\GaufretteAdapter;

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
    MimeTypeProvider
{

    private $pathStrategy;

    public function __construct(PathStrategyInterface $pathStrategy)
    {
        $this->pathStrategy = $pathStrategy;
    }

    /**
     * {@inheritDoc}
     */
    public function read($key)
    {

    }

    /**
     * {@inheritDoc}
     */
    public function write($key, $content)
    {

    }

    /**
     * {@inheritDoc}
     */
    public function rename($sourceKey, $targetKey)
    {

    }

    /**
     * {@inheritDoc}
     */
    public function exists($key)
    {
        return file_exists($this->computePath($key));
    }

    /**
     * {@inheritDoc}
     */
    public function keys()
    {

    }

    /**
     * {@inheritDoc}
     */
    public function mtime($key)
    {

    }

    /**
     * {@inheritDoc}
     */
    public function delete($key)
    {

    }

    /**
     * @param  string  $key
     * @return boolean
     */
    public function isDirectory($key)
    {

    }

    /**
     * {@inheritDoc}
     */
    public function createStream($key)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function checksum($key)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function size($key)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function mimeType($key)
    {
        $fileInfo = new \finfo(FILEINFO_MIME_TYPE);

        return $fileInfo->file($this->computePath($key));
    }
}
