<?php

namespace App\Generator\Type;

final class HashmapType implements TypeInterface
{
    /**
     * @var array<string|int, HashmapKey>
     */
    private(set) array $keys = [];

    /**
     * @param  list<HashmapKey>  $keys
     */
    public function __construct(array $keys)
    {
        foreach ($keys as $key) {
            $this->keys[$key->name] = $key;
        }
    }

    public function merge(TypeInterface $other): UnionType|static
    {
        if (!$other instanceof HashmapType) {
            return new UnionType([$this, $other]);
        }

        $allKeys = array_unique([
            ...array_keys($this->keys),
            ...array_keys($other->keys),
        ]);

        $mergedKeys = [];
        foreach ($allKeys as $keyName) {
            $inThis = isset($this->keys[$keyName]);
            $inOther = isset($other->keys[$keyName]);

            if ($inThis && $inOther) {
                $mergedType = $this->keys[$keyName]->type->merge(
                    $other->keys[$keyName]->type
                );
                $optional = $this->keys[$keyName]->optional ||
                           $other->keys[$keyName]->optional;
                $mergedKey = new HashmapKey($keyName, $mergedType, $optional);
            } elseif ($inThis) {
                $mergedKey = new HashmapKey($keyName, $this->keys[$keyName]->type, true);
            } else {
                $mergedKey = new HashmapKey($keyName, $other->keys[$keyName]->type, true);
            }
            $mergedKeys[] = $mergedKey;
        }

        return new HashmapType($mergedKeys);
    }

    public function accept(TypeVisitorInterface $visitor): mixed
    {
        return $visitor->visitHashmapType($this);
    }

    public function toString(): string
    {
        $items = [];
        foreach ($this->keys as $key) {
            $items[] = $key->toString();
        }
        return 'array{'.implode(', ', $items).'}';
    }
}
