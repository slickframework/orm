<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Exception;

use RuntimeException;
use Slick\Orm\Exception;

/**
 * Entity Not Found Exception
 * 
 * @package Slick\Orm\Exception
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityNotFoundException extends RuntimeException implements Exception
{

}