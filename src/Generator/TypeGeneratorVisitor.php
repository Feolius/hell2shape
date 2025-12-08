<?php

namespace App\Generator;

use App\Parser\Node\AnonymousObjectNode;
use App\Parser\Node\BoolNode;
use App\Parser\Node\FloatNode;
use App\Parser\Node\HashmapItemNode;
use App\Parser\Node\HashmapNode;
use App\Parser\Node\IntNode;
use App\Parser\Node\ListNode;
use App\Parser\Node\NullNode;
use App\Parser\Node\ObjectNode;
use App\Parser\Node\ResourceNode;
use App\Parser\Node\StdObjectItemNode;
use App\Parser\Node\StdObjectNode;
use App\Parser\Node\StringNode;

final class TypeGeneratorVisitor
{
    public function __construct(
        private readonly KeyQuotingStyle $keyQuotingStyle = KeyQuotingStyle::NoQuotes,
        private readonly int $maxListUnionTypes = 3
    ) {
    }

    public function visitBool(BoolNode $node): string
    {
        return 'bool';
    }

    public function visitInt(IntNode $node): string
    {
        return 'int';
    }

    public function visitFloat(FloatNode $node): string
    {
        return 'float';
    }

    public function visitString(StringNode $node): string
    {
        return 'string';
    }

    public function visitNull(NullNode $node): string
    {
        return 'null';
    }

    public function visitResource(ResourceNode $node): string
    {
        return 'resource';
    }

    public function visitObject(ObjectNode $node): string
    {
        return $node->className;
    }

    public function visitAnonymousObject(AnonymousObjectNode $node): string
    {
        return 'object';
    }

    public function visitHashmap(HashmapNode $node): string
    {
        // Empty arrays are treated as generic array type
        if (empty($node->items)) {
            return 'array';
        }

        $items = [];
        foreach ($node->items as $item) {
            $items[] = $item->accept($this);
        }

        return 'array{'.implode(', ', $items).'}';
    }

    public function visitHashmapItem(HashmapItemNode $node): string
    {
        $key = $this->formatKey($node->key);
        $type = $node->value->accept($this);
        return "$key: $type";
    }

    public function visitStdObject(StdObjectNode $node): string
    {
        $items = [];
        foreach ($node->items as $item) {
            $items[] = $item->accept($this);
        }

        return 'object{'.implode(', ', $items).'}';
    }

    public function visitStdObjectItem(StdObjectItemNode $node): string
    {
        $key = $this->formatKey($node->key);
        $type = $node->value->accept($this);
        return "$key: $type";
    }

    public function visitList(ListNode $node): string
    {
        if (empty($node->items)) {
            return 'list<mixed>';
        }

        // Collect unique types
        $types = [];
        foreach ($node->items as $item) {
            $type = $item->value->accept($this);
            $types[$type] = true; // Use array key for deduplication
        }

        $uniqueTypes = array_keys($types);
        $typeCount = count($uniqueTypes);

        if ($typeCount === 1) {
            return 'list<'.$uniqueTypes[0].'>';
        }

        if ($typeCount <= $this->maxListUnionTypes) {
            return 'list<'.implode('|', $uniqueTypes).'>';
        }

        return 'list<mixed>';
    }

    private function formatKey(IntNode|StringNode $key): string
    {
        $keyValue = (string)$key;

        if ($key instanceof StringNode) {
            return match ($this->keyQuotingStyle) {
                KeyQuotingStyle::SingleQuotes => "'".$keyValue."'",
                KeyQuotingStyle::DoubleQuotes => '"'.$keyValue.'"',
                KeyQuotingStyle::NoQuotes => $keyValue,
            };
        }

        return $keyValue;
    }
}
