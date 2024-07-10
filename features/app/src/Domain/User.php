<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Features\App\Domain;

use Doctrine\ORM\Mapping as ORM;
use Features\App\Domain\User\Email;
use Slick\WebStack\Domain\Security\User\PasswordAuthenticatedUserInterface;
use Slick\WebStack\Domain\Security\User\PasswordUpgradableInterface;

/**
 * User
 *
 * @package Features\App\Domain
 */
#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User implements PasswordAuthenticatedUserInterface, PasswordUpgradableInterface
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $userId = null;

    public function __construct(
        #[ORM\Column(type: 'string')]
        private string $name,
        #[ORM\Column(type: 'Email', unique: true)]
        private Email $email,
        #[ORM\Column(type: 'string')]
        private string $password = ''
    ) {
    }

    public function userId(): ?int
    {
        return $this->userId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function upgradePassword(string $hashedPassword): PasswordUpgradableInterface
    {
        $this->password = $hashedPassword;
        return $this;
    }

    public function userIdentifier(): string
    {
        return (string) $this->email;
    }

    public function roles(): array
    {
        return ['ROLE_USER'];
    }
}
