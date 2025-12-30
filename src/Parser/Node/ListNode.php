<?php

namespace Feolius\Hell2Shape\Parser\Node;

final readonly class ListNode extends AbstractNode
{
    /**
     * @param list<ListItemNode> $items
     */
    public function __construct(
        public array $items
    ) {
    }

    public function __toString(): string
    {
        $items = [];
        foreach ($this->items as $item) {
            $items[] = (string)$item;
        }

        return '['.implode(', ', $items).']';
    }

    public function accept(NodeVisitorInterface $visitor): mixed
    {
        return $visitor->visitList($this);
    }
}
