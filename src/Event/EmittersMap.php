<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Event;

use League\Event\EmitterInterface;
use Slick\Common\Utils\Collection\AbstractMap;
use Slick\Common\Utils\Collection\MapInterface;
use Slick\Orm\Exception\InvalidArgumentException;

/**
 * Emitters Map
 *
 * @package Slick\Orm\Event
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EmittersMap extends AbstractMap implements MapInterface
{

    /**
     * Puts a new emitter in the map.
     *
     * @param mixed $key
     * @param EmitterInterface $value
     *
     * @return $this|self|MapInterface
     */
    public function set($key, $value)
    {
        if (! $value instanceof EmitterInterface) {
            throw new InvalidArgumentException(
                "Only EmitterInterface object can be putted in a ".
                "EmittersMap."
            );
        }
        return parent::set($key, $value);
    }
}