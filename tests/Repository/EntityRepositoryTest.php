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
use Slick\Database\Adapter\AdapterInterface;
use Slick\Database\Sql\Select;
use Slick\Orm\Descriptor\EntityDescriptorRegistry;
use Slick\Orm\Orm;
use Slick\Orm\Repository\EntityRepository;
use Slick\Orm\Repository\IdentityMapInterface;
use Slick\Tests\Orm\Descriptor\Person;

/**
 * Entity Repository test case
 *
 * @package Slick\Tests\Orm\Repository
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityRepositoryTest extends TestCase
{

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * Sets the SUT EntityRepository object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->repository = new EntityRepository();
        $this->repository->setEntityDescriptor(
            EntityDescriptorRegistry::getInstance()
                ->getDescriptorFor(Person::class)
        );
    }

    /**
     * Should return the entity that lives in the identity map
     * @test
     */
    public function getStoredEntity()
    {
        $id = 2;
        $person = new Person(['id' => $id, 'name' => 'mike']);

        /** @var IdentityMapInterface|MockObject $idMap */
        $idMap = $this->getMocked(IdentityMapInterface::class);
        $idMap->expects($this->once())
            ->method('get')
            ->with($id)
            ->willReturn($person);
        $this->repository->setIdentityMap($idMap);
        $this->assertSame($person, $this->repository->get($id));
    }

    /**
     * Should query the database, ans store the entity in the identity map.
     * @test
     */
    public function getAnEntityFromDb()
    {
        $id = 2;
        $data =['id' => $id, 'name' => 'mike'];
        $person = new Person($data);
        /** @var IdentityMapInterface|MockObject $idMap */
        $idMap = $this->getMocked(IdentityMapInterface::class);
        $idMap->expects($this->once())
            ->method('get')
            ->with($id)
            ->willReturn(false);
        $idMap->expects($this->once())
            ->method('set')
            ->willReturn($person)
            ->willReturn($this->returnSelf());

        /** @var AdapterInterface|MockObject $adapter */
        $adapter = $this->getMocked(AdapterInterface::class);
        $adapter->expects($this->once())
            ->method('query')
            ->with($this->isInstanceOf(Select::class))
            ->willReturn([$data]);
        $this->repository->setAdapter($adapter)
            ->setIdentityMap($idMap)
            ->setEntityMapper(Orm::getMapper(Person::class));
        $this->assertEquals('mike', $this->repository->get($id)->name);
    }

    /**
     * Get a mocked object from provided class
     *
     * @param string $class
     *
     * @return MockObject
     */
    protected function getMocked($class)
    {
        $methods = get_class_methods($class);
        $object = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        return $object;
    }
}
