<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm\Descriptor;

use Slick\Orm\Entity;
use Slick\Orm\EntityInterface;

/**
 * Test entity Person
 *
 * @package Slick\Tests\Orm\Descriptor
 * @author  Filipe Silva
 *
 * @table users
 * @adapter default
 */
class Person extends Entity implements EntityInterface
{
    /**
     * @readwrite
     * @Slick\Orm\Annotations\Column type=integer, primaryKey, autoIncrement, field=uid
     * @var int
     */
    public $id;

    /**
     * @readwrite
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