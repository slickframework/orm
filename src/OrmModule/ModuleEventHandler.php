<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Orm\OrmModule;

/**
 * ModuleEventHandler
 *
 * @package Slick\Orm\OrmModule
 */
interface ModuleEventHandler
{

    /**
     * Called whenever the module is enabled
     *
     * @param array<string, mixed> $context
     * @return void
     */
    public function onEnable(array $context = []): void;

    /**
     * Handle the event when the module is disabled
     *
     * @param array<string, mixed> $context
     * @return void
     */
    public function onDisable(array $context = []): void;
}
