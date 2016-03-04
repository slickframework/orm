<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm;

use Slick\Database\Adapter\AdapterAwareInterface;
use Slick\Orm\Descriptor\EntityDescriptorInterface;
use Slick\Orm\Repository\IdentityMapInterface;
use Slick\Orm\Repository\QueryObjectInterface;

/**
 * Entity Repository Interface
 *
 * @package Slick\Orm
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
interface RepositoryInterface extends AdapterAwareInterface
{

    /**
     * Gets an entity by its id
     *
     * @param mixed $entityId
     *
     * @return EntityInterface|null
     */
    public function get($entityId);

    /**
     * Finds entities
     *
     * @return QueryObjectInterface
     *
     * @see Slick\Database\Sql\Select
     */
    public function find();

    /**
     * Set the entity descriptor interface
     *
     * @param EntityDescriptorInterface $descriptor
     * @return $this|self|RepositoryInterface
     */
    public function setEntityDescriptor(EntityDescriptorInterface $descriptor);

    /**
     * Gets entity descriptor
     *
     * @return EntityDescriptorInterface
     */
    public function getEntityDescriptor();

    /**
     * Gets the entity mapper fot current repository
     *
     * @return EntityMapperInterface
     */
    public function getEntityMapper();

    /**
     * Gets identity map for this repository
     *
     * @return IdentityMapInterface
     */
    public function getIdentityMap();
}