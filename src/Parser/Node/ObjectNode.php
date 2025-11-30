<?php

namespace App\Parser\Node;

final readonly class ObjectNode extends AbstractNode
{
    public function __construct(public string $className)
    {
    }

    public function __toString(): string
    {
        return '(object) ' . $this->className;
    }
}
