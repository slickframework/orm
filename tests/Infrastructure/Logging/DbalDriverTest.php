<?php
/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Slick\Orm\Infrastructure\Logging;

use Doctrine\DBAL\Driver as DriverInterface;
use PHPUnit\Framework\Attributes\Test;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;
use Slick\Orm\Infrastructure\Logging\Connection;
use Slick\Orm\Infrastructure\Logging\DbalDriver;
use PHPUnit\Framework\TestCase;

class DbalDriverTest extends TestCase
{

    use ProphecyTrait;

    #[Test]
    public function connect(): void
    {
        $params = ['password' => 'foo'];
        $wrappedDriver = $this->prophesize(DriverInterface::class);
        $connection = $this->prophesize(DriverInterface\Connection::class);
        $wrappedDriver->connect($params)->shouldBeCalled()->willReturn($connection->reveal());
        $logger = $this->prophesize(LoggerInterface::class)->reveal();
        $driver = new DbalDriver($wrappedDriver->reveal(), $logger);
        $this->assertInstanceOf(Connection::class, $driver->connect($params));
    }
}
