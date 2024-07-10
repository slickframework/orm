<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Orm\Infrastructure\Logging;

use Doctrine\DBAL\Driver as DriverInterface;
use Doctrine\DBAL\Driver\Middleware as MiddlewareInterface;
use Psr\Log\LoggerInterface;

/**
 * DbalMiddleware
 *
 * @package Slick\Orm\Infrastructure\Logging
 */
final class DbalMiddleware implements MiddlewareInterface
{

    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function wrap(DriverInterface $driver): DriverInterface
    {
        return new DbalDriver($driver, $this->logger);
    }
}
