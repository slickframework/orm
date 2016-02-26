<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm\Descriptor;

use PHPUnit_Framework_TestCase as TestCase;
use Slick\Orm\Descriptor\DescriptorsCollection;
use Slick\Orm\Descriptor\EntityDescriptorInterface;

/**
 * Descriptors Collection Test
 *
 * @package Slick\Tests\Orm\Descriptor
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class DescriptorsCollectionTest extends TestCase
{

    /**
     * @var DescriptorsCollection
     */
    protected $descriptors;

    /**
     * Set the SUT descriptors collection object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->descriptors = new DescriptorsCollection();
    }

    /**
     * Should raise an exception if adding other type of object
     * @test
     * @expectedException \Slick\Orm\Exception\InvalidArgumentException
     */
    public function onlyDescriptorsAccepted()
    {
        $this->descriptors->set('test', new \stdClass());
    }

    /**
     * Should add the descriptor to the map and return a self instance
     * @test
     */
    public function setAnDescriptor()
    {
        $descriptor = $this->getMock(EntityDescriptorInterface::class);
        $this->assertSame(
            $this->descriptors,
            $this->descriptors->set('test', $descriptor)
        );
    }
}
