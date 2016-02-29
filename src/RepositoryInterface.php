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
     * Set the entity descriptor interface
     *
     * @param EntityDescriptorInterface $descriptor
     * @return $this|self|AbstractRepository
     */
    public function setEntityDescriptor(EntityDescriptorInterface $descriptor);

    /**
     * Gets entity descriptor
     *
     * @return EntityDescriptorInterface
     */
    public function getEntityDescriptor();
}