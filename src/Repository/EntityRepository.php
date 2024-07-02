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
use Slick\Orm\Repository\QueryObject\QueryObject;
use Slick\Orm\Repository\QueryObject\QueryObjectInterface;
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
     * Finds entities
     *
     * @return QueryObjectInterface|\Slick\Database\Sql\Select
     *
     * @see Slick\Database\Sql\Select
     */
    public function find()
    {
        return new QueryObject($this);
    }

    /**
     * Gets an entity by its id
     *
     * @param mixed $entityId
     *
     * @return EntityInterface|null
     */
    public function get($entityId)
    {
        $key = $this->getEntityDescriptor()->className();
        $key .= '::'.$entityId;
        $entity = $this->getIdentityMap()->get($key, false);
        if ($entity === false) {
            $entity = $this->load($entityId);
        }
        return $entity;
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

        return $this->find()
            ->where([
                "{$table}.{$primaryKey} = :id" => [':id' => $entityId]
            ])
            ->first();
    }

    /**
     * Gets a list ready for use with select boxes
     * 
     * @return array
     */
    public function getList()
    {
        $data = $this->find()
            ->limit(250)
            ->all();
        $list = [];
        $pmk = $this->getEntityDescriptor()->getPrimaryKey();
        $display = $this->getEntityDescriptor()->getDisplayFiled();
        foreach ($data as $entity) {
            $list[$entity->{$pmk->getName()}] = $entity->{$display->getName()};
        }
        return $list;
    }
}