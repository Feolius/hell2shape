<?php

namespace Feolius\Hell2Shape\Generator\Type;

final readonly class ObjectType implements TypeInterface
{
    public function __construct(
        public string $className,
    ) {
    }

    public function merge(TypeInterface $other): UnionType|static
    {
        if ($other instanceof ObjectType && $other->className === $this->className) {
            return $this;
        }

        return new UnionType([$this, $other]);
    }

    public function accept(TypeVisitorInterface $visitor): mixed
    {
        return $visitor->visitObjectType($this);
    }

    public function toString(): string
    {
        return $this->className;
    }
}
