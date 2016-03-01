<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Repository;

use Slick\Common\Utils\Collection\AbstractMap;
use Slick\Common\Utils\Collection\MapInterface;
use Slick\Orm\Exception\InvalidArgumentException;
use Slick\Orm\RepositoryInterface;

/**
 * Repository Map
 *
 * @package Slick\Orm\Repository
 * @author Filipe Silva
 */
class RepositoryMap extends AbstractMap implements MapInterface
{

    /**
     * Puts a new repository in the map.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return $this|self|MapInterface
     */
    public function set($key, $value)
    {
        if (! $value instanceof RepositoryInterface) {
            throw new InvalidArgumentException(
                "Only RepositoryInterface object can be putted in a ".
                "RepositoryMap."
            );
        }
        return parent::set($key, $value);
    }
}