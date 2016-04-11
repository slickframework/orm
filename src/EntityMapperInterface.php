<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm;

use Slick\Database\Adapter\AdapterAwareInterface;
use Slick\Database\RecordList;
use Slick\Orm\Entity\EntityCollection;

/**
 * Class EntityMapperInterface
 *
 * @package Slick\Orm
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
interface EntityMapperInterface extends AdapterAwareInterface
{

    /**
     * Saves current entity object to database
     *
     * Optionally saves only the partial data if $data argument is passed. If
     * no data is given al the field properties will be updated.
     *
     * @param EntityInterface $entity
     *
     * @return self|$this|EntityMapperInterface
     */
    public function save(EntityInterface $entity);

    /**
     * Deletes current entity from database
     *
     * @param EntityInterface $entity
     *
     * @return self|$this|EntityInterface
     */
    public function delete(EntityInterface $entity);

    /**
     * Creates an entity object from provided data
     *
     * Data can be an array with single row fields or a RecordList from
     * a query.
     *
     * @param array|RecordList $data
     *
     * @return EntityInterface|EntityMapperInterface[]|EntityCollection
     */
    public function createFrom($data);

    /**
     * Sets entity class name
     *
     * @param $entityClass
     *
     * @return self|$this|EntityMapperInterface
     */
    public function setEntityClassName($entityClass);

}