<?php

namespace App\Parser\Node;

class StdObjectItemNode extends AbstractNode
{
    public function __construct(private StringNode $key, private AbstractNode $value)
    {
    }

    public function __toString(): string
    {
        return (string) $this->key . ' => ' . (string) $this->value;
    }
}
