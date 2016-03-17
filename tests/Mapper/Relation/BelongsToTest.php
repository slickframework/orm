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
use Slick\Database\Sql\Dialect;
use Slick\Database\Sql\Select;
use Slick\Orm\Descriptor\EntityDescriptorRegistry;
use Slick\Orm\Mapper\Relation\BelongsTo;
use Slick\Tests\Orm\Descriptor\Person;
use Slick\Tests\Orm\Descriptor\Profile;


/**
 * BelongsTo test case
 *
 * @package Slick\Tests\Orm\Mapper\Relation
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class BelongsToTest extends TestCase
{

    /**
     * @var BelongsTo
     */
    protected $belongsTo;

    /**
     * @var \Slick\Orm\Annotations\BelongsTo
     */
    protected $annotation;

    /**
     * Sets the SUT relation object.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->belongsTo = new BelongsTo(
            [
                'annotation' => $this->getAnnotation(),
                'propertyName' => 'profile',
                'entityDescriptor' => EntityDescriptorRegistry::getInstance()
                    ->getDescriptorFor(Person::class)
            ]
        );
        EntityDescriptorRegistry::getInstance()->getDescriptorFor(Person::class)
            ->getRelationsMap()->set('profile', $this->belongsTo);
    }

    /**
     * Cleaning for next test
     */
    protected function tearDown()
    {
        $this->belongsTo = null;
        parent::tearDown();
    }

    /**
     * Should return the property name
     * @test
     */
    public function getProperty()
    {
        $this->assertEquals('profile', $this->belongsTo->getPropertyName());
    }

    /**
     * Should load entity from database
     * @test
     */
    public function loadEntity()
    {
        $person = New Person(['id' => 2, 'name' => 'John']);
        $adapter = $this->getMockedAdapter();
        $adapter->expects($this->once())
            ->method('query')
            ->with($this->isInstanceOf(Select::class), [':id' => 2])
            ->willReturn(new RecordList());
        $this->belongsTo->setAdapter($adapter);
        $this->belongsTo->load($person);
    }

    /**
     * Should add a join to the passed select event
     * @test
     */
    public function beforeSelect()
    {
        /** @var Select|MockObject $query */
        $query = $this->getMockBuilder(Select::class)
            ->setConstructorArgs(['people'])
            ->setMethods(['join'])
            ->getMock();
        $query->expects($this->once())
            ->method('join')
            ->with(
                'profiles',
                'users.profile_id = profiles.id',
                [
                    'id AS profiles_id',
                    'email AS profiles_email'
                ],
                'profiles'
            )
            ->willReturn($this->returnSelf());
        $event = new \Slick\Orm\Event\Select(null, ['query' => $query]);
        $this->belongsTo->beforeSelect($event);
    }

    /**
     * Creates a fake annotation
     *
     * @return \Slick\Orm\Annotations\BelongsTo
     */
    protected function getAnnotation()
    {
        if (null == $this->annotation) {
            $this->annotation = new \Slick\Orm\Annotations\BelongsTo(
                'BelongsTo',
                [
                    Profile::class => true
                ]
            );
        }
        return $this->annotation;
    }

    /**
     * Gets a mocked database adapter
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
}
