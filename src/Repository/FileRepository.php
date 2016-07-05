<?php

namespace Sokil\FileStorageBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Sokil\FileStorageBundle\Entity\File;

/**
 * @method File find($key) find File entity by id
 */
class FileRepository extends EntityRepository
{
    /**
     * Find file by hash
     *
     * @param $hash
     * @return null|object
     */
    public function findOneByHash($hash)
    {
        $file = $this->findOneBy(array(
            'hash' => $hash,
        ));

        return $file;
    }
}