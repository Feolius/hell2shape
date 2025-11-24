<?php

namespace App\Parser\Node;

class StringNode extends AbstractNode
{
    public function __construct(private string $value)
    {
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
