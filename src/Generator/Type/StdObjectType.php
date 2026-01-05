<?php

namespace Feolius\Hell2Shape\Generator\Type;

final readonly class StdObjectType implements TypeInterface
{
    /**
     * @var array<string, StdObjectKey>
     */
    public array $keys;

    /**
     * @param  list<StdObjectKey>  $keys
     */
    public function __construct(array $keys)
    {
        $keysByName = [];
        foreach ($keys as $key) {
            $keysByName[$key->name] = $key;
        }
        $this->keys = $keysByName;
    }

    public function merge(TypeInterface $other): UnionType|static
    {
        if (!$other instanceof StdObjectType) {
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
                $mergedKey = new StdObjectKey($keyName, $mergedType, $optional);
            } elseif ($inThis) {
                $mergedKey = new StdObjectKey($keyName, $this->keys[$keyName]->type, true);
            } else {
                $mergedKey = new StdObjectKey($keyName, $other->keys[$keyName]->type, true);
            }
            $mergedKeys[] = $mergedKey;
        }

        return new StdObjectType($mergedKeys);
    }

    public function accept(TypeVisitorInterface $visitor): mixed
    {
        return $visitor->visitStdObjectType($this);
    }

    public function toString(): string
    {
        $items = [];
        foreach ($this->keys as $key) {
            $items[] = $key->toString();
        }
        return 'object{'.implode(', ', $items).'}';
    }
}
