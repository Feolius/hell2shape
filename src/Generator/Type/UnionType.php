<?php

namespace App\Generator\Type;

use App\Generator\KeyQuotingStyle;

final readonly class UnionType implements TypeInterface
{
    /**
     * @var list<TypeInterface>
     */
    private array $types;

    /**
     * @param list<TypeInterface> $types
     */
    public function __construct(array $types)
    {
        $this->types = $this->deduplicateTypes($types);
    }

    public function merge(TypeInterface $other): TypeInterface
    {
        if ($other instanceof UnionType) {
            return new UnionType([...$this->types, ...$other->types]);
        }

        return new UnionType([...$this->types, $other]);
    }

    public function toString(KeyQuotingStyle $style): string
    {
        $typeStrings = array_map(
            fn(TypeInterface $type) => $type->toString($style),
            $this->types
        );

        return implode('|', $typeStrings);
    }

    /**
     * @param list<TypeInterface> $types
     * @return list<TypeInterface>
     */
    private function deduplicateTypes(array $types): array
    {
        $deduplicated = [];
        $seen = [];

        foreach ($types as $type) {
            if ($type instanceof UnionType) {
                foreach ($type->types as $innerType) {
                    $key = $this->getTypeKey($innerType);
                    if (!isset($seen[$key])) {
                        $deduplicated[] = $innerType;
                        $seen[$key] = true;
                    }
                }
            } else {
                $key = $this->getTypeKey($type);
                if (!isset($seen[$key])) {
                    $deduplicated[] = $type;
                    $seen[$key] = true;
                }
            }
        }

        return $deduplicated;
    }

    private function getTypeKey(TypeInterface $type): string
    {
        return $type->toString(KeyQuotingStyle::NoQuotes);
    }
}
