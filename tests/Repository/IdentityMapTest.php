<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm\Repository;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Slick\Cache\CacheStorageInterface;
use Slick\Orm\Repository\IdentityMap;
use Slick\Tests\Orm\Descriptor\Person;

/**
 * IdentityMap test case
 *
 * @package Slick\Tests\Orm\Repository
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class IdentityMapTest extends TestCase
{

    /**
     * @var IdentityMap
     */
    protected $idMap;

    /**
     * Sets the SUT identity map object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->idMap = new IdentityMap();
    }

    /**
     * Clear all for next test
     */
    protected function tearDown()
    {
        $this->idMap = null;
        parent::tearDown();
    }

    /**
     * Should use Memory cache driver by default
     * @test
     */
    public function getDefaultCacheStorage()
    {
        $cache = $this->idMap->getCache();
        $this->assertInstanceOf(CacheStorageInterface::class, $cache);
    }

    /**
     * Add an entity to the map
     * @test
     */
    public function setEntity()
    {
        $person = new Person(['id' => 1, 'name' => 'Ana']);
        $storage = $this->getCacheStorageMock();
        $storage->expects($this->once())
            ->method('set')
            ->with('Slick\Tests\Orm\Descriptor\Person::1', $person)
            ->willReturn($this->returnSelf());
        $this->idMap->setCache($storage);
        $this->assertSame($this->idMap, $this->idMap->set($person));
    }

    /**
     * Should return null if no default is set
     * @test
     */
    public function getMissingEntity()
    {
        $storage = $this->getCacheStorageMock();
        $storage->expects($this->once())
            ->method('get')
            ->with('1')
            ->willReturn(null);
        $this->idMap->setCache($storage);
        $this->assertNull($this->idMap->get(1, null));
    }

    /**
     * Should remove the provided entity
     * @test
     */
    public function removeEntity()
    {
        $person = new Person(['id' => 1, 'name' => 'Ana']);
        $storage = $this->getCacheStorageMock();
        $storage->expects($this->once())
            ->method('erase')
            ->with('Slick\Tests\Orm\Descriptor\Person::'.$person->getId())
            ->willReturn($this->returnSelf());
        $this->idMap->setCache($storage);
        $this->assertSame($this->idMap, $this->idMap->remove($person));
    }

    /**
     * Get cache storage mock for tests
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
