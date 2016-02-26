<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Descriptor\Field;

use Slick\Common\Utils\Collection\AbstractCollection;
use Slick\Common\Utils\CollectionInterface;

/**
 * Table Fields Collection
 *
 * @package Slick\Orm\Descriptor\Field
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class FieldsCollection extends AbstractCollection implements
    CollectionInterface
{

    /**
     * Adds a field descriptor to collection
     *
     * @param FieldDescriptor $descriptor
     * @return $this|self|FieldsCollection
     */
    public function add(FieldDescriptor $descriptor)
    {
        $this->data[$descriptor->getField()] = $descriptor;
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