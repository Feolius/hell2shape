<?php

namespace Feolius\Hell2Shape\Parser\Node;

final readonly class StdObjectItemNode extends AbstractNode
{
    public function __construct(
        public StringNode $key,
        public AbstractNode $value
    ) {
    }

    public function __toString(): string
    {
        return (string)$this->key.' => '.(string)$this->value;
    }

    public function accept(NodeVisitorInterface $visitor): mixed
    {
        return $visitor->visitStdObjectItem($this);
    }
}
