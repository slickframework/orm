<?php
/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Slick\Orm;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Dotenv\Dotenv;
use Features\App\Infrastructure\Persistence\DoctrineEmail;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Server\MiddlewareInterface;
use Slick\Di\Container;
use Slick\Di\ContainerInterface;
use Slick\Orm\Infrastructure\Persistence\EntityManagerCollection;
use Slick\Orm\Infrastructure\Persistence\EntityManagerFactory;
use Slick\Orm\OrmModule;
use Slick\WebStack\Infrastructure\ApplicationSettingsInterface;
use Slick\WebStack\Infrastructure\DependencyContainerFactory;
use Symfony\Component\Console\Application;

class OrmModuleTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function initializable(): void
    {
        $module = new OrmModule();
        $this->assertInstanceOf(OrmModule::class, $module);
    }

    #[Test]
    public function itHasADescription(): void
    {
        $module = new OrmModule();
        $this->assertIsString($module->description());
    }

    #[Test]
    public function itHasSettings(): void
    {
        $module = new OrmModule();
        $this->assertArrayHasKey(
            'url',
            $module->settings($this->prophesize(Dotenv::class)->reveal())['databases']['default']
        );
    }

    #[Test]
    public function itConfiguresConsoleApp(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $entityManager = $this->prophesize(EntityManagerInterface::class)->reveal();
        DependencyContainerFactory::instance()->container()->register(EntityManagerInterface::class, $entityManager);
        $cli = new Application();
        $module = new OrmModule();
        $module->configureConsole($cli, $container);
        $this->assertTrue($cli->has('orm:info'));
    }

    #[Test]
    public function createEntityManagers(): void
    {
        $settings = $this->prophesize(ApplicationSettingsInterface::class);
        $settings->get('databases', [])->willReturn(['default' => []]);
        $settings->get('types', [])->willReturn(['Email' => DoctrineEmail::class]);
        $container = $this->prophesize(Container::class);
        $container->get(ApplicationSettingsInterface::class)->willReturn($settings->reveal());
        $container->get(EntityManagerFactory::class)->willReturn(new EntityManagerFactory($container->reveal()));
        $container->register(
            "default.entity.manager",
            Argument::type(EntityManagerInterface::class)
        )->willReturn($container->reveal());
        $container->register(
            "default.db.connection",
            Argument::type(Connection::class)
        )->willReturn($container->reveal());
        $module = new OrmModule();
        $services = $module->services();
        $collection = $services[EntityManagerCollection::class]($container->reveal());
        $this->assertInstanceOf(EntityManagerCollection::class, $collection);
    }

    #[Test]
    public function connection(): void
    {
        $connection = $this->prophesize(Connection::class)->reveal();
        $container = $this->prophesize(Container::class);
        $entityManager = $this->prophesize(EntityManagerInterface::class);
        $entityManager->getConnection()->willReturn($connection);
        $collection = new EntityManagerCollection();
        $collection->add('default', $entityManager->reveal());
        $container->get(EntityManagerCollection::class)->willReturn($collection);
        $module = new OrmModule();
        $services = $module->services();

        $this->assertSame($connection, $services[Connection::class]($container->reveal()));
    }

    #[Test]
    public function isHasServices(): void
    {
        $module = new OrmModule();
        $this->assertIsArray($module->services());
    }

    #[Test]
    public function hasAMiddleware(): void
    {
        $module = new OrmModule();
        $middlewares = $module->middlewareHandlers();
        $this->assertIsArray($middlewares);
    }
}
