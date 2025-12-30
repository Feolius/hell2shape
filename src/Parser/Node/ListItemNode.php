<?php

namespace Feolius\Hell2Shape\Parser\Node;

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

    public function accept(NodeVisitorInterface $visitor): mixed
    {
        return $this->value->accept($visitor);
    }
}
