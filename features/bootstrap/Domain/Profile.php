<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Domain;

use Slick\Orm\Entity;
use Slick\Orm\Annotations\Column;
use Slick\Orm\Annotations\BelongsTo;

/**
 * Profile
 *
 * @package Domain
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Profile extends Entity
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
     * @return integer
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
     * @return self|$this|Profile
     */
    public function setId($entityId)
    {
        $this->id = $entityId;
        return $this;
    }
}