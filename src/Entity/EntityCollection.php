<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Entity;

use Slick\Common\Utils\Collection\AbstractCollection;
use Slick\Common\Utils\CollectionInterface;
use Slick\Orm\EntityInterface;

/**
 * Entity Collection
 *
 * @package Slick\Orm\Entity
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityCollection extends AbstractCollection implements
    CollectionInterface
{

    /**
     * Adds an entity to the collection
     *
     * @param EntityInterface $entity
     * @return $this|self|EntityCollection
     */
    public function add(EntityInterface $entity)
    {
        $this->data[] = $entity;
        return $this;
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
}