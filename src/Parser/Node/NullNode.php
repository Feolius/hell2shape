<?php

namespace App\Parser\Node;

use App\Generator\TypeGeneratorVisitor;

final readonly class NullNode extends AbstractNode
{
    public function __construct()
    {
    }

    public function __toString(): string
    {
        return 'null';
    }

    public function accept(TypeGeneratorVisitor $visitor): string
    {
        return $visitor->visitNull($this);
    }
}
