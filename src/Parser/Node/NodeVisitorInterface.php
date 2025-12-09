<?php

namespace App\Parser\Node;

/**
 * Generic visitor interface for traversing the AST.
 *
 * @template R The return type of visit methods
 */
interface NodeVisitorInterface
{
    public function visitBool(BoolNode $node): mixed;
    public function visitInt(IntNode $node): mixed;
    public function visitFloat(FloatNode $node): mixed;
    public function visitString(StringNode $node): mixed;
    public function visitNull(NullNode $node): mixed;
    public function visitResource(ResourceNode $node): mixed;
    public function visitObject(ObjectNode $node): mixed;
    public function visitAnonymousObject(AnonymousObjectNode $node): mixed;
    public function visitHashmap(HashmapNode $node): mixed;
    public function visitHashmapItem(HashmapItemNode $node): mixed;
    public function visitStdObject(StdObjectNode $node): mixed;
    public function visitStdObjectItem(StdObjectItemNode $node): mixed;
    public function visitList(ListNode $node): mixed;
}
