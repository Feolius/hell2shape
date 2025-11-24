<?php

namespace App\Parser\Node;

class FloatNode extends AbstractNode
{
    public function __construct(private float $value)
    {
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
