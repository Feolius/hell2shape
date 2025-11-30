<?php

namespace App\Parser\Node;

final readonly class FloatNode extends AbstractNode
{
    public function __construct(
        public float $value
    ) {
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }
}
