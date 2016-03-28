<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Domain;

use Slick\Orm\Annotations as Orm;
use Slick\Orm\Entity;
use Slick\Orm\EntityInterface;

/**
 * Tag
 *
 * @package Domain
 */
class Tag extends Entity
{

    /**
     * @readwrite
     * @Orm\Column type=integer, primaryKey, autoIncrement
     * @var integer
     */
    protected $id;

    /**
     * @readwrite
     * @Orm\Column type=text
     * @var string
     */
    protected $description;

    /**
     * @readwrite
     * @Orm\HasAndBelongsToMany Domain\Post
     * @var Post
     */
    protected $posts;

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
    }
}