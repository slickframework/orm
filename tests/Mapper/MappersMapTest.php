<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm\Mapper;

use PHPUnit_Framework_TestCase as TestCase;
use Slick\Orm\Mapper\MappersMap;

/**
 * MappersMap test case
 *
 * @package Slick\Tests\Orm\Mapper
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class MappersMapTest extends TestCase
{

    /**
     * Should raise an exception
     * @test
     * @expectedException \Slick\Orm\Exception\InvalidArgumentException
     */
    public function addOtherObject()
    {
        $map = new MappersMap();
        $map->set('test', new \stdClass());
    }
}
