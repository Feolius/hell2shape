<?php

namespace Feolius\Hell2Shape\Parser\Node;

/**
 * Generic visitor interface for traversing the AST.
 *
 * @template R
 */
interface NodeVisitorInterface
{
    /**
     * @return R
     */
    public function visitBool(BoolNode $node): mixed;

    /**
     * @return R
     */
    public function visitInt(IntNode $node): mixed;

    /**
     * @return R
     */
    public function visitFloat(FloatNode $node): mixed;

    /**
     * @return R
     */
    public function visitString(StringNode $node): mixed;

    /**
     * @return R
     */
    public function visitNull(NullNode $node): mixed;

    /**
     * @return R
     */
    public function visitResource(ResourceNode $node): mixed;

    /**
     * @return R
     */
    public function visitObject(ObjectNode $node): mixed;

    /**
     * @return R
     */
    public function visitAnonymousObject(AnonymousObjectNode $node): mixed;

    /**
     * @return R
     */
    public function visitHashmap(HashmapNode $node): mixed;

    /**
     * @return R
     */
    public function visitHashmapItem(HashmapItemNode $node): mixed;

    /**
     * @return R
     */
    public function visitStdObject(StdObjectNode $node): mixed;

    /**
     * @return R
     */
    public function visitStdObjectItem(StdObjectItemNode $node): mixed;

    /**
     * @return R
     */
    public function visitList(ListNode $node): mixed;
}
