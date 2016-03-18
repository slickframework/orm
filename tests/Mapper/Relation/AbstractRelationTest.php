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
use Slick\Orm\Descriptor\EntityDescriptorInterface;
use Slick\Orm\Mapper\Relation\AbstractRelation;
use Slick\Tests\Orm\Descriptor\Profile;

/**
 * Abstract Relation test case
 *
 * @package Slick\Tests\Orm\Mapper\Relation
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class AbstractRelationTest extends TestCase
{

    /**
     * @var AbstractRelation
     */
    protected $relation;

    protected function setUp()
    {
        parent::setUp();
        $this->relation = $this->getMockForAbstractClass(AbstractRelation::class);
    }

    /**
     * Should check Orm for entity adapter
     * @test
     */
    public function getAdapter()
    {
        $descriptor = $this->entityDescriptorMock();
        $descriptor->expects($this->once())
            ->method('className')
            ->willReturn(Profile::class);
        $this->relation->setEntityDescriptor($descriptor);
        $adapter = $this->relation->getAdapter();
        $this->assertInstanceOf(AdapterInterface::class, $adapter);
    }

    /**
     * Gets entity descriptor mocked
     *
     * @return MockObject|EntityDescriptorInterface
     */
    protected function entityDescriptorMock()
    {
        $class = EntityDescriptorInterface::class;
        $methods = get_class_methods($class);
        /** @var EntityDescriptorInterface|MockObject $descriptor */
        $descriptor = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        return $descriptor;
    }
}
