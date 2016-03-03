<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm;

use League\Event\EmitterInterface;
use League\Event\ListenerInterface;
use Slick\Common\Base;

/**
 * Entity
 *
 * @package Slick\Orm
 * @readwrite
 */
abstract class Entity extends Base implements EntityInterface
{

    /**
     * Saves current entity state
     *
     * Optionally saves only the partial data if $data argument is passed. If
     * no data is given al the field properties will be updated.
     *
     * @param array $data Partial data to save
     *
     * @return mixed
     */
    public function save(array $data = [])
    {
        return $this->getMapper()
            ->save($this, $data);

    }

    /**
     * Deletes current entity from its storage
     *
     * @return self|$this|EntityInterface
     */
    public function delete()
    {
        return $this->getMapper()
            ->delete($this);
    }

    /**
     * Retrieves the data mapper for this entity
     */
    public function getMapper()
    {
        return Orm::getMapper(get_class($this));
    }

    /**
     * Add a listener for an event.
     *
     * The first parameter should be the event name, and the second should be
     * the event listener. It may implement the League\Event\ListenerInterface
     * or simply be "callable". In this case, the priority emitter also accepts
     * an optional third parameter specifying the priority as an integer. You
     * may use one of EmitterInterface predefined constants here if you want.
     *
     * @param string                     $event
     * @param ListenerInterface|callable $listener
     * @param int                        $priority
     *
     * @return EmitterInterface
     */
    public function addListener(
        $event, $listener, $priority = EmitterInterface::P_NORMAL
    ) {
        return Orm::addListener($this, $event, $listener, $priority);
    }

}