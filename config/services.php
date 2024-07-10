<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Config\Services;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Slick\Di\ContainerInterface;
use Slick\Orm\Infrastructure\Persistence\EntityManagerCollection;
use Slick\Orm\Infrastructure\Persistence\EntityManagerFactory;
use Slick\Orm\Infrastructure\Persistence\ManagerSettings;
use Slick\WebStack\Infrastructure\ApplicationSettingsInterface;

return [
    EntityManagerFactory::class => fn (ContainerInterface $container) => new EntityManagerFactory($container),
    EntityManagerCollection::class => function (ContainerInterface $container) {
        $entityManagers = new EntityManagerCollection();
        $settings = $container->get(ApplicationSettingsInterface::class)->get('databases', []);
        $factory = $container->get(EntityManagerFactory::class);
        foreach ($settings as $name => $values) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $factory->createManager(new ManagerSettings($values), APP_ROOT);
            $entityManagers->add($name, $entityManager);
            $container->register("$name.entity.manager", $entityManager);
            $container->register("$name.db.connection", $entityManager->getConnection());
        }
        $types = $container->get(ApplicationSettingsInterface::class)->get('types', []);
        foreach ($types as $name => $className) {
            Type::addType($name, $className);
        }
        return $entityManagers;
    },
    EntityManagerInterface::class => fn (ContainerInterface $container)
    => $container->get(EntityManagerCollection::class)->get('default'),
    Connection::class => fn (ContainerInterface $container)
    => $container->get(EntityManagerCollection::class)->get('default')->getConnection()
];
