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
     * @Column type=integer, primaryKey, autoIncrement
     * @var integer
     */
    protected $uid;

    /**
     * @readwrite
     * @Column type=text
     * @var string
     */
    protected $name;

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
}