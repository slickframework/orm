<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Entity;

use League\Event\EmitterAwareTrait;
use Slick\Common\Utils\Collection\AbstractList;
use Slick\Orm\EntityInterface;
use Slick\Orm\Event\EntityAdded;
use Slick\Orm\Event\EntityRemoved;
use Slick\Orm\Exception\EntityNotFoundException;
use Slick\Orm\Exception\InvalidArgumentException;
use Slick\Orm\Orm;
use Slick\Orm\RepositoryInterface;

/**
 * Entity Collection
 *
 * @package Slick\Orm\Entity
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityCollection extends AbstractList implements
    EntityCollectionInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var EntityInterface|null
     */
    protected $parentEntity;

    /**
     * @var string
     */
    protected $cid;

    /**
     * Emitter methods
     */
    use EmitterAwareTrait;

    /**
     * Creates the collection for entity type with provided data
     *
     * @param string $entityType
     * @param array|\Traversable $data
     */
    public function __construct($entityType, $data = [])
    {
        parent::__construct($data);
        $this->type = $entityType;
    }

    /**
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value  The value to set.
     */
    public function offsetSet($offset, $value)
    {
        $this->add($value);
    }

    /**
     * Adds an entity to the collection
     *
     * This method adds an entity to the collection by accepting the entity
     * itself or the entity id value
     *
     * @param EntityInterface|integer $entity
     *
     * @throws EntityNotFoundException If the provided id is does not
     *  references an existing entity.
     * @throws InvalidArgumentException If the entity is not from
     *  the provided entity type
     *
     * @return self
     */
    public function add($entity)
    {
        if ($entity instanceof EntityInterface) {
            return $this->addEntity($entity);
        }
        return $this->addEntityWithId($entity);
    }

    /**
     * Adds an entity to the collection
     *
     * @param EntityInterface $entity
     * @throws InvalidArgumentException If the entity is not from
     *  the provided entity type
     *
     * @return self
     */
    public function addEntity(EntityInterface $entity)
    {
        if (!is_a($entity, $this->type)) {
            throw new InvalidArgumentException(
                "Trying to add an entity that is not a {$this->type}."
            );
        }
        $this->data[] = $entity;
        $event = new EntityAdded($entity, ['collection' => $this]);
        $this->getEmitter()->emit($event);
        return $this;
    }

    /**
     * Adds an entity by entity id
     *
     * @param mixed $entityId
     *
     * @throws EntityNotFoundException If the provided id is does not
     *  references an existing entity.
     * @throws InvalidArgumentException If the entity is not from
     *  the provided entity type
     *
     * @return self
     */
    public function addEntityWithId($entityId)
    {
        $entity = $this->getRepository()->get($entityId);
        if (!$entity) {
            throw new EntityNotFoundException(
                "The entity with is '{$entityId}' was not found."
            );
        }
        return $this->addEntity($entity);
        
    }

    /**
     * Gets entity repository for this collection
     * 
     * @return RepositoryInterface
     */
    public function getRepository()
    {
        if (null == $this->repository) {
            $this->setRepository(Orm::getRepository($this->type));
        }
        return $this->repository;
    }

    /**
     * Set repository for this entity collection
     * 
     * @param RepositoryInterface $repository
     * 
     * @return self|$this|EntityCollection
     */
    public function setRepository(RepositoryInterface $repository)
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * Get the parent entity if this collection is a relation in other entity
     *
     * @return null|EntityInterface
     */
    public function parentEntity()
    {
        return $this->parentEntity;
    }

    /**
     * Set the parent entity if this collection is a relation in other entity
     *
     * @param EntityInterface $entity
     *
     * @return self
     */
    public function setParentEntity(EntityInterface $entity)
    {
        $this->parentEntity = $entity;
        return $this;
    }

    /**
     * Gets the cache id used for this collection
     *
     * @return string
     */
    public function getId()
    {
        return $this->cid;
    }

    /**
     * Sets the cache id used for this collection
     *
     * @param string $collectionId
     *
     * @return self
     */
    public function setId($collectionId)
    {
        $this->cid = $collectionId;
        return $this;
    }

    /**
     * Removes the element at the given index, and returns it.
     *
     * @param integer|EntityInterface $index
     *
     * @return EntityInterface|null
     */
    public function remove($index)
    {
        if (!$index instanceof EntityInterface) {
            $index = $this->getRepository()->get($index);
        }
        
        return $this->findAndRemove($index);
    }

    /**
     * Iterates over the collection to remove an entity if found
     * 
     * @param EntityInterface|null $entity
     * @return EntityInterface
     */
    protected function findAndRemove(EntityInterface $entity = null)
    {
        if (null == $entity) {
            return $entity;
        }

        /** @var EntityInterface $existent */
        foreach ($this->data as $key => $existent) {
            if ($existent->getId() == $entity->getId()) {
                parent::remove($key);
                $this->getEmitter()
                    ->emit(
                        new EntityRemoved(
                            $entity,
                            ['collection' => $this]
                        )
                    );
                break;
            }
        }
        
        return $entity;
    }
}