<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm\Descriptor;

use Slick\Orm\Annotations\BelongsTo;
use Slick\Orm\Annotations\Column;
use Slick\Orm\Entity;
use Slick\Orm\EntityInterface;

/**
 * Class Post
 * 
 * @package Slick\Tests\Orm\Descriptor
 */
class Post extends Entity
{

    /**
     * @readwrite
     * @Column type=integer, primaryKey, autoIncrement
     * @var integer
     */
    protected $id;

    /**
     * @readwrite
     * @Column type=text
     * @var string
     */
    protected $title;

    /**
     * @readwrite
     * @Column type=text
     * @var string
     */
    protected $body;

    /**
     * @readwrite
     * @BelongsTo Slick\Tests\Orm\Descriptor\Person
     * @var Person
     */
    protected $author;

    /**
     * Returns entity ID
     *
     * This is usually the primary key or a UUID
     *
     * @return mixed
     */
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