<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm\Entity;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Slick\Database\Adapter\AdapterInterface;
use Slick\Orm\Entity\EntityCollection;
use Slick\Orm\Orm;
use Slick\Orm\RepositoryInterface;
use Slick\Tests\Orm\Descriptor\Person;
use Slick\Tests\Orm\Descriptor\Profile;

/**
 * EntityCollection test
 *
 * @package Slick\Tests\Orm\Entity
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityCollectionTest extends TestCase
{

    /**
     * @var EntityCollection
     */
    protected $entities;

    /**
     * Create the SUT collection object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->entities = new EntityCollection(Person::class);
        Orm::getInstance()->setAdapter('default', $this->getMockedAdapter());
    }

    /**
     * @test
     */
    public function acceptOnlyEntities()
    {
        $this->assertSame(
            $this->entities,
            $this->entities->add(new Person(['id' => 2]))
        );
    }

    /**
     * @test
     */
    public function entitiesAssignAsArray()
    {
        $person = new Person(['id' => 2]);
        $this->entities[] = $person;
        $this->assertSame($person, $this->entities[0]);

    }

    /**
     * Lazy load repository creation
     * @test
     */
    public function getRepository()
    {
        $this->assertInstanceOf(
            RepositoryInterface::class,
            $this->entities->getRepository()
        );
    }

    /**
     * Should user repository to get the entity with provided id, then
     * added it to the collection
     * @test
     */
    public function addWithId()
    {
        $eid = 2;
        $entity = new Person(['id' => $eid, 'name' => 'test']);
        $repository = $this->getMockedRepository();
        $repository->expects($this->once())
            ->method('get')
            ->with($eid)
            ->willReturn($entity);
        $this->entities->setRepository($repository);
        $this->assertSame($this->entities, $this->entities->add($eid));
    }

    /**
     * Should raise an exception
     * @test
     * @expectedException \Slick\Orm\Exception\EntityNotFoundException
     */
    public function addInvalidId()
    {
        $eid = 2;
        $entity = null;
        $repository = $this->getMockedRepository();
        $repository->expects($this->once())
            ->method('get')
            ->with($eid)
            ->willReturn($entity);
        $this->entities->setRepository($repository);
        $this->entities->add($eid);
    }

    /**
     * Should raise an exception
     * @test
     * @expectedException \Slick\Orm\Exception\InvalidArgumentException
     */
    public function addOtherEntity()
    {
        $entity = new Profile(['id' => 2, 'email' => 'test@example.com']);
        $this->entities->add($entity);
    }

    /**
     * Get mocked repository
     *
     * @return MockObject|RepositoryInterface
     */
    protected function getMockedRepository()
    {
        $class = RepositoryInterface::class;
        $methods = get_class_methods($class);
        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        return $repository;
    }

    /**
     * Gets a mocked adapter
     *
     * @return MockObject|AdapterInterface
     */
    protected function getMockedAdapter()
    {
        $class = AdapterInterface::class;
        $methods = get_class_methods($class);
        /** @var AdapterInterface|MockObject $adapter */
        $adapter = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        return $adapter;
    }
}
