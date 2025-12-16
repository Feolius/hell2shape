<?php

namespace App\Generator\Type;

use App\Generator\KeyQuotingStyle;

final class HashmapType implements TypeInterface
{
    /**
     * @var array<string, HashmapKey>
     */
    private array $keys = [];

    public function addKey(string $name, TypeInterface $type, bool $optional = false): void
    {
        if (!isset($this->keys[$name])) {
            $this->keys[$name] = new HashmapKey($name, $type, $optional);
        } else {
            $this->keys[$name] = $this->keys[$name]->merge($type);
        }
    }

    public function merge(TypeInterface $other): TypeInterface
    {
        if (!$other instanceof HashmapType) {
            return new UnionType([$this, $other]);
        }

        $merged = new HashmapType();

        $allKeys = array_unique([
            ...array_keys($this->keys),
            ...array_keys($other->keys)
        ]);

        foreach ($allKeys as $keyName) {
            $inThis = isset($this->keys[$keyName]);
            $inOther = isset($other->keys[$keyName]);

            if ($inThis && $inOther) {
                $mergedType = $this->keys[$keyName]->type->merge(
                    $other->keys[$keyName]->type
                );
                $optional = $this->keys[$keyName]->optional ||
                           $other->keys[$keyName]->optional;
                $merged->addKey($keyName, $mergedType, $optional);
            } elseif ($inThis) {
                $merged->addKey($keyName, $this->keys[$keyName]->type, true);
            } else {
                $merged->addKey($keyName, $other->keys[$keyName]->type, true);
            }
        }

        return $merged;
    }

    public function toString(KeyQuotingStyle $style): string
    {
        $items = [];
        foreach ($this->keys as $key) {
            $items[] = $key->toString($style);
        }
        return 'array{' . implode(', ', $items) . '}';
    }
}
