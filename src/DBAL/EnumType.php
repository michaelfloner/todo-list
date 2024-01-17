<?php

declare(strict_types=1);

namespace App\DBAL;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use UnitEnum;

abstract class EnumType extends Type
{
    protected string $name;

    abstract protected function getEnum(): string;

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $this->getName();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $values = $this->getValues();

        if (!in_array($value, $values, true)) {
            throw new \InvalidArgumentException("Invalid '" . $this->name . "' value.");
        }

        return $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): true
    {
        return true;
    }

    /**
     * @return array<string>
     */
    protected function getValues(): array
    {
        $values = [];
        $enumClass = $this->getEnum();

        foreach ($enumClass::cases() as $value) {
            $values[] = $value->value;
        }

        return $values;
    }

    protected static function nullable(): bool
    {
        return false;
    }
}
