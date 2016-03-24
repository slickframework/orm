<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm\Repository\QueryObject;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Slick\Database\Adapter;
use Slick\Database\Adapter\AdapterInterface;
use Slick\Database\Sql\Dialect;
use Slick\Orm\Descriptor\EntityDescriptorInterface;
use Slick\Orm\Entity\CollectionsMapInterface;
use Slick\Orm\Entity\EntityCollection;
use Slick\Orm\EntityInterface;
use Slick\Orm\EntityMapperInterface;
use Slick\Orm\Event\EntityRemoved;
use Slick\Orm\Repository\IdentityMapInterface;
use Slick\Orm\Repository\QueryObject\QueryObject;
use Slick\Orm\RepositoryInterface;
use Slick\Tests\Orm\Descriptor\Person;

/**
 * QueryObject test case
 *
 * @package Slick\Tests\Orm\Repository\QueryObject
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class QueryObjectTest extends TestCase
{

    /**
     * @var QueryObject
     */
    protected $queryObject;

    /**
     * Sets the SUT object
     */
    protected function setUp()
    {
        parent::setUp();
        /** @var RepositoryInterface $repo */
        $repo = $this->getRepositoryMock();
        $this->queryObject = new QueryObject($repo);
        $this->queryObject->setAdapter($this->getMockedAdapter());
    }

    /**
     * Clears object for next test
     */
    protected function tearDown()
    {
        $this->queryObject = null;
        parent::tearDown();
    }

    /**
     * Should return the list from collections map storage
     * @test
     */
    public function getSavedCollection()
    {
        $collection = $this->getEntityCollection();
        $collectionsMap = $this->getMockedCollectionMap();
        $collectionsMap->expects($this->once())
            ->method('get')
            ->with('SELECT people.* FROM people', false)
            ->willReturn($collection);
        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->queryObject->getRepository();
        $repository->expects($this->once())
            ->method('getCollectionsMap')
            ->willReturn($collectionsMap);
        $this->assertSame($collection, $this->queryObject->all());
    }

    /**
     * Should query the database, create the collection, save it in the collections map and
     * save each entity in the entity map.
     * @test
     */
    public function getCollectionFromDb()
    {
        $collection = $this->getEntityCollection();
        // Collections map mocck
        $collectionsMap = $this->getMockedCollectionMap();
        $collectionsMap->expects($this->once())
            ->method('get')
            ->with('SELECT people.* FROM people', false)
            ->willReturn(false);
        $collectionsMap->expects($this->once())
            ->method('set')
            ->with('SELECT people.* FROM people', $collection);

        // Adapter mock
        $adapter = $this->getMockedAdapter();
        $adapter->expects($this->once())
            ->method('query')
            ->with($this->queryObject, [])
            ->willReturn([]);
        $this->queryObject->setAdapter($adapter);


        $entityMapper = $this->getMockedEntityMapper();
        $entityMapper->expects($this->once())
            ->method('createFrom')
            ->with([])
            ->willReturn($collection);

        $identityMap = $this->getIdentityMapMock();
        $identityMap->expects($this->atLeast(2))
            ->method('set')
            ->with($this->isInstanceOf(EntityInterface::class));

        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->queryObject->getRepository();
        $repository->expects($this->once())
            ->method('getEntityMapper')
            ->willReturn($entityMapper);
        $repository->expects($this->atLeast(2))
            ->method('getCollectionsMap')
            ->willReturn($collectionsMap);
        $repository->expects($this->atLeast(2))
            ->method('getIdentityMap')
            ->willReturn($identityMap);
        $this->assertSame($collection, $this->queryObject->all());
    }

    /**
     * Should query the database, create the collection, save it in the collections map and
     * save each entity in the entity map.
     * @test
     */
    public function getFirstMatch()
    {
        $collection = $this->getEntityCollection();
        $collection = new EntityCollection(Person::class, [$collection[0]]);
        // Collections map mocck
        $collectionsMap = $this->getMockedCollectionMap();
        $collectionsMap->expects($this->atLeastOnce())
            ->method('get')
            ->with('SELECT people.* FROM people LIMIT 1', false)
            ->willReturn(false);
        $collectionsMap->expects($this->once())
            ->method('set')
            ->with('SELECT people.* FROM people LIMIT 1', $collection);

        // Adapter mock
        $adapter = $this->getMockedAdapter();
        $adapter->expects($this->once())
            ->method('query')
            ->with($this->isInstanceOf(QueryObject::class), [])
            ->willReturn([]);
        $this->queryObject->setAdapter($adapter);


        $entityMapper = $this->getMockedEntityMapper();
        $entityMapper->expects($this->once())
            ->method('createFrom')
            ->with([])
            ->willReturn($collection);

        $identityMap = $this->getIdentityMapMock();
        $identityMap->expects($this->atLeast(1))
            ->method('set')
            ->with($this->isInstanceOf(EntityInterface::class));

        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->queryObject->getRepository();
        $repository->expects($this->once())
            ->method('getEntityMapper')
            ->willReturn($entityMapper);
        $repository->expects($this->atLeast(1))
            ->method('getCollectionsMap')
            ->willReturn($collectionsMap);
        $repository->expects($this->atLeast(1))
            ->method('getIdentityMap')
            ->willReturn($identityMap);
        $this->assertSame($collection[0], $this->queryObject->first());
    }

    /**
     * Empty collections have no update in the identity map
     * @test
     */
    public function getEmptyCollection()
    {
        $collection = new EntityCollection(Person::class);
        // Collections map mocck
        $collectionsMap = $this->getMockedCollectionMap();
        $collectionsMap->expects($this->once())
            ->method('get')
            ->with('SELECT people.* FROM people', false)
            ->willReturn(false);
        $collectionsMap->expects($this->once())
            ->method('set')
            ->with('SELECT people.* FROM people', $collection);

        // Adapter mock
        $adapter = $this->getMockedAdapter();
        $adapter->expects($this->once())
            ->method('query')
            ->with($this->queryObject, [])
            ->willReturn([]);
        $this->queryObject->setAdapter($adapter);


        $entityMapper = $this->getMockedEntityMapper();
        $entityMapper->expects($this->once())
            ->method('createFrom')
            ->with([])
            ->willReturn($collection);



        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->queryObject->getRepository();
        $repository->expects($this->once())
            ->method('getEntityMapper')
            ->willReturn($entityMapper);
        $repository->expects($this->atLeast(2))
            ->method('getCollectionsMap')
            ->willReturn($collectionsMap);
        $this->assertSame($collection, $this->queryObject->all());
    }

    /**
     * Should replace the cached collection on event
     * @test
     */
    public function updateCollection()
    {

        $entity = new Person(['id' => 1, 'name' => 'test']);
        $collection = new EntityCollection(Person::class, [$entity]);
        $cid = 'SELECT people.* FROM people';
        $collection->setId($cid);
        // Collections map mock
        $collectionsMap = $this->getMockedCollectionMap();
        $collectionsMap->expects($this->once())
            ->method('set')
            ->with($cid, $collection)
            ->willReturn($collectionsMap);
        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->queryObject->getRepository();
        $repository->expects($this->once())
            ->method('getCollectionsMap')
            ->willReturn($collectionsMap);
        $event = new EntityRemoved($entity, ['collection' => $collection]);
        $this->queryObject->updateCollection($event);
    }

    /**
     * Get a mocked repository
     *
     * @return RepositoryInterface|MockObject
     */
    protected function getRepositoryMock()
    {
        $class = RepositoryInterface::class;
        $methods = get_class_methods($class);
        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        $repository->method('getEntityDescriptor')
            ->willReturn($this->getMockedEntityDescriptor());
        return $repository;
    }

    /**
     * Get a mocked adapter
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
        $adapter->method('getDialect')
            ->willReturn(Dialect::MYSQL);
        return $adapter;

    }

    /**
     * Gets entity descriptor
     *
     * @return MockObject|EntityDescriptorInterface
     */
    protected function getMockedEntityDescriptor()
    {
        $class = EntityDescriptorInterface::class;
        $methods = get_class_methods($class);
        /** @var EntityDescriptorInterface|MockObject $descriptor */
        $descriptor = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        $descriptor->method('getTableName')
            ->willReturn('people');
        return $descriptor;
    }

    /**
     * Get a mocked collections map
     *
     * @return MockObject|CollectionsMapInterface
     */
    protected function getMockedCollectionMap()
    {
        $class = CollectionsMapInterface::class;
        $methods = get_class_methods($class);
        /** @var CollectionsMapInterface|MockObject $map */
        $map = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        return $map;
    }

    /**
     * Returns an entity collection
     * @return EntityCollection
     */
    protected function getEntityCollection()
    {
        $data = [
            new Person(['id' => 1, 'name' => 'Mike']),
            new Person(['id' => 2, 'name' => 'Ana']),
        ];
        return new EntityCollection(Person::class, $data);
    }

    /**
     * Get mocked mapper
     *
     * @return MockObject|EntityMapperInterface
     */
    protected function getMockedEntityMapper()
    {
        $class = EntityMapperInterface::class;
        $methods = get_class_methods($class);
        /** @var EntityMapperInterface|MockObject $mapper */
        $mapper = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        return $mapper;
    }

    /**
     * Gets a mocked Identity map
     *
     * @return MockObject|IdentityMapInterface
     */
    protected function getIdentityMapMock()
    {
        $class = IdentityMapInterface::class;
        $methods = get_class_methods($class);
        /** @var IdentityMapInterface|MockObject $identityMap */
        $identityMap = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        return $identityMap;
    }
}
