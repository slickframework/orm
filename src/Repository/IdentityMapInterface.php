<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Repository;

use Slick\Orm\EntityInterface;

/**
 * Identity Map Interface
 *
 * Ensures that each object gets loaded only once by keeping
 * every loaded object in a map. Looks up objects using the
 * map when referring to them.
 *
 * @package Slick\Orm\EntityMap
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
interface IdentityMapInterface
{

    /**
     * Set an entity
     *
     * @param EntityInterface $entity
     *
     * @return self|$this|IdentityMapInterface
     */
    public function set(EntityInterface $entity);

    /**
     * Gets the entity with provided id
     *
     * @param mixed $entityId
     *
     * @param null $default
     *
     * @return null|mixed|EntityInterface
     */
    public function get($entityId, $default = null);

    /**
     * Remove an entity from identity map
     *
     * @param EntityInterface $entity
     *
     * @return self|$this|IdentityMapInterface
     */
    public function remove($entity);

}