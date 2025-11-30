<?php

namespace App\Parser\Node;

final readonly class ResourceNode extends AbstractNode
{
    public function __construct(public string $value)
    {
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
