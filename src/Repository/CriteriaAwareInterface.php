<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Repository;

/**
 * CriteriaAware Interface
 *
 * @package Slick\Orm\Repository
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
interface CriteriaAwareInterface
{

    /**
     * Sets query condition for this query object
     *
     * @param string $condition
     * @return self|$this
     */
    public function where($condition);

    /**
     * Sets query condition for query object
     *
     * Ann alias of self::where() method
     *
     * @param string $condition
     * @return self|$this
     */
    public function andWhere($condition);

    /**
     * Sets SQL where condition for this statement
     *
     * @param string $condition
     * @return self|$this
     */
    public function orWhere($condition);

    /**
     * Returns the criteria statement for query
     *
     * @return null|string
     */
    public function getWhereStatement();

    /**
     * Returns the parameters to be bound to criteria
     *
     * @return array
     */
    public function getParameters();
}