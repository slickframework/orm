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
use Slick\Orm\Exception\InvalidArgumentException;
use Slick\Orm\Mapper\RelationInterface;

/**
 * RelationsMap
 *
 * @package Slick\Orm\Descriptor
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class RelationsMap extends AbstractMap implements MapInterface
{

    /**
     * Puts a new relation in the map.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return RelationsMap
     */
    public function set($key, $value)
    {
        if (! $value instanceof RelationInterface) {
            throw new InvalidArgumentException(
                "Only RelationInterface objects can be putted in a ".
                "RelationsMap."
            );
        }
        return parent::set($key, $value);
    }
}