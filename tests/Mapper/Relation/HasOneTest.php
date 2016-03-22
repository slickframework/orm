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
use Slick\Database\Adapter\AdapterInterface;
use Slick\Database\RecordList;
use Slick\Database\Sql\Select;
use Slick\Orm\Descriptor\EntityDescriptorRegistry;
use Slick\Orm\Entity\EntityCollection;
use Slick\Orm\Event\Delete;
use Slick\Orm\Mapper\Relation\HasOne;
use Slick\Tests\Orm\Descriptor\Person;
use Slick\Tests\Orm\Descriptor\Profile;

/**
 * HasOne relation test case
 *
 * @package Slick\Tests\Orm\Mapper\Relation
 */
class HasOneTest extends TestCase
{

    /**
     * @var HasOne
     */
    protected $hasOne;

    /**
     * @var \Slick\Orm\Annotations\HasOne
     */
    protected $annotation;

    /**
     * Sets the SUT relation object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->hasOne = new HasOne(
            [
                'annotation' => $this->getAnnotation(),
                'propertyName' => 'profile',
                'entityDescriptor' => EntityDescriptorRegistry::getInstance()
                    ->getDescriptorFor(Person::class)
            ]
        );
        EntityDescriptorRegistry::getInstance()->getDescriptorFor(Person::class)
            ->getRelationsMap()->set('profile', $this->hasOne);
    }

    /**
     * Clears for next test
     */
    protected function tearDown()
    {
        $this->hasOne = null;
        parent::tearDown();
    }

    /**
     * Should return the property name
     * @test
     */
    public function getProperty()
    {
        $this->assertEquals('profile', $this->hasOne->getPropertyName());
    }

    /**
     * Should do nothing and return
     * @test
     */
    public function beforeSelectLazy()
    {
        $query = $this->getQueryMock();
        $query->expects($this->never())
            ->method('join');
        $this->hasOne->lazyLoaded = true;
        $event = new \Slick\Orm\Event\Select(null, ['query' => $query]);
        $this->hasOne->beforeSelect($event);
    }

    /**
     * Should add the join information to the query
     * @test
     */
    public function beforeSelect()
    {
        $query = $this->getQueryMock();
        $query->expects($this->once())
            ->method('join')
            ->with(
                'profiles',
                'users.uid = profiles.user_id',
                [
                    'id AS profiles_id',
                    'email AS profiles_email'
                ],
                'profiles'
            )
            ->willReturn($this->returnSelf());
        $event = new \Slick\Orm\Event\Select(null, ['query' => $query]);
        $this->hasOne->beforeSelect($event);
    }

    /**
     * Should create the entity from query data and assign it to entity
     * @test
     */
    public function afterSelect()
    {
        $ana = new Person(['id' => 2, 'name' => 'Ana']);
        $entityCollection = new EntityCollection([$ana]);
        $select = new \Slick\Orm\Event\Select(
            null,
            [
                'entityCollection' => $entityCollection,
                'data' => [
                    [
                        'id' => 2,
                        'name' => 'Ana',
                        'profiles_id' => 2,
                        'profiles_email' => 'ana@example.com'
                    ]
                ]
            ]
        );
        $this->hasOne->afterSelect($select);
        $this->assertInstanceOf(Profile::class, $entityCollection->offsetGet(0)->profile);
    }

    /**
     * Should delete the related entity before delete it self
     * @test
     */
    public function beforeDelete()
    {
        $profile = $this->getMockBuilder(Profile::class)
            ->setConstructorArgs(['id' => 2, 'email' => 'test@example.com'])
            ->setMethods(['delete'])
            ->getMock();
        $profile->expects($this->once())
            ->method('delete')
            ->willReturn(1);
        $ana = new Person(['id' => 2, 'name' => 'Ana', 'profile' => $profile]);
        $event = new Delete($ana);
        $this->hasOne->beforeDelete($event);
    }

    /**
     * Should query the related table for the right row
     * @test
     */
    public function load()
    {
        $ana = new Person(['id' => 2, 'name' => 'Ana']);
        $data = ['id' => 2, 'email' => 'ana@example.com', 'user_id' => 2];
        /** @var AdapterInterface|MockObject $adapter */
        $adapter = $this->getMockBuilder(AdapterInterface::class)
            ->setMethods(get_class_methods(AdapterInterface::class))
            ->getMock();
        $adapter->expects($this->once())
            ->method('query')
            ->with($this->isInstanceOf(Select::class), [':id' => $ana->getId()])
            ->willReturn(new RecordList(['data' => [$data]]));
        $this->hasOne->setAdapter($adapter);
        
        $profile = $this->hasOne->load($ana);
        $this->assertInstanceOf(Profile::class, $profile);
    }

    /**
     * Creates a query mock
     * 
     * @return MockObject|Select
     */
    protected function getQueryMock()
    {
        /** @var Select|MockObject $query */
        $query = $this->getMockBuilder(Select::class)
            ->setConstructorArgs(['people'])
            ->setMethods(['join'])
            ->getMock();
        return $query;
    }

    /**
     * Creates a fake annotation
     *
     * @return \Slick\Orm\Annotations\HasOne
     */
    protected function getAnnotation()
    {
        if (null == $this->annotation) {
            $this->annotation = new \Slick\Orm\Annotations\HasOne(
                'HasOne',
                [
                    Profile::class => true
                ]
            );
        }
        return $this->annotation;
    }
}
