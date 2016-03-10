<?php
/**
 * Created by PhpStorm.
 * User: fsilva
 * Date: 09-03-2016
 * Time: 18:47
 */

namespace Slick\Orm\Event;


use Slick\Database\RecordList;
use Slick\Orm\Entity\EntityCollection;

class Select extends AbstractEvent implements EventInterface
{

    const ACTION_BEFORE_SELECT = 'before.insert';
    const ACTION_AFTER_SELECT  = 'after.insert';

    /**
     * @var string
     */
    protected $name = 'Select';

    /**
     * @var string
     */
    protected $action = self::ACTION_BEFORE_SELECT;

    /**
     * Gets select query
     *
     * @return \Slick\Database\Sql\Select
     */
    public function getQuery()
    {
        return $this->params['query'];
    }

    /**
     * Gets data returned from query
     *
     * @return RecordList|null
     */
    public function getData()
    {
        return $this->params['data'];
    }

    /**
     * Gets already mapped entity collection
     *
     * @return EntityCollection
     */
    public function getEntityCollection()
    {
        return $this->params['entityCollection'];
    }
}