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
use Slick\Database\RecordList;
use Slick\Database\Sql\Dialect;
use Slick\Database\Sql\Select;
use Slick\Orm\Descriptor\EntityDescriptorInterface;
use Slick\Orm\Descriptor\EntityDescriptorRegistry;
use Slick\Orm\Descriptor\Field\FieldDescriptor;
use Slick\Orm\Orm;
use Slick\Orm\Repository\EntityRepository;
use Slick\Orm\Repository\IdentityMapInterface;
use Slick\Orm\Repository\QueryObject\QueryObject;
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


        $field = new FieldDescriptor(
            [
                'primaryKey' => true,
                'name' => 'id',
                'field' => 'uid'
            ]
        );
        /** @var EntityDescriptorInterface|MockObject $entityDescriptor */
        $entityDescriptor = $this->getMocked(EntityDescriptorInterface::class);
        $entityDescriptor->method('getTableName')->willReturn('people');
        $entityDescriptor->method('getPrimaryKey')->willReturn($field);
        /** @var EntityRepository|MockObject $repository */
        $repository = $this->getMockBuilder(EntityRepository::class)
            ->setMethods(['find'])
            ->getMock();
        /** @var AdapterInterface $adapter */
        $adapter = $this->getMock(AdapterInterface::class);
        $repository->setAdapter($adapter)
            ->setEntityDescriptor($entityDescriptor);

        $queryObject = $this->getMockBuilder(QueryObject::class)
            ->setMethods(['where', 'first'])
            ->setConstructorArgs([$repository])
            ->getMock();
        $repository->expects($this->once())
            ->method('find')
            ->willReturn($queryObject);
        $queryObject->expects($this->once())
            ->method('where')
            ->with(['people.uid = :id' => [':id' => 20]])
            ->willReturn($queryObject);
        $queryObject->expects($this->once())
            ->method('first')
            ->willReturn(new Person(['id' => '20', 'name' => 'Mike']));
        $repository->get(20);
    }

    /**
     * Should create an ObjectQuery with calling repository
     * @test
     */
    public function find()
    {
        $queryObject = $this->repository->find();
        $this->assertSame($this->repository, $queryObject->getRepository());
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
