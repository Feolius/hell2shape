<?php

namespace Feolius\Hell2Shape\Parser\Node;

final readonly class BoolNode extends AbstractNode
{
    public function __construct(
        public bool $value
    ) {
    }

    public function __toString(): string
    {
        return $this->value ? 'true' : 'false';
    }

    public function accept(NodeVisitorInterface $visitor): mixed
    {
        return $visitor->visitBool($this);
    }
}
