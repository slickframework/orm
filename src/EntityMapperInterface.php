<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm;
use Slick\Database\Adapter\AdapterAwareInterface;
use Slick\Database\Adapter\AdapterInterface;

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
     * @param array $data Partial data to save
     * @param EntityInterface $entity
     *
     * @return self|$this|EntityMapperInterface
     */
    public function save(EntityInterface $entity, array $data = []);

}