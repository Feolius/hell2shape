<?php

namespace App\Parser\Node;

class ListNode extends AbstractNode
{
    /**
     * @param list<ListItemNode> $items
     */
    public function __construct(private array $items)
    {
    }

    public function __toString(): string
    {
        $items = [];
        foreach ($this->items as $item) {
            $items[] = (string) $item;
        }

        return '[' . implode(', ', $items) . ']';
    }
}
