<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Descriptor;

use Slick\Common\Utils\Collection\AbstractMap;
use Slick\Common\Utils\Collection\MapInterface;
use Slick\Common\Utils\HashableInterface;
use Slick\Orm\Exception\InvalidArgumentException;

/**
 * Descriptors Collection
 *
 * @package Slick\Orm\Descriptor
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class DescriptorsCollection extends AbstractMap implements MapInterface
{

    /**
     * Puts a new element in the map.
     *
     * @param string|int|HashableInterface $key
     * @param mixed $value
     *
     * @return $this|self|MapInterface
     */
    public function set($key, $value)
    {
        if (! $value instanceof EntityDescriptorInterface) {
            throw new InvalidArgumentException (
                "Invalid descriptor. DescriptorsCollection only accepts ".
                "EntityDescriptorInterface object."
            );
        }
        return parent::set($key, $value);
    }
}