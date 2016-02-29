<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm\Descriptor;

use PHPUnit_Framework_TestCase as TestCase;
use Slick\Orm\Descriptor\EntityDescriptor;
use Slick\Orm\Descriptor\EntityDescriptorRegistry;
use Slick\Orm\EntityInterface;

/**
 * Entity Descriptor Registry test case
 *
 * @package Slick\Tests\Orm\Descriptor
 */
class EntityDescriptorRegistryTest extends TestCase
{
    /**
     * @var EntityInterface
     */
    protected $entity;

    public function testCreateDescriptor()
    {
        $descriptor = EntityDescriptorRegistry::getInstance()
            ->getDescriptorFor($this->getEntity());
        $this->assertInstanceOf(EntityDescriptor::class, $descriptor);
        return $descriptor;
    }

    /**
     * @param EntityDescriptor $lastDescriptor
     * @depends testCreateDescriptor
     */
    public function testSameDescriptor($lastDescriptor)
    {
        $this->assertSame(
            $lastDescriptor,
            EntityDescriptorRegistry::getInstance()
                ->getDescriptorFor($this->getEntity())
        );
    }

    /**
     * Gets entity stub
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|EntityInterface
     */
    protected function getEntity()
    {
        if (null == $this->entity) {
            $this->entity = Person::class;
        }
        return $this->entity;
    }
}
