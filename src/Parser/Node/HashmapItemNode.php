<?php

namespace App\Parser\Node;

class HashmapItemNode extends AbstractNode
{
    public function __construct(private IntNode|StringNode $key, private AbstractNode $value)
    {
    }

    public function __toString(): string
    {
        return (string) $this->key . ' => ' . (string) $this->value;
    }
}
