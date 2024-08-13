<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Orm\OrmModule\EventHandlers;

use Slick\Orm\OrmModule;
use Slick\Orm\OrmModule\ModuleEventHandler;
use Slick\WebStack\Infrastructure\ComposerParser;

/**
 * MigrationsHandler
 *
 * @package Slick\Orm\OrmModule\EventHandlers
 */
final class MigrationsHandler implements ModuleEventHandler
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

    public function __construct(private readonly ComposerParser $composerParser)
    {
    }

    /**
     * @inheritDoc
     */
    public function onEnable(array $context = []): void
    {
        if (is_file(OrmModule::$migrationCnfFile)) {
            return;
        }

        $settings = self::$defaultSettings;
        $namespace = '';
        $namespaces = $this->composerParser->psr4Namespaces();
        if (!empty($namespaces)) {
            $namespace = reset($namespaces);
        }

        $settings['migrations_paths'] = (object) ["{$namespace}Migrations" => '../lib/Migrations'];
        if (!is_dir(OrmModule::$appConfig)) {
            mkdir(OrmModule::$appConfig, 0755, true);
        }

        if (!is_dir(APP_ROOT . '/lib/Migrations')) {
            mkdir(APP_ROOT . '/lib/Migrations', 0755, true);
        }

        file_put_contents(OrmModule::$migrationCnfFile, json_encode($settings, JSON_PRETTY_PRINT));
    }

    /**
     * @inheritDoc
     */
    public function onDisable(array $context = []): void
    {
        if (!$context['purge']) {
            return;
        }

        if (is_file(OrmModule::$migrationCnfFile)) {
            unlink(OrmModule::$migrationCnfFile);
        }
    }
}
