<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Annotations;

/**
 * BelongsTo annotation (many-to-one)
 *
 * @package Slick\Orm\Annotations
 * @author  Filipe Silva <silam.filipe@gmail.com>
 */
class BelongsTo extends OrmAnnotation
{

    /**
     * @var string
     */
    protected $name = 'belongsTo';

    /**
     * @var array
     */
    protected $parameters = [
        'className' => null,
        'foreignKey' => null,
        'lazyLoaded' => false,
        'dependent' => true
    ];

}