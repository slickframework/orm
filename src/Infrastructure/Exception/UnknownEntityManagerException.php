<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Orm\Infrastructure\Exception;

use RuntimeException;
use Slick\Orm\OrmExceptionInterface;

/**
 * UnknownEntityManagerException
 *
 * @package Slick\Orm\Infrastructure\Exception
 */
final class UnknownEntityManagerException extends RuntimeException implements OrmExceptionInterface
{

}
