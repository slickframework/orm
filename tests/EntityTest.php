<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Slick\Database\Adapter\AdapterInterface;
use Slick\Orm\Entity;
use Slick\Orm\EntityMapperInterface;
use Slick\Orm\Orm;
use Slick\Tests\Orm\Descriptor\Person;

/**
 * Entity test case
 *
 * @package Slick\Tests\Orm
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityTest extends TestCase
{

    /**
     * @var Entity|MockObject
     */
    protected $entity;

    /**
     * Setup the SUT entity object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->entity = $this->getMockBuilder(Entity::class)
            ->setMethods(['getMapper'])
            ->getMockForAbstractClass();
    }

    /**
     * Execute save on mapper
     * @test
     */
    public function testSave()
    {
        $mapper = $this->getMockedMapper();
        $mapper->expects($this->once())
            ->method('save')
            ->with($this->entity, [])
            ->willReturn(1);
        $this->entity->expects($this->once())
            ->method('getMapper')
            ->willReturn($mapper);
        $this->entity->save();
    }

    public function testGetMapper()
    {
        /** @var AdapterInterface $adapter */
        $adapter = $this->getMock(AdapterInterface::class);
        Orm::getInstance()->setDefaultAdapter($adapter);
        $mike = new Person(['name' => 'Mike']);
        $mapper = $mike->getMapper();
        $this->assertInstanceOf(EntityMapperInterface::class, $mapper);
    }

    /**
     * Gets a mocked entity mapper
     *
     * @return MockObject|EntityMapperInterface
     */
    protected function getMockedMapper()
    {
        $class = EntityMapperInterface::class;
        $methods = get_class_methods($class);
        /** @var MockObject|EntityMapperInterface $mapper */
        $mapper = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        return $mapper;
    }
}
