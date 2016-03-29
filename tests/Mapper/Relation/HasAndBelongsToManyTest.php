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
use Slick\Orm\Mapper\Relation\HasAndBelongsToMany;
use Slick\Orm\Repository\QueryObject\QueryObject;
use Slick\Orm\RepositoryInterface;
use Slick\Tests\Orm\Descriptor\Post;
use Slick\Tests\Orm\Descriptor\Tag;

/**
 * HasAndBelongsToMany relation test case
 * 
 * @package Slick\Tests\Orm\Mapper\Relation
 */
class HasAndBelongsToManyTest extends TestCase
{

    /**
     * @var HasAndBelongsToMany
     */
    protected $hasAndBelongsToMany;

    /**
     * @var \Slick\Orm\Annotations\HasAndBelongsToMany
     */
    protected $annotation;

    /**
     * Sets the SUT relation object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->hasAndBelongsToMany = new HasAndBelongsToMany(
            [
                'annotation' => $this->getAnnotation(),
                'propertyName' => 'tags',
                'entityDescriptor' => EntityDescriptorRegistry::getInstance()
                    ->getDescriptorFor(Post::class)
            ]
        );
        EntityDescriptorRegistry::getInstance()->getDescriptorFor(Post::class)
            ->getRelationsMap()->set('tags', $this->hasAndBelongsToMany);
    }

    /**
     * Cleat after test
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->hasAndBelongsToMany = null;
    }

    /**
     * Should set the convention to the relation table name:
     * concatenate the table names with "_" and set the
     * @test
     */
    public function getRelatedTableName()
    {
        $expected = 'post_tags';
        $this->assertEquals(
            $expected,
            $this->hasAndBelongsToMany->getRelationTable()
        );
    }

    /**
     * Should get the "normal" foreign key
     * @test
     */
    public function getForeignKey()
    {
        $this->assertEquals('post_id', $this->hasAndBelongsToMany->getForeignKey());
    }

    /**
     * Should get the related foreign key
     * @test
     */
    public function getRelaterForeignKey()
    {
        $this->assertEquals('tag_id', $this->hasAndBelongsToMany->getRelatedForeignKey());
    }

    /**
     * @test
     */
    public function load()
    {
        $tag = new Tag(['id' => 1, 'description' => 'PHP']);
        $collection = new EntityCollection(Tag::class, [$tag]);
        $post = new Post(['id' => 1, 'title' => 'tet']);
        $query = $this->getQueryObjectMocked(
            ['where', 'all', 'limit', 'join']
        );
        $query->method('all')->willReturn($collection);
        $query->method('limit')->willReturn($query);
        $query->expects($this->once())
            ->method('where')
            ->with(
                [
                    "rel.post_id = :post" => [
                        ':post' => 1
                    ]
                ]
            )
            ->willReturn($query);
        $query->expects($this->once())
            ->method('join')
            ->with(
                'post_tags',
                'rel.tag_id = tags.id',
                null,
                'rel'
            )
            ->willReturn($query);

        $repository = $this->getRepositoryMocked();
        $repository->expects($this->once())
            ->method('find')
            ->willReturn($query);
        $this->hasAndBelongsToMany->setParentRepository($repository);
        $this->assertSame($collection, $this->hasAndBelongsToMany->load($post));
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
     * Gets the annotation for relation
     *
     * @return \Slick\Orm\Annotations\HasMany
     */
    protected function getAnnotation()
    {
        if (null == $this->annotation) {
            $this->annotation = new \Slick\Orm\Annotations\HasAndBelongsToMany(
                'HasAndBelongsToMany',
                [
                    Tag::class => true,
                ]
            );
        }
        return $this->annotation;
    }
}
