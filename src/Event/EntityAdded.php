<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Event;

/**
 * Entity Added: triggered when an entity is added to an entity collection
 * 
 * @package Slick\Orm\Event
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityAdded extends AbstractEvent implements EventInterface
{

    /**
     * @var string
     */
    protected $name = 'Save';

    /**
     * @var string
     */
    protected $action = 'entity.added';
}