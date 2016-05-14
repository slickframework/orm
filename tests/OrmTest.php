<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm;

use League\Event\EmitterInterface;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Slick\Database\Adapter\AdapterInterface;
use Slick\Orm\EntityMapperInterface;
use Slick\Orm\Exception\InvalidArgumentException;
use Slick\Orm\Orm;
use Slick\Tests\Orm\Descriptor\OtherType;
use Slick\Tests\Orm\Descriptor\Person;
use Slick\Tests\Orm\Descriptor\Post;
use Slick\Tests\Orm\Descriptor\Repository\PostsRepository;
use Slick\Tests\Orm\Descriptor\Type;

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

    /**
     * Should reuse the repository for the same class name.
     * @test
     */
    public function getRepository()
    {
        $repository = Orm::getRepository(Person::class);
        $this->assertSame(
            $repository,
            $this->orm->getRepositoryFor(Person::class)
        );
    }

    /**
     * @test
     * @expectedException \Slick\Orm\Exception\InvalidArgumentException
     */
    public function ormCreatesRepositoriesOnlyForEntities()
    {
        Orm::getRepository(\stdClass::class);
    }

    /**
     * Should read the @repository annotation to determine the repository class
     * @test
     */
    public function createCustomRepository()
    {
        $repository = Orm::getRepository(Post::class);
        $this->assertInstanceOf(PostsRepository::class, $repository);
    }

    /**
     * The class set in the @repository annotation must exists
     * @test
     */
    public function customRepositoryMustBeAnExistingClass()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        Orm::getRepository(Type::class);
    }

    /**
     * The class set in the @repository annotation must implement the RepositoryInterface
     * @test
     */
    public function customRepositoryMustImplementRepositoryInterface()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        Orm::getRepository(OtherType::class);
    }

    /**
     * Add a listener to the entity emitter
     * @test
     */
    public function addListener()
    {
        $emitter = $this->getEmitterMock();
        $emitter->expects($this->once())
            ->method('addListener')
            ->with('get.test', $this->isType('callable'))
            ->willReturn($this->returnSelf());
        Orm::getInstance()->setEmitter('test', $emitter);
        Orm::addListener('test', 'get.test', function(){});
    }

    /**
     * Get a mocker emitter
     * @return EmitterInterface|MockObject
     */
    protected function getEmitterMock()
    {
        $class = EmitterInterface::class;
        $methods = get_class_methods($class);
        /** @var EmitterInterface|MockObject $emitter */
        $emitter = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        return $emitter;
    }

}
