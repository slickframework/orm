<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Features\App\Infrastructure\Persistence\DoctrineEmail;
use Psr\Cache\CacheItemPoolInterface;

return [
    "databases" => [
        "default" => [
            "url" => isset($_ENV["DATABASE_URL"]) ? $_ENV["DATABASE_URL"] : 'pdo-sqlite:///:memory:',
            "devMode" => getenv("APP_ENV") === "develop",
            "cache" => CacheItemPoolInterface::class,
            "SQLLogger" => 'default.logger',
        ]
    ],
    "types" => [
        'Email' => DoctrineEmail::class,
    ]
];
