<?php

namespace Feolius\Hell2Shape\Generator\Type;

use Feolius\Hell2Shape\Generator\GeneratorException;

final readonly class UnionType implements TypeInterface
{
    /**
     * @var list<TypeInterface>
     */
    public array $types;

    /**
     * @param  list<TypeInterface>  $types
     * @throws GeneratorException
     */
    public function __construct(array $types)
    {
        $this->types = $this->deduplicateTypes($types);
    }

    public function merge(TypeInterface $other): UnionType
    {
        if ($other instanceof UnionType) {
            return new UnionType([...$this->types, ...$other->types]);
        }

        return new UnionType([...$this->types, $other]);
    }

    public function accept(TypeVisitorInterface $visitor): mixed
    {
        return $visitor->visitUnionType($this);
    }

    public function toString(): string
    {
        $typeStrings = array_map(
            static fn(TypeInterface $type) => $type->toString(),
            $this->types
        );

        return implode('|', $typeStrings);
    }

    /**
     * @param  list<TypeInterface>  $types
     * @return list<TypeInterface>
     * @throws GeneratorException
     */
    private function deduplicateTypes(array $types): array
    {
        $types = $this->unpackUnions($types);

        /** @var array<string, bool> $scalarTypes */
        $scalarTypes = [];
        /** @var array<string, bool> $scalarTypes */
        $classNames = [];
        /** @var ?HashmapType $hashMapType */
        $hashMapType = null;
        /** @var ?StdObjectType $stdObjectType */
        $stdObjectType = null;
        /** @var ?ListType $listType */
        $listType = null;

        $uniqueTypes = [];
        foreach ($types as $type) {
            switch ($type::class) {
                case ScalarType::class:
                    if (!isset($scalarTypes[$type->getTypeName()])) {
                        $uniqueTypes[] = $type;
                        $scalarTypes[$type->getTypeName()] = true;
                    }
                    break;
                case ObjectType::class:
                    if (!isset($classNames[$type->className])) {
                        $uniqueTypes[] = $type;
                        $classNames[$type->className] = true;
                    }
                    break;
                case HashmapType::class:
                    $hashMapType = $hashMapType === null ? $type : $hashMapType->merge($type);
                    break;
                case ListType::class:
                    $listType = $listType === null ? $type : $listType->merge($type);
                    break;
                case StdObjectType::class:
                    $stdObjectType = $stdObjectType === null ? $type : $stdObjectType->merge($type);
                    break;
                case UnionType::class:
                    throw new GeneratorException('Union type is not expected after unpacking.');
                default:
                    throw new GeneratorException(sprintf('Unexpected type "%s".', $type::class));
            }
        }

        if ($hashMapType !== null) {
            $uniqueTypes[] = $hashMapType;
        }
        if ($listType !== null) {
            $uniqueTypes[] = $listType;
        }
        if ($stdObjectType !== null) {
            $uniqueTypes[] = $stdObjectType;
        }

        return $uniqueTypes;
    }

    /**
     * @param  list<TypeInterface>  $types
     * @return list<TypeInterface>
     */
    private function unpackUnions(array $types): array
    {
        do {
            $unionExists = false;
            $unpackedTypes = [];
            foreach ($types as $type) {
                if ($type instanceof UnionType) {
                    $unpackedTypes = [...$unpackedTypes, ...$type->types];
                    $unionExists = true;
                    continue;
                }
                $unpackedTypes[] = $type;
            }
            $types = $unpackedTypes;
        } while ($unionExists);
        return $unpackedTypes;
    }
}
