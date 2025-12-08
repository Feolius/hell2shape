<?php

namespace App\Parser\Node;

use App\Generator\TypeGeneratorVisitor;

final readonly class BoolNode extends AbstractNode
{
    public function __construct(
        public bool $value
    ) {
    }

    public function __toString(): string
    {
        return $this->value ? 'true' : 'false';
    }

    public function accept(TypeGeneratorVisitor $visitor): string
    {
        return $visitor->visitBool($this);
    }
}
