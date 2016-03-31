<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Mapper\Relation;
use Slick\Common\Utils\Text;
use Slick\Database\Sql;
use Slick\Database\Sql\Select;
use Slick\Orm\Entity\EntityCollectionInterface;
use Slick\Orm\EntityInterface;
use Slick\Orm\Event\EntityAdded;
use Slick\Orm\Event\EntityRemoved;

/**
 * HasAndBelongsToMany relation
 * 
 * @package Slick\Orm\Mapper\Relation
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class HasAndBelongsToMany extends HasMany
{

    /**
     * @readwrite
     * @var string
     */
    protected $relatedForeignKey;

    /**
     * @readwrite
     * @var string
     */
    protected $relationTable;

    /**
     * Gets the related foreign key
     * 
     * @return string
     */
    public function getRelatedForeignKey()
    {
        if (null == $this->relatedForeignKey) {
            $name = $this->getParentTableName();
            $this->relatedForeignKey = "{$this->normalizeFieldName($name)}_id";
        }
        return $this->relatedForeignKey;
    }

    /**
     * Gets the related table
     * 
     * @return string
     */
    public function getRelationTable()
    {
        if (is_null($this->relationTable)) {
            $parentTable = $this->getParentTableName();
            $table = $this->getEntityDescriptor()->getTableName();
            $names = [$parentTable, $table];
            asort($names);
            $first = array_shift($names);
            $tableName = $this->normalizeFieldName($first);
            array_unshift($names, $tableName);
            $this->relationTable = implode('_', $names);
        }
        return $this->relationTable;
    }

    /**
     * Loads the entity or entity collection for this relation
     *
     * @param EntityInterface $entity
     *
     * @return EntityCollectionInterface|EntityInterface[]
     */
    public function load(EntityInterface $entity)
    {
        $repository = $this->getParentRepository();

        $query = $repository->find()
            ->where($this->getConditions($entity))
            ->limit($this->limit);
        $this->addJoinTable($query);
        $this->checkConditions($query)
            ->checkOrder($query);

        /** @var EntityCollectionInterface $collection */
        $collection = $query->all();

        $collection
            ->setParentEntity($entity)
            ->getEmitter()
            ->addListener(EntityAdded::ACTION_ADD, [$this, 'add'])
            ->addListener(EntityRemoved::ACTION_REMOVE, [$this, 'remove']);

        return $collection;
    }

    /**
     * Saves the relation row upon entity add
     *
     * @param EntityAdded $event
     */
    public function add(EntityAdded $event)
    {
        $entity = $event->getEntity();
        $collection = $event->getCollection();
        $this->deleteRelation($entity, $collection);

        $repository = $this->getParentRepository();
        $adapter = $repository->getAdapter();
        $value = $collection->parentEntity()->getId();

        Sql::createSql($adapter)
            ->insert($this->getRelationTable())
            ->set(
                [
                    "{$this->getRelatedForeignKey()}" => $entity->getId(),
                    "{$this->getForeignKey()}" => $value
                ]
            )
            ->execute();
    }

    /**
     * Deletes the relation row upon entity remove
     * 
     * @param EntityRemoved $event
     */
    public function remove(EntityRemoved $event)
    {
        $entity = $event->getEntity();
        $collection = $event->getCollection();
        $this->deleteRelation($entity, $collection);
    }

    /**
     * Removes existing relations
     *
     * @param EntityInterface $entity
     * @param EntityCollectionInterface $collection
     *
     * @return self
     */
    protected function deleteRelation(
        EntityInterface $entity,
        EntityCollectionInterface $collection
    ) {
        $repository = $this->getParentRepository();
        $adapter = $repository->getAdapter();
        $parts = explode('\\', $this->getEntityDescriptor()->className());
        $entityName = lcfirst(array_pop($parts));
        $parts = explode('\\', $this->getParentEntityDescriptor()->className());
        $relatedName = lcfirst(array_pop($parts));
        $value = $collection->parentEntity()->getId();
        Sql::createSql($adapter)
            ->delete($this->getRelationTable())
            ->where(
                [
                    "{$this->getRelatedForeignKey()} = :{$relatedName} AND
                     {$this->getForeignKey()} = :{$entityName}" => [
                        ":{$relatedName}" => $entity->getId(),
                        ":{$entityName}" => $value
                    ]
                ]
            )
            ->execute();
        return $this;
    }

    /**
     * Gets the relation conditions
     *
     * @param EntityInterface $entity
     * @return array
     */
    protected function getConditions(EntityInterface $entity)
    {
        $parts = explode('\\', $this->getEntityDescriptor()->className());
        $property = lcfirst(array_pop($parts));
        return [
            "rel.{$this->getForeignKey()} = :{$property}" => [
                ":{$property}" => $entity->getId()
            ]
        ];
    }

    /**
     * Adds the join table to the query
     *
     * @param Select $query
     * @return self
     */
    protected function addJoinTable(Select $query)
    {
        $relationTable = $this->getRelationTable();
        $table = $this->getParentTableName();
        $pmk = $this->getParentPrimaryKey();
        $rfk = $this->getRelatedForeignKey();
        $query->join(
            $relationTable,
            "rel.{$rfk} = {$table}.{$pmk}",
            null,
            'rel'
        );
        return $this;
    }

}