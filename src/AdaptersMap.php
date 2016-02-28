<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm;

use Slick\Common\Utils\Collection\AbstractMap;
use Slick\Common\Utils\Collection\MapInterface;
use Slick\Common\Utils\HashableInterface;
use Slick\Database\Adapter\AdapterInterface;
use Slick\Orm\Exception\InvalidArgumentException;

/**
 * Adapters Map
 *
 * @package Slick\Orm
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class AdaptersMap extends AbstractMap implements MapInterface
{

    /**
     * Puts a new adapter in the map.
     *
     * @param string|int|HashableInterface $key
     * @param AdapterInterface $value
     * @return $this|MapInterface|AdaptersMap
     *
     * @throws InvalidArgumentException If trying to add an object that
     * does not implements the AdapterInterface.
     */
    public function set($key, $value)
    {
        if (! $value instanceof AdapterInterface) {
            throw new InvalidArgumentException(
                'Adding an object that does not implement AdapterInterface '.
                'to AdaptersMap.'
            );
        }
        return parent::set($key, $value);
    }
}