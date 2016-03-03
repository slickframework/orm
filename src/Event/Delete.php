<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Event;

/**
 * ORM Delete Event
 *
 * @package Slick\Orm\Event
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Delete extends AbstractEvent implements EventInterface
{

    const ACTION_BEFORE_DELETE = 'before.delete';
    const ACTION_AFTER_DELETE  = 'after.delete';

    /**
     * @var string
     */
    protected $name = 'Save';

    /**
     * @var string
     */
    protected $action = self::ACTION_BEFORE_DELETE;
}