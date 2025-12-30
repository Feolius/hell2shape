<?php

namespace Feolius\Hell2Shape\Generator\Type;

/**
 * @template R
 */
interface TypeVisitorInterface
{
    /**
     * @return R
     */
    public function visitScalarType(ScalarType $type): mixed;

    /**
     * @return R
     */
    public function visitUnionType(UnionType $type): mixed;

    /**
     * @return R
     */
    public function visitHashmapType(HashmapType $type): mixed;

    /**
     * @return R
     */
    public function visitStdObjectType(StdObjectType $type): mixed;

    /**
     * @return R
     */
    public function visitListType(ListType $type): mixed;
}
