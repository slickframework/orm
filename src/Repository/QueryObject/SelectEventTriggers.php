<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Repository\QueryObject;

use Slick\Database\RecordList;
use Slick\Database\Sql\Select;
use Slick\Orm\Descriptor\EntityDescriptorInterface;
use Slick\Orm\Entity\EntityCollection;
use Slick\Orm\Mapper\EventTriggers;
use Slick\Orm\Orm;

/**
 * Select Event Trigger methods
 *
 * @package Slick\Orm\Repository\QueryObject
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
trait SelectEventTriggers
{

    /**
     * Use event triggers
     */
    use EventTriggers;

    /**
     * Emit before select event
     *
     * @param Select $query
     * @param EntityDescriptorInterface $entityDescriptor
     *
     * @return \League\Event\EventInterface|string
     */
    public function triggerBeforeSelect(
        Select $query, EntityDescriptorInterface $entityDescriptor
    ) {
        $event = new \Slick\Orm\Event\Select(
            null,
            [
                'query' => $query,
                'entityDescriptor' => $entityDescriptor
            ]
        );
        $event->setAction(\Slick\Orm\Event\Select::ACTION_BEFORE_SELECT);
        return Orm::getEmitter($this->getEntityClassName())
            ->emit($event);
    }

    /**
     * Emits the after select event
     *
     * @param RecordList $data
     * @param EntityCollection $entities
     *
     * @return \League\Event\EventInterface|string
     */
    public function triggerAfterSelect($data, EntityCollection $entities)
    {
        $event = new \Slick\Orm\Event\Select(
            null,
            [
                'data' => $data,
                'entityCollection' => $entities
            ]
        );
        $event->setAction(\Slick\Orm\Event\Select::ACTION_AFTER_SELECT);
        return Orm::getEmitter($this->getEntityClassName())
            ->emit($event);
    }


}