<?php

namespace Feolius\Hell2Shape\Generator\Type;

final readonly class StdObjectKey
{
    public function __construct(
        public string $name,
        public TypeInterface $type,
        public bool $optional = false
    ) {
    }

    public function merge(TypeInterface $newType): self
    {
        return new self(
            $this->name,
            $this->type->merge($newType),
            $this->optional
        );
    }

    public function toString(): string
    {
        $optional = $this->optional ? '?' : '';
        return "{$this->name}{$optional}: {$this->type->toString()}";
    }
}
