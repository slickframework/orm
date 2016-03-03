<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Orm;

use League\Event\AbstractListener;
use League\Event\EventInterface;
use League\Event\ListenerInterface;
use Slick\Orm\Event\Save;

/**
 * Simple Listener
 *
 * @package Orm
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Listener extends AbstractListener implements ListenerInterface
{

    /**
     * @var Save
     */
    public $event;

    /**
     * Handle an event.
     *
     * @param EventInterface $event
     *
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $this->event = $event;
    }
}