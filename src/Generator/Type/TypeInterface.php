<?php

namespace App\Generator\Type;

use App\Generator\GeneratorException;
use App\Generator\KeyQuotingStyle;

interface TypeInterface
{
    /**
     * Merge this type with another type.
     * Returns a new type that represents the union/merge of both types.
     * @throws GeneratorException
     */
    public function merge(TypeInterface $other): UnionType|static;

    /**
     * Convert this type to a PHPStan type string.
     */
    public function toString(KeyQuotingStyle $style): string;
}
