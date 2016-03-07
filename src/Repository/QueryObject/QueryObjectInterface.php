<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Repository\QueryObject;

use Slick\Database\Sql\ConditionsAwareInterface;
use Slick\Orm\Entity\EntityCollection;
use Slick\Orm\EntityInterface;
use Slick\Orm\RepositoryInterface;

/**
 * QueryObject Interface
 *
 * @package Slick\Orm\Repository
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
interface QueryObjectInterface extends
    ConditionsAwareInterface,
    \Countable,
    CriteriaAwareInterface
{

    /**
     * Retrieve all object matching current criteria
     *
     * @return EntityCollection
     */
    public function all();

    /**
     * Retrieve first object matching current criteria
     *
     * @return EntityInterface|null
     */
    public function first();

    /**
     * Returns the repository that is using this query object
     *
     * @return RepositoryInterface
     */
    public function getRepository();
}