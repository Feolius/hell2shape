<?php

namespace App\Generator\Type;

use App\Generator\KeyQuotingStyle;

final readonly class HashmapKey
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

    public function markAsOptional(): self
    {
        return new self($this->name, $this->type, true);
    }

    public function toString(KeyQuotingStyle $style): string
    {
        $key = $this->formatKey($style);
        $optional = $this->optional ? '?' : '';
        return "{$key}{$optional}: {$this->type->toString($style)}";
    }

    private function formatKey(KeyQuotingStyle $style): string
    {
        return match ($style) {
            KeyQuotingStyle::SingleQuotes => "'{$this->name}'",
            KeyQuotingStyle::DoubleQuotes => "\"{$this->name}\"",
            KeyQuotingStyle::NoQuotes => $this->name,
        };
    }
}
