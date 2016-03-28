<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Domain;

use Slick\Orm\Entity;
use Slick\Orm\Annotations as Orm;
use Slick\Orm\EntityInterface;

/**
 * Class Person
 * @package Domain
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 *
 * @property integer $uid
 * @property string  $name
 */
class Person extends Entity
{
    /**
     * @readwrite
     * @Orm\Column type=integer, primaryKey, autoIncrement
     * @var integer
     */
    protected $uid;

    /**
     * @readwrite
     * @Orm\Column type=text
     * @var string
     */
    protected $name;

    /**
     * @readwrite
     * @Orm\HasOne Domain\Profile
     * @var Profile
     */
    protected $profile;

    /**
     * @readwrite
     * @Orm\HasMany Domain\Post, order=posts.title ASC, conditions=1=1
     *
     * @var Post[]|Entity\EntityCollection
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
        return $this->uid;
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
       $this->uid = $entityId;
        return $this;
    }
}