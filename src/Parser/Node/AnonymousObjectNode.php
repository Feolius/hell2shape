<?php

namespace App\Parser\Node;

final readonly class AnonymousObjectNode extends AbstractNode
{
    public function __construct()
    {
    }

    public function __toString(): string
    {
        return '(anonymous object)';
    }
}
