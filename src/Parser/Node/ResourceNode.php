<?php

namespace App\Parser\Node;

final readonly class ResourceNode extends AbstractNode
{
    public function __construct(public string $type)
    {
    }

    public function __toString(): string
    {
        return "resource of type {$this->type}";
    }
}
