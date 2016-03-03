<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm\Event;

use PHPUnit_Framework_TestCase as TestCase;
use Slick\Orm\Event\EmittersMap;

/**
 * EmittersMap test case
 *
 * @package Slick\Tests\Orm\Event
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EmittersMapTest extends TestCase
{

    /**
     * Should raise an exception if object is other then an EmitterInterface
     * @test
     * @expectedException \Slick\Orm\Exception\InvalidArgumentException
     */
    public function acceptOnlyEmitterInterface()
    {
        $map = new EmittersMap();
        $map->set('test', new \stdClass());
    }
}
