<?php

namespace App\Parser\Node;

use App\Generator\TypeGeneratorVisitor;

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

    public function accept(TypeGeneratorVisitor $visitor): string
    {
        return $visitor->visitFloat($this);
    }
}
