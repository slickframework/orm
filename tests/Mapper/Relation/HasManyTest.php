<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm\Mapper\Relation;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Slick\Orm\Descriptor\EntityDescriptorRegistry;
use Slick\Orm\Entity\EntityCollection;
use Slick\Orm\Mapper\Relation\HasMany;
use Slick\Orm\Repository\QueryObject\QueryObject;
use Slick\Orm\RepositoryInterface;
use Slick\Tests\Orm\Descriptor\Person;
use Slick\Tests\Orm\Descriptor\Post;

/**
 * HasMany relation test case
 *
 * @package Slick\Tests\Orm\Mapper\Relation
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class HasManyTest extends TestCase
{

    /**
     * @var HasMany
     */
    protected $hasMany;

    /**
     * @var \Slick\Orm\Annotations\HasMany
     */
    protected $annotation;

    /**
     * Sets the SUT relation object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->hasMany = new HasMany(
            [
                'annotation' => $this->getAnnotation(),
                'propertyName' => 'posts',
                'entityDescriptor' => EntityDescriptorRegistry::getInstance()
                    ->getDescriptorFor(Person::class)
            ]
        );
        EntityDescriptorRegistry::getInstance()->getDescriptorFor(Person::class)
            ->getRelationsMap()->set('posts', $this->hasMany);
    }

    /**
     * Clear for next test
     */
    protected function tearDown()
    {
        $this->hasMany = null;
        parent::tearDown();
    }

    public function testForeignKey()
    {
        $this->assertEquals('user_id', $this->hasMany->getForeignKey());
    }

    public function testLoad()
    {
        $collection = new EntityCollection(Person::class);
        $entity = new Person(['id' => 1, 'name' => 'test']);
        $query = $this->getQueryObjectMocked(['where', 'all', 'limit']);
        $query->method('all')->willReturn($collection);
        $query->method('limit')->willReturn($query);
        $query->expects($this->once())
            ->method('where')
            ->with(
                [
                    "posts.user_id = :posts" => [
                        ':posts' => 1
                    ]
                ]
            )
            ->willReturn($query);
        $repository = $this->getRepositoryMocked();
        $repository->expects($this->once())
            ->method('find')
            ->willReturn($query);
        $this->hasMany->setParentRepository($repository);
        $this->assertSame($collection, $this->hasMany->load($entity));
    }

    /**
     * Gets the annotation for relation
     *
     * @return \Slick\Orm\Annotations\HasMany
     */
    protected function getAnnotation()
    {
        if (null == $this->annotation) {
            $this->annotation = new \Slick\Orm\Annotations\HasMany(
                'HasMany',
                [
                    Post::class => true
                ]
            );
        }
        return $this->annotation;
    }

    /**
     * Get mocked query object
     *
     * @param array $methods
     * @return MockObject|QueryObject
     */
    protected function getQueryObjectMocked($methods = [])
    {
        /** @var QueryObject|MockObject $query */
        $query = $this->getMockBuilder(QueryObject::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
        return $query;
    }

    /**
     * Gets a mocked repository
     *
     * @return MockObject|RepositoryInterface
     */
    protected function getRepositoryMocked()
    {
        $class = RepositoryInterface::class;
        $methods = get_class_methods($class);
        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        return $repository;
    }
}
