<?php
/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Slick\Orm\Infrastructure\Http;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slick\Orm\Infrastructure\Http\EntityManagerFlushMiddleware;
use PHPUnit\Framework\TestCase;
use Slick\Orm\Infrastructure\Persistence\EntityManagerCollection;

class EntityManagerFlushMiddlewareTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function initializable(): void
    {
        $entityManager = $this->prophesize(EntityManagerInterface::class);
        $collection = new EntityManagerCollection();
        $collection->add('default', $entityManager->reveal());
        $middleware = new EntityManagerFlushMiddleware($collection);
        $this->assertInstanceOf(EntityManagerFlushMiddleware::class, $middleware);
    }

    #[Test]
    public function process(): void
    {
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $response = $this->prophesize(ResponseInterface::class)->reveal();
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle($request)->willReturn($response);

        $entityManager = $this->prophesize(EntityManagerInterface::class);
        $entityManager->flush()->shouldBeCalled();
        $collection = new EntityManagerCollection();
        $collection->add('default', $entityManager->reveal());

        $middleware = new EntityManagerFlushMiddleware($collection);

        $this->assertSame($response, $middleware->process($request, $handler->reveal()));
    }
}
