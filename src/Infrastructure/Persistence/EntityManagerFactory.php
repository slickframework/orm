<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Orm\Infrastructure\Persistence;

use Doctrine\DBAL\Schema\Sequence;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slick\Di\ContainerInterface;
use Slick\Orm\Infrastructure\Persistence\EntityManagerFactory\ConnectionFactory;

/**
 * EntityManagerFactory
 *
 * @package Slick\Orm\Infrastructure\Persistence
 */
final readonly class EntityManagerFactory
{

    /**
     * Creates an EntityManagerFactory
     *
     * @param ContainerInterface $container
     */
    public function __construct(private ContainerInterface $container)
    {
    }

    /**
     * Creates a new EntityManager instance.
     *
     * @param ManagerSettings $managerSettings The manager settings.
     * @param string $appRoot The root directory of the application.
     *
     * @return EntityManagerInterface The created EntityManager instance.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function createManager(ManagerSettings $managerSettings, string $appRoot): EntityManagerInterface
    {
        $connectionFactory = new ConnectionFactory($managerSettings, $this->container);
        // SQLLogger
        $connection = $connectionFactory->createConnection();
        if ($managerSettings->filterSchemaAssetsExpression()) {
            $connection->getConfiguration()->setSchemaAssetsFilter(
                function ($asset) use ($managerSettings) {
                    // Extract the name if it's a Sequence object
                    $assetName = $asset instanceof Sequence ? $asset->getName() : $asset;

                    // Apply the regex filter
                    return !preg_match($managerSettings->filterSchemaAssetsExpression(), $assetName);
                }
            );
        }
        return new EntityManager(
            $connection,
            $this->configuration($managerSettings, $appRoot)
        );
    }

    /**
     * Creates and configures a Doctrine Configuration object.
     *
     * @param ManagerSettings $managerSettings The manager settings object.
     * @param string $appRoot
     * @return Configuration The configured Doctrine Configuration object.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function configuration(ManagerSettings $managerSettings, string $appRoot): Configuration
    {
        $config = new Configuration();
        $config->setMetadataDriverImpl($this->driver($managerSettings, $appRoot));

        // proxies
        $config->setProxyDir($this->fixPath($managerSettings->proxiesDir(), $appRoot));
        $config->setProxyNamespace($managerSettings->proxiesNamespace());
        if ($managerSettings->generateProxyClasses()) {
            // @phpstan-ignore argument.type
            $config->setAutoGenerateProxyClasses($managerSettings->proxyGenerationMode());
        }
        // cache
        $this->configureCache($managerSettings, $config);

        return $config;
    }

    /**
     * Returns an instance of MappingDriver based on the given ManagerSettings.
     *
     * @param ManagerSettings $managerSettings The manager settings to determine the driver implementation.
     *
     * @return MappingDriver An instance of the appropriate MappingDriver implementation.
     */
    private function driver(ManagerSettings $managerSettings, string $appRoot): MappingDriver
    {
        if ($managerSettings->implDriver() === ManagerSettings::ATTRIBUTE_DRIVER_IMPL) {
            return new AttributeDriver($this->fixPaths($managerSettings->entityPaths(), $appRoot));
        }

        return new XmlDriver($this->fixPaths($managerSettings->entityPaths(), $appRoot));
    }


    /**
     * Fixes the paths by appending the root directory to each path.
     *
     * @param array<string> $paths An array of paths to be fixed.
     * @param string $root The root directory to be appended to each path.
     *
     * @return array<string> An array of fixed paths.
     */
    public function fixPaths(array $paths, string $root): array
    {
        return array_map(function ($path) use ($root) {
            return $this->fixPath($path, $root);
        }, $paths);
    }

    /**
     * Fixes the path by replacing double slashes with a single slash and concatenating it with the root path.
     *
     * @param string $path The path to be fixed.
     * @param string $root The root path to be concatenated with the fixed path.
     *
     * @return string The fixed path concatenated with the root path.
     */
    private function fixPath(string $path, string $root): string
    {
        return str_replace('//', '/', "$root/$path");
    }

    /**
     * Configures the cache handlers for a given manager settings and configuration object.
     *
     * If the cache container ID is not set in the manager settings, no cache configuration will be performed.
     * Otherwise, the cache handler specified by the cache container ID will be retrieved from the container
     * and set as the hydration, metadata, query, and result cache in the configuration object.
     *
     * @param ManagerSettings $managerSettings The manager settings object.
     * @param Configuration $config The configuration object to configure the cache handlers.
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function configureCache(ManagerSettings $managerSettings, Configuration $config): void
    {
        if (!$managerSettings->cacheContainerId()) {
            return;
        }

        $cacheHandler = $this->container->get($managerSettings->cacheContainerId());
        $config->setHydrationCache($cacheHandler);
        $config->setMetadataCache($cacheHandler);
        $config->setQueryCache($cacheHandler);
        $config->setResultCache($cacheHandler);
    }
}
