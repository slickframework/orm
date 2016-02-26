<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Mapper;

use Slick\Database\Adapter\AdapterInterface;
use Slick\Database\Sql;
use Slick\Orm\Descriptor\EntityDescriptorInterface;
use Slick\Orm\Descriptor\EntityDescriptorRegistry;
use Slick\Orm\Descriptor\Field\FieldDescriptor;
use Slick\Orm\EntityInterface;
use Slick\Orm\EntityMapperInterface;

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
            $this->setDescriptor(
                EntityDescriptorRegistry::getInstance()
                    ->getDescriptorFor($this->entity)
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

    protected function getUpdateQuery()
    {
        $primaryKey = $this->descriptor->getPrimaryKey()->getName();
        $table = $this->getDescriptor()->getTableName();
        $sql = Sql::createSql($this->adapter);
        $query = (null === $this->entity->{$primaryKey})
            ? $sql->insert($table)
            : $this->setUpdateCriteria(
                $sql->update($table),
                $primaryKey,
                $table
            );
        return $query;
    }

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
            $data[$field->getField()] = $this->entity->${$field->getName()};
        }
        return $data;
    }
}