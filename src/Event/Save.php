<?php
/**
 * Created by PhpStorm.
 * User: filipesilva
 * Date: 03/03/16
 * Time: 00:25
 */

namespace Slick\Orm\Event;


class Save extends AbstractEvent implements EventInterface
{
    const ACTION_BEFORE_INSERT = 'before.insert';
    const ACTION_AFTER_INSERT  = 'after.insert';

    /**
     * @var string
     */
    protected $name = 'Save';

    /**
     * @var string
     */
    protected $action = self::ACTION_BEFORE_INSERT;
}