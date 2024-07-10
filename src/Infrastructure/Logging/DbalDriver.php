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
use Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;
use Psr\Log\LoggerInterface;
use SensitiveParameter;

/**
 * DbalDriver
 *
 * @package Slick\Orm\Infrastructure\Logging
 */
final class DbalDriver extends AbstractDriverMiddleware
{

    public function __construct(DriverInterface $driver, private readonly LoggerInterface $logger)
    {
        parent::__construct($driver);
    }

    /**
     * {@inheritDoc}
     */
    public function connect(
        #[SensitiveParameter]
        array $params,
    ): Connection {
        $this->logger->info('Connecting with parameters {params}', ['params' => $this->maskPassword($params)]);

        return new Connection(
            parent::connect($params),
            $this->logger,
        );
    }

    /**
     * @param array<string,mixed> $params Connection parameters
     *
     * @return array<string,mixed>
     */
    private function maskPassword(
        #[SensitiveParameter]
        array $params,
    ): array {
        if (isset($params['password'])) {
            $params['password'] = '*********';
        }

        return $params;
    }
}
