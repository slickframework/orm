<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Mapper\Relation;

use Slick\Common\Base;
use Slick\Common\Utils\Text;
use Slick\Database\Adapter\AdapterInterface;
use Slick\Orm\Descriptor\EntityDescriptorInterface;
use Slick\Orm\Descriptor\EntityDescriptorRegistry;
use Slick\Orm\Entity\EntityCollection;
use Slick\Orm\EntityInterface;
use Slick\Orm\Orm;

/**
 * AbstractRelation
 *
 * @package Slick\Orm\Mapper\Relation
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 *
 * @property AdapterInterface $adapter
 * @property bool $lazyLoaded
 *
 * @method bool isLazyLoaded()
 */
abstract class AbstractRelation extends Base
{
    /**
     * @readwrite
     * @var string
     */
    protected $propertyName;

    /**
     * @readwrite
     * @var EntityDescriptorInterface
     */
    protected $entityDescriptor;

    /**
     * Parent or related entity class name
     * @readwrite
     * @var string
     */
    protected $parentEntity;

    /**
     * @readwrite
     * @var EntityDescriptorInterface
     */
    protected $parentEntityDescriptor;

    /**
     * @readwrite
     * @var string
     */
    protected $foreignKey;

    /**
     * @readwrite
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @readwrite
     * @var bool
     */
    protected $lazyLoaded = true;

    /**
     * Returns the property holding the relation
     *
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }


    /**
     * Gets the entity descriptor
     *
     * @return EntityDescriptorInterface
     */
    public function getEntityDescriptor()
    {
        return $this->entityDescriptor;
    }

    /**
     * Sets entity descriptor
     *
     * @param EntityDescriptorInterface $descriptor
     * @return $this
     */
    public function setEntityDescriptor(EntityDescriptorInterface $descriptor)
    {
        $this->entityDescriptor = $descriptor;
        return $this;
    }

    /**
     * Gets parent entity class name
     *
     * @return string
     */
    public function getParentEntity()
    {
        return $this->parentEntity;
    }

    /**
     * Gets the parent or related entity descriptor
     *
     * @return EntityDescriptorInterface
     */
    public function getParentEntityDescriptor()
    {
        if (is_null($this->parentEntityDescriptor)) {
            $this->setParentEntityDescriptor(
                EntityDescriptorRegistry::getInstance()
                    ->getDescriptorFor($this->parentEntity)
            );
        }
        return $this->parentEntityDescriptor;
    }

    /**
     * Sets parent entity descriptor
     *
     * @param EntityDescriptorInterface $parentEntityDescriptor
     * @return BelongsTo
     */
    public function setParentEntityDescriptor(
        EntityDescriptorInterface $parentEntityDescriptor
    ) {
        $this->parentEntityDescriptor = $parentEntityDescriptor;
        return $this;
    }

    /**
     * Gets the foreign key field name
     *
     * @return string
     */
    public function getForeignKey()
    {
        if (is_null($this->foreignKey)) {
            $name = $this->getParentEntityDescriptor()->getTableName();
            $this->foreignKey = "{$this->normalizeFieldName($name)}_id";
        }
        return $this->foreignKey;
    }

    /**
     * Normalizes the key field by convention
     * 
     * @param string $tableName
     * @return string
     */
    protected function normalizeFieldName($tableName)
    {
        $tableName = Text::camelCaseToSeparator($tableName, '#');
        $parts = explode('#', $tableName);
        $lastName = array_pop($parts);
        $lastName = Text::singular(strtolower($lastName));
        array_push($parts, ucfirst($lastName));
        return lcfirst(implode('', $parts));
    }

    /**
     * Register the retrieved entities in the repository identity map
     *
     * @param EntityInterface|EntityCollection $entity
     *
     * @return EntityInterface|EntityCollection
     */
    protected function registerEntity($entity)
    {
        Orm::getRepository($this->getParentEntity())
            ->getIdentityMap()
            ->set($entity);

        return $entity;
    }

    /**
     * Set the database adapter for this relation
     *
     * @param AdapterInterface $adapter
     * @return $this|self|AbstractRelation
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * Gets relation adapter
     *
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        if (null == $this->adapter) {
            $className = $this->getEntityDescriptor()->className();
            $repository = Orm::getRepository($className);
            $this->setAdapter($repository->getAdapter());
        }
        return $this->adapter;
    }
}