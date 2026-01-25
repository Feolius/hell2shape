<?php

namespace Feolius\Hell2Shape\Generator\Type;

use Feolius\Hell2Shape\Generator\ClassNameStyle;
use Feolius\Hell2Shape\Generator\GeneratorConfig;
use Feolius\Hell2Shape\Generator\KeyQuotingStyle;

/**
 * @implements TypeVisitorInterface<string>
 */
final class TypeFormatterVisitor implements TypeVisitorInterface
{
    private const string DOC_COMMENT_START = '/**';

    private const string DOC_COMMENT_END = ' */';

    private const string DOC_COMMENT_LINE_PREFIX = ' * ';

    private int $currentIndent = 0;

    private bool $isSingleLine = true;

    private bool $inStructure = false;

    private bool $isEntryPoint = true;

    public function __construct(
        private readonly GeneratorConfig $config,
    ) {
    }

    public function visitScalarType(ScalarType $type): string
    {
        return $this->outputWrapper($type->getTypeName());
    }

    public function visitUnionType(UnionType $type): string
    {
        $typeStrings = array_map(
            fn(TypeInterface $t) => $t->accept($this),
            $type->types
        );

        return $this->outputWrapper(implode('|', $typeStrings));
    }

    public function visitListType(ListType $type): string
    {
        return $this->outputWrapper('list<'.$type->elementType->accept($this).'>');
    }

    public function visitHashmapType(HashmapType $type): string
    {
        return $this->outputWrapper($this->formatStructure('array', $type->keys));
    }

    public function visitStdObjectType(StdObjectType $type): string
    {
        return $this->outputWrapper($this->formatStructure('object', $type->keys));
    }

    public function visitObjectType(ObjectType $type): string
    {
        return $this->outputWrapper($this->formatClassName($type->className));
    }

    private function outputWrapper(string $content): string
    {
        if (!$this->config->asDocComment || $this->inStructure) {
            return $content;
        }
        if ($this->isSingleLine) {
            return self::DOC_COMMENT_START.' '.$content.self::DOC_COMMENT_END;
        }

        return self::DOC_COMMENT_START.PHP_EOL.self::DOC_COMMENT_LINE_PREFIX.$content.PHP_EOL.self::DOC_COMMENT_END;
    }

    /**
     * @param  array<string|int, HashmapKey>|array<string, StdObjectKey>  $keys
     */
    private function formatStructure(string $prefix, array $keys): string
    {
        if (empty($keys)) {
            return $prefix;
        }
        $isEntryPoint = $this->isEntryPoint;
        if ($isEntryPoint) {
            $this->isSingleLine = $this->config->indentSize === 0;
            $this->inStructure = true;
            $this->isEntryPoint = false;
        }

        // Single-line format (no indentation)
        if ($this->isSingleLine) {
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
        if ($isEntryPoint) {
            $this->inStructure = false;
        }

        $newLineSeparator = $this->config->asDocComment ? PHP_EOL.self::DOC_COMMENT_LINE_PREFIX : PHP_EOL;
        $outerIndent = str_repeat(' ', $this->currentIndent);
        return $prefix.'{'.$newLineSeparator.implode(
            ','.$newLineSeparator,
            $items
        ).','.$newLineSeparator.$outerIndent.'}';
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

    private function formatClassName(string $className): string
    {
        $uqn = static fn(string $class) => (string)current(array_slice(explode('\\', $class), -1));
        return match ($this->config->classNameStyle) {
            ClassNameStyle::Unqualified => $uqn($className),
            ClassNameStyle::Qualified => $className,
            ClassNameStyle::FullyQualified => '\\'.$className,
        };
    }
}
