<?php

namespace App\Generator\Type;

final readonly class ListType implements TypeInterface
{
    public function __construct(
        private(set) TypeInterface $elementType
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

    public function accept(TypeVisitorInterface $visitor): mixed
    {
        return $visitor->visitListType($this);
    }

    public function toString(): string
    {
        return 'list<'.$this->elementType->toString().'>';
    }
}
