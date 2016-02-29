<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm;

use Slick\Common\Base;

/**
 * Entity
 *
 * @package Slick\Orm
 * @readwrite
 */
abstract class Entity extends Base implements EntityInterface
{

    /**
     * Saves current entity state
     *
     * Optionally saves only the partial data if $data argument is passed. If
     * no data is given al the field properties will be updated.
     *
     * @param array $data Partial data to save
     *
     * @return mixed
     */
    public function save(array $data = [])
    {
        return $this->getMapper()
            ->save($this, $data);

    }

    /**
     * Retrieves the data mapper for this entity
     */
    public function getMapper()
    {
        return Orm::getMapper(get_class($this));
    }
}