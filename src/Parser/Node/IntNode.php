<?php

namespace App\Parser\Node;

class IntNode extends AbstractNode
{
    public function __construct(private int $value)
    {
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
