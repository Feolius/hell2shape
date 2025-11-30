<?php

namespace App\Parser\Node;

final readonly class ListItemNode extends AbstractNode
{
    public function __construct(
        public AbstractNode $value
    ) {
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }
}
