<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Entity;

use League\Event\EmitterAwareInterface;
use Slick\Common\Utils\CollectionInterface;
use Slick\Orm\EntityInterface;
use Slick\Orm\Exception\EntityNotFoundException;
use Slick\Orm\Exception\InvalidArgumentException;

/**
 * EntityCollectionInterface
 *
 * @package Slick\Orm\Entity
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
interface EntityCollectionInterface extends
    CollectionInterface,
    EmitterAwareInterface
{

    /**
     * Creates the collection for entity type with provided data
     *
     * @param string $entityType
     * @param array|\Traversable $data
     */
    public function __construct($entityType, $data = []);

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
    public function add($entity);

    /**
     * Adds an entity to the collection
     * 
     * @param EntityInterface $entity

     * @throws InvalidArgumentException If the entity is not from
     *  the provided entity type
     * 
     * @return self
     */
    public function addEntity(EntityInterface $entity);

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
    public function addEntityWithId($entityId);

    /**
     * Get the parent entity if this collection is a relation in other entity
     * 
     * @return null|EntityInterface
     */
    public function parentEntity();

    /**
     * Set the parent entity if this collection is a relation in other entity
     * 
     * @param EntityInterface $entity
     * 
     * @return self
     */
    public function setParentEntity(EntityInterface $entity);
}