<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm\Descriptor;

use PHPUnit_Framework_TestCase as TestCase;
use Slick\Orm\Descriptor\RelationsMap;
use Slick\Orm\Mapper\RelationInterface;

/**
 * RelationsMap test case
 *
 * @package Slick\Tests\Orm\Descriptor
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class RelationsMapTest extends TestCase
{

    /**
     * @var RelationsMap
     */
    protected $relationsMap;

    /**
     * Sets the SUT map object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->relationsMap = new RelationsMap();
    }

    /**
     * Should only accept RelationInterface object: Should throw an
     * exception otherwise.
     * @test
     * @expectedException \Slick\Orm\Exception\InvalidArgumentException
     */
    public function setRelation()
    {
        $relation = $this->getMock(RelationInterface::class);
        $this->assertSame($this->relationsMap, $this->relationsMap->set('test', $relation));
        $this->relationsMap->set('test2', new \stdClass());
    }
}
