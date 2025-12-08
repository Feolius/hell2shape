<?php

namespace App\Parser\Node;

use App\Generator\TypeGeneratorVisitor;

final readonly class ResourceNode extends AbstractNode
{
    public function __construct(
        public string $type
    ) {
    }

    public function __toString(): string
    {
        return 'resource('.$this->type.')';
    }

    public function accept(TypeGeneratorVisitor $visitor): string
    {
        return $visitor->visitResource($this);
    }
}
