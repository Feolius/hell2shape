<?php

namespace Feolius\Hell2Shape\Generator;

use Feolius\Hell2Shape\Generator\Type\HashmapKey;
use Feolius\Hell2Shape\Generator\Type\HashmapType;
use Feolius\Hell2Shape\Generator\Type\ListType;
use Feolius\Hell2Shape\Generator\Type\ObjectType;
use Feolius\Hell2Shape\Generator\Type\ScalarType;
use Feolius\Hell2Shape\Generator\Type\StdObjectKey;
use Feolius\Hell2Shape\Generator\Type\StdObjectType;
use Feolius\Hell2Shape\Generator\Type\TypeInterface;
use Feolius\Hell2Shape\Parser\Node\AnonymousObjectNode;
use Feolius\Hell2Shape\Parser\Node\BoolNode;
use Feolius\Hell2Shape\Parser\Node\FloatNode;
use Feolius\Hell2Shape\Parser\Node\HashmapItemNode;
use Feolius\Hell2Shape\Parser\Node\HashmapNode;
use Feolius\Hell2Shape\Parser\Node\IntNode;
use Feolius\Hell2Shape\Parser\Node\ListNode;
use Feolius\Hell2Shape\Parser\Node\NodeVisitorInterface;
use Feolius\Hell2Shape\Parser\Node\NullNode;
use Feolius\Hell2Shape\Parser\Node\ObjectNode;
use Feolius\Hell2Shape\Parser\Node\ResourceNode;
use Feolius\Hell2Shape\Parser\Node\StdObjectItemNode;
use Feolius\Hell2Shape\Parser\Node\StdObjectNode;
use Feolius\Hell2Shape\Parser\Node\StringNode;

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
        return new ObjectType($node->className);
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
