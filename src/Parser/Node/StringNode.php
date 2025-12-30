<?php

namespace Feolius\Hell2Shape\Parser\Node;

final readonly class StringNode extends AbstractNode
{
    public function __construct(
        public string $value
    ) {
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function accept(NodeVisitorInterface $visitor): mixed
    {
        return $visitor->visitString($this);
    }
}
