<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Features\App\Infrastructure\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use Features\App\Domain\User;
use Features\App\Domain\UserRepository;
use Slick\WebStack\Domain\Security\Exception\UserNotFoundException;

/**
 * DoctrineUserRepository
 *
 * @package Features\App\Infrastructure\Persistence
 */
final readonly class DoctrineUserRepository implements UserRepository
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function withId(int $userId): User
    {
        $user = $this->entityManager->find(User::class, $userId);
        if ($user instanceof User) {
            return $user;
        }

        throw new UserNotFoundException("User not found");
    }
}
