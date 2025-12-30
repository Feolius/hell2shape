# Active Context

## Current Focus
- CLI interface fully implemented with Symfony SingleCommandApplication
- Complete pipeline: STDIN → Lexer → Parser → Generator → STDOUT

## Recent Decisions
- Using Lexer/Parser/Generator architecture
- PHP 8.4+ as minimum version
- Symfony Console for CLI interface
- All Node classes are now `readonly` and `final` for immutability
- Generic visitor pattern with NodeVisitorInterface<R> for extensibility
- **Intermediate Type IR system** for mergeable type representations
- KeyQuotingStyle enum for array shape key formatting (NoQuotes, SingleQuotes, DoubleQuotes)
- **Removed maxListUnionTypes threshold** - all union types shown
- Empty arrays generate `array` type (not `list<mixed>`)
- StdObject generates `object{...}` syntax (not `array{...}`)
- **Visitor pattern for formatting** - TypeFormatterVisitor handles all formatting logic
- **Configurable indentation** - Default 4 spaces, can be 0 (single-line) or any custom size
- **Separation of concerns** - Types handle structure, formatters handle presentation

## Recent Changes
- Implemented intermediate Type IR system (TypeInterface hierarchy)
- Created mergeable type classes: HashmapType, StdObjectType, UnionType, ListType, ScalarType
- Updated TypeGeneratorVisitor to return TypeInterface instead of strings
- Hashmap merging logic:
  - Missing keys become optional (?)
  - Different types for same key create unions
  - Recursive merging for nested hashmaps
- Removed maxListUnionTypes threshold from tests
- Added comprehensive hashmap merging tests
- **Implemented TypeVisitorInterface** for generic visitor pattern on Type IR
- **Created TypeFormatterVisitor** with configurable indentation and formatting
- **Added accept() method** to all Type classes for visitor pattern
- **Updated Generator** to use TypeFormatterVisitor with configurable indentSize
- **Exposed properties** using `private(set)` in UnionType, ListType, HashmapType, StdObjectType
- **Made HashmapType and StdObjectType immutable** - removed addKey() method, constructor-only initialization
- **Added FormattingTest** to demonstrate multi-line formatting capabilities
- **Split key types**: Introduced StdObjectKey (string keys only) separate from HashmapKey (int|string keys)
- **Integer key formatting rule**: Integer keys always output without quotes, regardless of KeyQuotingStyle
- **Added tests** for integer key formatting behavior (testIntegerKeysWithSingleQuotes, testMixedIntegerAndStringKeysWithDoubleQuotes)
- **Implemented CLI interface** using Symfony SingleCommandApplication:
  - Version 0.1.0
  - STDIN input handling for piping var_dump output
  - `--indent/-i` option: Configurable indentation (default: 4, use 0 for single-line)
  - `--quotes` option: Key quoting style (none, single, double)
  - Comprehensive error handling for all pipeline stages
  - User-friendly help text with usage examples

## Type IR Architecture
- **Three-phase generation**: AST → Type IR → Formatter → String
- **TypeInterface**: Base interface with merge(), accept(), and toString() methods
- **TypeVisitorInterface**: Generic visitor for operations on Type IR
- **TypeFormatterVisitor**: Handles all formatting logic with indentation
- **ScalarType**: Non-mergeable types (creates unions when merged)
- **UnionType**: Automatic deduplication of union members, exposed types via `private(set)`
- **HashmapType**: Mergeable array shapes with optional key support, exposed keys via `private(set)`
- **StdObjectType**: Mergeable stdClass objects (same logic as HashmapType), exposed keys via `private(set)`
- **ListType**: Contains element type (can be merged or union), exposed elementType via `private(set)`
- **HashmapKey**: Represents individual keys with optional flag

## Formatting System
- **TypeFormatterVisitor**: Implements TypeVisitorInterface for formatting
- **Configurable indentation**: 0 (single-line), 2, 4 (default), or custom
- **Multi-line formatting**: Hashmaps and objects get newlines after each element
- **Recursive indentation**: Nested structures properly indented
- **KeyQuotingStyle applied**: NoQuotes, SingleQuotes, or DoubleQuotes
- **Single-line unions**: Union types stay on one line (e.g., `int|string|float`)
- **Backward compatible**: toString() methods unchanged, tests use indentSize: 0

## CLI Interface
- **Command**: `bin/hell2shape`
- **Input**: Reads var_dump output from STDIN
- **Output**: PHPStan type annotations to STDOUT
- **Options**:
  - `--indent/-i <size>`: Indentation size (0 for single-line, default: 4)
  - `--quotes <style>`: Key quoting style (none, single, double) - no shortcut
- **Error Handling**:
  - Empty input validation with helpful usage example
  - Indent validation (must be non-negative integer)
  - Quoting style validation with available options
  - LexerException, ParserException, and generic error catching
- **Usage Example**: `php -r 'var_dump($data);' | bin/hell2shape`

## Immediate Next Steps
1. Add end-to-end integration tests for CLI
2. Consider adding more CLI options if needed

## Open Questions
None - CLI implementation is complete and functional
