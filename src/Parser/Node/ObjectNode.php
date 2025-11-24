<?php

namespace App\Parser\Node;

class ObjectNode extends AbstractNode
{
    public function __construct(private string $className)
    {
    }

    public function __toString(): string
    {
        return '(object) ' . $this->className;
    }
}
