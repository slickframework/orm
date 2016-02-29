<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Repository;

use Slick\Database\Sql;
use Slick\Orm\EntityInterface;
use Slick\Orm\RepositoryInterface;

/**
 * Class EntityRepository
 *
 * @package Slick\Orm\Repository
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityRepository extends AbstractRepository implements
    RepositoryInterface
{
    /**
     * @var IdentityMapInterface
     */
    protected $identityMap;

    /**
     * Gets an entity by its id
     *
     * @param mixed $entityId
     *
     * @return EntityInterface|null
     */
    public function get($entityId)
    {
        $entity = $this->getIdentityMap()->get($entityId, false);
        if ($entity === false) {
            $entity = $this->load($entity);
        }
        return $entity;
    }

    /**
     * Sets identity map for this repository
     *
     * @param IdentityMapInterface $map
     * @return $this|self|EntityRepository
     */
    public function setIdentityMap(IdentityMapInterface $map)
    {
        $this->identityMap = $map;
        return $this;
    }

    /**
     * Gets identity map for this repository
     *
     * @return IdentityMapInterface
     */
    protected function getIdentityMap()
    {
        if (null == $this->identityMap) {
            $this->setIdentityMap(new IdentityMap());
        }
        return $this->identityMap;
    }

    /**
     * Loads entity from database
     *
     * @param $entityId
     *
     * @return null|EntityInterface
     */
    protected function load($entityId)
    {
        $table = $this->getEntityDescriptor()->getTableName();
        $primaryKey = $this->getEntityDescriptor()
            ->getPrimaryKey()
            ->getField();
        $data = Sql::createSql($this->getAdapter())
            ->select($this->getEntityDescriptor()->getTableName())
            ->where(["{$table}.{$primaryKey} = ?" => $entityId])
            ->first();
        $entity = null;
        if ($data) {
            $class = $this->getEntityDescriptor()->className();
            $entity = new $class($data);
            $this->getIdentityMap()->set($entity);
        }
        return $entity;
    }
}