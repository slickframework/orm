<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm\Descriptor;

use Slick\Orm\Annotations\Column;
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
     * @column type=integer, primaryKey, autoIncrement, field=uid
     * @var int
     */
    public $id;

    /**
     * @readwrite
     * @Column type=text, size=tiny
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $other;

    /**
     * @readwrite
     * @MyRelation
     * @var string
     */
    public $testRelation;

    /**
     * @readwrite
     * @HasOne Slick\Tests\Orm\Descriptor\Profile
     * @var string
     */
    protected $profile;

    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets entity ID
     *
     * @param mixed $entityId Primary key or a UUID
     *
     * @return self|$this|EntityInterface
     */
    public function setId($entityId)
    {
        $this->id = $entityId;
        return $this;
    }
}