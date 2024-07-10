<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Features\App\Infrastructure\Persistence;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use Features\App\Domain\User\Email;

/**
 * DoctrineEmail
 *
 * @package Features\App\Infrastructure\Persistence
 */
final class DoctrineEmail extends StringType
{
    /**
     * @inheritDoc
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        return is_null($value) ? null : (string) $value;
    }

    /**
     * @inheritDoc
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Email
    {
        return is_null($value) ? null : new Email($value);
    }

    /**
     * @inheritDoc
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column["length"] = 255;
        return parent::getSQLDeclaration($column, $platform);
    }


}
