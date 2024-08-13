<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Orm\Infrastructure\Persistence\EntityManagerFactory;

use Doctrine\DBAL\Configuration as DbalConfiguration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slick\Di\ContainerInterface;
use Slick\Orm\Infrastructure\Logging\DbalMiddleware;
use Slick\Orm\Infrastructure\Persistence\ManagerSettings;

/**
 * ConnectionFactory
 *
 * @package Slick\Orm\Infrastructure\Persistence\EntityManagerFactory
 */
final readonly class ConnectionFactory
{

    public function __construct(
        private ManagerSettings $managerSettings,
        private ContainerInterface $container
    ) {
    }

    /**
     * Creates a new database connection using the specified manager settings.
     * If a sqlLoggerContainerId is provided in the manager settings, the logger is configured for the connection.
     *
     * @return Connection The newly created database connection.
     * @throws ContainerExceptionInterface If an error occurs while retrieving the logger from the container.
     * @throws NotFoundExceptionInterface If the specified logger is not found in the container.
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function createConnection(): Connection
    {
        $config = new DbalConfiguration();
        $this->configureLogger($this->managerSettings, $config);
        return DriverManager::getConnection($this->managerSettings->connectionSettings(), $config);
    }

    /**
     * Configures the logger for the manager.
     * If a sqlLoggerContainerId is provided in the manager settings, the logger is obtained from the container
     * and added as a middleware to the configuration.
     *
     * @param ManagerSettings $managerSettings The settings for the manager.
     * @param DbalConfiguration $config The configuration for the manager.
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function configureLogger(ManagerSettings $managerSettings, DbalConfiguration $config): void
    {
        if (!$managerSettings->sqlLoggerContainerId()) {
            return;
        }

        $logger = $this->container->get($managerSettings->sqlLoggerContainerId());
        $config->setMiddlewares([new DbalMiddleware($logger)]);
    }
}
