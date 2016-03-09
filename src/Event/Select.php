<?php
/**
 * Created by PhpStorm.
 * User: fsilva
 * Date: 09-03-2016
 * Time: 18:47
 */

namespace Slick\Orm\Event;


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
}