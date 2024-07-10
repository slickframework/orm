<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Config\Services;

use Psr\Cache\CacheItemPoolInterface;
use Slick\Di\Definition\ObjectDefinition;
use Symfony\Component\Cache\Adapter\ApcuAdapter;

$services = [];

$services[CacheItemPoolInterface::class] = ObjectDefinition
    ::create(ApcuAdapter::class)
    ->call('setLogger')->with('@default.logger');

return $services;
