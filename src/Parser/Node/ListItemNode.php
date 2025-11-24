<?php

namespace App\Parser\Node;

class ListItemNode extends AbstractNode
{
    public function __construct(private AbstractNode $value)
    {
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
