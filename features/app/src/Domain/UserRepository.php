<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Features\App\Domain;

/**
 * UserRepository
 *
 * @package Features\App\Domain
 */
interface UserRepository
{

    public function withId(int $userId): User;
}
