<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Annotations;


class HasMany extends OrmAnnotation
{

    /**
     * @var string
     */
    protected $name = 'hasMany';

    /**
     * @var array
     */
    protected $parameters = [
        'className' => null,
        'foreignKey' => null,
        'limit' => 25,
        'order' => null,
        'conditions' => null
    ];
}