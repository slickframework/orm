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
use function PHPUnit\Framework\stringContains;

/**
 * OrmHandler
 *
 * @package Slick\Orm\OrmModule\EventHandlers
 */
final class OrmHandler implements ModuleEventHandler
{

    private static string $defaultSettings = <<<EOS
<?php

/**
 * This file is part of orm module configuration.
 */
 
 use Slick\Orm\Infrastructure\Persistence\ManagerSettings;

return [
    "databases" => [
        "default" => [
            "url" => isset(\$_ENV["DATABASE_URL"]) ? \$_ENV["DATABASE_URL"] : 'pdo-sqlite:///data/database.sqlite',
            "devMode" => getenv("APP_ENV") === "develop",
            'entityPaths' => ["/src/Domain"],
            'implDriver' => ManagerSettings::ATTRIBUTE_DRIVER_IMPL
        ]
    ],
    "types" => [
    ]
];

EOS;

    /**
     * @inheritDoc
     */
    public function onEnable(array $context = []): void
    {
        if (is_file(OrmModule::$ormCnfFile)) {
            return;
        }

        $folders = [OrmModule::$appConfig.'/modules', APP_ROOT .'/data'];

        array_walk($folders, function (string $path) {
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        });

        file_put_contents(OrmModule::$ormCnfFile, self::$defaultSettings);
        $this->verifyEnvironment();
    }

    /**
     * @inheritDoc
     */
    public function onDisable(array $context = []): void
    {
        if (!$context['purge']) {
            return;
        }

        if (is_file(OrmModule::$ormCnfFile)) {
            unlink(OrmModule::$ormCnfFile);
        }
    }


    private function verifyEnvironment(): void
    {
        $file = APP_ROOT . '/.env';
        $append = [
            '# Data base DSN for the default connection.',
            '# This will be used in the config/modules/orm.php settings file.',
            '',
            '# DATABASE_URL=pdo-mysql://user:pass@localhost:3306/database?charset=utf8'
        ];

        if (!file_exists($file)) {
            file_put_contents($file, implode("\n", $append));
            return;
        }

        $content = file_get_contents($file);
        if (is_string($content) && str_contains($content, 'DATABASE_URL=')) {
            return;
        }

        file_put_contents($file, $content . "\n" . implode("\n", $append));
    }
}
