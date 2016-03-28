<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Annotations;

/**
 * Class HasAndBelongsToMany
 * 
 * @package Slick\Orm\Annotations
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class HasAndBelongsToMany extends OrmAnnotation
{

    /**
     * @var string
     */
    protected $name = 'hasAndBelongsToMany';

    /**
     * @var array
     */
    protected $parameters = [
        'className' => null,
        'foreignKey' => null,
        'relatedForeignKey' => null,
        'relationTable' => null,
        'limit' => 25,
        'order' => null,
        'conditions' => null
    ];
}