<?php

namespace Feolius\Hell2Shape\Parser\Node;

final readonly class FloatNode extends AbstractNode
{
    public function __construct(
        public float $value
    ) {
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public function accept(NodeVisitorInterface $visitor): mixed
    {
        return $visitor->visitFloat($this);
    }
}
