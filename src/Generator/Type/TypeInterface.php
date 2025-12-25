<?php

namespace App\Generator\Type;

use App\Generator\GeneratorException;

interface TypeInterface
{
    /**
     * Merge this type with another type.
     * Returns a new type that represents the union/merge of both types.
     * @throws GeneratorException
     */
    public function merge(TypeInterface $other): UnionType|static;

    /**
     * Accept a visitor for processing this type.
     *
     * @template R
     * @param  TypeVisitorInterface<R>  $visitor
     * @return R
     */
    public function accept(TypeVisitorInterface $visitor): mixed;

    /**
     * Convert this type to a PHPStan type string (single-line, for testing).
     */
    public function toString(): string;
}
