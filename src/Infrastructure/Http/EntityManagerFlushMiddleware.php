<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Orm\Infrastructure\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slick\Orm\Infrastructure\Persistence\EntityManagerCollection;

/**
 * OrmMiddleware
 *
 * @package Slick\Orm\Infrastructure\Http
 */
final readonly class EntityManagerFlushMiddleware implements MiddlewareInterface
{

    public function __construct(private EntityManagerCollection $managerCollection)
    {
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        foreach ($this->managerCollection as $manager) {
            $manager->flush();
        }
        return $response;
    }
}
