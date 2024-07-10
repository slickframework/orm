<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Orm\Infrastructure\Logging;

use Doctrine\DBAL\Driver\Middleware\AbstractStatementMiddleware;
use Doctrine\DBAL\Driver\Result as ResultInterface;
use Doctrine\DBAL\Driver\Statement as StatementInterface;
use Doctrine\DBAL\ParameterType;
use Psr\Log\LoggerInterface;

/**
 * Statement
 *
 * @package Slick\Orm\Infrastructure\Logging
 */
final class Statement extends AbstractStatementMiddleware
{
    /** @var array<int|string, mixed>  */
    private array $params = [];

    /** @var array<int,ParameterType>|array<string,ParameterType> */
    private array $types = [];

    /** @internal This statement can be only instantiated by its connection. */
    public function __construct(
        StatementInterface $statement,
        private readonly LoggerInterface $logger,
        private readonly string $sql,
    ) {
        parent::__construct($statement);
    }

    public function bindValue(int|string $param, mixed $value, ParameterType $type): void
    {
        $this->params[$param] = $value;
        $this->types[$param]  = $type;

        parent::bindValue($param, $value, $type);
    }

    public function execute(): ResultInterface
    {
        $time = microtime(true);
        $result = parent::execute();
        $duration = microtime(true) - $time;
        $this->logger->debug('Executing statement: {sql} (parameters: {params}, types: {types})', [
            'sql'    => $this->sql,
            'params' => $this->params,
            'types'  => $this->types,
            'duration'  => $duration
        ]);

        return $result;
    }
}
