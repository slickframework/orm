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
use Slick\Cache\CacheStorageInterface;
use Slick\Orm\Entity\CollectionsMap;
use Slick\Orm\Entity\EntityCollection;
use Slick\Tests\Orm\Descriptor\Person;

/**
 * CollectionsMap test case
 *
 * @package Slick\Tests\Orm\Entity
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class CollectionsMapTest extends TestCase
{

    /**
     * @var CollectionsMap
     */
    protected $collectionsMap;

    /**
     * set the SUT collections map object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->collectionsMap = new CollectionsMap();
    }

    /**
     * Clean up for next test
     */
    protected function tearDown()
    {
        $this->collectionsMap = null;
        parent::tearDown();
    }

    /**
     * Should create a cache storage (Memory driver) if no cache is set yet
     * @test
     */
    public function createCache()
    {
        $cache = $this->collectionsMap->getCache();
        $this->assertInstanceOf(CacheStorageInterface::class, $cache);
    }

    /**
     * Should use cache to store a collection under a given key
     * @test
     */
    public function setACollection()
    {
        $collection = new EntityCollection(Person::class);
        $cache = $this->getCacheStorageMock();
        $cache->expects($this->once())
            ->method('set')
            ->with('test1', $collection)
            ->willReturn($this->returnSelf());
        $this->collectionsMap->setCache($cache);
        $this->assertSame(
            $this->collectionsMap,
            $this->collectionsMap->set('test1', $collection)
        );
    }

    /**
     * Should use cache to retrieve a collection save under a given key
     * @test
     */
    public function getACollection()
    {
        $collection = new EntityCollection(Person::class);
        $cache = $this->getCacheStorageMock();
        $cache->expects($this->once())
            ->method('get')
            ->with('test1')
            ->willReturn($collection);
        $this->collectionsMap->setCache($cache);
        $this->assertSame(
            $collection,
            $this->collectionsMap->get('test1')
        );
    }

    /**
     * Gets cache storage mock object
     *
     * @return MockObject|CacheStorageInterface
     */
    protected function getCacheStorageMock()
    {
        $class = CacheStorageInterface::class;
        $methods = get_class_methods($class);
        /** @var CacheStorageInterface|MockObject $storage */
        $storage = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        return $storage;
    }
}
