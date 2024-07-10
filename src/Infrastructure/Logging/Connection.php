<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Orm\Infrastructure\Logging;

use Doctrine\DBAL\Driver\Connection as ConnectionInterface;
use Doctrine\DBAL\Driver\Middleware\AbstractConnectionMiddleware;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement as DriverStatement;
use Psr\Log\LoggerInterface;

/**
 * Connection
 *
 * @package Slick\Orm\Infrastructure\Logging
 */
final class Connection extends AbstractConnectionMiddleware
{
    private float $startTransaction = 0.0;

    public function __construct(ConnectionInterface $connection, private readonly LoggerInterface $logger)
    {
        parent::__construct($connection);
    }

    public function __destruct()
    {
        $this->logger->info('Disconnecting');
    }

    public function prepare(string $sql): DriverStatement
    {
        return new Statement(
            parent::prepare($sql),
            $this->logger,
            $sql,
        );
    }

    public function query(string $sql): Result
    {
        $start = microtime(true);
        $result = parent::query($sql);
        $duration = microtime(true) - $start;
        $this->logger->debug('Executing query: {sql}', [
            'sql' => $sql,
            'duration' => $duration
        ]);

        return $result;
    }

    public function exec(string $sql): int|string
    {
        $start = microtime(true);
        $exec = parent::exec($sql);
        $duration = microtime(true) - $start;
        $this->logger->debug('Executing statement: {sql}', ['sql' => $sql, 'duration' => $duration]);

        return $exec;
    }

    public function beginTransaction(): void
    {
        $this->logger->debug('Beginning transaction');
        $this->startTransaction = microtime(true);
        parent::beginTransaction();
    }

    public function commit(): void
    {
        parent::commit();
        $duration = microtime(true) - $this->startTransaction;
        $this->logger->debug('Committing transaction', ['duration' => $duration]);
    }

    public function rollBack(): void
    {
        $this->logger->debug('Rolling back transaction');

        parent::rollBack();
    }
}
