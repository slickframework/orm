<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Annotations;

/**
 * HasOne
 * 
 * @package Slick\Orm\Annotations
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class HasOne extends OrmAnnotation
{

    /**
     * @var string
     */
    protected $name = 'hasOne';

    /**
     * @var array
     */
    protected $parameters = [
        'className' => null,
        'foreignKey' => null,
        'lazyLoaded' => false
    ];
}