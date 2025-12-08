<?php

namespace App\Parser\Node;

use App\Generator\TypeGeneratorVisitor;

final readonly class StringNode extends AbstractNode
{
    public function __construct(
        public string $value
    ) {
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function accept(TypeGeneratorVisitor $visitor): string
    {
        return $visitor->visitString($this);
    }
}
