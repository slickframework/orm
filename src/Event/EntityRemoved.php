<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Event;

use Slick\Orm\Entity\EntityCollectionInterface;

/**
 * Entity Removed event
 * 
 * @package Slick\Orm\Event
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityRemoved extends AbstractEvent implements EntityChangeEventInterface
{

    const ACTION_REMOVE = 'entity.removed';

    /**
     * @var string
     */
    protected $name = 'EntityRemoved';

    /**
     * @var string
     */
    protected $action = self::ACTION_REMOVE;

    /**
     * Gets the entity collection that triggers the event
     *
     * @return EntityCollectionInterface
     */
    public function getCollection()
    {
        return $this->params['collection'];
    }
}