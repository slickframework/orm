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
use Slick\ModuleApi\Infrastructure\AbstractModule;
use Slick\ModuleApi\Infrastructure\Console\ConsoleModuleInterface;
use Slick\ModuleApi\Infrastructure\FrontController\MiddlewareHandler;
use Slick\ModuleApi\Infrastructure\FrontController\MiddlewareHandlerInterface;
use Slick\ModuleApi\Infrastructure\FrontController\MiddlewarePosition;
use Slick\ModuleApi\Infrastructure\FrontController\Position;
use Slick\ModuleApi\Infrastructure\FrontController\WebModuleInterface;
use Slick\Orm\Infrastructure\Http\EntityManagerFlushMiddleware;
use Slick\WebStack\Infrastructure\ComposerParser;
use Symfony\Component\Console\Application;
use function Slick\WebStack\importSettingsFile;

/**
 * OrmModule
 *
 * @package Slick\Orm
 *
 */
final class OrmModule extends AbstractModule implements ConsoleModuleInterface, WebModuleInterface
{

    /** @var array<string, mixed>  */
    private static array $defaultSettings = [
        'table_storage' => [
            'table_name' => 'doctrine_migration_versions',
            'version_column_name' => 'version',
            'version_column_length' => 192,
            'executed_at_column_name' => 'executed_at',
            'execution_time_column_name' => 'execution_time',
        ],

        'migrations_paths' => null,

        'all_or_nothing' => true,
        'transactional' => true,
        'check_database_platform' => true,
        'organize_migrations' => 'none',
        'connection' => null,
        'em' => null,
    ];

    private static string $appConfig = APP_ROOT . '/config';
    private static string $migrationCnfFile = APP_ROOT . '/config/migrations.json';

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
        return "This module offers Migrations, Database Abstraction (DBA), and Object-Relational Mapping (ORM) ".
            "features utilizing the doctrine/migrations and doctrine/orm packages.";
    }

    /**
     * @inheritDoc
     */
    public function onEnable(array $context = []): void
    {
        if (is_file(self::$migrationCnfFile)) {
            return;
        }

        $settings = self::$defaultSettings;
        $namespace = '';
        $namespaces = $this->composerParser->psr4Namespaces();
        if (!empty($namespaces)) {
            $namespace = reset($namespaces);
        }

        $settings['migrations_paths'] = (object) ["{$namespace}Migrations" => '../lib/Migrations'];
        if (!is_dir(self::$appConfig)) {
            mkdir(self::$appConfig, 0755, true);
        }

        if (!is_dir(APP_ROOT . '/lib/Migrations')) {
            mkdir(APP_ROOT . '/lib/Migrations', 0755, true);
        }

        file_put_contents(self::$migrationCnfFile, json_encode($settings, JSON_PRETTY_PRINT));
    }

    /**
     * @inheritDoc
     */
    public function onDisable(array $context = []): void
    {
        if (!$context['purge']) {
            return;
        }

        if (is_file(self::$migrationCnfFile)) {
            unlink(self::$migrationCnfFile);
        }
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
    public function configureConsole(Application $cli): void
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
