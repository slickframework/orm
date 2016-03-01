<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm\Repository;

use PHPUnit_Framework_TestCase as TestCase;
use Slick\Orm\Repository\RepositoryMap;
use Slick\Orm\RepositoryInterface;

/**
 * RepositoryMap test case
 *
 * @package Slick\Tests\Orm\Repository
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class RepositoryMapTest extends TestCase
{

    /**
     * @test
     * @expectedException \Slick\Orm\Exception\InvalidArgumentException
     */
    public function mapIsForRepositoriesOnly()
    {
        $map = new RepositoryMap();
        $this->assertSame(
            $map,
            $map->set('test', $this->getMock(RepositoryInterface::class))
        );
        $map->set('test1', new \stdClass());
    }
}
