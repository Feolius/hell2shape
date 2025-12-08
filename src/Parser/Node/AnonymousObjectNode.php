<?php

namespace App\Parser\Node;

use App\Generator\TypeGeneratorVisitor;

final readonly class AnonymousObjectNode extends AbstractNode
{
    public function __construct()
    {
    }

    public function __toString(): string
    {
        return 'object(anonymous)';
    }

    public function accept(TypeGeneratorVisitor $visitor): string
    {
        return $visitor->visitAnonymousObject($this);
    }
}
