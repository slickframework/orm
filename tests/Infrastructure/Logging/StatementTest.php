<?php
/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Slick\Orm\Infrastructure\Logging;

use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement as DbalStatement;
use Doctrine\DBAL\ParameterType;
use PHPUnit\Framework\Attributes\Test;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;
use Slick\Orm\Infrastructure\Logging\Statement;
use PHPUnit\Framework\TestCase;

class StatementTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function bindValue(): void
    {
        $wrappedStatement = $this->prophesize(DbalStatement::class);
        $logger = $this->prophesize(LoggerInterface::class)->reveal();
        $statement = new Statement($wrappedStatement->reveal(), $logger, 'Select...');
        $type = ParameterType::STRING;
        $statement->bindValue('foo', 'bar', $type);
        $wrappedStatement->bindValue('foo', 'bar', $type)->shouldHaveBeenCalled();
    }

    #[Test]
    public function execute(): void
    {
        $result = $this->prophesize(Result::class);
        $wrappedStatement = $this->prophesize(DbalStatement::class);
        $wrappedStatement->execute()->willReturn($result->reveal())->shouldBeCalled();
        $logger = $this->prophesize(LoggerInterface::class)->reveal();
        $statement = new Statement($wrappedStatement->reveal(), $logger, 'Select...');
        $this->assertSame($result->reveal(), $statement->execute());
    }
}
