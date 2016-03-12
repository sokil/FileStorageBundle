<?php

namespace Sokil\FileStorageBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Sokil\FileStorageBundle\Entity\File;

class FileRepository extends EntityRepository
{
    /**
     * Find file by hash
     *
     * @param $hash
     * @return null|object
     */
    public function findOneByHsh($hash)
    {
        $file = $this->findOneBy(array(
            'hash' => $hash,
        ));

        return $file;
    }
}