<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Event;

/**
 * ORM Save event
 *
 * @package Slick\Orm\Event
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Save extends AbstractEvent implements EventInterface
{
    const ACTION_BEFORE_INSERT = 'before.insert';
    const ACTION_AFTER_INSERT  = 'after.insert';
    const ACTION_BEFORE_UPDATE = 'before.update';
    const ACTION_AFTER_UPDATE  = 'after.update';

    /**
     * @var string
     */
    protected $name = 'Save';

    /**
     * @var string
     */
    protected $action = self::ACTION_BEFORE_INSERT;
}