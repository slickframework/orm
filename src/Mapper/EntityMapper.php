<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Mapper;

use Slick\Database\Adapter\AdapterInterface;
use Slick\Database\RecordList;
use Slick\Database\Sql;
use Slick\Orm\Descriptor\EntityDescriptorInterface;
use Slick\Orm\Descriptor\EntityDescriptorRegistry;
use Slick\Orm\Descriptor\Field\FieldDescriptor;
use Slick\Orm\Entity\EntityCollection;
use Slick\Orm\EntityInterface;
use Slick\Orm\EntityMapperInterface;
use Slick\Orm\Orm;

/**
 * Generic Entity Mapper
 *
 * @package Slick\Orm\Mapper
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityMapper implements EntityMapperInterface
{
    /**
     * @var EntityDescriptorInterface
     */
    protected $descriptor;

    /**
     * @var EntityInterface
     */
    protected $entity;

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * Saves current entity object to database
     *
     * Optionally saves only the partial data if $data argument is passed. If
     * no data is given al the field properties will be updated.
     *
     * @param array $data Partial data to save
     * @param EntityInterface $entity
     *
     * @return self|$this|EntityMapperInterface
     */
    public function save(EntityInterface $entity, array $data = [])
    {
        $this->entity = $entity;
        $query = $this->getUpdateQuery();
        $query->set($this->getData())
            ->execute();
        $lastId = $query->getAdapter()->getLastInsertId();
        if ($lastId) {
            $entity->setId($lastId);
        }
        $this->registerEntity($entity);
        return $this;
    }

    /**
     * Get entity descriptor
     *
     * @return EntityDescriptorInterface
     */
    public function getDescriptor()
    {
        if (null == $this->descriptor) {
            $class = is_object($this->entity)
                ? get_class($this->entity)
                : $this->entity;
            $this->setDescriptor(
                EntityDescriptorRegistry::getInstance()
                    ->getDescriptorFor($class)
            );
        }
        return $this->descriptor;
    }

    /**
     * Set entity descriptor
     *
     * @param EntityDescriptorInterface $descriptor
     *
     * @return $this|self|EntityMapper
     */
    public function setDescriptor($descriptor)
    {
        $this->descriptor = $descriptor;
        return $this;
    }

    /**
     * Sets the adapter for this mapper
     *
     * @param AdapterInterface $adapter
     *
     * @return self|$this|EntityMapper
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * Retrieves the current adapter
     *
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Creates the insert/update query for current entity state
     *
     * @return Sql\Insert|Sql\Update
     */
    protected function getUpdateQuery()
    {
        $primaryKey = $this->getDescriptor()->getPrimaryKey()->getName();
        $table = $this->getDescriptor()->getTableName();
        $sql = Sql::createSql($this->getAdapter());
        $query = (null === $this->entity->{$primaryKey})
            ? $sql->insert($table)
            : $this->setUpdateCriteria(
                $sql->update($table),
                $primaryKey,
                $table
            );
        return $query;
    }

    /**
     * Adds the update criteria for an update query
     *
     * @param Sql\Update $query
     * @param string $primaryKey
     * @param string $table
     *
     * @return Sql\Update
     */
    protected function setUpdateCriteria(
        Sql\Update $query, $primaryKey, $table
    ) {
        $key = "{$table}.{$primaryKey} = :id";
        $query->where([$key => [':id' => $this->entity->{$primaryKey}]]);
        return $query;
    }

    /**
     * Gets data to be used in queries
     *
     * @return array
     */
    protected function getData()
    {
        $data = [];
        $fields = $this->getDescriptor()->getFields();
        /** @var FieldDescriptor $field */
        foreach ($fields as $field) {
            $data[$field->getField()] = $this->entity->{$field->getName()};
        }
        return $data;
    }

    /**
     * Creates an entity object from provided data
     *
     * Data can be an array with single row fields or a RecordList from
     * a query.
     *
     * @param array|RecordList $data
     *
     * @return EntityInterface|EntityMapperInterface[]|EntityCollection
     */
    public function createFrom($data)
    {
        if ($data instanceof RecordList) {
            return $this->createMultiple($data);
        }
        return $this->createSingle($data);
    }

    /**
     * Creates an entity for provided row array
     *
     * @param array $source
     * @return EntityInterface
     */
    protected function createSingle(array $source)
    {
        $data = [];
        /** @var FieldDescriptor $field */
        foreach ($this->getDescriptor()->getFields() as $field) {
            if (array_key_exists($field->getField(), $source)) {
                $data[$field->getName()] = $source[$field->getField()];
            }
        }
        $class = $this->getDescriptor()->className();
        return new $class($data);
    }

    /**
     * Creates an entity collection for provided record list
     *
     * @param RecordList $source
     * @return EntityCollection
     */
    protected function createMultiple(RecordList $source)
    {
        $data = [];
        foreach ($source as $item) {
            $data[] = $this->createSingle($item);
        }
        return new EntityCollection($data);
    }

    /**
     * Sets entity class name
     *
     * @param $entityClass
     *
     * @return self|$this|EntityMapper
     */
    public function setEntity($entityClass)
    {
        $this->entity = $entityClass;
        return $this;
    }

    /**
     * Sets the entity in the identity map of its repository.
     *
     * This avoids a select when one client creates an entity and
     * other client gets it from the repository.
     *
     * @param EntityInterface $entity
     * @return $this
     */
    protected function registerEntity(EntityInterface $entity)
    {
        Orm::getRepository(get_class($entity))
            ->getIdentityMap()
            ->set($entity);
        return $this;
    }
}