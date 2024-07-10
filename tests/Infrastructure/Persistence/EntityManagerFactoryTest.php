<?php
/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Slick\Orm\Infrastructure\Persistence;

use Doctrine\DBAL\Logging\Middleware;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Slick\Di\ContainerInterface;
use Slick\Orm\Infrastructure\Logging\DbalMiddleware;
use Slick\Orm\Infrastructure\Persistence\EntityManagerFactory;
use Slick\Orm\Infrastructure\Persistence\ManagerSettings;

class EntityManagerFactoryTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function initializable(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $factory = new EntityManagerFactory($container);
        $this->assertInstanceOf(EntityManagerFactory::class, $factory);
    }

    #[Test]
    public function createEntityManager(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $factory = new EntityManagerFactory($container);
        $this->assertInstanceOf(
            EntityManagerInterface::class,
            $factory->createManager(new ManagerSettings(), __DIR__)
        );
    }

    #[Test]
    public function createEntityManagerXmlDriver(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $factory = new EntityManagerFactory($container);
        $this->assertInstanceOf(
            EntityManagerInterface::class,
            $factory->createManager(new ManagerSettings([
                'implDriver' => ManagerSettings::XML_DRIVER_IMPL
            ]), __DIR__)
        );
    }

    /**
     * Test case for creating an entity manager with cache.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function createEntityManagerWithCache(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $cache = $this->prophesize(CacheItemPoolInterface::class);
        $container->get(CacheItemPoolInterface::class)->willReturn($cache->reveal());
        $factory = new EntityManagerFactory($container->reveal());
        $entityManager = $factory->createManager(new ManagerSettings([
            'cache' => CacheItemPoolInterface::class
        ]), __DIR__);

        $this->assertInstanceOf(EntityManagerInterface::class, $entityManager);
        $this->assertSame($cache->reveal(), $entityManager->getConfiguration()->getMetadataCache());
        $this->assertSame($cache->reveal(), $entityManager->getConfiguration()->getResultCache());
        $this->assertSame($cache->reveal(), $entityManager->getConfiguration()->getQueryCache());
        $this->assertSame($cache->reveal(), $entityManager->getConfiguration()->getHydrationCache());
    }

    #[Test]
    public function createEntityManagerWithLogger(): void
    {
        $logger = $this->prophesize(LoggerInterface::class)->reveal();
        $container = $this->prophesize(ContainerInterface::class);
        $container->get(LoggerInterface::class)->willReturn($logger);

        $factory = new EntityManagerFactory($container->reveal());
        $entityManager = $factory->createManager(new ManagerSettings([
            'SQLLogger' => LoggerInterface::class
        ]), __DIR__);
        $this->assertInstanceOf(EntityManagerInterface::class, $entityManager);

        /** @var Middleware $middlewares */
        $dbalConfig = $entityManager->getConnection()->getConfiguration();
        $configuredMiddlewares = $dbalConfig->getMiddlewares();
        $middleware = reset($configuredMiddlewares);
        $this->assertInstanceOf(DbalMiddleware::class, $middleware);
    }
}
