<?php

namespace Feolius\Hell2Shape\Generator\Type;

use Feolius\Hell2Shape\Generator\GeneratorConfig;
use Feolius\Hell2Shape\Generator\KeyQuotingStyle;

/**
 * @implements TypeVisitorInterface<string>
 */
final class TypeFormatterVisitor implements TypeVisitorInterface
{
    private int $currentIndent = 0;

    public function __construct(
        private readonly GeneratorConfig $config,
    ) {
    }

    public function visitScalarType(ScalarType $type): string
    {
        return $type->getTypeName();
    }

    public function visitUnionType(UnionType $type): string
    {
        $typeStrings = array_map(
            fn(TypeInterface $t) => $t->accept($this),
            $type->types
        );

        return implode('|', $typeStrings);
    }

    public function visitListType(ListType $type): string
    {
        return 'list<'.$type->elementType->accept($this).'>';
    }

    public function visitHashmapType(HashmapType $type): string
    {
        return $this->formatStructure('array', $type->keys);
    }

    public function visitStdObjectType(StdObjectType $type): string
    {
        return $this->formatStructure('object', $type->keys);
    }

    /**
     * @param array<string|int, HashmapKey>|array<string, StdObjectKey> $keys
     */
    private function formatStructure(string $prefix, array $keys): string
    {
        if (empty($keys)) {
            return $prefix;
        }

        // Single-line format (no indentation)
        if ($this->config->indentSize === 0) {
            $items = [];
            foreach ($keys as $key) {
                $items[] = $this->formatKey($key);
            }
            return $prefix.'{'.implode(', ', $items).'}';
        }

        // Multi-line format with indentation
        $this->currentIndent += $this->config->indentSize;
        $indent = str_repeat(' ', $this->currentIndent);

        $items = [];
        foreach ($keys as $key) {
            $items[] = $indent.$this->formatKey($key);
        }

        $this->currentIndent -= $this->config->indentSize;
        $outerIndent = str_repeat(' ', $this->currentIndent);

        return $prefix."{\n".implode(",\n", $items)."\n".$outerIndent.'}';
    }

    private function formatKey(HashmapKey|StdObjectKey $key): string
    {
        $keyName = is_int($key->name) ? (string)$key->name : $this->formatKeyName($key->name);
        $optional = $key->optional ? '?' : '';
        $typeString = $key->type->accept($this);

        return "{$keyName}{$optional}: {$typeString}";
    }

    private function formatKeyName(string $name): string
    {
        return match ($this->config->keyQuotingStyle) {
            KeyQuotingStyle::SingleQuotes => "'{$name}'",
            KeyQuotingStyle::DoubleQuotes => "\"{$name}\"",
            KeyQuotingStyle::NoQuotes => $name,
        };
    }
}
