<?php

namespace App\Parser\Node;

use App\Generator\TypeGeneratorVisitor;

final readonly class ObjectNode extends AbstractNode
{
    public function __construct(
        public string $className
    ) {
    }

    public function __toString(): string
    {
        return 'object('.$this->className.')';
    }

    public function accept(TypeGeneratorVisitor $visitor): string
    {
        return $visitor->visitObject($this);
    }
}
