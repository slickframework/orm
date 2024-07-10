<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Features\App\Domain\User;

use RuntimeException;
use Stringable;

/**
 * Email
 *
 * @package Features\App\Domain\User
 */
final class Email implements Stringable
{

    private string $email;

    /**
     * Creates a Email
     *
     * @param string $email
     */
    public function __construct(string $email)
    {
        $email = strtolower($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException("Invalid email address: $email");
        }
        $this->email = $email;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->email;
    }
}
