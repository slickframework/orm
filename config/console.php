<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Config\Services;

use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\JsonFile;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Slick\WebStack\Infrastructure\DependencyContainerFactory;
use Symfony\Component\Console\Application;
use Doctrine\Migrations\Tools\Console\Command;

return function (Application $cli, string $migrationSettingsFile) {
    $config = new JsonFile($migrationSettingsFile);
    $container = DependencyContainerFactory::instance()->container();
    $entityManager = $container->get(EntityManagerInterface::class);
    $dependencyFactory = DependencyFactory::fromEntityManager($config, new ExistingEntityManager($entityManager));
    ConsoleRunner::addCommands($cli, new SingleManagerProvider($entityManager));

    $cli->addCommands(array(
        new Command\DumpSchemaCommand($dependencyFactory),
        new Command\ExecuteCommand($dependencyFactory),
        new Command\GenerateCommand($dependencyFactory),
        new Command\LatestCommand($dependencyFactory),
        new Command\ListCommand($dependencyFactory),
        new Command\MigrateCommand($dependencyFactory),
        new Command\RollupCommand($dependencyFactory),
        new Command\StatusCommand($dependencyFactory),
        new Command\SyncMetadataCommand($dependencyFactory),
        new Command\VersionCommand($dependencyFactory),
        new Command\DiffCommand($dependencyFactory),
    ));
};
