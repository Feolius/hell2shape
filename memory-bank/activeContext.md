# Active Context

## Current Focus
- Generator implementation complete
- Ready to move to CLI interface

## Recent Decisions
- Using Lexer/Parser/Generator architecture
- PHP 8.4+ as minimum version
- Symfony Console for CLI interface
- All Node classes are now `readonly` and `final` for immutability
- Visitor pattern with double dispatch for Generator
- KeyQuotingStyle enum for array shape key formatting (NoQuotes, SingleQuotes, DoubleQuotes)
- List union type threshold: max 3 types before falling back to `list<mixed>`
- Empty arrays generate `array` type (not `list<mixed>`)
- StdObject generates `object{...}` syntax (not `array{...}`)

## Recent Changes
- Implemented Generator component with Visitor pattern
- Added abstract `accept()` method to AbstractNode
- Implemented `accept()` in all 14 Node classes for double dispatch
- Created TypeGeneratorVisitor with visit methods for each node type
- Created KeyQuotingStyle enum for configurable key quoting
- Created Generator entry point class
- Added comprehensive test suite (24 tests, all passing)
- Total test coverage: 33 tests (9 Lexer + 24 Generator)

## Generator Implementation Details
- **Scalar types**: Direct mapping (bool, int, float, string, null, resource)
- **Objects**: Class name for regular objects, "object" for anonymous
- **Hashmaps**: `array{key: type, ...}` with configurable key quoting
- **StdObjects**: `object{key: type, ...}` syntax
- **Lists**: Smart union handling with threshold (1 type → `list<T>`, 2-3 types → `list<T1|T2|T3>`, >3 → `list<mixed>`)
- **Empty arrays**: `array` (generic type)

## Immediate Next Steps
1. Create CLI interface with Symfony Console

## Open Questions
None - Generator design is complete and tested
