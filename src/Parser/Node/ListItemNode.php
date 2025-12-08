<?php

namespace App\Parser\Node;

use App\Generator\TypeGeneratorVisitor;

final readonly class ListItemNode extends AbstractNode
{
    public function __construct(
        public AbstractNode $value
    ) {
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public function accept(TypeGeneratorVisitor $visitor): string
    {
        return $this->value->accept($visitor);
    }
}
