<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Orm;

use Dotenv\Dotenv;
use JsonException;
use Slick\Di\ContainerInterface;
use Slick\ModuleApi\Infrastructure\AbstractModule;
use Slick\ModuleApi\Infrastructure\Console\ConsoleModuleInterface;
use Slick\ModuleApi\Infrastructure\FrontController\MiddlewareHandler;
use Slick\ModuleApi\Infrastructure\FrontController\MiddlewareHandlerInterface;
use Slick\ModuleApi\Infrastructure\FrontController\MiddlewarePosition;
use Slick\ModuleApi\Infrastructure\FrontController\Position;
use Slick\ModuleApi\Infrastructure\FrontController\WebModuleInterface;
use Slick\Orm\Infrastructure\Http\EntityManagerFlushMiddleware;
use Slick\Orm\OrmModule\ModuleEventHandling;
use Slick\WebStack\Infrastructure\ComposerParser;
use Symfony\Component\Console\Application;
use function Slick\ModuleApi\importSettingsFile;

/**
 * OrmModule
 *
 * @package Slick\Orm
 *
 */
final class OrmModule extends AbstractModule implements ConsoleModuleInterface, WebModuleInterface
{
    use ModuleEventHandling;

    public static string $appConfig = APP_ROOT . '/config';
    public static string $migrationCnfFile = APP_ROOT . '/config/migrations.json';
    public static string $ormCnfFile = APP_ROOT . '/config/modules/orm.php';

    private ComposerParser $composerParser;

    /**
     * @throws JsonException
     */
    public function __construct()
    {
        $this->composerParser = new ComposerParser(APP_ROOT . "/composer.json");
    }

    public function description(): ?string
    {
        return "This module offers Migrations, Database Abstraction (DBA), and Object-Relational Mapping (ORM)";
    }

    /**
     * @inheritdoc
     */
    public function settings(Dotenv $dotenv): array
    {
        $settingsFile = APP_ROOT .'/config/modules/orm.php';
        $defaultSettings = [
            'databases' => [
                'default' => [
                    'url' => 'pdo-sqlite:///:memory:'
                ]
            ]
        ];
        return importSettingsFile($settingsFile, $defaultSettings);
    }

    public function services(): array
    {
        return importSettingsFile(dirname(__DIR__) . '/config/services.php');
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function configureConsole(Application $cli, ContainerInterface $container): void
    {
        $configure = require dirname(__DIR__) . '/config/console.php';
        $configure($cli, self::$migrationCnfFile);
    }


    /**
     * Retrieve the middleware handlers for the application.
     *
     * @return array<MiddlewareHandlerInterface> The middleware handlers.
     */
    public function middlewareHandlers(): array
    {
        return [
            new MiddlewareHandler(
                'orm-flush',
                new MiddlewarePosition(Position::Top),
                EntityManagerFlushMiddleware::class
            )
        ];
    }
}
