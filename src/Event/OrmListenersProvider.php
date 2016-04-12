<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Event;

use League\Event\ListenerAcceptorInterface;
use League\Event\ListenerInterface;
use League\Event\ListenerProviderInterface;
use Slick\Common\Utils\Collection\AbstractCollection;
use Slick\Common\Utils\Collection\AbstractMap;

/**
 * General ORM Listeners Provider
 * 
 * @package Slick\Orm\Event
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class OrmListenersProvider extends AbstractMap implements
    ListenerProviderInterface
{

    /**
     * @param string $event
     * @param ListenerInterface|callable $listener
     */
    public function addListener($event, $listener)
    {
        $this->set($event, $listener);
    }


    /**
     * Provide event
     *
     * @param ListenerAcceptorInterface $listenerAcceptor
     *
     * @return $this
     */
    public function provideListeners(ListenerAcceptorInterface $listenerAcceptor)
    {
        foreach ($this->getIterator() as $event => $listener) {
            $listenerAcceptor->addListener($event, $listener);
        }
    }
}