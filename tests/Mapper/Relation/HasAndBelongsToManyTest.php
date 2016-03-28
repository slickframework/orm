<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm\Mapper\Relation;

use PHPUnit_Framework_TestCase as TestCase;
use Slick\Orm\Descriptor\EntityDescriptorRegistry;
use Slick\Orm\Mapper\Relation\HasAndBelongsToMany;
use Slick\Tests\Orm\Descriptor\Post;

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
                    Post::class => true,
                ]
            );
        }
        return $this->annotation;
    }
}
