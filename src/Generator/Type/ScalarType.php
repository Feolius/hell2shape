<?php

namespace App\Generator\Type;

use App\Generator\KeyQuotingStyle;

final readonly class ScalarType implements TypeInterface
{
    public function __construct(
        private string $typeName
    ) {
    }

    public function merge(TypeInterface $other): UnionType|static
    {
        if ($other instanceof ScalarType && $other->typeName === $this->typeName) {
            return $this;
        }

        return new UnionType([$this, $other]);
    }

    public function toString(KeyQuotingStyle $style): string
    {
        return $this->typeName;
    }

    public function getTypeName(): string
    {
        return $this->typeName;
    }
}
