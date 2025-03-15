<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Orm\Infrastructure\Persistence;

use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\ORM\Proxy\ProxyFactory;

/**
 * ManagerSettings
 *
 * @package Slick\Orm\Infrastructure\Persistence
 */
final class ManagerSettings
{

    public const ATTRIBUTE_DRIVER_IMPL = 'attribute';
    public const XML_DRIVER_IMPL = 'xml';
    private const AUTOGENERATE_DEV = 99;

    /** @var array<string, mixed> */
    private static array $defaultSettings = [
        'url' => 'sqlite3:///:memory:?cache=shared',
        'entityPaths' => ["/src/Domain"],
        'proxiesDir' => "/tmp/Proxies",
        'proxiesNamespace' => 'App\Persistence\Proxies',
        'devMode' => true,
        'cache' => null,
        'SQLLogger' => null,
        'autoGenerateProxiesMode' => self::AUTOGENERATE_DEV,
        'implDriver' => self::ATTRIBUTE_DRIVER_IMPL,
        'autoGenerateProxyClasses' => true,
        'filterSchemaAssetsExpression' => null
    ];

    /** @var array<string, mixed>  */
    private array $settings;

    /**
     * @var array<string, mixed>
     */
    private array $connectionSettings;

    private string $proxiesDir;

    private string $proxiesNamespace;

    private bool $inDevMode;

    private ?string $cacheContainerId;

    private ?string $sqlLoggerContainerId;

    private int $generateProxiesMode;

    private string $implDriver;

    private bool $generateProxyClasses;

    private ?string $filterSchemaAssetsExpression;

    /**
     * Constructor for the class.
     *
     * @param array<string, mixed> $settings An array of settings.
     */
    public function __construct(array $settings = [])
    {
        $this->settings = [...self::$defaultSettings, ...$settings];
        $dsnParser = new DsnParser();
        $this->connectionSettings = $dsnParser->parse($this->settings['url']);
        $this->proxiesDir = $this->settings['proxiesDir'];
        $this->proxiesNamespace = $this->settings['proxiesNamespace'];
        $this->inDevMode = (bool) $this->settings['devMode'];
        $this->cacheContainerId = $this->settings['cache'];
        $this->sqlLoggerContainerId = $this->settings['SQLLogger'];
        $this->setProxyGenerationMode();

        $this->implDriver = $this->settings['implDriver'];
        $this->filterSchemaAssetsExpression = $this->settings['filterSchemaAssetsExpression'];
        $this->generateProxyClasses = (bool) $this->settings['autoGenerateProxyClasses'];
    }

    /**
     * Get the entity paths to search entities for.
     *
     * @return array<string> An array of entity paths.
     */
    public function entityPaths(): array
    {
        return $this->settings['entityPaths'];
    }

    /**
     * Returns the database connection settings.
     *
     * @return array<string, mixed> The connection settings.
     */
    public function connectionSettings(): array
    {
        return $this->connectionSettings;
    }

    /**
     * Get the directory for storing proxy classes.
     *
     * @return string The directory path for proxy classes.
     */
    public function proxiesDir(): string
    {
        return $this->proxiesDir;
    }

    /**
     * Retrieves the proxies namespace.
     *
     * @return string The proxies' namespace.
     */
    public function proxiesNamespace(): string
    {
        return $this->proxiesNamespace;
    }

    /**
     * Check if the application is in development mode.
     *
     * @return bool Returns true if the application is in development mode, false otherwise.
     */
    public function isInDevMode(): bool
    {
        return $this->inDevMode;
    }

    /**
     * Returns the cache container ID.
     *
     * @return string|null The cache container ID.
     */
    public function cacheContainerId(): ?string
    {
        return $this->cacheContainerId;
    }

    /**
     * Returns the SQL Logger container ID.
     *
     * @return string|null The SQL Logger container ID.
     */
    public function sqlLoggerContainerId(): ?string
    {
        return $this->sqlLoggerContainerId;
    }

    /**
     * Returns the auto-generate proxies mode.
     *
     * @return bool|int The auto-generate proxies mode.
     */
    public function proxyGenerationMode(): bool|int
    {
        return $this->generateProxiesMode;
    }

    /**
     * Returns the implementation driver.
     *
     * @return string The implementation driver.
     */
    public function implDriver(): string
    {
        return $this->implDriver;
    }

    /**
     * Returns the flag indicating whether to auto generate proxy classes.
     *
     * @return bool The flag indicating whether to auto generate proxy classes.
     */
    public function generateProxyClasses(): bool
    {
        return $this->generateProxyClasses;
    }

    public function filterSchemaAssetsExpression(): ?string
    {
        return $this->filterSchemaAssetsExpression;
    }

    /**
     * Sets the proxy generation mode based on the application settings.
     */
    private function setProxyGenerationMode(): void
    {
        $this->generateProxiesMode = $this->settings['autoGenerateProxiesMode'];
        if ($this->settings['autoGenerateProxiesMode'] === self::AUTOGENERATE_DEV) {
            $this->generateProxiesMode = $this->inDevMode
                ? ProxyFactory::AUTOGENERATE_EVAL
                : ProxyFactory::AUTOGENERATE_FILE_NOT_EXISTS;
        }
    }
}
