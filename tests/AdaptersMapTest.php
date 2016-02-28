<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm;

use PHPUnit_Framework_TestCase as TestCase;
use Slick\Orm\AdaptersMap;

/**
 * AdaptersMap test case
 *
 * @package Slick\Tests\Orm
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class AdaptersMapTest extends TestCase
{

    /**
     * Should raise an exception
     * @test
     * @expectedException \Slick\Orm\Exception\InvalidArgumentException
     */
    public function addInvalidObject()
    {
        $map = new AdaptersMap();
        $map->set('test', new \stdClass());
    }
}
