# Active Context

## Current Focus
- Intermediate Type IR system implemented
- Hashmap merging functionality working (27/28 tests passing)
- One edge case remaining: mixed lists with hashmaps need grouping logic

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

## Recent Changes
- Implemented intermediate Type IR system (TypeInterface hierarchy)
- Created mergeable type classes: HashmapType, StdObjectType, UnionType, ListType, ScalarType
- Updated TypeGeneratorVisitor to return TypeInterface instead of strings
- Updated Generator to convert Type IR to strings via toString()
- Hashmap merging logic:
  - Missing keys become optional (?)
  - Different types for same key create unions
  - Recursive merging for nested hashmaps
- Removed maxListUnionTypes threshold from tests
- Added comprehensive hashmap merging tests

## Type IR Architecture
- **Two-phase generation**: AST → Type IR → String
- **TypeInterface**: Base interface with merge() and toString() methods
- **ScalarType**: Non-mergeable types (creates unions when merged)
- **UnionType**: Automatic deduplication of union members
- **HashmapType**: Mergeable array shapes with optional key support
- **StdObjectType**: Mergeable stdClass objects (same logic as HashmapType)
- **ListType**: Contains element type (can be merged or union)
- **HashmapKey**: Represents individual keys with optional flag

## Immediate Next Steps
1. Create CLI interface with Symfony Console

## Open Questions
None - Generator design is complete and tested
