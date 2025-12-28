<?php

namespace App\Parser\Node;

final readonly class HashmapItemNode extends AbstractNode
{
    public function __construct(
        public IntNode|StringNode $key,
        public AbstractNode $value
    ) {
    }

    public function __toString(): string
    {
        return $this->key.' => '.$this->value;
    }

    public function accept(NodeVisitorInterface $visitor): mixed
    {
        return $visitor->visitHashmapItem($this);
    }
}
