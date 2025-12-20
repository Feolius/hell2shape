<?php

namespace App\Generator\Type;

use App\Generator\KeyQuotingStyle;

final readonly class ListType implements TypeInterface
{
    public function __construct(
        private TypeInterface $elementType
    ) {
    }

    public function merge(TypeInterface $other): UnionType|static
    {
        if (!$other instanceof ListType) {
            return new UnionType([$this, $other]);
        }

        $mergedElement = $this->elementType->merge($other->elementType);
        return new ListType($mergedElement);
    }

    public function toString(KeyQuotingStyle $style): string
    {
        return 'list<'.$this->elementType->toString($style).'>';
    }
}
