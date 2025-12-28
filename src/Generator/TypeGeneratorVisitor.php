<?php

namespace App\Generator;

use App\Generator\Type\HashmapKey;
use App\Generator\Type\HashmapType;
use App\Generator\Type\ListType;
use App\Generator\Type\ScalarType;
use App\Generator\Type\StdObjectKey;
use App\Generator\Type\StdObjectType;
use App\Generator\Type\TypeInterface;
use App\Parser\Node\AnonymousObjectNode;
use App\Parser\Node\BoolNode;
use App\Parser\Node\FloatNode;
use App\Parser\Node\HashmapItemNode;
use App\Parser\Node\HashmapNode;
use App\Parser\Node\IntNode;
use App\Parser\Node\ListNode;
use App\Parser\Node\NodeVisitorInterface;
use App\Parser\Node\NullNode;
use App\Parser\Node\ObjectNode;
use App\Parser\Node\ResourceNode;
use App\Parser\Node\StdObjectItemNode;
use App\Parser\Node\StdObjectNode;
use App\Parser\Node\StringNode;

/**
 * Visitor that generates Type IR from AST nodes.
 *
 * @implements NodeVisitorInterface<TypeInterface>
 */
final class TypeGeneratorVisitor implements NodeVisitorInterface
{
    public function visitBool(BoolNode $node): TypeInterface
    {
        return new ScalarType('bool');
    }

    public function visitInt(IntNode $node): TypeInterface
    {
        return new ScalarType('int');
    }

    public function visitFloat(FloatNode $node): TypeInterface
    {
        return new ScalarType('float');
    }

    public function visitString(StringNode $node): TypeInterface
    {
        return new ScalarType('string');
    }

    public function visitNull(NullNode $node): TypeInterface
    {
        return new ScalarType('null');
    }

    public function visitResource(ResourceNode $node): TypeInterface
    {
        return new ScalarType('resource');
    }

    public function visitObject(ObjectNode $node): TypeInterface
    {
        return new ScalarType($node->className);
    }

    public function visitAnonymousObject(AnonymousObjectNode $node): TypeInterface
    {
        return new ScalarType('object');
    }

    public function visitHashmap(HashmapNode $node): TypeInterface
    {
        if (empty($node->items)) {
            return new ScalarType('array');
        }

        $keys = [];
        foreach ($node->items as $item) {
            $keys[$item->key->value] = new HashmapKey($item->key->value, $item->value->accept($this));
        }
        return new HashmapType(array_values($keys));
    }

    public function visitHashmapItem(HashmapItemNode $node): TypeInterface
    {
        return $node->value->accept($this);
    }

    public function visitStdObject(StdObjectNode $node): TypeInterface
    {
        if (empty($node->items)) {
            return new ScalarType('object');
        }

        $keys = [];
        foreach ($node->items as $item) {
            $keyName = (string)$item->key;
            $keys[$keyName] = new StdObjectKey($keyName, $item->value->accept($this));
        }
        return new StdObjectType(array_values($keys));
    }

    public function visitStdObjectItem(StdObjectItemNode $node): TypeInterface
    {
        return $node->value->accept($this);
    }

    public function visitList(ListNode $node): TypeInterface
    {
        if (empty($node->items)) {
            return new ScalarType('array');
        }

        /** @var TypeInterface $itemType */
        $itemType = $node->items[0]->value->accept($this);
        foreach ($node->items as $item) {
            $itemType = $itemType->merge($item->value->accept($this));
        }

        return new ListType($itemType);
    }
}
