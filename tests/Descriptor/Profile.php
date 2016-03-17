<?php
/**
 * Created by PhpStorm.
 * User: fsilva
 * Date: 14-03-2016
 * Time: 14:29
 */

namespace Slick\Tests\Orm\Descriptor;


use Slick\Orm\Entity;
use Slick\Orm\Annotations\Column;
use Slick\Orm\EntityInterface;

class Profile extends Entity implements EntityInterface
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
    protected $email;

    /**
     * @readwrite
     * @BelongsTo Domain\Person
     * @var Person
     */
    protected $person;

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