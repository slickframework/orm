<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm\Mapper;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Slick\Database\Adapter\AdapterInterface;
use Slick\Database\Sql\Insert;
use Slick\Database\Sql\Update;
use Slick\Orm\Mapper\EntityMapper;
use Slick\Tests\Orm\Descriptor\Person;

/**
 * EntityMapper test case
 *
 * @package Slick\Tests\Orm\Mapper
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityMapperTest extends TestCase
{

    /**
     * @var EntityMapper
     */
    protected $mapper;

    /**
     * Set the SUT entity mapper object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mapper = new EntityMapper();
    }

    /**
     * Clear SUT for next test
     */
    protected function tearDown()
    {
        $this->mapper = null;
        parent::tearDown();
    }

    /**
     * Creates and returns a mocked adapter interface
     *
     * @return MockObject|AdapterInterface
     */
    protected function getAdapterMock()
    {
        $class = AdapterInterface::class;
        $methods = get_class_methods($class);
        /** @var MockObject|AdapterInterface $adapter */
        $adapter = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        return $adapter;
    }

    /**
     * Should use adapter to execute an Insert query
     * @test
     */
    public function saveEntity()
    {
        $mike = new Person(['name' => 'Mike']);
        $adapter = $this->getAdapterMock();
        $adapter->expects($this->once())
            ->method('execute')
            ->with(
                $this->isInstanceOf(Insert::class),
                $this->isType('array')
            )
            ->willReturn(1);
        $this->mapper->setAdapter($adapter);
        $this->assertSame($this->mapper, $this->mapper->save($mike));
    }

    /**
     * Should use adapter to execute an Update query
     * @test
     */
    public function saveExistingEntity()
    {
        $mike = new Person(['name' => 'Mike', 'id' => 1]);
        $adapter = $this->getAdapterMock();
        $adapter->expects($this->once())
            ->method('execute')
            ->with(
                $this->isInstanceOf(Update::class),
                $this->isType('array')
            )
            ->willReturn(1);
        $this->mapper->setAdapter($adapter);
        $this->assertSame($this->mapper, $this->mapper->save($mike));
    }
}
