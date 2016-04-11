<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm;

/**
 * ORM Entity Interface
 *
 * @package Slick\Orm
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
interface EntityInterface
{

    /**
     * Returns entity ID
     *
     * This is usually the primary key or a UUID
     *
     * @return mixed
     */
    public function getId();

    /**
     * Sets entity ID
     *
     * @param mixed $entityId Primary key or a UUID
     *
     * @return self|$this|EntityInterface
     */
    public function setId($entityId);

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
    public function save(array $data = []);

    /**
     * Deletes current entity from its storage
     *
     * @return self|$this|EntityInterface
     */
    public function delete();

    /**
     * Returns the entity fields as a key/value associative array
     * 
     * @return array
     */
    public function asArray();
}