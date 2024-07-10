<?php
/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Slick\Orm\Infrastructure\Logging;

use Doctrine\DBAL\Driver\Connection as ConnectionInterface;
use Doctrine\DBAL\Driver\Middleware\AbstractStatementMiddleware;
use Doctrine\DBAL\Driver\Result;
use PHPUnit\Framework\Attributes\Test;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;
use Slick\Orm\Infrastructure\Logging\Connection;
use PHPUnit\Framework\TestCase;
use Slick\Orm\Infrastructure\Logging\Statement;

class ConnectionTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function initialize(): void
    {
        $wrappedConnection = $this->prophesize(ConnectionInterface::class)->reveal();
        $logger = $this->prophesize(LoggerInterface::class)->reveal();
        $connection = new Connection($wrappedConnection, $logger);
        $this->assertInstanceOf(Connection::class, $connection);
    }

    #[Test]
    public function prepare(): void
    {
        $sql = 'Select * from users';
        $statement = $this->prophesize(AbstractStatementMiddleware::class)->reveal();
        $wrappedConnection = $this->prophesize(ConnectionInterface::class);
        $wrappedConnection->prepare($sql)->willReturn($statement);
        $logger = $this->prophesize(LoggerInterface::class)->reveal();
        $connection = new Connection($wrappedConnection->reveal(), $logger);
        $this->assertInstanceOf(Statement::class, $connection->prepare($sql));
    }

    #[Test]
    public function query(): void
    {
        $sql = 'Select * from users';
        $result = $this->prophesize(Result::class)->reveal();
        $wrappedConnection = $this->prophesize(ConnectionInterface::class);
        $wrappedConnection->query($sql)->willReturn($result);
        $logger = $this->prophesize(LoggerInterface::class)->reveal();
        $connection = new Connection($wrappedConnection->reveal(), $logger);
        $this->assertSame($result, $connection->query($sql));
    }

    #[Test]
    public function execute(): void
    {
        $sql = 'Select * from users';
        $wrappedConnection = $this->prophesize(ConnectionInterface::class);
        $wrappedConnection->exec($sql)->willReturn(1);
        $logger = $this->prophesize(LoggerInterface::class)->reveal();
        $connection = new Connection($wrappedConnection->reveal(), $logger);
        $this->assertEquals(1, $connection->exec($sql));
    }

    #[Test]
    public function beginTransaction(): void
    {
        $wrappedConnection = $this->prophesize(ConnectionInterface::class);
        $logger = $this->prophesize(LoggerInterface::class)->reveal();
        $connection = new Connection($wrappedConnection->reveal(), $logger);
        $connection->beginTransaction();
        $wrappedConnection->beginTransaction()->shouldHaveBeenCalled();
    }

    #[Test]
    public function commit(): void
    {
        $wrappedConnection = $this->prophesize(ConnectionInterface::class);
        $logger = $this->prophesize(LoggerInterface::class)->reveal();
        $connection = new Connection($wrappedConnection->reveal(), $logger);
        $connection->commit();
        $wrappedConnection->commit()->shouldHaveBeenCalled();
    }

    #[Test]
    public function rollback(): void
    {
        $wrappedConnection = $this->prophesize(ConnectionInterface::class);
        $logger = $this->prophesize(LoggerInterface::class)->reveal();
        $connection = new Connection($wrappedConnection->reveal(), $logger);
        $connection->rollBack();
        $wrappedConnection->rollBack()->shouldHaveBeenCalled();
    }
}
