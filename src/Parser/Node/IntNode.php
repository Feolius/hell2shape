<?php

namespace App\Parser\Node;

final readonly class IntNode extends AbstractNode
{
    public function __construct(
        public int $value
    ) {
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }
}
