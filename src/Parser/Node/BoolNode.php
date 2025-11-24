<?php

namespace App\Parser\Node;

class BoolNode extends AbstractNode
{
    public function __construct(private bool $value)
    {
    }

    public function __toString(): string
    {
        return $this->value ? 'true' : 'false';
    }
}
