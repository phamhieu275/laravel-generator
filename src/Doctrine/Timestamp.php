<?php

namespace Bluecode\Generator\Doctrine;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Timestamp type for the Doctrine 2 ORM
 */
class Timestamp extends Type
{
    /**
     * Type name
     *
     * @var string
     */
    const TIMESTAMP = 'timestamp';

    /**
     *
     * @return string
     */
    public function getName()
    {
        return self::TIMESTAMP;
    }

    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getIntegerTypeDeclarationSQL($fieldDeclaration);
    }
}
