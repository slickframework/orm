<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Entity;

/**
 * Collections Map Interface
 *
 * @package Slick\Orm\Entity
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
interface CollectionsMapInterface
{

    /**
     * Set an entity
     *
     * @param string $collectionId
     * @param EntityCollection $collection
     *
     * @return self|$this|CollectionsMapInterface
     */
    public function set($collectionId, EntityCollection $collection);

    /**
     * Gets the entity with provided id
     *
     * @param string $collectionId
     *
     * @param null $default
     *
     * @return EntityCollection
     */
    public function get($collectionId, $default = null);

}