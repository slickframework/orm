<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm;

use PHPUnit_Framework_TestCase as TestCase;
use Slick\Database\Adapter\AdapterInterface;
use Slick\Orm\EntityMapperInterface;
use Slick\Orm\Orm;
use Slick\Tests\Orm\Descriptor\Person;

/**
 * Orm Test case
 *
 * @package Slick\Tests\Orm
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class OrmTest extends TestCase
{

    /**
     * @var Orm
     */
    protected $orm;

    /**
     * Set the SUT orm registry object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->orm = Orm::getInstance();
        /** @var AdapterInterface $adapter */
        $adapter = $this->getMock(AdapterInterface::class);
        $this->orm->setDefaultAdapter($adapter);
    }

    public function testSingleton()
    {
        $this->assertSame($this->orm, Orm::getInstance());
    }

    /**
     * Should create the mapper for the entity
     * @test
     */
    public function getMapperForEntity()
    {
        $entity = new Person();
        $mapper = Orm::getMapper($entity);
        $this->assertInstanceOf(EntityMapperInterface::class, $mapper);
        return $mapper;
    }

    /**
     * Should use the same mapper for all same entity requests
     * @param $firstMapper
     * @depends getMapperForEntity
     */
    public function reuseTheMapper($firstMapper)
    {
        $entity = new Person();
        $mapper = Orm::getMapper($entity);
        $this->assertSame($mapper, $firstMapper);
    }
}
