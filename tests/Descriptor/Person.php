<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm\Descriptor;

use Slick\Orm\EntityInterface;

/**
 * Test entity Person
 *
 * @package Slick\Tests\Orm\Descriptor
 * @author  Filipe Silva
 *
 * @table users
 */
abstract class Person implements EntityInterface
{
    /**
     * @Slick\Orm\Annotations\Column type=integer, primaryKey, autoIncrement, field=uid
     * @var int
     */
    public $id;

    /**
     * @Slick\Orm\Annotations\Column type=text, size=tiny
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $other;

    public function getId()
    {
        return $this->id;
    }
}