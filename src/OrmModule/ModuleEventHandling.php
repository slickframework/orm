<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Orm\OrmModule;

use Slick\Orm\OrmModule\EventHandlers\MigrationsHandler;
use Slick\Orm\OrmModule\EventHandlers\OrmHandler;

/**
 * ModuleEventHandling
 *
 * @package Slick\Orm\OrmModule
 */
trait ModuleEventHandling
{

    /**
     * @inheritDoc
     */
    public function onEnable(array $context = []): void
    {
        $migrationsHandler = new MigrationsHandler($this->composerParser);
        $migrationsHandler->onEnable($context);

        $ormHandler = new OrmHandler();
        $ormHandler->onEnable($context);
    }

    /**
     * @inheritDoc
     */
    public function onDisable(array $context = []): void
    {
        $migrationsHandler = new MigrationsHandler($this->composerParser);
        $migrationsHandler->onDisable($context);

        $ormHandler = new OrmHandler();
        $ormHandler->onDisable($context);
    }
}
