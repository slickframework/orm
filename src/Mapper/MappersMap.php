<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Mapper;

use Slick\Common\Utils\Collection\AbstractMap;
use Slick\Common\Utils\Collection\MapInterface;
use Slick\Common\Utils\HashableInterface;
use Slick\Orm\EntityMapperInterface;
use Slick\Orm\Exception\InvalidArgumentException;

/**
 * Mappers Map
 *
 * @package Slick\Orm\Mapper
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class MappersMap extends AbstractMap implements MapInterface
{

    /**
     * Puts a new Entity mapper in the map.
     *
     * @param string|int|HashableInterface $key
     * @param mixed $value
     *
     * @return $this|self|MapInterface
     */
    public function set($key, $value)
    {
        if (! $value instanceof EntityMapperInterface) {
            throw new InvalidArgumentException(
                'Trying to add a non EntityMapper to MappersMap.'
            );
        }
        return parent::set($key, $value);
    }
}