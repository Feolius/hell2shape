<?php

namespace App\Parser\Node;

use App\Generator\TypeGeneratorVisitor;

abstract readonly class AbstractNode
{
    abstract public function __toString(): string;

    abstract public function accept(TypeGeneratorVisitor $visitor): string;
}
