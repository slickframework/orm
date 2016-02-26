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

/**
 * Entity Descriptor test case
 *
 * @package Slick\Tests\Orm\Descriptor
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityDescriptorTest extends TestCase
{

    public function names()
    {
        return [
            'single' => ['Person', 'people'],
            'path' => ['Path\To\Class', 'classes'],
            'composed' => ['Path\Name\UserType', 'userTypes']
        ];
    }

    /**
     * @param string $name
     * @param string $expected
     *
     * @dataProvider names
     */
    public function testTableName($name, $expected)
    {
        $descriptor = new EntityDescriptor($name);
        $this->assertEquals(
            $expected,
            $descriptor->getTableName()
        );
    }

}

